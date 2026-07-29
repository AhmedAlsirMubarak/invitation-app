<?php

namespace App\Http\Controllers\Admin\Landing;

use App\Http\Controllers\Controller;
use App\Models\LandingFeature;
use Illuminate\Http\Request;

class FeatureController extends Controller
{
    public function index(Request $request)
    {
        $group = $request->query('group', 'core');
        $features = LandingFeature::group($group)->ordered()->get();

        return view('admin.landing.features.index', compact('features', 'group'));
    }

    public function create(Request $request)
    {
        $group = $request->query('group', 'core');

        return view('admin.landing.features.create', compact('group'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        LandingFeature::create($data);

        return redirect()->route('admin.landing.features.index', ['group' => $data['group']])->with('status', __('admin.landing.saved'));
    }

    public function edit(LandingFeature $feature)
    {
        return view('admin.landing.features.edit', compact('feature'));
    }

    public function update(Request $request, LandingFeature $feature)
    {
        $data = $this->validated($request);
        $feature->update($data);

        return redirect()->route('admin.landing.features.index', ['group' => $data['group']])->with('status', __('admin.landing.saved'));
    }

    public function destroy(LandingFeature $feature)
    {
        $group = $feature->group;
        $feature->delete();

        return redirect()->route('admin.landing.features.index', ['group' => $group])->with('status', __('admin.landing.deleted'));
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'group' => ['required', 'string', 'in:core,highlight,how_it_works'],
            'icon' => ['nullable', 'string', 'max:10'],
            'title' => ['required', 'array'],
            'title.ar' => ['required', 'string', 'max:255'],
            'title.en' => ['nullable', 'string', 'max:255'],
            'body' => ['required', 'array'],
            'body.ar' => ['required', 'string'],
            'body.en' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['title']['en'] = $data['title']['en'] ?? '';
        $data['body']['en'] = $data['body']['en'] ?? '';
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
