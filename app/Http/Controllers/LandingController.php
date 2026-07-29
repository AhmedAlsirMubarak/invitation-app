<?php

namespace App\Http\Controllers;

use App\Models\LandingFaq;
use App\Models\LandingFeature;
use App\Models\LandingPackage;
use App\Models\LandingSetting;
use App\Models\LandingShowcaseItem;
use App\Models\LandingTestimonial;
use Illuminate\Support\Facades\Storage;

class LandingController extends Controller
{
    public function index()
    {
        $locale = app()->getLocale();

        $settings = LandingSetting::all()->mapWithKeys(function (LandingSetting $setting) use ($locale) {
            if ($setting->type === 'image') {
                return [$setting->key => $setting->image_path ? Storage::disk('public')->url($setting->image_path) : null];
            }

            $value = $setting->value ?? [];

            return [$setting->key => $value[$locale] ?? $value['ar'] ?? ''];
        });

        $coreFeatures = LandingFeature::group('core')->active()->ordered()->get();
        $highlightFeatures = LandingFeature::group('highlight')->active()->ordered()->get();
        $howItWorks = LandingFeature::group('how_it_works')->active()->ordered()->get();
        $showcaseItems = LandingShowcaseItem::active()->ordered()->get();
        $testimonials = LandingTestimonial::active()->ordered()->get();
        $packages = LandingPackage::active()->ordered()->get();
        $faqs = LandingFaq::active()->ordered()->get();

        return view('landing', compact(
            'settings', 'coreFeatures', 'highlightFeatures', 'howItWorks',
            'showcaseItems', 'testimonials', 'packages', 'faqs'
        ));
    }
}
