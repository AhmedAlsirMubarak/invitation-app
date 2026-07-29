<?php

namespace App\Http\Controllers\Admin\Landing;

use App\Http\Controllers\Controller;
use App\Models\LandingSetting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    // Every text/textarea setting key this form manages, plus the image keys.
    private const TEXT_KEYS = [
        'hero_title', 'hero_subtitle', 'hero_cta', 'trust_statement',
        'features_title', 'showcase_title', 'highlights_title', 'how_title', 'testimonials_title',
        'packages_title', 'faq_title', 'contact_title', 'contact_subtitle',
        'contact_phone', 'footer_rights',
    ];

    private const IMAGE_KEYS = ['hero_image'];

    public function edit()
    {
        $settings = LandingSetting::all()->keyBy('key');

        return view('admin.landing.settings', [
            'settings' => $settings,
            'textKeys' => self::TEXT_KEYS,
            'imageKeys' => self::IMAGE_KEYS,
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'value' => ['array'],
            'value.*.ar' => ['nullable', 'string'],
            'value.*.en' => ['nullable', 'string'],
            'images' => ['array'],
            'images.*' => ['nullable', 'image', 'max:4096'],
        ]);

        foreach (self::TEXT_KEYS as $key) {
            LandingSetting::updateOrCreate(['key' => $key], [
                'type' => 'text',
                'value' => [
                    'ar' => $data['value'][$key]['ar'] ?? '',
                    'en' => $data['value'][$key]['en'] ?? '',
                ],
            ]);
        }

        foreach (self::IMAGE_KEYS as $key) {
            $setting = LandingSetting::firstOrNew(['key' => $key], ['type' => 'image']);
            if ($request->hasFile("images.$key")) {
                $setting->image_path = $request->file("images.$key")->store('landing', 'public');
            }
            $setting->type = 'image';
            $setting->save();
        }

        return back()->with('status', __('admin.landing.saved'));
    }
}
