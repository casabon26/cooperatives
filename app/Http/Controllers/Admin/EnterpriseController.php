<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Enterprise;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class EnterpriseController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search', '');
        $size = $request->input('size', '');
        
        $query = Enterprise::query();
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('industry', 'like', '%' . $search . '%')
                  ->orWhere('address', 'like', '%' . $search . '%');
            });
        }
        
        if ($size) {
            $query->where('category', $size);
        }
        
        $enterprises = $query->orderBy('created_at','desc')->paginate(15);
        $sizes = ['Micro', 'Small', 'Medium', 'Large', 'Unknown'];
        
        return view('admin.enterprises.index', compact('enterprises', 'search', 'size', 'sizes'));
    }

    /**
     * Show CSV import form.
     */
    public function importForm()
    {
        $hasSpreadsheet = class_exists('\PhpOffice\\PhpSpreadsheet\\IOFactory');
        return view('admin.enterprises.import', compact('hasSpreadsheet'));
    }

    /**
     * Process CSV import.
     */
    public function importProcess(Request $request)
    {
        $request->validate([ 'file' => 'required|file' ]);

        $file = $request->file('file');
        $ext = strtolower($file->getClientOriginalExtension());

        $inserted = 0;
        $skipped = 0;
        $defaultImage = 'assets/images/default-enterprise.svg';

        // Handle CSV or Excel (XLS/XLSX) files
        if(in_array($ext, ['csv'])){
            $handle = fopen($file->getRealPath(), 'r');
            if(!$handle) return redirect()->back()->with('error', 'Could not open uploaded file.');

            // detect delimiter from first line (support TSV files)
            $firstLine = fgets($handle);
            if($firstLine === false) { fclose($handle); return redirect()->back()->with('error','Empty file'); }
            rewind($handle);
            $delimiter = strpos($firstLine, "\t") !== false ? "\t" : (substr_count($firstLine,';') > substr_count($firstLine,',') ? ';' : ',');

            // read header with detected delimiter and normalize keys
            $headerRow = fgetcsv($handle, 0, $delimiter);
            if(!$headerRow){ fclose($handle); return redirect()->back()->with('error','Could not read header row'); }
            $header = array_map(function($h){ return trim(strtolower(str_replace(['_','-'], ' ', (string)$h))); }, $headerRow);

            // helper to find first matching key from candidates (normalize candidates same way)
            $find = function($assoc, $candidates){
                foreach($candidates as $c){
                    $k = trim(strtolower(str_replace(['_','-'],' ', (string)$c)));
                    if(array_key_exists($k, $assoc) && trim((string)$assoc[$k]) !== '') return $assoc[$k];
                }
                return null;
            };

            while(($row = fgetcsv($handle, 0, $delimiter)) !== false){
                // skip empty rows
                if(count($row) === 1 && trim($row[0]) === '') continue;

                $data = @array_combine($header, $row);
                if(!$data) { $skipped++; continue; }

                $account = $find($data, ['account no','account number','acct no','acct number','acctno','acct']);
                $name = $find($data, ['business name','business name','name','acctname']);
                $address = $find($data, ['business address','business address','address']);
                $industry = $find($data, ['nature of business','nature of business','nature','business nature','industry/line','industry','line']);
                $size = $find($data, ['enterprise','enterprise type','size','category']);

                $name = $name ? trim($name) : null;
                if(!$name){
                    $fallback = $industry ?? $account ?? $address ?? $size ?? null;
                    if(!$fallback){
                        $fallback = trim(implode(' ', array_filter($row, function($v){ return trim((string)$v) !== ''; })));
                    }
                    $name = $fallback ? trim(preg_replace('/\s+/', ' ', (string)$fallback)) : null;
                }
                if(!$name){ $skipped++; continue; }

                $size = $size ? trim(ucfirst(strtolower($size))) : 'Micro';
                $allowed = ['Micro','Small','Medium','Large','Unknown'];
                if(!in_array($size, $allowed)) $size = 'Micro';

                try{
                    Enterprise::create([
                        'account_no' => $account ? trim($account) : null,
                        'name' => $name,
                        'address' => $address ? trim($address) : null,
                        'industry' => $industry ? trim($industry) : null,
                        'nature_of_business' => $industry ? trim($industry) : null,
                        'category' => $size,
                        'summary' => null,
                        'description' => null,
                        'image' => $defaultImage,
                    ]);
                    $inserted++;
                }catch(\Throwable $e){ $skipped++; }
            }
            fclose($handle);

        } elseif(in_array($ext, ['xls','xlsx'])){
            if(!class_exists('\\PhpOffice\\PhpSpreadsheet\\IOFactory')){
                return redirect()->back()->with('error', 'XLS/XLSX files are supported but the PhpSpreadsheet library is not installed. Run: composer require phpoffice/phpspreadsheet');
            }

            try{
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());
                $sheet = $spreadsheet->getActiveSheet();
                $rows = $sheet->toArray(null, true, true, true);
                // rows is 1-indexed; first non-empty row used as header
                $header = null;
                foreach($rows as $r){
                    // normalize row to numeric-indexed array of values
                    $values = array_values($r);
                    // skip empty rows
                    $nonEmpty = array_filter($values, function($v){ return trim((string)$v) !== ''; });
                    if(empty($nonEmpty)) continue;

                        if($header === null){
                            $header = array_map(function($h){ return trim(strtolower(str_replace(['_','-'],' ', (string)$h))); }, $values);
                            continue;
                        }

                        $assoc = array_combine($header, $values);
                        if(!$assoc){ $skipped++; continue; }

                        // helper to find first matching key from candidates (normalize candidates same way)
                        $find = function($assoc, $candidates){
                            foreach($candidates as $c){
                                $k = trim(strtolower(str_replace(['_','-'],' ', (string)$c)));
                                if(array_key_exists($k, $assoc) && trim((string)$assoc[$k]) !== '') return $assoc[$k];
                            }
                            return null;
                        };

                        $account = $find($assoc, ['account no','account number','acct no','acct number','acctno','acct']);
                        $name = $find($assoc, ['business name','business_name','name']);
                        $address = $find($assoc, ['business address','business_address','address']);
                        $industry = $find($assoc, ['nature of business','nature_of_business','nature','business nature','industry/line','industry','line']);
                        $size = $find($assoc, ['enterprise','enterprise type','size','category']);

                        $name = $name ? trim($name) : null;
                        if(!$name){
                            $fallback = $industry ?? $account ?? $address ?? $size ?? null;
                            if(!$fallback){
                                $fallback = trim(implode(' ', array_filter($values, function($v){ return trim((string)$v) !== ''; })));
                            }
                            $name = $fallback ? trim(preg_replace('/\s+/', ' ', (string)$fallback)) : null;
                        }
                        if(!$name){ $skipped++; continue; }

                    $size = $size ? trim(ucfirst(strtolower($size))) : 'Micro';
                    $allowed = ['Micro','Small','Medium','Large','Unknown'];
                    if(!in_array($size, $allowed)) $size = 'Micro';

                    try{
                        Enterprise::create([
                            'account_no' => $account ? trim($account) : null,
                            'name' => $name,
                            'address' => $address ? trim($address) : null,
                            'industry' => $industry ? trim($industry) : null,
                            'nature_of_business' => $industry ? trim($industry) : null,
                            'category' => $size,
                            'summary' => null,
                            'description' => null,
                            'image' => $defaultImage,
                        ]);
                        $inserted++;
                    }catch(\Throwable $e){ $skipped++; }
                }
            }catch(\Throwable $e){
                return redirect()->back()->with('error', 'Failed to parse the Excel file: ' . $e->getMessage());
            }

        } else {
            return redirect()->back()->with('error', 'Unsupported file type. Please upload CSV, XLS or XLSX.');
        }

        return redirect()->back()->with('success', "Import completed. Inserted: {$inserted}. Skipped: {$skipped}.");
    }

    public function create()
    {
        return view('admin.enterprises.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'industry' => 'nullable|string|max:255',
            'category' => 'required|in:Micro,Small,Medium,Large,Unknown',
            // Use `file` instead of `image` to avoid requiring the fileinfo extension for MIME guessing
            'image' => 'nullable|file|max:5120',
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time().'_'.preg_replace('/[^A-Za-z0-9._-]/', '_', $file->getClientOriginalName());
            $dest = storage_path('app/public/enterprises');
            if (!is_dir($dest)) { mkdir($dest, 0755, true); }
            $file->move($dest, $filename);
            $data['image'] = 'enterprises/'.$filename;
            // copy to public/enterprises for direct serving if needed
            $publicDir = public_path('enterprises');
            if (!is_dir($publicDir)) { @mkdir($publicDir, 0755, true); }
            try { copy($dest.DIRECTORY_SEPARATOR.$filename, $publicDir.DIRECTORY_SEPARATOR.$filename); } catch (\Throwable $e) { /* non-fatal */ }

            // store base64/mime metadata similar to news controller to avoid relying on finfo
            try {
                $contents = file_get_contents($dest.DIRECTORY_SEPARATOR.$filename);
                if ($contents !== false) {
                    $data['image_data'] = base64_encode($contents);
                    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                    $map = ['jpg'=>'image/jpeg','jpeg'=>'image/jpeg','png'=>'image/png','gif'=>'image/gif','webp'=>'image/webp','svg'=>'image/svg+xml','bmp'=>'image/bmp'];
                    $data['image_mime'] = $map[$ext] ?? 'image/jpeg';
                    $data['image_filename'] = $file->getClientOriginalName();
                }
            } catch (\Throwable $e) { /* non-fatal */ }
        }

        Enterprise::create($data);
        return redirect()->route('admin.enterprises.index')->with('success', 'Enterprise created.');
    }

    public function edit($id)
    {
        $enterprise = Enterprise::findOrFail($id);
        return view('admin.enterprises.edit', compact('enterprise'));
    }

    public function update(Request $request, $id)
    {
        $enterprise = Enterprise::findOrFail($id);
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'industry' => 'nullable|string|max:255',
            'category' => 'required|in:Micro,Small,Medium,Large,Unknown',
            'image' => 'nullable|file|max:5120',
        ]);

        if ($request->hasFile('image')) {
            // delete old storage file and public copy if present
            if ($enterprise->image) {
                try {
                    $oldBase = basename($enterprise->image);
                    $oldStorage = storage_path('app/public/'. $oldBase);
                    if (file_exists($oldStorage)) { @unlink($oldStorage); }
                } catch (\Throwable $e) { Log::warning('Could not unlink old enterprise storage file', ['id'=>$enterprise->id, 'error'=>$e->getMessage()]); }
                try {
                    $oldPublic = public_path('enterprises/'. basename($enterprise->image));
                    if (file_exists($oldPublic)) { @unlink($oldPublic); }
                } catch (\Throwable $e) { /* ignore */ }
            }

            $file = $request->file('image');
            $filename = time().'_'.preg_replace('/[^A-Za-z0-9._-]/', '_', $file->getClientOriginalName());
            $dest = storage_path('app/public/enterprises');
            if (!is_dir($dest)) { mkdir($dest, 0755, true); }
            $file->move($dest, $filename);
            $data['image'] = 'enterprises/'.$filename;

            $publicDir = public_path('enterprises');
            if (!is_dir($publicDir)) { @mkdir($publicDir, 0755, true); }
            try { copy($dest.DIRECTORY_SEPARATOR.$filename, $publicDir.DIRECTORY_SEPARATOR.$filename); } catch (\Throwable $e) { /* non-fatal */ }

            try {
                $contents = file_get_contents($dest.DIRECTORY_SEPARATOR.$filename);
                if ($contents !== false) {
                    $data['image_data'] = base64_encode($contents);
                    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                    $map = ['jpg'=>'image/jpeg','jpeg'=>'image/jpeg','png'=>'image/png','gif'=>'image/gif','webp'=>'image/webp','svg'=>'image/svg+xml','bmp'=>'image/bmp'];
                    $data['image_mime'] = $map[$ext] ?? 'image/jpeg';
                    $data['image_filename'] = $file->getClientOriginalName();
                }
            } catch (\Throwable $e) { /* non-fatal */ }
        }

        $enterprise->update($data);
        return redirect()->route('admin.enterprises.index')->with('success', 'Enterprise updated.');
    }

    public function destroy($id)
    {
        $enterprise = Enterprise::findOrFail($id);
        $this->performDelete($enterprise);
        return redirect()->route('admin.enterprises.index')->with('success', 'Enterprise deleted.');
    }

    /**
     * Perform deletion of an enterprise and its stored files.
     * Returns true on success, false on failure.
     */
    protected function performDelete(Enterprise $enterprise): bool
    {
        try {
            if ($enterprise->image) {
                try {
                    $base = basename($enterprise->image);
                    $storageFile = storage_path('app/public/' . $base);
                    if (file_exists($storageFile)) { @unlink($storageFile); }
                } catch (\Throwable $e) { Log::warning('Failed to unlink enterprise storage file', ['id'=>$enterprise->id, 'error'=>$e->getMessage()]); }

                try {
                    $publicCopy = public_path('enterprises/' . basename($enterprise->image));
                    if (file_exists($publicCopy)) { @unlink($publicCopy); }
                } catch (\Throwable $e) { /* ignore */ }
            }

            $enterprise->delete();
            return true;
        } catch (\Throwable $e) {
            Log::warning('Failed to delete enterprise', ['id'=>$enterprise->id, 'error'=>$e->getMessage()]);
            return false;
        }
    }

    /**
     * Bulk delete enterprises selected in admin panel.
     */
    public function bulkDelete(Request $request)
    {
        $selectAll = $request->input('select_all') === '1';
        $deleted = 0;

        if ($selectAll) {
            $search = $request->input('search', '');
            $size = $request->input('size', '');

            $query = Enterprise::query();
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                      ->orWhere('industry', 'like', '%' . $search . '%')
                      ->orWhere('address', 'like', '%' . $search . '%');
                });
            }
            if ($size) {
                $query->where('category', $size);
            }

            $query->orderBy('created_at','desc')->chunk(100, function($rows) use (&$deleted) {
                foreach ($rows as $e) {
                    if ($this->performDelete($e)) $deleted++;
                }
            });

            return redirect()->route('admin.enterprises.index')->with('success', "{$deleted} enterprises deleted.");
        }

        $ids = $request->input('ids', []);
        if (!is_array($ids) || empty($ids)) {
            return redirect()->route('admin.enterprises.index')->with('error', 'No enterprises selected.');
        }

        // Process explicit ids (current page selection)
        Enterprise::whereIn('id', $ids)->chunk(100, function($rows) use (&$deleted) {
            foreach ($rows as $e) {
                if ($this->performDelete($e)) $deleted++;
            }
        });

        return redirect()->route('admin.enterprises.index')->with('success', "{$deleted} enterprises deleted.");
    }

    /**
     * Return a JSON list of enterprises for the given category.
     * This is a simple placeholder endpoint; replace with real DB queries later.
     */
    public function list(\Illuminate\Http\Request $request)
    {
        $cat = $request->get('category', 'Micro');
        $cat = ucfirst(strtolower(trim($cat)));
        $allowed = ['Micro','Small','Medium','Large','Unknown'];
        if(!in_array($cat, $allowed)){
            return response()->json(['data' => []]);
        }

        // Return enterprises from DB for the requested category
        $query = \App\Models\Enterprise::where('category', $cat);

        // Apply search filter if 'q' parameter is provided
        $q = $request->get('q');
        if(!empty($q)){
            $q = trim($q);
            $query->where(function($subquery) use ($q){
                $subquery->where('name', 'like', '%'.$q.'%')
                    ->orWhere('summary', 'like', '%'.$q.'%');
            });
        }

        // select only columns that exist to avoid SQL errors if migrations haven't been run yet
        $cols = ['id','name','address','industry','category','summary','image'];
        if (Schema::hasColumn('enterprises', 'account_no')) { array_splice($cols, 1, 0, 'account_no'); }
        if (Schema::hasColumn('enterprises', 'nature_of_business')) { array_splice($cols, 4, 0, 'nature_of_business'); }
        $enterprises = $query->orderBy('created_at', 'desc')->get($cols);
        $total = $enterprises->count();

        // include public URL for each enterprise
        $enterprises = $enterprises->map(function($e){
            $imageUrl = null;
            if (!empty($e->image)) {
                $img = str_replace('\\', '/', $e->image);
                // Check for a public copy (public/enterprises/...)
                if (file_exists(public_path($img))) {
                    $imageUrl = asset($img);
                } elseif (file_exists(public_path('storage/'.$img))) {
                    $imageUrl = asset('storage/'.$img);
                } elseif (preg_match('/^https?:\/\//', $img)) {
                    $imageUrl = $img;
                }
            }
            return [
                'id' => $e->id,
                'account_no' => isset($e->account_no) ? $e->account_no : null,
                'name' => $e->name,
                'address' => $e->address ?? null,
                'industry' => $e->industry ?? null,
                'nature_of_business' => isset($e->nature_of_business) ? $e->nature_of_business : null,
                'category' => $e->category ?? null,
                'summary' => $e->summary ?? null,
                'image' => $e->image ?? null,
                'image_url' => $imageUrl,
                'url' => url('/enterprises/'.$e->id),
            ];
        });

        return response()->json(['data' => $enterprises, 'total' => $total]);
    }
}
