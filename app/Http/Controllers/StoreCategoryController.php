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
        $request->validate([ 'category' => 'required|string', 'item' => 'required|string', 'map_url' => 'nullable|string' ]);
        $cat = $request->input('category');
        $label = $request->input('item');
        $mapUrl = trim((string) $request->input('map_url')) ?: null;
        $data = $this->readData();
        $items = $data[$cat] ?? [];

        // Normalize new item: if map_url provided, store as object {label, map_url}, otherwise store as string
        $new = $mapUrl ? ['label' => $label, 'map_url' => $mapUrl] : $label;

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
