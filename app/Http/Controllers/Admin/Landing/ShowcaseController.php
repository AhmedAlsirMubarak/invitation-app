<?php

namespace App\Http\Controllers\Admin\Landing;

use App\Http\Controllers\Controller;
use App\Models\LandingShowcaseItem;
use Illuminate\Http\Request;

class ShowcaseController extends Controller
{
    public function index()
    {
        $items = LandingShowcaseItem::ordered()->get();

        return view('admin.landing.showcase.index', compact('items'));
    }

    public function create()
    {
        return view('admin.landing.showcase.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'image' => ['required', 'image', 'max:4096'],
            'caption' => ['nullable', 'array'],
            'caption.ar' => ['nullable', 'string', 'max:255'],
            'caption.en' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        LandingShowcaseItem::create([
            'image_path' => $request->file('image')->store('landing/showcase', 'public'),
            'caption' => $data['caption'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.landing.showcase.index')->with('status', __('admin.landing.saved'));
    }

    public function edit(LandingShowcaseItem $showcase)
    {
        return view('admin.landing.showcase.edit', ['item' => $showcase]);
    }

    public function update(Request $request, LandingShowcaseItem $showcase)
    {
        $data = $request->validate([
            'image' => ['nullable', 'image', 'max:4096'],
            'caption' => ['nullable', 'array'],
            'caption.ar' => ['nullable', 'string', 'max:255'],
            'caption.en' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if ($request->hasFile('image')) {
            $showcase->image_path = $request->file('image')->store('landing/showcase', 'public');
        }
        $showcase->caption = $data['caption'] ?? null;
        $showcase->sort_order = $data['sort_order'] ?? 0;
        $showcase->is_active = $request->boolean('is_active');
        $showcase->save();

        return redirect()->route('admin.landing.showcase.index')->with('status', __('admin.landing.saved'));
    }

    public function destroy(LandingShowcaseItem $showcase)
    {
        $showcase->delete();

        return redirect()->route('admin.landing.showcase.index')->with('status', __('admin.landing.deleted'));
    }
}
