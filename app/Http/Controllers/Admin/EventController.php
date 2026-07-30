<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Template;
use App\Support\EventTypes;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::withCount('guests')->orderByDesc('event_date')->paginate(15);

        return view('admin.events.index', compact('events'));
    }

    public function create()
    {
        $templates = Template::where('is_active', true)->orderBy('category')->get();

        return view('admin.events.create', compact('templates'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['created_by'] = $request->user()->id;

        if ($request->hasFile('cover_image')) {
            $data['cover_image_path'] = $request->file('cover_image')->store('events/covers', 'public');
        }

        $event = Event::create($data);

        return redirect()->route('admin.events.show', $event)->with('status', __('admin.flash.event_created'));
    }

    public function show(Event $event)
    {
        $event->load('guests.checkin', 'template');
        $stats = $event->attendanceStats();

        return view('admin.events.show', compact('event', 'stats'));
    }

    public function edit(Event $event)
    {
        $templates = Template::where('is_active', true)->orderBy('category')->get();

        return view('admin.events.edit', compact('event', 'templates'));
    }

    public function update(Request $request, Event $event)
    {
        $data = $this->validated($request);

        if ($request->hasFile('cover_image')) {
            $data['cover_image_path'] = $request->file('cover_image')->store('events/covers', 'public');
        }

        $event->update($data);

        return redirect()->route('admin.events.show', $event)->with('status', __('admin.flash.event_updated'));
    }

    public function destroy(Event $event)
    {
        $event->delete();

        return redirect()->route('admin.events.index')->with('status', __('admin.flash.event_deleted'));
    }

    // Invalidates the old client portal link (e.g. if it was shared somewhere it shouldn't have been).
    public function regenerateClientLink(Event $event)
    {
        $event->update(['client_token' => Str::random(40)]);

        return back()->with('status', __('admin.flash.client_link_regenerated'));
    }

    // Invalidates the old scanner link (e.g. door staff list changed between events).
    public function regenerateScannerLink(Event $event)
    {
        $event->update(['scanner_token' => Str::random(40)]);

        return back()->with('status', __('admin.flash.scanner_link_regenerated'));
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'event_type' => ['required', 'string', EventTypes::validationRule()],
            'template_id' => ['nullable', 'exists:templates,id'],
            'event_date' => ['required', 'date'],
            'venue_name' => ['nullable', 'string', 'max:255'],
            'venue_map_url' => ['nullable', 'url', 'max:500'],
            'description' => ['nullable', 'string'],
            'invite_message_template' => ['nullable', 'string', 'max:2000'],
            'reminder_message_template' => ['nullable', 'string', 'max:2000'],
            'cover_image' => ['nullable', 'image', 'max:4096'],
            'status' => ['required', 'string', 'in:draft,active,completed,archived'],
            'package' => ['required', 'string', 'in:basic,premium'],
            'guest_quota' => ['nullable', 'integer', 'min:0'],
            'primary_color' => ['nullable', 'string', 'max:7'],
            'accent_color' => ['nullable', 'string', 'max:7'],
        ]) + [
            'design_overrides' => array_filter([
                'primary_color' => $request->input('primary_color'),
                'accent_color' => $request->input('accent_color'),
            ]),
        ];
    }
}
