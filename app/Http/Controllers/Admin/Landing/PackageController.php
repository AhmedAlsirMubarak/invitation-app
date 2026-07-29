<?php

namespace App\Http\Controllers\Admin\Landing;

use App\Http\Controllers\Controller;
use App\Models\LandingPackage;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index()
    {
        $packages = LandingPackage::ordered()->get();

        return view('admin.landing.packages.index', compact('packages'));
    }

    public function create()
    {
        return view('admin.landing.packages.create');
    }

    public function store(Request $request)
    {
        LandingPackage::create($this->validated($request));

        return redirect()->route('admin.landing.packages.index')->with('status', __('admin.landing.saved'));
    }

    public function edit(LandingPackage $package)
    {
        return view('admin.landing.packages.edit', compact('package'));
    }

    public function update(Request $request, LandingPackage $package)
    {
        $package->update($this->validated($request));

        return redirect()->route('admin.landing.packages.index')->with('status', __('admin.landing.saved'));
    }

    public function destroy(LandingPackage $package)
    {
        $package->delete();

        return redirect()->route('admin.landing.packages.index')->with('status', __('admin.landing.deleted'));
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'array'],
            'name.ar' => ['required', 'string', 'max:255'],
            'name.en' => ['nullable', 'string', 'max:255'],
            'price_label' => ['nullable', 'array'],
            'price_label.ar' => ['nullable', 'string', 'max:255'],
            'price_label.en' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'array'],
            'description.ar' => ['nullable', 'string'],
            'description.en' => ['nullable', 'string'],
            'badge' => ['nullable', 'array'],
            'badge.ar' => ['nullable', 'string', 'max:100'],
            'badge.en' => ['nullable', 'string', 'max:100'],
            'cta_label' => ['nullable', 'array'],
            'cta_label.ar' => ['nullable', 'string', 'max:100'],
            'cta_label.en' => ['nullable', 'string', 'max:100'],
            'cta_url' => ['nullable', 'url', 'max:500'],
            'features_ar' => ['nullable', 'string'],
            'features_en' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $ar = array_filter(array_map('trim', explode("\n", $data['features_ar'] ?? '')));
        $en = array_filter(array_map('trim', explode("\n", $data['features_en'] ?? '')));
        $count = max(count($ar), count($en));
        $ar = array_values($ar);
        $en = array_values($en);
        $features = [];
        for ($i = 0; $i < $count; $i++) {
            $features[] = ['ar' => $ar[$i] ?? '', 'en' => $en[$i] ?? ''];
        }

        return [
            'name' => ['ar' => $data['name']['ar'], 'en' => $data['name']['en'] ?? ''],
            'price_label' => ['ar' => $data['price_label']['ar'] ?? '', 'en' => $data['price_label']['en'] ?? ''],
            'description' => ['ar' => $data['description']['ar'] ?? '', 'en' => $data['description']['en'] ?? ''],
            'badge' => ($data['badge']['ar'] ?? '') !== '' || ($data['badge']['en'] ?? '') !== ''
                ? ['ar' => $data['badge']['ar'] ?? '', 'en' => $data['badge']['en'] ?? '']
                : null,
            'cta_label' => ['ar' => $data['cta_label']['ar'] ?? '', 'en' => $data['cta_label']['en'] ?? ''],
            'cta_url' => $data['cta_url'] ?? null,
            'features' => $features,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
        ];
    }
}
