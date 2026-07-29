<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Template;
use App\Support\EventTypes;
use Illuminate\Http\Request;

class TemplateController extends Controller
{
    // Internal design library: reusable invite "skins" you build once
    // and reuse across many client events.
    public function index(Request $request)
    {
        $templates = Template::when($request->category, fn ($q, $cat) => $q->where('category', $cat))
            ->orderBy('category')
            ->orderBy('name')
            ->get()
            ->groupBy('category');

        return view('admin.templates.index', compact('templates'));
    }

    public function create()
    {
        return view('admin.templates.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', EventTypes::validationRule()],
            'description' => ['nullable', 'string'],
            'blade_view' => ['required', 'string', 'max:255'],
            'thumbnail' => ['nullable', 'image', 'max:4096'],
            'background_image' => ['nullable', 'image', 'max:4096'],
            'primary_color' => ['nullable', 'string', 'max:7'],
            'accent_color' => ['nullable', 'string', 'max:7'],
            'text_color' => ['nullable', 'string', 'max:7'],
            'font_family' => ['nullable', 'string', 'max:100'],
        ]);

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail_path'] = $request->file('thumbnail')->store('templates/thumbnails', 'public');
        }

        if ($request->hasFile('background_image')) {
            $data['background_image_path'] = $request->file('background_image')->store('templates/backgrounds', 'public');
        }

        // A background image only ever renders through the image-overlay
        // view — force it here so a mistyped blade_view can't silently
        // fall back to the plain "default" design and drop the image.
        if (! empty($data['background_image_path'])) {
            $data['blade_view'] = 'image-overlay';
        }

        $data['default_config'] = [
            'primary_color' => $data['primary_color'] ?? '#7F77DD',
            'accent_color' => $data['accent_color'] ?? '#1D9E75',
            'text_color' => $data['text_color'] ?? '#1F2937',
            'font_family' => $data['font_family'] ?? 'serif',
        ];

        Template::create($data);

        return redirect()->route('admin.templates.index')->with('status', __('admin.flash.template_created'));
    }

    public function edit(Template $template)
    {
        return view('admin.templates.edit', compact('template'));
    }

    public function update(Request $request, Template $template)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', EventTypes::validationRule()],
            'description' => ['nullable', 'string'],
            'blade_view' => ['required', 'string', 'max:255'],
            'thumbnail' => ['nullable', 'image', 'max:4096'],
            'background_image' => ['nullable', 'image', 'max:4096'],
            'primary_color' => ['nullable', 'string', 'max:7'],
            'accent_color' => ['nullable', 'string', 'max:7'],
            'text_color' => ['nullable', 'string', 'max:7'],
            'font_family' => ['nullable', 'string', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail_path'] = $request->file('thumbnail')->store('templates/thumbnails', 'public');
        }

        if ($request->hasFile('background_image')) {
            $data['background_image_path'] = $request->file('background_image')->store('templates/backgrounds', 'public');
        }

        // A background image only ever renders through the image-overlay
        // view — force it here (whether the image was just uploaded, or was
        // already on the template) so a mistyped blade_view can't silently
        // fall back to the plain "default" design and drop the image.
        if (! empty($data['background_image_path']) || $template->background_image_path) {
            $data['blade_view'] = 'image-overlay';
        }

        $data['default_config'] = [
            'primary_color' => $data['primary_color'] ?? $template->default_config['primary_color'] ?? '#7F77DD',
            'accent_color' => $data['accent_color'] ?? $template->default_config['accent_color'] ?? '#1D9E75',
            'text_color' => $data['text_color'] ?? $template->default_config['text_color'] ?? '#1F2937',
            'font_family' => $data['font_family'] ?? $template->default_config['font_family'] ?? 'serif',
        ];
        $data['is_active'] = $request->boolean('is_active');

        $template->update($data);

        return redirect()->route('admin.templates.index')->with('status', __('admin.flash.template_updated'));
    }

    public function destroy(Template $template)
    {
        $template->delete();

        return back()->with('status', __('admin.flash.template_deleted'));
    }
}
