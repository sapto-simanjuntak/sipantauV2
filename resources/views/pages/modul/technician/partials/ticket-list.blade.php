{{--
    Partial View: Ticket List
    Used for AJAX loading of tickets
--}}

@forelse($tickets as $ticket)
    @include('pages.modul.technician.partials.ticket-card', ['ticket' => $ticket])
@empty
    <div class="empty-state">
        <div class="empty-icon"><i class='bx bx-inbox'></i></div>
        <h3 class="empty-title">Tidak Ada Tiket</h3>
        <p class="empty-description">
            @if (request('search'))
                Tidak ditemukan tiket dengan kata kunci "{{ request('search') }}".
            @elseif(request('status') && request('status') !== 'all')
                Tidak ada tiket dengan status {{ request('status') }}.
            @elseif(request('priority') && request('priority') !== 'all')
                Tidak ada tiket dengan priority {{ request('priority') }}.
            @else
                Tidak ada tiket yang di-assign ke Anda saat ini.
            @endif
        </p>
        @if (request('search') ||
                (request('status') && request('status') !== 'all') ||
                (request('priority') && request('priority') !== 'all'))
            <button onclick="resetFilters()" class="empty-action">
                <i class='bx bx-refresh'></i> Reset Filter
            </button>
        @endif
    </div>
@endforelse

@if ($tickets->hasPages())
    <div class="pagination-wrapper">
        {{ $tickets->links('pagination::bootstrap-4') }}
    </div>
@endif
