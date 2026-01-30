<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Technician Dashboard</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Space+Mono:wght@400;700&display=swap"
        rel="stylesheet">

    <!-- Boxicons -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

    <!-- Custom Styles -->
    <link rel="stylesheet" href="{{ asset('css/technician.css') }}">
</head>

<body>
    <!-- Cosmic Background -->
    <div class="cosmic-bg" id="cosmicBg">
        @for ($i = 0; $i < 25; $i++)
            <div class="particle"
                style="left: {{ rand(0, 100) }}%; animation-delay: {{ rand(0, 20) }}s; animation-duration: {{ rand(15, 25) }}s;">
            </div>
        @endfor
    </div>

    <div class="page-wrapper">
        <div class="page-content">

            <!-- Flash Messages -->
            @if (session('success') || session('error') || session('warning'))
                <div class="flash-message-container">
                    @if (session('success'))
                        <div class="flash-message flash-success animate__animated animate__slideInRight">
                            <i class='bx bx-check-circle'></i>
                            <span>{{ session('success') }}</span>
                            <button class="flash-close" onclick="this.parentElement.remove()">
                                <i class='bx bx-x'></i>
                            </button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="flash-message flash-error animate__animated animate__slideInRight">
                            <i class='bx bx-error-circle'></i>
                            <span>{{ session('error') }}</span>
                            <button class="flash-close" onclick="this.parentElement.remove()">
                                <i class='bx bx-x'></i>
                            </button>
                        </div>
                    @endif

                    @if (session('warning'))
                        <div class="flash-message flash-warning animate__animated animate__slideInRight">
                            <i class='bx bx-info-circle'></i>
                            <span>{{ session('warning') }}</span>
                            <button class="flash-close" onclick="this.parentElement.remove()">
                                <i class='bx bx-x'></i>
                            </button>
                        </div>
                    @endif
                </div>
            @endif

            <!-- App Bar -->
            <div class="app-bar">
                <div class="app-bar-content">
                    <div class="app-bar-title">
                        <i class='bx bx-wrench'></i>
                        <h1>Tech Dashboard</h1>
                    </div>
                    <div class="app-bar-actions">
                        <button class="icon-button" onclick="location.reload()">
                            <i class='bx bx-refresh'></i>
                        </button>

                        <button class="icon-button" id="notificationBtn">
                            <i class='bx bx-bell'></i>
                            @if (($stats['overdue'] ?? 0) > 0 || ($stats['critical'] ?? 0) > 0)
                                <span class="badge">{{ ($stats['overdue'] ?? 0) + ($stats['critical'] ?? 0) }}</span>
                            @endif
                        </button>

                        <div class="user-menu">
                            <div class="user-avatar" id="userMenuToggle">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div class="user-dropdown" id="userDropdown">
                                <div class="user-dropdown-header">
                                    <div class="user-dropdown-name">{{ $user->name }}</div>
                                    <div class="user-dropdown-email">{{ $user->email ?? 'technician@example.com' }}
                                    </div>
                                </div>
                                <div class="user-dropdown-menu">
                                    <a href="#" class="user-dropdown-item">
                                        <i class='bx bx-user'></i>
                                        <span>Profile</span>
                                    </a>
                                    <a href="#" class="user-dropdown-item">
                                        <i class='bx bx-cog'></i>
                                        <span>Settings</span>
                                    </a>
                                    <div style="height: 1px; background: var(--border-light); margin: 8px 0;"></div>
                                    <a href="{{ route('logout') }}" class="user-dropdown-item danger"
                                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class='bx bx-log-out'></i>
                                        <span>Logout</span>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>

            <!-- Hero Section -->
            <div class="hero-section">
                <div class="hero-content">
                    <div class="hero-greeting">
                        <i class='bx bx-shield-alt-2'></i>
                        Teknisi On Duty
                    </div>
                    <h2 class="hero-title">{{ $user->name }}</h2>
                    <p class="hero-subtitle">Kelola dan selesaikan tiket Anda</p>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="stats-grid">
                <div class="stat-card assigned">
                    <div class="stat-icon"><i class='bx bx-task'></i></div>
                    <div class="stat-label">Assigned</div>
                    <div class="stat-value" id="statAssigned">{{ $stats['assigned'] ?? 0 }}</div>
                </div>

                <div class="stat-card progress">
                    <div class="stat-icon"><i class='bx bx-loader-circle'></i></div>
                    <div class="stat-label">In Progress</div>
                    <div class="stat-value" id="statInProgress">{{ $stats['in_progress'] ?? 0 }}</div>
                </div>

                <div class="stat-card critical">
                    <div class="stat-icon"><i class='bx bx-error-circle'></i></div>
                    <div class="stat-label">Critical</div>
                    <div class="stat-value" id="statCritical">{{ $stats['critical'] ?? 0 }}</div>
                </div>

                <div class="stat-card overdue">
                    <div class="stat-icon"><i class='bx bx-time-five'></i></div>
                    <div class="stat-label">Overdue</div>
                    <div class="stat-value" id="statOverdue">{{ $stats['overdue'] ?? 0 }}</div>
                </div>

                <div class="stat-card pending">
                    <div class="stat-icon"><i class='bx bx-pause-circle'></i></div>
                    <div class="stat-label">Pending</div>
                    <div class="stat-value" id="statPending">{{ $stats['pending'] ?? 0 }}</div>
                </div>

                <div class="stat-card resolved">
                    <div class="stat-icon"><i class='bx bx-check-circle'></i></div>
                    <div class="stat-label">Resolved</div>
                    <div class="stat-value" id="statResolved">{{ $stats['resolved'] ?? 0 }}</div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="quick-actions">
                <div class="quick-actions-grid">
                    <button class="quick-action-btn" onclick="filterByStatus('Assigned')">
                        <i class='bx bx-file-blank'></i>
                        New Tasks
                    </button>
                    <button class="quick-action-btn" onclick="filterByPriority('Critical')">
                        <i class='bx bx-error-alt'></i>
                        Urgent
                    </button>
                </div>
            </div>

            <!-- Search Bar -->
            <div class="search-container">
                <div class="search-box" id="searchBox">
                    <i class='bx bx-search'></i>
                    <input type="text" class="search-input" id="searchInput"
                        placeholder="Cari tiket atau lokasi...">
                    <button class="search-clear" id="searchClear">
                        <i class='bx bx-x'></i>
                    </button>
                </div>
            </div>

            <!-- Filter Tabs -->
            <div class="filter-section">
                <div class="filter-tabs">
                    <button class="filter-tab active" data-status="all">
                        <i class='bx bx-grid-alt'></i> Semua
                    </button>
                    <button class="filter-tab" data-status="Assigned">
                        <i class='bx bx-task'></i> Assigned
                    </button>
                    <button class="filter-tab" data-status="In Progress">
                        <i class='bx bx-loader-circle'></i> In Progress
                    </button>
                    <button class="filter-tab" data-status="Pending">
                        <i class='bx bx-pause-circle'></i> Pending
                    </button>
                    <button class="filter-tab" data-status="Resolved">
                        <i class='bx bx-check-circle'></i> Resolved
                    </button>
                </div>

                <div class="priority-filter">
                    <button class="priority-chip active" data-priority="all">All</button>
                    <button class="priority-chip critical" data-priority="Critical">Critical</button>
                    <button class="priority-chip high" data-priority="High">High</button>
                    <button class="priority-chip medium" data-priority="Medium">Medium</button>
                    <button class="priority-chip low" data-priority="Low">Low</button>
                </div>
            </div>

            <!-- Tickets List -->
            <div class="tickets-container">
                <div class="section-header">
                    <h3 class="section-title">My Tickets</h3>
                    <span class="section-count" id="ticketCount">{{ $tickets->total() }} tiket</span>
                </div>

                <div class="tickets-list" id="ticketsList">
                    @forelse($tickets as $ticket)
                        @include('pages.modul.technician.partials.ticket-card', ['ticket' => $ticket])
                    @empty
                        <div class="empty-state">
                            <div class="empty-icon"><i class='bx bx-inbox'></i></div>
                            <h3 class="empty-title">Tidak Ada Tiket</h3>
                            <p class="empty-description">
                                @if (request('search'))
                                    Tidak ditemukan tiket dengan kata kunci "{{ request('search') }}".
                                @else
                                    Tidak ada tiket yang di-assign ke Anda saat ini.
                                @endif
                            </p>
                            @if (request('search') || request('status') !== 'all')
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
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/technician.js') }}"></script>
</body>

</html>
