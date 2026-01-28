<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $ticket->ticket_number }} - Ticket Detail</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Space+Mono:wght@400;700&display=swap"
        rel="stylesheet">

    <!-- Boxicons -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        :root {
            /* Warna background lebih terang */
            --bg-primary: #f8fafc;
            --bg-secondary: #ffffff;
            --bg-tertiary: #f1f5f9;

            /* Accent colors lebih vibrant */
            --accent-primary: #0ea5e9;
            --accent-secondary: #8b5cf6;
            --accent-success: #10b981;
            --accent-warning: #f59e0b;
            --accent-danger: #ef4444;

            /* Text colors lebih kontras */
            --text-primary: #0f172a;
            --text-secondary: #475569;
            --text-muted: #64748b;

            /* Border colors */
            --border-light: #e2e8f0;
            --border-medium: #cbd5e1;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html,
        body {
            width: 100%;
            max-width: 100vw;
            overflow-x: hidden;
            scroll-behavior: smooth;
        }

        /* Prevent any element from causing horizontal scroll */
        * {
            max-width: 100%;
        }

        img,
        video,
        iframe {
            max-width: 100%;
            height: auto;
        }

        body {
            font-family: 'Outfit', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            line-height: 1.6;
        }

        /* Modern Background */
        .cosmic-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            background: linear-gradient(135deg, #e0f2fe 0%, #f1f5f9 50%, #fae8ff 100%);
            overflow: hidden;
        }

        .cosmic-bg::before {
            content: '';
            position: absolute;
            width: 200%;
            height: 200%;
            top: -50%;
            left: -50%;
            background:
                radial-gradient(circle at 20% 30%, rgba(14, 165, 233, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 70%, rgba(139, 92, 246, 0.1) 0%, transparent 50%);
            animation: cosmicFloat 40s ease-in-out infinite;
        }

        @keyframes cosmicFloat {

            0%,
            100% {
                transform: translate(0, 0) rotate(0deg);
            }

            50% {
                transform: translate(20px, -20px) rotate(180deg);
            }
        }

        .particle {
            position: absolute;
            width: 3px;
            height: 3px;
            background: var(--accent-primary);
            border-radius: 50%;
            opacity: 0.3;
            animation: particleFloat 25s linear infinite;
        }

        @keyframes particleFloat {
            0% {
                transform: translateY(0);
                opacity: 0;
            }

            10% {
                opacity: 0.3;
            }

            90% {
                opacity: 0.3;
            }

            100% {
                transform: translateY(-100vh);
                opacity: 0;
            }
        }

        /* Layout */
        .page-wrapper {
            position: relative;
            z-index: 1;
            min-height: 100vh;
            width: 100%;
            overflow-x: hidden;
        }

        .page-content {
            padding: 24px 24px;
            max-width: 1400px;
            margin: 0 auto;
            width: 100%;
            box-sizing: border-box;
        }

        /* Top Bar - FIXED */
        .top-bar {
            position: sticky;
            top: 0;
            z-index: 100;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-bottom: 2px solid var(--border-light);
            padding: 16px 0;
            margin-bottom: 24px;
            width: 100%;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .top-bar-content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
        }

        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: var(--text-secondary);
            flex-wrap: wrap;
            flex: 1;
            min-width: 0;
        }

        .breadcrumb a {
            color: var(--text-secondary);
            text-decoration: none;
            transition: color 0.3s;
            font-weight: 500;
        }

        .breadcrumb a:hover {
            color: var(--accent-primary);
        }

        .breadcrumb-active {
            color: var(--text-primary);
            font-weight: 700;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 200px;
        }

        .top-bar-actions {
            display: flex;
            gap: 12px;
            flex-shrink: 0;
        }

        /* Buttons */
        .btn {
            padding: 10px 20px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            white-space: nowrap;
        }

        .btn:active {
            transform: scale(0.97);
        }

        .btn-primary {
            background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(14, 165, 233, 0.3);
        }

        .btn-primary:hover {
            box-shadow: 0 6px 16px rgba(14, 165, 233, 0.4);
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: white;
            color: var(--text-primary);
            border: 2px solid var(--border-medium);
        }

        .btn-secondary:hover {
            background: var(--bg-tertiary);
            border-color: var(--accent-primary);
        }

        .btn-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .btn-danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        }

        .btn-warning {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
        }

        .btn-info {
            background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
        }

        /* Cards */
        .ticket-header-card {
            background: white;
            border: 2px solid var(--border-light);
            border-radius: 16px;
            overflow: hidden;
            margin-bottom: 24px;
            width: 100%;
            max-width: 100%;
            box-sizing: border-box;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .ticket-header {
            background: linear-gradient(135deg, #0ea5e9 0%, #8b5cf6 100%);
            padding: 32px;
            position: relative;
            box-sizing: border-box;
            overflow-wrap: break-word;
            word-wrap: break-word;
        }

        .ticket-number-large {
            font-size: 28px;
            font-weight: 800;
            font-family: 'Space Mono', monospace;
            margin-bottom: 12px;
            word-wrap: break-word;
            overflow-wrap: break-word;
            color: white;
        }

        .ticket-title-large {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 16px;
            line-height: 1.4;
            word-wrap: break-word;
            overflow-wrap: break-word;
            color: white;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            white-space: nowrap;
            max-width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .badge-group {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            max-width: 100%;
        }

        .ticket-badges-container {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            flex-wrap: wrap;
            gap: 16px;
            width: 100%;
            max-width: 100%;
        }

        .sla-status-box {
            text-align: right;
        }

        .sla-label {
            font-size: 12px;
            opacity: 0.9;
            margin-bottom: 6px;
            color: white;
            font-weight: 600;
        }

        .sla-deadline {
            font-size: 11px;
            opacity: 0.85;
            margin-top: 6px;
            color: white;
        }

        .sla-pulse {
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
                transform: scale(1);
            }

            50% {
                opacity: 0.9;
                transform: scale(1.03);
            }
        }

        /* Action Bar */
        .action-bar {
            padding: 16px;
            background: var(--bg-tertiary);
            border-top: 2px solid var(--border-light);
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .action-bar .btn {
            flex: 1;
            min-width: 140px;
            justify-content: center;
        }

        .card {
            background: white;
            border: 2px solid var(--border-light);
            border-radius: 16px;
            overflow: hidden;
            margin-bottom: 24px;
            width: 100%;
            max-width: 100%;
            box-sizing: border-box;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .card-header {
            padding: 16px 24px;
            background: var(--bg-tertiary);
            border-bottom: 2px solid var(--border-light);
            box-sizing: border-box;
        }

        .card-title {
            font-size: 18px;
            font-weight: 700;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 12px;
            word-wrap: break-word;
            overflow-wrap: break-word;
            color: var(--text-primary);
        }

        .card-body {
            padding: 24px;
            box-sizing: border-box;
            overflow-wrap: break-word;
            word-wrap: break-word;
        }

        /* Info Rows */
        .info-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 24px;
            margin-bottom: 24px;
        }

        .info-row:last-child {
            margin-bottom: 0;
        }

        .info-item {
            margin-bottom: 16px;
        }

        .info-item:last-child {
            margin-bottom: 0;
        }

        .info-label {
            font-size: 12px;
            font-weight: 700;
            color: var(--text-secondary);
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-value {
            font-size: 15px;
            color: var(--text-primary);
            line-height: 1.6;
            word-wrap: break-word;
            overflow-wrap: break-word;
            word-break: break-word;
            max-width: 100%;
        }

        .info-value strong {
            font-weight: 700;
            word-wrap: break-word;
            overflow-wrap: break-word;
            color: var(--text-primary);
        }

        .info-value small {
            display: block;
            color: var(--text-muted);
            font-size: 13px;
            margin-top: 4px;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        /* Timeline */
        .timeline {
            position: relative;
            padding-left: 40px;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 12px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: var(--border-medium);
        }

        .timeline-item {
            position: relative;
            padding-bottom: 24px;
        }

        .timeline-item:last-child {
            padding-bottom: 0;
        }

        .timeline-icon {
            position: absolute;
            left: -40px;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            color: white;
            box-shadow: 0 0 0 4px white;
        }

        .timeline-content {
            background: var(--bg-tertiary);
            padding: 16px;
            border-radius: 12px;
            border-left: 3px solid var(--accent-primary);
        }

        .timeline-title {
            font-weight: 700;
            font-size: 15px;
            color: var(--text-primary);
            margin-bottom: 4px;
        }

        .timeline-description {
            font-size: 13px;
            color: var(--text-secondary);
            margin-bottom: 8px;
        }

        .timeline-meta {
            font-size: 12px;
            color: var(--text-muted);
        }

        /* Attachments */
        .attachment-item {
            background: var(--bg-tertiary);
            border: 2px solid var(--border-light);
            border-radius: 12px;
            padding: 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            transition: all 0.3s;
        }

        .attachment-item:hover {
            border-color: var(--accent-primary);
            background: rgba(14, 165, 233, 0.05);
            transform: translateY(-2px);
        }

        .attachment-info {
            display: flex;
            align-items: center;
            gap: 12px;
            flex: 1;
            min-width: 0;
        }

        .attachment-info span {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            color: var(--text-primary);
            font-weight: 500;
        }

        /* Grid Layout */
        .detail-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 24px;
            width: 100%;
            max-width: 100%;
            box-sizing: border-box;
        }

        @media (min-width: 1024px) {
            .detail-grid {
                grid-template-columns: 2fr 1fr;
            }
        }

        /* Force no overflow on all child elements */
        .detail-grid>* {
            min-width: 0;
            max-width: 100%;
            overflow-wrap: break-word;
            word-wrap: break-word;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .page-content {
                padding: 16px 12px;
            }

            .top-bar-content {
                padding: 0 12px;
                flex-direction: column;
                align-items: stretch;
            }

            .breadcrumb {
                font-size: 12px;
            }

            .breadcrumb-active {
                max-width: 150px;
            }

            .top-bar-actions {
                width: 100%;
            }

            .top-bar-actions .btn {
                flex: 1;
                font-size: 13px;
            }

            .ticket-header {
                padding: 20px 16px;
            }

            .ticket-number-large {
                font-size: 22px;
            }

            .ticket-title-large {
                font-size: 17px;
            }

            .ticket-badges-container {
                flex-direction: column;
            }

            .sla-status-box {
                text-align: left;
            }

            .action-bar {
                flex-direction: column;
                padding: 12px;
            }

            .action-bar .btn {
                width: 100%;
                min-width: 0;
            }

            .info-row {
                grid-template-columns: 1fr;
            }

            .card-body {
                padding: 16px 12px;
            }

            .card-header {
                padding: 12px 16px;
            }

            .timeline {
                padding-left: 32px;
            }

            .timeline-icon {
                left: -32px;
                width: 20px;
                height: 20px;
                font-size: 10px;
            }
        }

        @media (max-width: 480px) {
            .page-content {
                padding: 12px 8px;
            }

            .top-bar-content {
                padding: 0 8px;
            }

            .ticket-header {
                padding: 16px 12px;
            }

            .ticket-number-large {
                font-size: 18px;
            }

            .ticket-title-large {
                font-size: 15px;
            }

            .badge {
                font-size: 10px;
                padding: 6px 12px;
            }

            .card-body {
                padding: 12px 10px;
            }

            .card-header {
                padding: 10px 12px;
            }

            .action-bar {
                padding: 10px 8px;
            }
        }

        /* Utilities */
        .text-muted {
            color: var(--text-muted);
        }

        .mb-0 {
            margin-bottom: 0 !important;
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fadeInUp 0.5s ease;
        }

        /* Modal Custom */
        .modal-content {
            background: white !important;
            border: 2px solid var(--border-light);
            color: var(--text-primary);
        }

        .modal-header {
            border-bottom: 2px solid var(--border-light);
            background: var(--bg-tertiary);
        }

        .modal-title {
            color: var(--text-primary);
            font-weight: 700;
        }

        .form-label {
            color: var(--text-primary);
            font-weight: 600;
        }

        .form-control,
        .form-select {
            background: var(--bg-tertiary);
            border: 2px solid var(--border-light);
            color: var(--text-primary);
        }

        .form-control:focus,
        .form-select:focus {
            background: white;
            border-color: var(--accent-primary);
            color: var(--text-primary);
            box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.1);
        }

        /* SweetAlert Custom */
        .swal2-popup {
            background: white !important;
            border: 2px solid var(--border-light) !important;
            color: var(--text-primary) !important;
        }

        .swal2-title {
            color: var(--text-primary) !important;
        }

        .swal2-html-container {
            color: var(--text-secondary) !important;
        }
    </style>
</head>

<body>
    <!-- Cosmic Background -->
    <div class="cosmic-bg">
        @for ($i = 0; $i < 12; $i++)
            <div class="particle" style="left: {{ rand(0, 100) }}%; animation-delay: {{ rand(0, 25) }}s;"></div>
        @endfor
    </div>

    <div class="page-wrapper">
        <!-- Top Bar -->
        <div class="top-bar">
            <div class="top-bar-content">
                <div class="breadcrumb">
                    <a href="{{ route('dashboard') }}"><i class='bx bx-home-alt'></i></a>
                    <i class='bx bx-chevron-right'></i>
                    <a href="{{ route('service.index') }}">Daftar Tiket</a>
                    <i class='bx bx-chevron-right'></i>
                    <span class="breadcrumb-active">{{ $ticket->ticket_number }}</span>
                </div>
                <div class="top-bar-actions">
                    <a href="{{ route('service.print', $ticket->ticket_number) }}" class="btn btn-primary"
                        target="_blank">
                        <i class='bx bx-printer'></i> Print
                    </a>
                    <a href="{{ route('ticket.index') }}" class="btn btn-secondary">
                        <i class='bx bx-arrow-back'></i> Kembali
                    </a>
                </div>
            </div>
        </div>

        <div class="page-content">
            <!-- Ticket Header Card -->
            <div class="ticket-header-card animate-fade-in">
                <div class="ticket-header">
                    <div class="ticket-number-large">#{{ $ticket->ticket_number }}</div>
                    <div class="ticket-title-large">{{ $ticket->issue_title }}</div>

                    <div class="ticket-badges-container">
                        <div class="badge-group">
                            @php
                                $statusColors = [
                                    'Open' => 'rgba(59, 130, 246, 0.9)',
                                    'Pending' => 'rgba(245, 158, 11, 0.9)',
                                    'Approved' => 'rgba(14, 165, 233, 0.9)',
                                    'Assigned' => 'rgba(139, 92, 246, 0.9)',
                                    'In Progress' => 'rgba(14, 165, 233, 0.9)',
                                    'Resolved' => 'rgba(16, 185, 129, 0.9)',
                                    'Closed' => 'rgba(100, 116, 139, 0.9)',
                                    'Rejected' => 'rgba(239, 68, 68, 0.9)',
                                ];
                                $statusTextColors = [
                                    'Open' => '#ffffff',
                                    'Pending' => '#ffffff',
                                    'Approved' => '#ffffff',
                                    'Assigned' => '#ffffff',
                                    'In Progress' => '#ffffff',
                                    'Resolved' => '#ffffff',
                                    'Closed' => '#ffffff',
                                    'Rejected' => '#ffffff',
                                ];
                            @endphp
                            <span class="badge"
                                style="background: {{ $statusColors[$ticket->ticket_status] ?? 'rgba(148, 163, 184, 0.9)' }}; color: {{ $statusTextColors[$ticket->ticket_status] ?? '#ffffff' }};">
                                {{ $ticket->ticket_status }}
                            </span>

                            @php
                                $priorityColors = [
                                    'Critical' => 'background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);',
                                    'High' => 'background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);',
                                    'Medium' => 'background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);',
                                    'Low' => 'background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);',
                                ];
                            @endphp
                            <span class="badge" style="{{ $priorityColors[$ticket->priority] ?? '' }}">
                                {{ $ticket->priority }}
                            </span>

                            @if ($ticket->impact_patient_care)
                                <span class="badge sla-pulse"
                                    style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
                                    <i class='bx bx-error-circle'></i> Patient Impact
                                </span>
                            @endif
                        </div>

                        <div class="sla-status-box">
                            <div class="sla-label">SLA Status</div>
                            <span class="badge {{ $slaStatus['status'] === 'overdue' ? 'sla-pulse' : '' }}"
                                style="background: {{ $slaStatus['class'] === 'danger' ? 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)' : ($slaStatus['class'] === 'warning' ? 'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)' : 'linear-gradient(135deg, #10b981 0%, #059669 100%)') }};">
                                <i class='bx bx-time-five'></i> {{ $slaStatus['message'] }}
                            </span>
                            @if ($ticket->sla_deadline)
                                <div class="sla-deadline">
                                    Deadline: {{ \Carbon\Carbon::parse($ticket->sla_deadline)->format('d M Y, H:i') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="action-bar">
                    @if ($ticket->validation_status === 'pending')
                        <a href="{{ route('service.edit', $ticket->ticket_number) }}" class="btn btn-secondary">
                            <i class='bx bx-edit-alt'></i> Edit
                        </a>
                    @endif
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="detail-grid">
                <!-- Left Column -->
                <div>
                    <!-- Ticket Details -->
                    <div class="card animate-fade-in" style="animation-delay: 0.1s;">
                        <div class="card-header">
                            <h5 class="card-title">
                                <i class='bx bx-info-circle'></i> Ticket Details
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="info-row">
                                <div class="info-item">
                                    <div class="info-label">Requester</div>
                                    <div class="info-value">
                                        <strong>{{ $ticket->requester_name }}</strong>
                                        @if ($ticket->requester_phone)
                                            <small><i class='bx bx-phone'></i> {{ $ticket->requester_phone }}</small>
                                        @endif
                                        @if ($ticket->user)
                                            <small><i class='bx bx-envelope'></i> {{ $ticket->user->email }}</small>
                                        @endif
                                    </div>
                                </div>

                                <div class="info-item">
                                    <div class="info-label">Unit / Location</div>
                                    <div class="info-value">
                                        @if ($ticket->hospitalUnit)
                                            <span class="badge"
                                                style="background: rgba(14, 165, 233, 0.15); color: #0284c7; font-weight: 700;">
                                                {{ $ticket->hospitalUnit->unit_code }}
                                            </span>
                                            <strong
                                                style="margin-left: 8px;">{{ $ticket->hospitalUnit->unit_name }}</strong>
                                        @endif
                                        @if ($ticket->location)
                                            <small><i class='bx bx-map'></i> {{ $ticket->location }}</small>
                                        @endif
                                    </div>
                                </div>

                                <div class="info-item">
                                    <div class="info-label">Category</div>
                                    <div class="info-value">
                                        @if ($ticket->problemCategory)
                                            @php
                                                $categoryBadges = [
                                                    'HW' => 'background: rgba(59, 130, 246, 0.15); color: #2563eb;',
                                                    'SW' => 'background: rgba(14, 165, 233, 0.15); color: #0284c7;',
                                                    'NET' => 'background: rgba(245, 158, 11, 0.15); color: #d97706;',
                                                    'PABX' => 'background: rgba(139, 92, 246, 0.15); color: #7c3aed;',
                                                    'CCTV' => 'background: rgba(100, 116, 139, 0.15); color: #475569;',
                                                    'AC' => 'background: rgba(16, 185, 129, 0.15); color: #059669;',
                                                    'DEV' => 'background: rgba(239, 68, 68, 0.15); color: #dc2626;',
                                                    'EMAIL' => 'background: rgba(59, 130, 246, 0.15); color: #2563eb;',
                                                ];
                                                $badgeStyle =
                                                    $categoryBadges[$ticket->problemCategory->category_code] ??
                                                    'background: rgba(100, 116, 139, 0.15); color: #475569;';
                                            @endphp
                                            <span class="badge" style="{{ $badgeStyle }} font-weight: 700;">
                                                {{ $ticket->problemCategory->category_name }}
                                            </span>
                                            @if ($ticket->problemSubCategory)
                                                <small>{{ $ticket->problemSubCategory->sub_category_name }}</small>
                                            @endif
                                        @endif
                                    </div>
                                </div>

                                <div class="info-item">
                                    <div class="info-label">Severity Level</div>
                                    <div class="info-value">
                                        @php
                                            $severityStyles = [
                                                'Kritis' =>
                                                    'background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);',
                                                'Tinggi' =>
                                                    'background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);',
                                                'Sedang' =>
                                                    'background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);',
                                                'Rendah' =>
                                                    'background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);',
                                            ];
                                        @endphp
                                        <span class="badge"
                                            style="{{ $severityStyles[$ticket->severity_level] ?? '' }}">
                                            {{ $ticket->severity_level }}
                                        </span>
                                    </div>
                                </div>

                                @if ($ticket->device_affected)
                                    <div class="info-item">
                                        <div class="info-label">Device Affected</div>
                                        <div class="info-value">
                                            <i class='bx bx-laptop'></i> {{ $ticket->device_affected }}
                                            @if ($ticket->ip_address)
                                                <small><i class='bx bx-network-chart'></i>
                                                    {{ $ticket->ip_address }}</small>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                @if ($ticket->connection_status)
                                    <div class="info-item">
                                        <div class="info-label">Connection Status</div>
                                        <div class="info-value">{{ $ticket->connection_status }}</div>
                                    </div>
                                @endif

                                <div class="info-item">
                                    <div class="info-label">Occurrence Time</div>
                                    <div class="info-value">
                                        {{ \Carbon\Carbon::parse($ticket->occurrence_time)->format('d M Y, H:i') }}
                                    </div>
                                </div>

                                <div class="info-item">
                                    <div class="info-label">Created At</div>
                                    <div class="info-value">
                                        {{ $ticket->created_at->format('d M Y, H:i') }}
                                        <small>({{ $ticket->created_at->diffForHumans() }})</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="card animate-fade-in" style="animation-delay: 0.2s;">
                        <div class="card-header">
                            <h5 class="card-title">
                                <i class='bx bx-message-detail'></i> Description
                            </h5>
                        </div>
                        <div class="card-body">
                            <p class="mb-0">{{ $ticket->description ?? 'No description provided' }}</p>
                        </div>
                    </div>

                    @if ($ticket->expected_action)
                        <div class="card animate-fade-in" style="animation-delay: 0.3s;">
                            <div class="card-header">
                                <h5 class="card-title">
                                    <i class='bx bx-target-lock'></i> Expected Action
                                </h5>
                            </div>
                            <div class="card-body">
                                <p class="mb-0">{{ $ticket->expected_action }}</p>
                            </div>
                        </div>
                    @endif

                    <!-- Attachments -->
                    @if ($ticket->file_path)
                        <div class="card animate-fade-in" style="animation-delay: 0.4s;">
                            <div class="card-header">
                                <h5 class="card-title">
                                    <i class='bx bx-paperclip'></i> Attachments
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="attachment-item">
                                    <div class="attachment-info">
                                        <i class='bx bx-file'
                                            style="font-size: 24px; color: var(--accent-primary);"></i>
                                        <span>{{ basename($ticket->file_path) }}</span>
                                    </div>
                                    <a href="{{ Storage::url($ticket->file_path) }}" target="_blank"
                                        class="btn btn-primary" style="min-width: auto;">
                                        <i class='bx bx-download'></i> Download
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Validation Info -->
                    @if ($ticket->validation_status !== 'pending')
                        <div class="card animate-fade-in" style="animation-delay: 0.5s;">
                            <div class="card-header">
                                <h5 class="card-title">
                                    <i
                                        class='bx {{ $ticket->validation_status === 'approved' ? 'bx-check-circle' : 'bx-x-circle' }}'></i>
                                    Validation {{ ucfirst($ticket->validation_status) }}
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="info-row">
                                    <div class="info-item">
                                        <div class="info-label">Validated By</div>
                                        <div class="info-value">{{ optional($ticket->validator)->name ?? '-' }}</div>
                                    </div>
                                    <div class="info-item">
                                        <div class="info-label">Validated At</div>
                                        <div class="info-value">
                                            {{ $ticket->validated_at ? \Carbon\Carbon::parse($ticket->validated_at)->format('d M Y, H:i') : '-' }}
                                        </div>
                                    </div>
                                </div>
                                @if ($ticket->validation_notes)
                                    <div class="info-item" style="margin-top: 16px;">
                                        <div class="info-label">Notes</div>
                                        <div
                                            style="background: {{ $ticket->validation_status === 'approved' ? 'rgba(16, 185, 129, 0.1)' : 'rgba(239, 68, 68, 0.1)' }}; border-left: 3px solid {{ $ticket->validation_status === 'approved' ? 'var(--accent-success)' : 'var(--accent-danger)' }}; padding: 16px; border-radius: 8px; color: var(--text-primary);">
                                            {{ $ticket->validation_notes }}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Assignment Info -->
                    @if ($ticket->assigned_to)
                        <div class="card animate-fade-in" style="animation-delay: 0.6s;">
                            <div class="card-header">
                                <h5 class="card-title">
                                    <i class='bx bx-user-check'></i> Assignment Information
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="info-row">
                                    <div class="info-item">
                                        <div class="info-label">Assigned To</div>
                                        <div class="info-value">
                                            <strong>{{ optional($ticket->assignedTechnician)->name ?? '-' }}</strong>
                                            @if ($ticket->assignedTechnician)
                                                <small>{{ $ticket->assignedTechnician->email }}</small>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="info-item">
                                        <div class="info-label">Assigned At</div>
                                        <div class="info-value">
                                            {{ $ticket->assigned_at ? \Carbon\Carbon::parse($ticket->assigned_at)->format('d M Y, H:i') : '-' }}
                                        </div>
                                    </div>
                                    <div class="info-item">
                                        <div class="info-label">Assigned By</div>
                                        <div class="info-value">{{ optional($ticket->assignedBy)->name ?? '-' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Right Column -->
                <div>
                    <!-- Timeline -->
                    <div class="card animate-fade-in" style="animation-delay: 0.2s;">
                        <div class="card-header">
                            <h5 class="card-title">
                                <i class='bx bx-time-five'></i> Activity Timeline
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="timeline">
                                @forelse($timeline as $item)
                                    <div class="timeline-item">
                                        <div class="timeline-icon"
                                            style="background: {{ $item['color'] === 'primary' ? 'linear-gradient(135deg, #0ea5e9 0%, #8b5cf6 100%)' : ($item['color'] === 'success' ? 'linear-gradient(135deg, #10b981 0%, #059669 100%)' : ($item['color'] === 'warning' ? 'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)' : 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)')) }};">
                                            <i class='bx {{ $item['icon'] }}'></i>
                                        </div>
                                        <div class="timeline-content">
                                            <div class="timeline-title">{{ $item['title'] }}</div>
                                            <div class="timeline-description">{{ $item['description'] }}</div>
                                            <div class="timeline-meta">
                                                <i class='bx bx-time'></i>
                                                {{ $item['timestamp']->format('d M Y, H:i') }}
                                                @if (isset($item['user']) && $item['user'])
                                                    <br><i class='bx bx-user'></i> {{ $item['user']->name ?? '-' }}
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-muted mb-0">No activity yet</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modals -->
    @include('pages.modul.service-request.modals.approve')
    @include('pages.modul.service-request.modals.reject')
    @include('pages.modul.service-request.modals.assign')
    @include('pages.modul.service-request.modals.update-status')
    @include('pages.modul.service-request.modals.close')

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Approve
            $('#btn-approve').click(function(e) {
                e.preventDefault();
                new bootstrap.Modal($('#modal-approve')[0]).show();
            });

            $('#form-approve').submit(function(e) {
                e.preventDefault();
                const $btn = $('#btn-submit-approve');
                $.ajax({
                    url: '/service-request/ticket/{{ $ticket->ticket_number }}/approve',
                    type: 'POST',
                    data: $(this).serialize(),
                    beforeSend: () => $btn.prop('disabled', true).html(
                        '<i class="bx bx-loader-alt bx-spin"></i> Processing...'),
                    success: (res) => {
                        bootstrap.Modal.getInstance($('#modal-approve')[0]).hide();
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: res.message
                        }).then(() => location.reload());
                    },
                    error: (xhr) => Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: xhr.responseJSON?.message || 'Failed'
                    }),
                    complete: () => $btn.prop('disabled', false).html(
                        '<i class="bx bx-check-circle"></i> Approve')
                });
            });

            // Reject
            $('#btn-reject').click(function(e) {
                e.preventDefault();
                new bootstrap.Modal($('#modal-reject')[0]).show();
            });

            $('#form-reject').submit(function(e) {
                e.preventDefault();
                const $btn = $('#btn-submit-reject');
                $.ajax({
                    url: '/service-request/ticket/{{ $ticket->ticket_number }}/reject',
                    type: 'POST',
                    data: $(this).serialize(),
                    beforeSend: () => $btn.prop('disabled', true).html(
                        '<i class="bx bx-loader-alt bx-spin"></i> Processing...'),
                    success: (res) => {
                        bootstrap.Modal.getInstance($('#modal-reject')[0]).hide();
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: res.message
                        }).then(() => location.reload());
                    },
                    error: (xhr) => Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: xhr.responseJSON?.message || 'Failed'
                    }),
                    complete: () => $btn.prop('disabled', false).html(
                        '<i class="bx bx-x-circle"></i> Reject')
                });
            });

            // Assign
            $('#btn-assign, #btn-reassign').click(function(e) {
                e.preventDefault();
                new bootstrap.Modal($('#modal-assign')[0]).show();
            });

            $('#form-assign').submit(function(e) {
                e.preventDefault();
                const $btn = $('#btn-submit-assign');
                $.ajax({
                    url: '/service-request/ticket/{{ $ticket->ticket_number }}/assign',
                    type: 'POST',
                    data: $(this).serialize(),
                    beforeSend: () => $btn.prop('disabled', true).html(
                        '<i class="bx bx-loader-alt bx-spin"></i> Processing...'),
                    success: (res) => {
                        bootstrap.Modal.getInstance($('#modal-assign')[0]).hide();
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: res.message
                        }).then(() => location.reload());
                    },
                    error: (xhr) => Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: xhr.responseJSON?.message || 'Failed'
                    }),
                    complete: () => $btn.prop('disabled', false).html(
                        '<i class="bx bx-user-check"></i> Assign')
                });
            });

            // Update Status
            $('#btn-update-status').click(function(e) {
                e.preventDefault();
                new bootstrap.Modal($('#modal-update-status')[0]).show();
            });

            $('#form-update-status').submit(function(e) {
                e.preventDefault();
                const $btn = $('#btn-submit-status');
                $.ajax({
                    url: '/service-request/ticket/{{ $ticket->ticket_number }}/update-status',
                    type: 'POST',
                    data: $(this).serialize(),
                    beforeSend: () => $btn.prop('disabled', true).html(
                        '<i class="bx bx-loader-alt bx-spin"></i> Processing...'),
                    success: (res) => {
                        bootstrap.Modal.getInstance($('#modal-update-status')[0]).hide();
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: res.message
                        }).then(() => location.reload());
                    },
                    error: (xhr) => Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: xhr.responseJSON?.message || 'Failed'
                    }),
                    complete: () => $btn.prop('disabled', false).html(
                        '<i class="bx bx-edit"></i> Update')
                });
            });

            // Close
            $('#btn-close').click(function(e) {
                e.preventDefault();
                new bootstrap.Modal($('#modal-close')[0]).show();
            });

            $('#form-close').submit(function(e) {
                e.preventDefault();
                const $btn = $('#btn-submit-close');
                $.ajax({
                    url: '/service-request/ticket/{{ $ticket->ticket_number }}/update-status',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        status: 'Closed',
                        notes: $('#close_notes').val()
                    },
                    beforeSend: () => $btn.prop('disabled', true).html(
                        '<i class="bx bx-loader-alt bx-spin"></i> Processing...'),
                    success: () => {
                        bootstrap.Modal.getInstance($('#modal-close')[0]).hide();
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Ticket closed'
                        }).then(() => location.reload());
                    },
                    error: (xhr) => Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: xhr.responseJSON?.message || 'Failed'
                    }),
                    complete: () => $btn.prop('disabled', false).html(
                        '<i class="bx bx-check-double"></i> Close')
                });
            });

            // Cleanup modals
            $('.modal').on('hidden.bs.modal', function() {
                $('body').removeClass('modal-open');
                $('.modal-backdrop').remove();
            });
        });
    </script>
</body>

</html>
