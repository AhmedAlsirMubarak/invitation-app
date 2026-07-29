<?php

namespace App\Http\Controllers\Admin\Landing;

use App\Http\Controllers\Controller;
use App\Models\LandingFaq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index()
    {
        $faqs = LandingFaq::ordered()->get();

        return view('admin.landing.faqs.index', compact('faqs'));
    }

    public function create()
    {
        return view('admin.landing.faqs.create');
    }

    public function store(Request $request)
    {
        LandingFaq::create($this->validated($request));

        return redirect()->route('admin.landing.faqs.index')->with('status', __('admin.landing.saved'));
    }

    public function edit(LandingFaq $faq)
    {
        return view('admin.landing.faqs.edit', compact('faq'));
    }

    public function update(Request $request, LandingFaq $faq)
    {
        $faq->update($this->validated($request));

        return redirect()->route('admin.landing.faqs.index')->with('status', __('admin.landing.saved'));
    }

    public function destroy(LandingFaq $faq)
    {
        $faq->delete();

        return redirect()->route('admin.landing.faqs.index')->with('status', __('admin.landing.deleted'));
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'question' => ['required', 'array'],
            'question.ar' => ['required', 'string', 'max:500'],
            'question.en' => ['nullable', 'string', 'max:500'],
            'answer' => ['required', 'array'],
            'answer.ar' => ['required', 'string'],
            'answer.en' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['question']['en'] = $data['question']['en'] ?? '';
        $data['answer']['en'] = $data['answer']['en'] ?? '';
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
