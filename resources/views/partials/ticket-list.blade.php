{{-- resources/views/partials/ticket-list.blade.php --}}
{{-- Partial untuk AJAX loading --}}

@forelse($tickets as $ticket)
    <div class="ticket-card {{ $ticket->sla_deadline && now()->gt($ticket->sla_deadline) ? 'has-sla-warning' : '' }}"
        data-id="{{ $ticket->id }}" data-status="{{ $ticket->ticket_status }}"
        data-priority="{{ $ticket->priority ?? 'medium' }}"
        data-search="{{ strtolower($ticket->ticket_number . ' ' . $ticket->issue_title . ' ' . ($ticket->hospitalUnit ? $ticket->hospitalUnit->unit_name : '') . ' ' . ($ticket->problemCategory ? $ticket->problemCategory->category_name : '')) }}"
        style="animation: slideIn 0.3s ease-out;">

        <!-- Ticket Header -->
        <div class="ticket-header">
            <div class="ticket-number">
                <span class="ticket-id">#{{ $ticket->ticket_number }}</span>
                <span class="ticket-date">
                    <i class='bx bx-calendar'></i>
                    {{ $ticket->created_at->format('d M Y, H:i') }}
                </span>
            </div>
            <span class="status-badge status-{{ strtolower(str_replace(' ', '-', $ticket->ticket_status)) }}">
                {{ $ticket->ticket_status }}
            </span>
        </div>

        <!-- Ticket Body -->
        <div class="ticket-body">
            <h4 class="ticket-title">{{ $ticket->issue_title }}</h4>

            <div class="ticket-meta">
                @if ($ticket->hospitalUnit)
                    <span class="meta-chip">
                        <i class='bx bx-building'></i>
                        {{ $ticket->hospitalUnit->unit_name }}
                    </span>
                @endif

                @if ($ticket->problemCategory)
                    <span class="meta-chip">
                        <i class='bx bx-category'></i>
                        {{ $ticket->problemCategory->category_name }}
                    </span>
                @endif

                <span class="priority-badge priority-{{ strtolower($ticket->priority ?? 'medium') }}">
                    {{ $ticket->priority ?? 'Medium' }}
                </span>
            </div>

            @if ($ticket->sla_deadline)
                @php
                    $deadline = \Carbon\Carbon::parse($ticket->sla_deadline);
                    $hoursRemaining = now()->diffInHours($deadline, false);
                @endphp
                @if ($hoursRemaining < 0)
                    <div class="sla-alert">
                        <i class='bx bx-error-circle'></i>
                        <span>⚠️ Overdue {{ abs(round($hoursRemaining)) }} jam yang lalu</span>
                    </div>
                @elseif($hoursRemaining <= 4)
                    <div class="sla-alert warning">
                        <i class='bx bx-time-five'></i>
                        <span>⏰ {{ round($hoursRemaining) }} jam tersisa</span>
                    </div>
                @endif
            @endif
        </div>

        <!-- Ticket Actions -->
        <div class="ticket-actions">
            <a href="{{ route('ticket.show', $ticket->ticket_number) }}" class="action-button action-primary">
                <i class='bx bx-show'></i>
                Detail
            </a>

            @if (in_array($ticket->ticket_status, ['Open', 'Pending']))
                <a href="{{ route('ticket.edit', $ticket->ticket_number) }}" class="action-button action-secondary">
                    <i class='bx bx-edit'></i>
                    Edit
                </a>

                <button class="action-button action-danger delete-ticket" data-id="{{ $ticket->id }}"
                    data-ticket="{{ $ticket->ticket_number }}">
                    <i class='bx bx-trash'></i>
                </button>
            @endif
        </div>
    </div>
@empty
    <div class="empty-state empty-state-results" style="animation: fadeIn 0.5s ease;">
        <div class="empty-icon">
            <i class='bx bx-search-alt'></i>
        </div>
        <h3 class="empty-title">Tidak Ada Hasil</h3>
        <p class="empty-description">
            Tidak ditemukan tiket yang sesuai dengan filter atau pencarian Anda
        </p>
        <button class="empty-action" onclick="resetFilters()">
            <i class='bx bx-refresh'></i>
            Reset Filter
        </button>
    </div>
@endforelse

{{-- Pagination --}}
@if ($tickets->hasPages())
    <div class="pagination-wrapper" style="margin-top: 24px; display: flex; justify-content: center;">
        {{ $tickets->links() }}
    </div>
@endif
