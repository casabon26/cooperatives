<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StoreCategoryController extends Controller
{
    protected $path;

    public function __construct()
    {
        $this->path = resource_path('data/store_categories.json');
        if(!file_exists($this->path)){
            @file_put_contents($this->path, json_encode(['Food & Beverages' => ['siomai','empanada','milktea']], JSON_PRETTY_PRINT));
        }
    }

    public function storeItem(Request $request)
    {
        $request->validate([ 'category' => 'required|string', 'item' => 'required|string', 'map_url' => 'nullable|string', 'store_type' => 'nullable|string', 'origin' => 'nullable|string' ]);
        $cat = $request->input('category');
        $label = $request->input('item');
        $mapUrl = trim((string) $request->input('map_url')) ?: null;
        $storeType = trim((string) $request->input('store_type')) ?: null;
        $origin = trim((string) $request->input('origin')) ?: null;
        $data = $this->readData();
        $items = $data[$cat] ?? [];

        // Normalize new item: include map_url, store_type and optional origin when provided
        if($mapUrl || $storeType || $origin){
            $new = ['label' => $label];
            if($mapUrl) $new['map_url'] = $mapUrl;
            if($storeType) $new['store_type'] = $storeType;
            if($origin) $new['origin'] = $origin;
        } else {
            $new = $label;
        }

        // Avoid duplicates by label
        $exists = false;
        foreach($items as $it){
            if(is_array($it) && array_key_exists('label', $it) && $it['label'] === $label){ $exists = true; break; }
            if(!is_array($it) && $it === $label){ $exists = true; break; }
        }
        if(!$exists){
            $items[] = $new;
            $data[$cat] = array_values($items);
            $this->writeData($data);
        }
        return redirect()->back()->with('success','Item added.');
    }

    public function updateItem(Request $request)
    {
        $request->validate([ 'category' => 'required|string', 'old_item' => 'required|string', 'new_item' => 'required|string', 'map_url' => 'nullable|string', 'store_type' => 'nullable|string' ]);
        $cat = $request->input('category');
        $old = $request->input('old_item');
        $newLabel = $request->input('new_item');
        $mapUrl = trim((string) $request->input('map_url')) ?: null;
        $storeType = trim((string) $request->input('store_type')) ?: null;
        $data = $this->readData();
        if(empty($data[$cat])) return redirect()->back()->with('success','Category not found.');
        // Remove old
        $data[$cat] = array_values(array_filter($data[$cat], function($v) use($old){
            if(is_array($v) && array_key_exists('label', $v)) return $v['label'] !== $old;
            return $v !== $old;
        }));
        // Add new
        $new = $mapUrl || $storeType ? ['label' => $newLabel] : $newLabel;
        if(is_array($new)){
            if($mapUrl) $new['map_url'] = $mapUrl;
            if($storeType) $new['store_type'] = $storeType;
        }
        $data[$cat][] = $new;
        $this->writeData($data);
        return redirect()->back()->with('success','Item updated.');
    }

    public function updateCategory(Request $request)
    {
        $request->validate([ 'old_category' => 'required|string', 'new_category' => 'required|string' ]);
        $old = $request->input('old_category');
        $new = $request->input('new_category');
        $data = $this->readData();
        if(!array_key_exists($old, $data)) return redirect()->back()->with('success','Category not found.');
        if($old === $new) return redirect()->back()->with('success','No change.');
        // Avoid overwrite
        if(array_key_exists($new, $data)) return redirect()->back()->with('success','Target category already exists.');
        $data[$new] = $data[$old];
        unset($data[$old]);
        $this->writeData($data);
        return redirect()->back()->with('success','Category renamed.');
    }

    public function storeCategory(Request $request)
    {
        $request->validate([ 'category' => 'required|string' ]);
        $cat = $request->input('category');
        $data = $this->readData();
        if(!array_key_exists($cat, $data)){
            $data[$cat] = [];
            $this->writeData($data);
        }
        return redirect()->back()->with('success','Category added.');
    }

    public function create()
    {
        return view('admin.store_categories.create');
    }

    public function deleteItem(Request $request)
    {
        $request->validate([ 'category' => 'required|string', 'item' => 'required|string' ]);
        $cat = $request->input('category');
        $item = $request->input('item');
        $data = $this->readData();
        if(!empty($data[$cat])){
            $data[$cat] = array_values(array_filter($data[$cat], function($v) use($item){
                if(is_array($v) && array_key_exists('label', $v)) return $v['label'] !== $item;
                return $v !== $item;
            }));
            $this->writeData($data);
        }
        return redirect()->back()->with('success','Item removed.');
    }

    public function deleteCategory(Request $request)
    {
        $request->validate([ 'category' => 'required|string' ]);
        $cat = $request->input('category');
        $data = $this->readData();
        if(array_key_exists($cat, $data)){
            unset($data[$cat]);
            $this->writeData($data);
        }
        return redirect()->back()->with('success','Category removed.');
    }

    protected function readData()
    {
        $json = @file_get_contents($this->path);
        $data = json_decode($json, true);
        if(!is_array($data)) $data = [];
        return $data;
    }

    protected function writeData(array $data)
    {
        @file_put_contents($this->path, json_encode($data, JSON_PRETTY_PRINT));
    }
}
