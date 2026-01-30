<div class="ticket-card {{ $ticket->sla_deadline && now()->gt($ticket->sla_deadline) ? 'has-sla-warning' : '' }}"
    onclick="window.location.href='{{ route('technician.ticket.show', $ticket->ticket_number) }}'">

    <!-- Card Header -->
    <div class="ticket-header">
        <div class="ticket-meta">
            <div class="ticket-id">#{{ $ticket->ticket_number }}</div>
            <div class="ticket-date">
                <i class='bx bx-calendar'></i>
                {{ $ticket->created_at->format('d M Y, H:i') }}
            </div>
        </div>
        <span class="status-badge status-{{ strtolower(str_replace(' ', '-', $ticket->ticket_status)) }}">
            <i class='bx bx-radio-circle-marked'></i>
            {{ $ticket->ticket_status }}
        </span>
    </div>

    <!-- Card Body -->
    <div class="ticket-body">

        <!-- Ticket Title -->
        <h3 class="ticket-title">{{ $ticket->issue_title }}</h3>

        <!-- Requester Info Section -->
        <div class="ticket-requester">
            <div class="requester-avatar">
                <i class='bx bx-user-circle'></i>
            </div>
            <div class="requester-info">
                <div class="requester-name">{{ $ticket->requester_name }}</div>
                @if ($ticket->requester_phone)
                    <div class="requester-phone">
                        <i class='bx bx-phone'></i>
                        <a href="tel:{{ $ticket->requester_phone }}" onclick="event.stopPropagation();">
                            {{ $ticket->requester_phone }}
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Detail Info Boxes -->
        <div class="ticket-info-grid">
            <!-- Unit -->
            <div class="info-box info-unit">
                <div class="info-icon">
                    <i class='bx bx-buildings'></i>
                </div>
                <div class="info-content">
                    <div class="info-label">Unit:</div>
                    <div class="info-value">{{ $ticket->hospitalUnit->unit_name ?? 'N/A' }}</div>
                </div>
            </div>

            <!-- Location -->
            <div class="info-box info-location">
                <div class="info-icon">
                    <i class='bx bx-map-pin'></i>
                </div>
                <div class="info-content">
                    <div class="info-label">Lokasi:</div>
                    <div class="info-value">{{ $ticket->location ?? 'N/A' }}</div>
                </div>
            </div>
        </div>

        <!-- Chips Row -->
        <div class="ticket-chips">
            <!-- Category -->
            <div class="chip chip-category">
                <i class='bx bx-category-alt'></i>
                <span>{{ $ticket->problemCategory->category_name ?? 'N/A' }}</span>
            </div>

            <!-- Severity Level -->
            <div class="chip chip-severity severity-{{ strtolower($ticket->severity_level) }}">
                @if ($ticket->severity_level === 'Kritis')
                    <i class='bx bx-error-circle'></i>
                @elseif($ticket->severity_level === 'Tinggi')
                    <i class='bx bx-error'></i>
                @elseif($ticket->severity_level === 'Sedang')
                    <i class='bx bx-info-circle'></i>
                @else
                    <i class='bx bx-check-circle'></i>
                @endif
                <span>{{ $ticket->severity_level }}</span>
            </div>

            <!-- Priority -->
            <div class="chip chip-priority priority-{{ strtolower($ticket->priority) }}">
                <i class='bx bx-flag'></i>
                <span>{{ strtoupper($ticket->priority) }}</span>
            </div>
        </div>

        <!-- Device Info (if applicable) -->
        @if ($ticket->device_affected)
            <div class="device-info">
                <i class='bx bx-desktop'></i>
                <span>{{ $ticket->device_affected }}</span>
            </div>
        @endif

        <!-- SLA Alerts -->
        @if ($ticket->sla_deadline && now()->gt($ticket->sla_deadline))
            @php
                $hoursOverdue = now()->diffInHours(\Carbon\Carbon::parse($ticket->sla_deadline));
            @endphp
            <div class="overdue-alert">
                <i class='bx bx-error-alt'></i>
                <span>⚠️ Overdue {{ $hoursOverdue }} jam yang lalu</span>
            </div>
        @elseif($ticket->sla_deadline && now()->diffInHours(\Carbon\Carbon::parse($ticket->sla_deadline), false) <= 4)
            @php
                $hoursRemaining = now()->diffInHours(\Carbon\Carbon::parse($ticket->sla_deadline), false);
            @endphp
            <div class="warning-alert">
                <i class='bx bx-time-five'></i>
                <span>⏰ SLA: {{ round($hoursRemaining) }} jam tersisa</span>
            </div>
        @endif

    </div>

    <!-- Card Actions -->
    <div class="ticket-actions">
        @if ($ticket->ticket_status === 'Assigned')
            <button type="button" class="btn-action btn-primary start-work" data-ticket="{{ $ticket->ticket_number }}"
                onclick="event.preventDefault(); event.stopPropagation();">
                <i class='bx bx-play-circle'></i>
                <span>Mulai Kerja</span>
            </button>
            <a href="{{ route('technician.ticket.show', $ticket->ticket_number) }}" class="btn-action btn-secondary"
                onclick="event.stopPropagation();">
                <i class='bx bx-show'></i>
                <span>Detail</span>
            </a>
        @elseif($ticket->ticket_status === 'In Progress')
            <button type="button" class="btn-action btn-success update-status"
                data-ticket="{{ $ticket->ticket_number }}" data-current-status="{{ $ticket->ticket_status }}"
                onclick="event.preventDefault(); event.stopPropagation();">
                <i class='bx bx-check-circle'></i>
                <span>Selesai</span>
            </button>
            <a href="{{ route('technician.ticket.show', $ticket->ticket_number) }}" class="btn-action btn-secondary"
                onclick="event.stopPropagation();">
                <i class='bx bx-show'></i>
                <span>Detail</span>
            </a>
        @elseif($ticket->ticket_status === 'Pending')
            <button type="button" class="btn-action btn-warning update-status"
                data-ticket="{{ $ticket->ticket_number }}" data-current-status="{{ $ticket->ticket_status }}"
                onclick="event.preventDefault(); event.stopPropagation();">
                <i class='bx bx-play'></i>
                <span>Lanjutkan</span>
            </button>
            <a href="{{ route('technician.ticket.show', $ticket->ticket_number) }}" class="btn-action btn-secondary"
                onclick="event.stopPropagation();">
                <i class='bx bx-show'></i>
                <span>Detail</span>
            </a>
        @else
            <a href="{{ route('technician.ticket.show', $ticket->ticket_number) }}"
                class="btn-action btn-secondary btn-full" onclick="event.stopPropagation();">
                <i class='bx bx-show'></i>
                <span>Lihat Detail</span>
            </a>
        @endif
    </div>

</div>
