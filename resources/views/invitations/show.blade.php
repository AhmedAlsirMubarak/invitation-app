@php
    $event = $guest->event;
    $design = $event->design();
    $templateView = 'invitations.templates.'.($event->template?->blade_view ?? 'default');
    if (! View::exists($templateView)) {
        $templateView = 'invitations.templates.default';
    }
@endphp
@include($templateView, ['guest' => $guest, 'event' => $event, 'design' => $design])
