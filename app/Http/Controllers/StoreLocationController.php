<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\StoreLocation;
use PhpOffice\PhpSpreadsheet\IOFactory;

class StoreLocationController extends Controller
{
    // Admin listing
    public function index()
    {
        $q = request()->query();
        $builder = StoreLocation::query();
        if(!empty($q['category'])) $builder->where('category', $q['category']);
        if(!empty($q['tag'])) $builder->where('tags','like', '%'.$q['tag'].'%');
        if(!empty($q['place'])) $builder->where('place', $q['place']);
        if(!empty($q['store_type'])) $builder->where('store_type', $q['store_type']);
        $locations = $builder->orderBy('created_at','desc')->get();
        return view('admin.store_locations.index', compact('locations'));
    }

    public function create()
    {
        // Load cabstop place options from select_list_items if available
        $cabstops = [];
        try{
            if (\Schema::hasTable('select_list_items')) {
                $cabstops = \App\Models\SelectListItem::where('group','cabstop')->where('active', true)->orderBy('label')->get();
            }
        }catch(\Throwable $e){
            $cabstops = [];
        }
        return view('admin.store_locations.create', compact('cabstops'));
    }

    // Hidden form for uploading CABS MAIN Excel file
    public function cabsImportForm()
    {
        return view('admin.store_locations.import_cabs_main');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'owner_name' => 'nullable|string|max:255',
            'status' => 'nullable|string|in:seasonal,regular,ongoing',
            'address' => 'nullable|string|max:500',
            'category' => 'nullable|string|max:120',
            'tags' => 'nullable|string|max:1000',
            'item' => 'nullable|string|max:255',
            'place' => 'nullable|string|max:191',
            'store_type' => 'required|string|max:50',
            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',
            'map_url' => 'nullable|url',
            'description' => 'nullable|string',
            'icon_url' => 'nullable|url',
        ]);
        // If an 'item' was selected, save it into tags so public API can filter by tag
        if(!empty($data['item'])){
            $data['tags'] = $data['item'];
        }
        // If lat/lng not provided but map_url is, try to parse coordinates from the URL
        if((empty($data['lat']) || empty($data['lng'])) && !empty($data['map_url'])){
            try{
                [$plat, $plng] = $this->parseCoordinatesFromMapUrl($data['map_url']);
                if($plat !== null && $plng !== null){
                    $data['lat'] = $plat; $data['lng'] = $plng;
                }
            }catch(\Exception $e){
                // ignore parsing failure; user can enter coords manually
            }
        }

        try{
            StoreLocation::create($data);
            return redirect()->route('admin.store_locations.index')->with('success','Location added.');
        }catch(\Exception $e){
            return redirect()->back()->withInput()->with('error', 'Could not save location: '.$e->getMessage());
        }
    }

    // Process uploaded Excel/CSV file for CABS MAIN bulk import
    public function cabsImportProcess(Request $request)
    {
        $request->validate([
            'file' => 'required|file',
            'no_update' => 'nullable',
        ]);

        $file = $request->file('file');
        if (!$file || !$file->isValid()) {
            return redirect()->back()->with('error', 'Upload failed.');
        }

        $ext = strtolower($file->getClientOriginalExtension());
        if (!in_array($ext, ['xlsx','xls','csv'])) {
            return redirect()->back()->with('error', 'Only Excel (.xlsx/.xls) or CSV files are supported.');
        }

        $placeVal = 'CABS MAIN';
        $updateExisting = !$request->has('no_update');

        $path = $file->getRealPath();

        // XLSX/XLS handling (requires PhpSpreadsheet)
        if (in_array($ext, ['xlsx','xls'])) {
            try {
                $spreadsheet = IOFactory::load($path);
            } catch (\Throwable $e) {
                return redirect()->back()->with('error', 'Could not read spreadsheet: ' . $e->getMessage());
            }

            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, true);
            if (count($rows) <= 0) {
                return redirect()->back()->with('error', 'Uploaded file is empty.');
            }

            $header = array_shift($rows);
            $map = [];
            foreach ($header as $col => $h) {
                $h = $this->ensureUtf8String((string)$h);
                $lower = strtolower(trim($h));
                $norm = preg_replace('/[^a-z0-9]+/', '_', $lower);
                if (strpos($lower, 'business') !== false || (strpos($lower, 'store') !== false && strpos($lower, 'owner') === false)) {
                    $map[$col] = 'name';
                } elseif (strpos($lower, 'owner') !== false || strpos($norm, 'name_of_owner') !== false) {
                    $map[$col] = 'owner_name';
                } elseif (strpos($lower, 'status') !== false) {
                    $map[$col] = 'status';
                } elseif (strpos($lower, 'store type') !== false || strpos($lower, 'store_type') !== false || strpos($lower, 'type') !== false) {
                    $map[$col] = 'store_type';
                } else {
                    if (strpos($norm, 'owner') !== false) $map[$col] = 'owner_name';
                    elseif (strpos($norm, 'name') !== false) $map[$col] = 'name';
                }
            }

            $created = $updated = $skipped = 0;
            DB::beginTransaction();
            try {
                foreach ($rows as $r) {
                    $row = [];
                    foreach ($map as $col => $field) {
                        $row[$field] = isset($r[$col]) ? $this->ensureUtf8String(trim((string)$r[$col])) : '';
                    }
                    $name = $row['name'] ?? '';
                    $owner = $row['owner_name'] ?? '';
                    $statusRaw = $row['status'] ?? '';
                    $storeType = $row['store_type'] ?? null;

                    if ($name === '') { $skipped++; continue; }

                    $s = strtolower(trim($statusRaw));
                    $normStatus = null;
                    if ($s === '') { $normStatus = null; }
                    elseif (preg_match('/seasonal|red/', $s)) { $normStatus = 'seasonal'; }
                    elseif (preg_match('/regular|green/', $s)) { $normStatus = 'regular'; }
                    elseif (preg_match('/ongoing|blue|on[-_ ]?going/', $s)) { $normStatus = 'ongoing'; }
                    else { $normStatus = $s; }

                    $existing = StoreLocation::where('name', $name)->where('place', $placeVal)->first();
                    if ($existing) {
                        if ($updateExisting) {
                            $existing->owner_name = $owner ?: $existing->owner_name;
                            if ($normStatus) $existing->status = $normStatus;
                            if ($storeType) $existing->store_type = $storeType;
                            $existing->save();
                            $updated++;
                        } else {
                            $skipped++;
                        }
                    } else {
                        StoreLocation::create([
                            'name' => $name,
                            'owner_name' => $owner ?: null,
                            'status' => $normStatus ?: null,
                            'place' => $placeVal,
                            'store_type' => $storeType ?: null,
                        ]);
                        $created++;
                    }
                }
                DB::commit();
            } catch (\Throwable $e) {
                DB::rollBack();
                return redirect()->route('admin.store_locations.index')->with('error', 'Import failed: ' . $e->getMessage());
            }

            $msg = "Import complete. Created: {$created}, Updated: {$updated}, Skipped: {$skipped}";
            return redirect()->route('admin.store_locations.index')->with('success', $msg);
        }

        // CSV fallback: use native PHP parser so web upload works without PhpSpreadsheet
        if ($ext === 'csv') {
            $handle = fopen($path, 'r');
            if (!$handle) return redirect()->back()->with('error', 'Unable to read uploaded CSV.');

            $header = fgetcsv($handle);
            if ($header === false) { fclose($handle); return redirect()->back()->with('error', 'Empty CSV or invalid format.'); }

            $map = [];
            foreach ($header as $i => $h) {
                $h = $this->ensureUtf8String($h);
                $lower = strtolower(trim($h));
                $norm = preg_replace('/[^a-z0-9]+/', '_', $lower);
                if (strpos($lower, 'business') !== false || (strpos($lower, 'store') !== false && strpos($lower, 'owner') === false)) {
                    $map[$i] = 'name';
                } elseif (strpos($lower, 'owner') !== false || strpos($norm, 'name_of_owner') !== false) {
                    $map[$i] = 'owner_name';
                } elseif (strpos($lower, 'status') !== false) {
                    $map[$i] = 'status';
                } elseif (strpos($lower, 'store type') !== false || strpos($lower, 'store_type') !== false || strpos($lower, 'type') !== false) {
                    $map[$i] = 'store_type';
                } else {
                    if (strpos($norm, 'owner') !== false) $map[$i] = 'owner_name';
                    elseif (strpos($norm, 'name') !== false) $map[$i] = 'name';
                }
            }

            $created = $updated = $skipped = 0;
            DB::beginTransaction();
            try {
                while (($row = fgetcsv($handle)) !== false) {
                    $rdata = [];
                    foreach ($map as $i => $field) {
                        $val = isset($row[$i]) ? $this->ensureUtf8String(trim($row[$i])) : '';
                        $rdata[$field] = $val;
                    }

                    $name = $rdata['name'] ?? '';
                    $owner = $rdata['owner_name'] ?? '';
                    $statusRaw = $rdata['status'] ?? '';
                    $storeType = $rdata['store_type'] ?? null;

                    if ($name === '') { $skipped++; continue; }

                    $s = strtolower(trim($statusRaw));
                    $normStatus = null;
                    if ($s === '') { $normStatus = null; }
                    elseif (preg_match('/seasonal|red/', $s)) { $normStatus = 'seasonal'; }
                    elseif (preg_match('/regular|green/', $s)) { $normStatus = 'regular'; }
                    elseif (preg_match('/ongoing|blue|on[-_ ]?going/', $s)) { $normStatus = 'ongoing'; }
                    else { $normStatus = $s; }

                    $existing = StoreLocation::where('name', $name)->where('place', $placeVal)->first();
                    if ($existing) {
                        if ($updateExisting) {
                            $existing->owner_name = $owner ?: $existing->owner_name;
                            if ($normStatus) $existing->status = $normStatus;
                            if ($storeType) $existing->store_type = $storeType;
                            $existing->save();
                            $updated++;
                        } else {
                            $skipped++;
                        }
                    } else {
                        StoreLocation::create([
                            'name' => $name,
                            'owner_name' => $owner ?: null,
                            'status' => $normStatus ?: null,
                            'place' => $placeVal,
                            'store_type' => $storeType ?: null,
                        ]);
                        $created++;
                    }
                }
                DB::commit();
            } catch (\Throwable $e) {
                DB::rollBack();
                fclose($handle);
                return redirect()->route('admin.store_locations.index')->with('error', 'Import failed: ' . $e->getMessage());
            }
            fclose($handle);

            $msg = "Import complete. Created: {$created}, Updated: {$updated}, Skipped: {$skipped}";
            return redirect()->route('admin.store_locations.index')->with('success', $msg);
        }
    }

    public function edit(StoreLocation $store_location)
    {
        $cabstops = [];
        try{
            if (\Schema::hasTable('select_list_items')) {
                $cabstops = \App\Models\SelectListItem::where('group','cabstop')->where('active', true)->orderBy('label')->get();
            }
        }catch(\Throwable $e){
            $cabstops = [];
        }
        return view('admin.store_locations.edit', ['location' => $store_location, 'cabstops' => $cabstops]);
    }

    public function update(Request $request, StoreLocation $store_location)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'owner_name' => 'nullable|string|max:255',
            'status' => 'nullable|string|in:seasonal,regular,ongoing',
            'address' => 'nullable|string|max:500',
            'category' => 'nullable|string|max:120',
            'tags' => 'nullable|string|max:1000',
            'item' => 'nullable|string|max:255',
            'place' => 'nullable|string|max:191',
            'store_type' => 'nullable|string|max:50',
            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',
            'map_url' => 'nullable|url',
            'description' => 'nullable|string',
            'icon_url' => 'nullable|url',
        ]);
        if(!empty($data['item'])){
            $data['tags'] = $data['item'];
        }
        if((empty($data['lat']) || empty($data['lng'])) && !empty($data['map_url'])){
            try{
                [$plat, $plng] = $this->parseCoordinatesFromMapUrl($data['map_url']);
                if($plat !== null && $plng !== null){
                    $data['lat'] = $plat; $data['lng'] = $plng;
                }
            }catch(\Exception $e){}
        }

        try{
            $store_location->update($data);
            return redirect()->route('admin.store_locations.index')->with('success','Location updated.');
        }catch(\Exception $e){
            return redirect()->back()->withInput()->with('error', 'Could not update location: '.$e->getMessage());
        }
    }

    public function destroy(StoreLocation $store_location)
    {
        try{
            $store_location->delete();
            return redirect()->route('admin.store_locations.index')->with('success','Location removed.');
        }catch(\Exception $e){
            return redirect()->route('admin.store_locations.index')->with('error','Could not remove location: '.$e->getMessage());
        }
    }

    // Public API endpoint used by the map
    public function apiList()
    {
        $q = request()->query();
        $builder = StoreLocation::query();
        if(!empty($q['category'])){
            $builder->where('category', $q['category']);
        }
        if(!empty($q['tag'])){
            $tag = $q['tag'];
            $builder->where('tags', 'like', "%{$tag}%");
        }
        // allow filtering by place (cabstop)
        if(!empty($q['place'])){
            $builder->where('place', $q['place']);
        }
        // allow filtering by store type (food or non_food)
        if(!empty($q['store_type'])){
            $builder->where('store_type', $q['store_type']);
        }
        // Support 'map' param to select livelihood vs enterprise results:
        // - map=livelihood => return entries without coordinates (lat and lng null)
        // - map=enterprise => return entries with coordinates (lat and lng not null)
        $map = strtolower($q['map'] ?? $q['display'] ?? '');
        if($map === 'livelihood'){
            $builder->whereNull('lat')->whereNull('lng');
        } elseif($map === 'enterprise'){
            $builder->whereNotNull('lat')->whereNotNull('lng');
        }

        // Ensure owner_name is not exposed via the public JSON API
        return response()->json($builder->get()->makeHidden(['owner_name']));
    }

    // Try to extract coordinates from common Google Maps URLs or use Nominatim fallback
    protected function parseCoordinatesFromMapUrl(string $url): array
    {
        // Try to find /@lat,lng pattern
        $coords = null;
        if(preg_match('/@(-?\d+\.\d+),(-?\d+\.\d+)/', $url, $m)){
            return [ (float)$m[1], (float)$m[2] ];
        }

        // Try query parameter q= which may contain lat,lng or place text
        $parts = parse_url($url);
        if(!empty($parts['query'])){
            parse_str($parts['query'], $qs);
            if(!empty($qs['q'])){
                $q = $qs['q'];
                // if q contains comma-separated coords
                if(preg_match('/(-?\d+\.\d+)\s*,\s*(-?\d+\.\d+)/', $q, $m)){
                    return [ (float)$m[1], (float)$m[2] ];
                }
                // if q looks like a plus code (contains +)
                if(strpos($q, '+') !== false){
                    // try decode using Open Location Code web service via Nominatim attempt
                    $decoded = $this->nominatimGeocode($q);
                    if($decoded) return $decoded;
                }
                // else fall back to nominatim
                $decoded = $this->nominatimGeocode($q);
                if($decoded) return $decoded;
            }
        }

        // Try to extract path segments that include @ or coordinates
        if(!empty($parts['path']) && preg_match('/@(-?\d+\.\d+),(-?\d+\.\d+)/', $parts['path'], $m)){
            return [ (float)$m[1], (float)$m[2] ];
        }

        // Last resort: attempt to use the whole URL as a query to Nominatim
        $decoded = $this->nominatimGeocode($url);
        if($decoded) return $decoded;

        return [null, null];
    }

    protected function nominatimGeocode(string $query): ?array
    {
        $url = 'https://nominatim.openstreetmap.org/search?format=json&limit=1&q=' . urlencode($query);
        $opts = [
            'http' => [
                'method' => 'GET',
                'header' => "User-Agent: Cooperative-Portal/1.0\r\nAccept: application/json\r\n"
            ]
        ];
        $context = stream_context_create($opts);
        $resp = @file_get_contents($url, false, $context);
        if(!$resp) return null;
        $json = json_decode($resp, true);
        if(is_array($json) && count($json)){
            return [ (float)$json[0]['lat'], (float)$json[0]['lon'] ];
        }
        return null;
    }

    // Ensure a string is valid UTF-8, attempt conversion from common encodings
    protected function ensureUtf8String($s)
    {
        if ($s === null) return $s;
        $s = preg_replace('/^\xEF\xBB\xBF/', '', (string)$s);
        if (mb_check_encoding($s, 'UTF-8')) return $s;
        $enc = mb_detect_encoding($s, ['UTF-8', 'Windows-1252', 'ISO-8859-1', 'ASCII'], true);
        if ($enc && strtoupper($enc) !== 'UTF-8') {
            return mb_convert_encoding($s, 'UTF-8', $enc);
        }
        return mb_convert_encoding($s, 'UTF-8', 'Windows-1252');
    }
}
