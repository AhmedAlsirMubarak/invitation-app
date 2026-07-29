<?php

namespace App\Http\Controllers\Admin\Landing;

use App\Http\Controllers\Controller;
use App\Models\LandingTestimonial;
use Illuminate\Http\Request;

class TestimonialController extends Controller
{
    public function index()
    {
        $testimonials = LandingTestimonial::ordered()->get();

        return view('admin.landing.testimonials.index', compact('testimonials'));
    }

    public function create()
    {
        return view('admin.landing.testimonials.create');
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        if ($request->hasFile('avatar')) {
            $data['avatar_path'] = $request->file('avatar')->store('landing/testimonials', 'public');
        }

        LandingTestimonial::create($data);

        return redirect()->route('admin.landing.testimonials.index')->with('status', __('admin.landing.saved'));
    }

    public function edit(LandingTestimonial $testimonial)
    {
        return view('admin.landing.testimonials.edit', compact('testimonial'));
    }

    public function update(Request $request, LandingTestimonial $testimonial)
    {
        $data = $this->validated($request);

        if ($request->hasFile('avatar')) {
            $data['avatar_path'] = $request->file('avatar')->store('landing/testimonials', 'public');
        }

        $testimonial->update($data);

        return redirect()->route('admin.landing.testimonials.index')->with('status', __('admin.landing.saved'));
    }

    public function destroy(LandingTestimonial $testimonial)
    {
        $testimonial->delete();

        return redirect()->route('admin.landing.testimonials.index')->with('status', __('admin.landing.deleted'));
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'author_name' => ['required', 'string', 'max:255'],
            'event_category' => ['nullable', 'array'],
            'event_category.ar' => ['nullable', 'string', 'max:255'],
            'event_category.en' => ['nullable', 'string', 'max:255'],
            'quote' => ['required', 'array'],
            'quote.ar' => ['required', 'string'],
            'quote.en' => ['nullable', 'string'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['quote']['en'] = $data['quote']['en'] ?? '';
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
