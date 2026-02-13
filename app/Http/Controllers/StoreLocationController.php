<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StoreLocation;

class StoreLocationController extends Controller
{
    // Admin listing
    public function index()
    {
        $q = request()->query();
        $builder = StoreLocation::query();
        if(!empty($q['category'])) $builder->where('category', $q['category']);
        if(!empty($q['tag'])) $builder->where('tags','like', '%'.$q['tag'].'%');
        $locations = $builder->orderBy('created_at','desc')->get();
        return view('admin.store_locations.index', compact('locations'));
    }

    public function create()
    {
        return view('admin.store_locations.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'category' => 'nullable|string|max:120',
            'tags' => 'nullable|string|max:1000',
            'item' => 'nullable|string|max:255',
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

    public function edit(StoreLocation $store_location)
    {
        return view('admin.store_locations.edit', ['location' => $store_location]);
    }

    public function update(Request $request, StoreLocation $store_location)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'category' => 'nullable|string|max:120',
            'tags' => 'nullable|string|max:1000',
            'item' => 'nullable|string|max:255',
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
        return response()->json($builder->get());
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
}
