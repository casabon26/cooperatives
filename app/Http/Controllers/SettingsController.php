<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Setting;

class SettingsController extends Controller
{
    public function index()
    {
        // Company-style settings grouped logically
        $fields = [
            'general' => [
                'site_name' => ['label' => 'Site Name', 'type' => 'string', 'placeholder' => 'My Cooperative Portal'],
                'tagline' => ['label' => 'Tagline', 'type' => 'string', 'placeholder' => 'Services for cooperatives'],
                'logo' => ['label' => 'Logo', 'type' => 'file'],
            ],
            'branding' => [
                'primary_color' => ['label' => 'Primary Color', 'type' => 'color', 'placeholder' => '#B82132'],
                'accent_color' => ['label' => 'Accent Color', 'type' => 'color', 'placeholder' => '#fca5a5'],
            ],
            'contact' => [
                'contact_email' => ['label' => 'Contact Email', 'type' => 'email', 'placeholder' => 'info@example.com'],
                'contact_phone' => ['label' => 'Contact Phone', 'type' => 'string', 'placeholder' => '+63 912 345 6789'],
                'address' => ['label' => 'Address', 'type' => 'textarea', 'placeholder' => '123 Main St, City'],
            ],
            'social' => [
                'facebook' => ['label' => 'Facebook URL', 'type' => 'string', 'placeholder' => 'https://facebook.com/...'],
                'twitter' => ['label' => 'Twitter URL', 'type' => 'string', 'placeholder' => 'https://twitter.com/...'],
            ],
            'advanced' => [
                'google_analytics_id' => ['label' => 'Google Analytics ID', 'type' => 'string', 'placeholder' => 'G-XXXXXXX'],
                'per_page' => ['label' => 'Items Per Page', 'type' => 'number', 'placeholder' => '10'],
                'maintenance_mode' => ['label' => 'Maintenance Mode', 'type' => 'checkbox'],
                'footer_text' => ['label' => 'Footer Text', 'type' => 'string', 'placeholder' => '© 2026 Cooperative Portal'],
            ],
        ];

        $settings = Setting::pluck('value','key')->toArray();

        return view('settings.index', compact('fields','settings'));
    }

    public function update(Request $request)
    {
        $rules = [
            'site_name' => 'nullable|string|max:191',
            'tagline' => 'nullable|string|max:255',
            'primary_color' => 'nullable|string|regex:/^#?[0-9A-Fa-f]{6}$/',
            'accent_color' => 'nullable|string|regex:/^#?[0-9A-Fa-f]{6}$/',
            'per_page' => 'nullable|integer|min:1|max:100',
            'contact_email' => 'nullable|email',
            'contact_phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'facebook' => 'nullable|url',
            'twitter' => 'nullable|url',
            'google_analytics_id' => 'nullable|string|max:64',
            'footer_text' => 'nullable|string|max:255',
            'maintenance_mode' => 'nullable',
            'logo' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:10240',
        ];

        $validated = $request->validate($rules);

        // Handle logo upload separately
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $path = $file->store('settings', 'public');
            Setting::set('logo', $path);
        }

        // Normalize checkbox
        $validated['maintenance_mode'] = $request->has('maintenance_mode') ? '1' : '0';

        // Persist other settings
        $storeKeys = ['site_name','tagline','primary_color','accent_color','per_page','contact_email','contact_phone','address','facebook','twitter','google_analytics_id','footer_text','maintenance_mode'];
        foreach($storeKeys as $key) {
            if(array_key_exists($key, $validated)){
                Setting::set($key, is_array($validated[$key]) ? json_encode($validated[$key]) : $validated[$key]);
            }
        }

        return redirect()->back()->with('status','Settings saved.');
    }
}
