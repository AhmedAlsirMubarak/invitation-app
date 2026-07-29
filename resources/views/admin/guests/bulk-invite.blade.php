@extends('admin.layouts.app')
@section('title', __('admin.bulk_invite.title').' · '.$event->title)

@section('content')
<a href="{{ route('admin.events.guests.index', $event) }}" class="text-xs text-slate-500 hover:text-slate-700 underline">{{ __('admin.bulk_invite.back_to_guests') }}</a>

<h1 class="text-2xl font-semibold text-slate-900 mt-1 mb-1">{{ __('admin.bulk_invite.title') }}</h1>
<p class="text-sm text-slate-500 mb-6 max-w-xl">{{ __('admin.bulk_invite.subtitle') }}</p>

@if ($guests->isEmpty())
    <div class="bg-white border border-dashed border-slate-300 rounded-lg p-8 text-center text-slate-500 text-sm">
        {{ __('admin.bulk_invite.empty') }}
    </div>
@else
    <div class="mb-6">
        <div class="flex items-center justify-between text-sm text-slate-600 mb-1.5">
            <span id="progress-label"></span>
        </div>
        <div class="h-2 rounded-full bg-slate-200 overflow-hidden">
            <div id="progress-bar" class="h-full bg-emerald-500 transition-all duration-500" style="width:0%"></div>
        </div>
    </div>

    @if ($withoutPhoneCount > 0)
        <p class="text-xs text-amber-700 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2 mb-6">
            {{ __('admin.bulk_invite.without_phone_note', ['count' => $withoutPhoneCount]) }}
        </p>
    @endif

    {{-- Spotlight --}}
    <div id="spotlight" class="bg-slate-900 text-white rounded-2xl p-8 text-center mb-8"></div>

    {{-- Full list --}}
    <p class="text-sm font-medium text-slate-900 mb-3">{{ __('admin.bulk_invite.list_title') }}</p>
    <div class="bg-white border border-slate-200 rounded-lg overflow-hidden">
        <table class="w-full text-sm">
            <tbody id="guest-rows" class="divide-y divide-slate-100"></tbody>
        </table>
    </div>
@endif

<template id="row-template">
    <tr>
        <td class="px-4 py-3 text-slate-900 font-medium row-name"></td>
        <td class="px-4 py-3 text-slate-500 row-phone" dir="ltr"></td>
        <td class="px-4 py-3 row-status"></td>
        <td class="px-4 py-3 text-end">
            <a target="_blank" class="row-action text-xs underline"></a>
        </td>
    </tr>
</template>

<script>
    const guests = @json($guests);
    const sentLabel = @json(__('admin.bulk_invite.status_sent', ['time' => '__TIME__']));
    const pendingLabel = @json(__('admin.bulk_invite.status_pending'));
    const resendLabel = @json(__('admin.bulk_invite.resend'));
    const sendLabel = @json(__('admin.bulk_invite.send'));
    const progressLabel = @json(__('admin.bulk_invite.progress', ['sent' => '__SENT__', 'total' => '__TOTAL__']));
    const spotlightSendLabel = @json(__('admin.bulk_invite.spotlight_send'));
    const spotlightDoneTitle = @json(__('admin.bulk_invite.spotlight_done_title'));
    const spotlightDoneBody = @json(__('admin.bulk_invite.spotlight_done_body'));

    const spotlight = document.getElementById('spotlight');
    const rowsBody = document.getElementById('guest-rows');
    const rowTemplate = document.getElementById('row-template');
    const progressBar = document.getElementById('progress-bar');
    const progressLabelEl = document.getElementById('progress-label');

    function totalSent() {
        return guests.filter(g => g.sent).length;
    }

    function renderProgress() {
        const sent = totalSent();
        const total = guests.length;
        progressLabelEl.textContent = progressLabel.replace('__SENT__', sent).replace('__TOTAL__', total);
        progressBar.style.width = (total ? (sent / total * 100) : 0) + '%';
    }

    function renderRow(guest) {
        const node = rowTemplate.content.cloneNode(true);
        const tr = node.querySelector('tr');
        tr.dataset.id = guest.id;
        node.querySelector('.row-name').textContent = guest.name;
        node.querySelector('.row-phone').textContent = guest.phone;
        updateRowStatus(node, guest);
        const action = node.querySelector('.row-action');
        action.href = guest.url;
        action.textContent = guest.sent ? resendLabel : sendLabel;
        action.addEventListener('click', () => markSent(guest.id));
        rowsBody.appendChild(node);
    }

    function updateRowStatus(scope, guest) {
        const statusEl = scope.querySelector('.row-status');
        if (guest.sent) {
            statusEl.innerHTML = '<span class="text-emerald-600 text-xs">' + sentLabel.replace('__TIME__', guest.sent_ago ?? '') + '</span>';
        } else {
            statusEl.innerHTML = '<span class="text-slate-400 text-xs">' + pendingLabel + '</span>';
        }
    }

    function renderSpotlight() {
        const next = guests.find(g => !g.sent);
        if (!next) {
            spotlight.innerHTML = `
                <p class="text-xl font-semibold mb-1">${spotlightDoneTitle}</p>
                <p class="text-sm text-white/60">${spotlightDoneBody}</p>
            `;
            return;
        }
        spotlight.innerHTML = `
            <p class="text-xs text-white/50 mb-2">${sendLabel}</p>
            <p class="text-2xl font-semibold mb-1">${next.name}</p>
            <p class="text-sm text-white/50 mb-6" dir="ltr">${next.phone}</p>
            <a target="_blank" href="${next.url}" id="spotlight-btn" class="inline-flex items-center gap-2 bg-emerald-500 hover:bg-emerald-400 transition text-white px-6 py-3 rounded-full font-medium">
                ${spotlightSendLabel}
            </a>
        `;
        document.getElementById('spotlight-btn').addEventListener('click', () => markSent(next.id));
    }

    function markSent(id) {
        const guest = guests.find(g => g.id === id);
        if (!guest || guest.sent) return;
        guest.sent = true;
        guest.sent_ago = @json(__('admin.bulk_invite.just_now'));
        const row = rowsBody.querySelector(`tr[data-id="${id}"]`);
        if (row) {
            updateRowStatus(row, guest);
            const action = row.querySelector('.row-action');
            action.textContent = resendLabel;
        }
        renderProgress();
        renderSpotlight();
    }

    guests.forEach(renderRow);
    renderProgress();
    renderSpotlight();
</script>
@endsection
