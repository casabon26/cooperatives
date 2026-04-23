<?php

namespace App\Http\Controllers;

use App\Models\Cooperative;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CooperativeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:manage,cooperative')->only(['edit','update','destroy']);
    }

    public function create()
    {
        return view('admin.cooperatives.create');
    }

    public function store(Request $request)
    {
        $this->authorize('access-admin');
        $data = $request->validate([
            'name'=>'required|string|max:191',
            'sector'=>'nullable|string',
            'region'=>'nullable|string',
            'description'=>'nullable|string',
            'link'=>'nullable|url',
            'status'=>'required|in:pending,active,suspended,archived',
            'image'=>'nullable|file|max:4096',
            // Use 'file' validator to avoid relying on PHP fileinfo MIME guessers; we'll whitelist extensions below
            'gallery_files.*' => 'nullable|file|max:5120',
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time().'_'.preg_replace('/[^A-Za-z0-9._-]/', '_', $file->getClientOriginalName());
            $dest = storage_path('app/public/cooperatives');
            if (!is_dir($dest)) { @mkdir($dest, 0755, true); }
            $file->move($dest, $filename);
            $data['image'] = 'cooperatives/'.$filename;
            // copy to public/cooperative_images for direct serving (fallback if storage symlink missing)
            $publicDir = public_path('cooperative_images');
            if (!is_dir($publicDir)) { @mkdir($publicDir, 0755, true); }
            try { copy($dest.DIRECTORY_SEPARATOR.$filename, $publicDir.DIRECTORY_SEPARATOR.$filename); } catch (\Throwable $e) { /* non-fatal */ }
        }

        // collect profile-related inputs and handle uploaded gallery files
        $profileData = $request->only(['objectives','services','contact_info','mission','vision','achievements','years','members_count','address','contact_phone','contact_email','facebook','twitter','instagram','linkedin','map_embed','operating_hours']);
        // handle uploaded gallery files
        $galleryPaths = [];
        if ($request->hasFile('gallery_files')) {
            $files = $request->file('gallery_files');
            $dest = storage_path('app/public/cooperative_galleries');
            if (!is_dir($dest)) { @mkdir($dest, 0755, true); }
            $publicDir = public_path('cooperative_galleries');
            if (!is_dir($publicDir)) { @mkdir($publicDir, 0755, true); }
            foreach ($files as $file) {
                if (!$file->isValid()) continue;
                // basic extension whitelist check to avoid MIME guessing (works without fileinfo)
                $ext = strtolower(pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION));
                if (!in_array($ext, ['jpg','jpeg','png','gif','webp'])) continue;
                $filename = time().'_'.preg_replace('/[^A-Za-z0-9._-]/', '_', $file->getClientOriginalName());
                $file->move($dest, $filename);
                $rel = 'cooperative_galleries/'.$filename;
                $galleryPaths[] = $rel;
                try { copy($dest.DIRECTORY_SEPARATOR.$filename, $publicDir.DIRECTORY_SEPARATOR.$filename); } catch (\Throwable $e) { /* non-fatal */ }
            }
        }
        if (!empty($galleryPaths)) {
            $profileData['gallery'] = $galleryPaths;
        }

        // merge profile fields into cooperative data and create the cooperative
        $createData = array_merge($data, array_filter($profileData, function($v){ return $v !== null && $v !== ''; }));
        $coop = Cooperative::create($createData);

        // directory overrides were removed: use cooperative fields directly

        return redirect()->route('admin.cooperatives.index')->with('success','Created');
    }

    public function importDefault()
    {
        $this->authorize('access-admin');

        $defaults = [
            ['name'=>'ATEC Employees Multi-Purpose Cooperative','description'=>'Provide financial services, merchandise, and employee welfare benefits.','sector'=>'Service','region'=>'Region 1','status'=>'active'],
            ['name'=>'Cabuyao Athletes Basic School Vendors Consumers Cooperative','description'=>'Serve school vendors and consumers through retail services, savings, and support.','sector'=>'Service','region'=>'Region 1','status'=>'active'],
            ['name'=>'Cabueños Transport Cooperative (CabTransCo)','description'=>'Operate modern jeepney and shuttle transport services.','sector'=>'Transport','region'=>'Region 1','status'=>'active'],
            ['name'=>'Cabuyao Agriculture and Fishery Multi-Purpose Cooperative (CAFMPC)','description'=>'Support farmers and fisherfolk with inputs, credit, and training.','sector'=>'Agriculture','region'=>'Region 1','status'=>'active'],
            ['name'=>'Cabuyao Cityhall Vendors Producers Cooperative','description'=>'Assist city hall vendors and producers with savings and marketing support.','sector'=>'Service','region'=>'Region 1','status'=>'active'],
            ['name'=>'Cabuyao Employees Multi-Purpose Cooperative (CEMPCO)','description'=>'Provide emergency loans, savings, and welfare services to city hall employees.','sector'=>'Service','region'=>'Region 1','status'=>'active'],
            ['name'=>'Cabuyao Malasakit Producers Cooperative','description'=>'Support sustainable agricultural production such as mushrooms and aquaponics.','sector'=>'Agriculture','region'=>'Region 1','status'=>'active'],
            ['name'=>'Cabuyao Market Vendors Multi-Purpose Cooperative (CAMAVEMCO)','description'=>'Provide MSME loans and business assistance to public market vendors.','sector'=>'Service','region'=>'Region 1','status'=>'active'],
            ['name'=>'Cabuyao National High School Personnel Multi-Purpose Cooperative','description'=>'Offer credit, savings, and welfare services to school personnel.','sector'=>'Service','region'=>'Region 1','status'=>'active'],
            ['name'=>'Cabuyao OFW Consumers Cooperative (CAOFWCOOP)','description'=>'Support overseas Filipino workers and their families through consumer goods and savings.','sector'=>'Service','region'=>'Region 1','status'=>'active'],
            ['name'=>'Cabuyao School Personnel Multi-Purpose Cooperative','description'=>'Provide financial and welfare support to school personnel.','sector'=>'Service','region'=>'Region 1','status'=>'active'],
            ['name'=>'Cabuyao Solo Parent Producers Cooperative','description'=>'Assist solo parents through livelihood and income-generating activities.','sector'=>'Service','region'=>'Region 1','status'=>'active'],
            ['name'=>'Casile–Guinting Upland Marketing Cooperative','description'=>'Market agricultural products of upland farmers.','sector'=>'Agriculture','region'=>'Region 1','status'=>'active'],
            ['name'=>'Driving N Safety Transport Cooperative','description'=>'Provide safe and reliable transport services with emphasis on road safety.','sector'=>'Transport','region'=>'Region 1','status'=>'active'],
            ['name'=>'Fastech Employees Multi-Purpose Cooperative (FEMCO)','description'=>'Provide credit, merchandise, calamity loans, and welfare services to employees.','sector'=>'Service','region'=>'Region 1','status'=>'active'],
            ['name'=>'Foodlink Advocacy Cooperative','description'=>'Conduct seminars, trainings, mentoring, and advocacy programs.','sector'=>'Service','region'=>'Region 1','status'=>'active'],
            ['name'=>'Go Ladies Producers Cooperative','description'=>'Empower women through production, marketing, and livelihood activities.','sector'=>'Service','region'=>'Region 1','status'=>'active'],
            ['name'=>'J.A. Services Cooperative','description'=>'Provide labor and manpower services.','sector'=>'Service','region'=>'Region 1','status'=>'active'],
            ['name'=>'Kaagapay ng Pamilya Credit Cooperative','description'=>'Offer credit and savings services focused on family welfare.','sector'=>'Finance','region'=>'Region 1','status'=>'active'],
            ['name'=>'Kababaihan, Kaibigan ng Bigaa Multi-Purpose Cooperative (KABIG MPC)','description'=>'Provide diversified livelihood opportunities for women.','sector'=>'Service','region'=>'Region 1','status'=>'active'],
            ['name'=>'Lakas Kababaihan Credit Cooperative','description'=>'Provide credit and savings services for women.','sector'=>'Finance','region'=>'Region 1','status'=>'active'],
            ['name'=>'Mamatid–Festival Mall Transport and Multi-Purpose Cooperative (MAFESTCO)','description'=>'Operate van transport services and provide member benefits.','sector'=>'Transport','region'=>'Region 1','status'=>'active'],
            ['name'=>'Masigasig na Kababaihan ng Baclaran STK Credit Cooperative','description'=>'Provide credit and savings services to women members.','sector'=>'Finance','region'=>'Region 1','status'=>'active'],
            ['name'=>'Mr. Veggies Multi-Purpose Cooperative','description'=>'Promote health, nutrition, and livelihood through vegetable trading and programs.','sector'=>'Agriculture','region'=>'Region 1','status'=>'active'],
            ['name'=>'Nagkakaisang Samahan ng mga Kababaihan ng Brgy. Sala STK Credit Cooperative','description'=>'Provide credit and savings services to women in the community.','sector'=>'Finance','region'=>'Region 1','status'=>'active'],
            ['name'=>'Nestlé Employees Multi-Purpose Cooperative (NEMPC)','description'=>'Provide loans, appliances, and consumer goods to factory workers.','sector'=>'Service','region'=>'Region 1','status'=>'active'],
            ['name'=>'NEXPEREON Multi-Purpose Cooperative','description'=>'Provide comprehensive financial and consumer services to employees.','sector'=>'Service','region'=>'Region 1','status'=>'active'],
            ['name'=>'Pamana Agriculture Cooperative (PAGCO)','description'=>'Support agricultural production and farming activities.','sector'=>'Agriculture','region'=>'Region 1','status'=>'active'],
            ['name'=>'Pamantasan ng Cabuyao Multi-Purpose Cooperative (PNC-MPC)','description'=>'Provide financial and consumer services to the university community.','sector'=>'Service','region'=>'Region 1','status'=>'active'],
            ['name'=>'Quality Labor Service Cooperative (QLSC)','description'=>'Provide manpower and employment services.','sector'=>'Service','region'=>'Region 1','status'=>'active'],
            ['name'=>'Samahan ng Nagkakaisang Kababaihan Purok 1 Credit Cooperative (SANAKAP)','description'=>'Provide credit and savings services to women members.','sector'=>'Finance','region'=>'Region 1','status'=>'active'],
            ['name'=>'Sumiden Employees’ Multi-Purpose Cooperative (SUMECO)','description'=>'Provide financial and welfare services to employees.','sector'=>'Service','region'=>'Region 1','status'=>'active'],
            ['name'=>'Una ang Malasakit Imahe ng Tunay na Samahan Credit Cooperative (UMITS)','description'=>'Provide compassionate credit and savings services.','sector'=>'Finance','region'=>'Region 1','status'=>'active'],
            ['name'=>'Wyeth Philippines Employees Multi-Purpose Cooperative','description'=>'Provide credit services and employee recognition benefits.','sector'=>'Service','region'=>'Region 1','status'=>'active'],
        ];

        $created = 0;
        foreach ($defaults as $d) {
            $c = Cooperative::firstOrCreate(['name' => $d['name']], $d);
            if ($c->wasRecentlyCreated) $created++;
        }

        return redirect()->route('admin.cooperatives.index')->with('success', "Imported $created cooperatives.");
    }

    public function index()
    {
        // return all cooperatives (no pagination) for admin listing
        $cooperatives = Cooperative::orderBy('name')->get();
        return view('admin.cooperatives.index', compact('cooperatives'));
    }

    /**
     * Show recently deleted (soft-deleted) cooperatives.
     */
    public function trashed()
    {
        $this->authorize('access-admin');
        $cooperatives = Cooperative::onlyTrashed()->orderBy('deleted_at','desc')->get();
        return view('admin.cooperatives.trashed', compact('cooperatives'));
    }

    /** Restore a soft-deleted cooperative */
    public function restore($id)
    {
        $this->authorize('access-admin');
        $coop = Cooperative::onlyTrashed()->where('id', $id)->firstOrFail();
        $coop->restore();
        return redirect()->route('admin.cooperatives.trashed')->with('success', 'Cooperative restored.');
    }

    /** Permanently delete a soft-deleted cooperative */
    public function forceDelete($id)
    {
        $this->authorize('access-admin');
        $coop = Cooperative::onlyTrashed()->where('id', $id)->firstOrFail();
        // Attempt to delete storage files if present (image/gallery)
        try {
            if ($coop->image) {
                $p = storage_path('app/public/'.ltrim($coop->image,'/'));
                if (file_exists($p)) @unlink($p);
                $publicCopy = public_path(basename($coop->image));
                if (file_exists($publicCopy)) @unlink($publicCopy);
            }
        } catch (\Throwable $e) { /* ignore */ }
        $coop->forceDelete();
        return redirect()->route('admin.cooperatives.trashed')->with('success', 'Cooperative permanently deleted.');
    }

    /**
     * Read-only overview for administrators (no edit/delete controls).
     */
    public function overview()
    {
        $this->authorize('access-admin');
        $cooperatives = Cooperative::orderBy('name')->get();
        return view('admin.cooperatives.view', compact('cooperatives'));
    }

    public function edit(Cooperative $cooperative)
    {
        return view('admin.cooperatives.edit', compact('cooperative'));
    }

    public function update(Request $request, Cooperative $cooperative)
    {
        $this->authorize('manage', $cooperative);
        $data = $request->validate([
            'name'=>'required|string|max:191',
            'sector'=>'nullable|string',
            'region'=>'nullable|string',
            'description'=>'nullable|string',
            'link'=>'nullable|url',
            'status'=>'required|in:pending,active,suspended,archived',
            'image'=>'nullable|file|max:4096',
            'gallery_files.*' => 'nullable|file|max:5120',
        ]);

        if ($request->hasFile('image')) {
            // delete old storage file if present
            try {
                if ($cooperative->image) {
                    // attempt Storage disk delete
                    if (class_exists('Illuminate\\Support\\Facades\\Storage')) {
                        try { Storage::disk('public')->delete($cooperative->image); } catch (\Throwable $e) { /* ignore */ }
                    }
                    // attempt to delete public copy
                    try {
                        $oldBase = basename($cooperative->image);
                        $oldPublic = public_path('cooperative_images/'. $oldBase);
                        if (file_exists($oldPublic)) { @unlink($oldPublic); }
                    } catch (\Throwable $e) { /* ignore */ }
                }
            } catch (\Throwable $e) { /* ignore */ }

            $file = $request->file('image');
            $filename = time().'_'.preg_replace('/[^A-Za-z0-9._-]/', '_', $file->getClientOriginalName());
            $dest = storage_path('app/public/cooperatives');
            if (!is_dir($dest)) { @mkdir($dest, 0755, true); }
            $file->move($dest, $filename);
            $data['image'] = 'cooperatives/'.$filename;

            $publicDir = public_path('cooperative_images');
            if (!is_dir($publicDir)) { @mkdir($publicDir, 0755, true); }
            try { copy($dest.DIRECTORY_SEPARATOR.$filename, $publicDir.DIRECTORY_SEPARATOR.$filename); } catch (\Throwable $e) { /* non-fatal */ }
        }

        // collect profile-related inputs
        $profileData = $request->only(['objectives','services','contact_info','mission','vision','achievements','years','members_count','address','contact_phone','contact_email','facebook','twitter','instagram','linkedin','map_embed','operating_hours']);
        $newGallery = [];
        if ($request->hasFile('gallery_files')) {
            $files = $request->file('gallery_files');
            $dest = storage_path('app/public/cooperative_galleries');
            if (!is_dir($dest)) { @mkdir($dest, 0755, true); }
            $publicDir = public_path('cooperative_galleries');
            if (!is_dir($publicDir)) { @mkdir($publicDir, 0755, true); }
            foreach ($files as $file) {
                if (!$file->isValid()) continue;
                $ext = strtolower(pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION));
                if (!in_array($ext, ['jpg','jpeg','png','gif','webp'])) continue;
                $filename = time().'_'.preg_replace('/[^A-Za-z0-9._-]/', '_', $file->getClientOriginalName());
                $file->move($dest, $filename);
                $rel = 'cooperative_galleries/'.$filename;
                $newGallery[] = $rel;
                try { copy($dest.DIRECTORY_SEPARATOR.$filename, $publicDir.DIRECTORY_SEPARATOR.$filename); } catch (\Throwable $e) { /* non-fatal */ }
            }
        }
        // merge new gallery images with existing cooperative gallery
        if (!empty($newGallery)) {
            $existing = $cooperative->gallery ?? [];
            if (!is_array($existing)) $existing = (array) $existing;
            $profileData['gallery'] = array_values(array_merge($existing, $newGallery));
        }

        // merge profile data into main update data
        $updateData = array_merge($data, array_filter($profileData, function($v){ return $v !== null && $v !== ''; }));
        $cooperative->update($updateData);

        // directory overrides removed; public listing uses cooperative fields directly

        return redirect()->route('admin.cooperatives.index')->with('success','Updated');
    }

    public function destroy(Cooperative $cooperative)
    {
        $this->authorize('manage', $cooperative);
        $cooperative->delete();
        return redirect()->route('admin.cooperatives.index')->with('success', 'Deleted');
    }

    /**
     * Update just the cooperative directory card content (edited from public profile by admin)
     */
    public function updateCardContent(Request $request, Cooperative $cooperative)
    {
        $this->authorize('manage', $cooperative);

        $data = $request->validate([
            'card_name' => 'nullable|string|max:255',
            'card_sector' => 'nullable|string|max:255',
            'card_region' => 'nullable|string|max:255',
            'card_description' => 'nullable|string|max:2000',
        ]);

        $dirData = [
            'name' => $data['card_name'] ?? null,
            'sector' => $data['card_sector'] ?? null,
            'region' => $data['card_region'] ?? null,
            'description' => $data['card_description'] ?? null,
        ];

        // Save into the new cooperative_directories table (one-to-one per cooperative)
        $directory = $cooperative->directory;
        if ($directory) {
            $directory->update($dirData);
        } else {
            $cooperative->directory()->create($dirData);
        }

        return redirect()->route('cooperatives.profile', $cooperative)->with('success','Card content saved');
    }

    /**
     * Delete a single gallery image from the cooperative profile.
     * Expects 'path' parameter (relative path stored in profile->gallery).
     */
    public function deleteGalleryImage(Request $request, Cooperative $cooperative)
    {
        $this->authorize('manage', $cooperative);
        $path = $request->input('path');
        if (empty($path)) {
            return redirect()->back()->with('error', 'No image specified.');
        }

        $gallery = $cooperative->gallery ?? [];
        if (!is_array($gallery)) $gallery = (array) $gallery;

        $found = false;
        $new = [];
        foreach ($gallery as $g) {
            if ($g === $path) { $found = true; continue; }
            $new[] = $g;
        }
        if (!$found) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Image not found in gallery.'], 404);
            }
            return redirect()->back()->with('error','Image not found in gallery.');
        }

        // attempt to delete files from storage and public copy
        try {
            $storagePath = storage_path('app/public/'.ltrim($path,'/'));
            if (file_exists($storagePath)) { @unlink($storagePath); }
            $publicPath = public_path(ltrim($path,'/'));
            if (file_exists($publicPath)) { @unlink($publicPath); }
            $publicCopy = public_path('cooperative_galleries/'.basename($path));
            if (file_exists($publicCopy)) { @unlink($publicCopy); }
        } catch (\Throwable $e) { /* ignore deletion errors */ }

        $cooperative->gallery = array_values($new);
        $cooperative->save();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Image removed from gallery.']);
        }

        return redirect()->back()->with('success','Image removed from gallery.');
    }
}
