<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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

    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

    <style>
        /* ========================================
           CSS VARIABLES
           ======================================== */
        :root {
            --bg-primary: #0a0e27;
            --bg-secondary: #151932;
            --bg-tertiary: #1e2339;
            --accent-primary: #00d4ff;
            --accent-secondary: #7c3aed;
            --accent-success: #10b981;
            --accent-warning: #f59e0b;
            --accent-danger: #ef4444;
            --text-primary: #ffffff;
            --text-secondary: #94a3b8;
            --text-muted: #64748b;

            --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --gradient-success: linear-gradient(135deg, #10b981 0%, #059669 100%);
            --gradient-warning: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            --gradient-danger: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            --gradient-cosmic: linear-gradient(135deg, #00d4ff 0%, #7c3aed 100%);

            --spacing-xs: 8px;
            --spacing-sm: 12px;
            --spacing-md: 16px;
            --spacing-lg: 24px;
            --spacing-xl: 32px;

            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 16px;
            --radius-xl: 24px;
            --radius-full: 9999px;

            --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.1);
            --shadow-md: 0 4px 16px rgba(0, 0, 0, 0.15);
            --shadow-lg: 0 8px 32px rgba(0, 0, 0, 0.2);
            --shadow-glow: 0 0 20px rgba(0, 212, 255, 0.3);
        }

        /* ========================================
           RESET & BASE
           ======================================== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-tap-highlight-color: transparent;
        }

        html {
            scroll-behavior: smooth;
            overflow-x: hidden;
        }

        body {
            font-family: 'Outfit', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            overflow-x: hidden;
            line-height: 1.6;
            max-width: 100vw;
        }

        /* ========================================
           COSMIC BACKGROUND
           ======================================== */
        .cosmic-bg {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 0;
            overflow: hidden;
            background: radial-gradient(ellipse at top, #1e293b 0%, #0a0e27 50%);
        }

        .cosmic-bg::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background:
                radial-gradient(circle at 20% 30%, rgba(103, 126, 234, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 80% 70%, rgba(124, 58, 237, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 50% 50%, rgba(0, 212, 255, 0.1) 0%, transparent 50%);
            animation: cosmicFloat 30s ease-in-out infinite;
        }

        @keyframes cosmicFloat {

            0%,
            100% {
                transform: translate(0, 0) rotate(0deg);
            }

            33% {
                transform: translate(30px, -30px) rotate(120deg);
            }

            66% {
                transform: translate(-20px, 20px) rotate(240deg);
            }
        }

        .particle {
            position: absolute;
            width: 3px;
            height: 3px;
            background: var(--accent-primary);
            border-radius: 50%;
            opacity: 0.6;
            animation: particleFloat 20s infinite;
        }

        @keyframes particleFloat {

            0%,
            100% {
                transform: translateY(0) translateX(0);
                opacity: 0;
            }

            10% {
                opacity: 0.6;
            }

            90% {
                opacity: 0.6;
            }

            100% {
                transform: translateY(-100vh) translateX(50px);
                opacity: 0;
            }
        }

        /* ========================================
           LAYOUT
           ======================================== */
        .page-wrapper {
            position: relative;
            z-index: 1;
            min-height: 100vh;
            width: 100%;
            max-width: 100vw;
            overflow-x: hidden;
        }

        .page-content {
            padding: var(--spacing-lg);
            max-width: 1400px;
            margin: 0 auto;
            width: 100%;
        }

        /* ========================================
           TOP BAR
           ======================================== */
        .top-bar {
            position: sticky;
            top: 0;
            z-index: 100;
            background: rgba(10, 14, 39, 0.95);
            backdrop-filter: blur(20px) saturate(180%);
            border-bottom: 1px solid rgba(148, 163, 184, 0.1);
            padding: var(--spacing-md) var(--spacing-lg);
            margin: calc(var(--spacing-lg) * -1) calc(var(--spacing-lg) * -1) var(--spacing-lg);
            width: calc(100% + var(--spacing-lg) * 2);
            margin-left: calc(var(--spacing-lg) * -1);
        }

        .top-bar-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: var(--spacing-md);
            flex-wrap: wrap;
            max-width: 100%;
        }

        .breadcrumb {
            display: flex;
            align-items: center;
            gap: var(--spacing-xs);
            font-size: 14px;
            color: var(--text-secondary);
            flex-wrap: wrap;
            min-width: 0;
            flex: 1;
            overflow: hidden;
        }

        .breadcrumb a,
        .breadcrumb span {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            flex-shrink: 1;
            min-width: 0;
        }

        .breadcrumb a {
            color: var(--text-secondary);
            text-decoration: none;
            transition: color 0.3s;
        }

        .breadcrumb a:hover {
            color: var(--accent-primary);
        }

        .breadcrumb-active {
            color: var(--text-primary);
            font-weight: 600;
            max-width: 150px;
        }

        .breadcrumb i {
            flex-shrink: 0;
        }

        .top-bar-actions {
            display: flex;
            gap: var(--spacing-sm);
            flex-shrink: 0;
        }

        .btn {
            padding: 10px 20px;
            border-radius: var(--radius-md);
            font-size: 14px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            white-space: nowrap;
        }

        .btn:active {
            transform: scale(0.95);
        }

        .btn-primary {
            background: var(--gradient-cosmic);
            color: var(--text-primary);
            box-shadow: 0 4px 12px rgba(0, 212, 255, 0.3);
        }

        .btn-secondary {
            background: rgba(148, 163, 184, 0.1);
            color: var(--text-primary);
            border: 1px solid rgba(148, 163, 184, 0.2);
        }

        .btn-success {
            background: var(--gradient-success);
            color: var(--text-primary);
        }

        .btn-danger {
            background: var(--gradient-danger);
            color: var(--text-primary);
        }

        .btn-warning {
            background: var(--gradient-warning);
            color: var(--text-primary);
        }

        .btn-info {
            background: var(--gradient-primary);
            color: var(--text-primary);
        }

        .btn-dark {
            background: linear-gradient(135deg, #374151 0%, #1f2937 100%);
            color: var(--text-primary);
        }

        /* ========================================
           TICKET HEADER CARD
           ======================================== */
        .ticket-header-card {
            background: rgba(21, 25, 50, 0.8);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(148, 163, 184, 0.1);
            border-radius: var(--radius-lg);
            overflow: hidden;
            margin-bottom: var(--spacing-lg);
            width: 100%;
            max-width: 100%;
        }

        .ticket-header {
            background: var(--gradient-cosmic);
            padding: var(--spacing-xl);
            position: relative;
            overflow: hidden;
        }

        .ticket-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><rect width="100" height="100" fill="none"/><circle cx="50" cy="50" r="40" fill="none" stroke="white" stroke-width="0.5" opacity="0.1"/></svg>');
            opacity: 0.3;
        }

        .ticket-header-content {
            position: relative;
            z-index: 1;
            width: 100%;
            overflow: hidden;
        }

        .ticket-number-large {
            font-size: 28px;
            font-weight: 800;
            font-family: 'Space Mono', monospace;
            margin-bottom: var(--spacing-sm);
            word-break: break-word;
        }

        .ticket-title-large {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: var(--spacing-md);
            line-height: 1.4;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: var(--radius-full);
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            white-space: nowrap;
        }

        .badge-group {
            display: flex;
            gap: var(--spacing-sm);
            flex-wrap: wrap;
        }

        /* ========================================
           TICKET BADGES CONTAINER - MOBILE FRIENDLY
           ======================================== */
        .ticket-badges-container {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            flex-wrap: wrap;
            gap: var(--spacing-md);
            width: 100%;
        }

        .sla-status-box {
            text-align: right;
        }

        .sla-label {
            font-size: 12px;
            opacity: 0.8;
            margin-bottom: 6px;
        }

        .sla-deadline {
            font-size: 11px;
            opacity: 0.7;
            margin-top: 6px;
            word-wrap: break-word;
        }

        @media (max-width: 640px) {
            .ticket-badges-container {
                flex-direction: column;
                gap: var(--spacing-md);
            }

            .sla-status-box {
                text-align: left;
                width: 100%;
            }
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
                opacity: 0.7;
                transform: scale(1.05);
            }
        }

        /* ========================================
           ACTION BUTTONS BAR - IMPROVED MOBILE
           ======================================== */
        .action-bar {
            padding: var(--spacing-md);
            background: rgba(30, 35, 57, 0.5);
            border-top: 1px solid rgba(148, 163, 184, 0.1);
            display: flex;
            flex-wrap: wrap;
            gap: var(--spacing-sm);
            width: 100%;
        }

        .action-bar .btn {
            flex: 1 1 auto;
            min-width: 140px;
            justify-content: center;
        }

        /* Mobile: Stack buttons vertically */
        @media (max-width: 768px) {
            .action-bar {
                flex-direction: column;
                gap: var(--spacing-xs);
            }

            .action-bar .btn {
                width: 100%;
                min-width: 0;
                font-size: 14px;
                padding: 12px 16px;
            }
        }

        /* Small mobile: Ensure proper spacing */
        @media (max-width: 480px) {
            .action-bar {
                padding: var(--spacing-sm);
            }

            .action-bar .btn {
                font-size: 13px;
                padding: 10px 14px;
            }
        }

        /* ========================================
           CONTENT CARDS
           ======================================== */
        .card {
            background: rgba(21, 25, 50, 0.8);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(148, 163, 184, 0.1);
            border-radius: var(--radius-lg);
            overflow: hidden;
            margin-bottom: var(--spacing-lg);
            width: 100%;
            max-width: 100%;
        }

        .card-header {
            padding: var(--spacing-md) var(--spacing-lg);
            background: rgba(30, 35, 57, 0.5);
            border-bottom: 1px solid rgba(148, 163, 184, 0.1);
        }

        .card-title {
            font-size: 18px;
            font-weight: 700;
            margin: 0;
            display: flex;
            align-items: center;
            gap: var(--spacing-sm);
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        .card-body {
            padding: var(--spacing-lg);
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        /* ========================================
           INFO ROWS
           ======================================== */
        .info-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: var(--spacing-lg);
            margin-bottom: var(--spacing-lg);
        }

        .info-item {
            margin-bottom: var(--spacing-md);
            min-width: 0;
            overflow: hidden;
        }

        .info-label {
            font-size: 12px;
            font-weight: 600;
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
        }

        .info-value strong {
            font-weight: 700;
        }

        .info-value small {
            display: block;
            color: var(--text-muted);
            font-size: 13px;
            margin-top: 4px;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        /* ========================================
           TIMELINE
           ======================================== */
        .timeline {
            position: relative;
            padding-left: 40px;
            width: 100%;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 12px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: rgba(148, 163, 184, 0.2);
        }

        .timeline-item {
            position: relative;
            padding-bottom: var(--spacing-lg);
            width: 100%;
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
            z-index: 1;
            box-shadow: 0 0 0 4px var(--bg-tertiary);
        }

        .timeline-content {
            background: rgba(30, 35, 57, 0.5);
            padding: var(--spacing-md);
            border-radius: var(--radius-md);
            border-left: 3px solid var(--accent-primary);
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        .timeline-title {
            font-weight: 700;
            font-size: 15px;
            margin-bottom: 4px;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        .timeline-description {
            font-size: 13px;
            color: var(--text-secondary);
            margin-bottom: var(--spacing-xs);
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        .timeline-meta {
            font-size: 12px;
            color: var(--text-muted);
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        /* ========================================
           ATTACHMENT
           ======================================== */
        .attachment-item {
            background: rgba(30, 35, 57, 0.5);
            border: 1px solid rgba(148, 163, 184, 0.1);
            border-radius: var(--radius-md);
            padding: var(--spacing-md);
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: all 0.3s;
            flex-wrap: wrap;
            gap: var(--spacing-sm);
            width: 100%;
        }

        .attachment-item:hover {
            border-color: var(--accent-primary);
            background: rgba(0, 212, 255, 0.05);
        }

        .attachment-info {
            display: flex;
            align-items: center;
            gap: var(--spacing-sm);
            flex: 1;
            min-width: 0;
            overflow: hidden;
        }

        .attachment-info span {
            word-wrap: break-word;
            overflow-wrap: break-word;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* ========================================
           RESPONSIVE GRID
           ======================================== */
        .detail-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: var(--spacing-lg);
            width: 100%;
        }

        @media (min-width: 1024px) {
            .detail-grid {
                grid-template-columns: 2fr 1fr;
            }
        }

        /* ========================================
           RESPONSIVE
           ======================================== */
        @media (max-width: 768px) {
            .page-content {
                padding: var(--spacing-md);
            }

            .top-bar {
                padding: var(--spacing-md);
                margin: calc(var(--spacing-md) * -1) calc(var(--spacing-md) * -1) var(--spacing-md);
                width: calc(100% + var(--spacing-md) * 2);
                margin-left: calc(var(--spacing-md) * -1);
            }

            .top-bar-content {
                flex-direction: column;
                align-items: stretch;
            }

            .breadcrumb {
                font-size: 12px;
                max-width: 100%;
            }

            .breadcrumb-active {
                max-width: 120px;
            }

            .top-bar-actions {
                width: 100%;
                flex-wrap: wrap;
            }

            .top-bar-actions .btn {
                flex: 1;
                min-width: 0;
            }

            .ticket-header {
                padding: var(--spacing-lg);
            }

            .ticket-number-large {
                font-size: 20px;
            }

            .ticket-title-large {
                font-size: 16px;
            }

            .badge {
                font-size: 10px;
                padding: 6px 12px;
            }

            .badge-group {
                gap: 6px;
            }

            .info-row {
                grid-template-columns: 1fr;
                gap: var(--spacing-md);
            }

            .card-body {
                padding: var(--spacing-md);
            }

            .card-header {
                padding: var(--spacing-sm) var(--spacing-md);
            }

            .card-title {
                font-size: 16px;
            }

            .timeline {
                padding-left: 30px;
            }

            .timeline::before {
                left: 8px;
            }

            .timeline-icon {
                left: -30px;
                width: 20px;
                height: 20px;
                font-size: 10px;
            }

            .timeline-content {
                padding: var(--spacing-sm);
            }
        }

        @media (max-width: 480px) {
            .ticket-number-large {
                font-size: 18px;
            }

            .ticket-title-large {
                font-size: 15px;
            }

            .info-label {
                font-size: 11px;
            }

            .info-value {
                font-size: 14px;
            }

            .breadcrumb-active {
                max-width: 100px;
            }

            .top-bar-actions {
                flex-direction: column;
            }

            .top-bar-actions .btn {
                width: 100%;
            }
        }

        /* ========================================
           UTILITY CLASSES
           ======================================== */
        .text-muted {
            color: var(--text-muted);
        }

        .mb-0 {
            margin-bottom: 0;
        }

        .mt-2 {
            margin-top: var(--spacing-sm);
        }

        /* ========================================
           ANIMATIONS
           ======================================== */
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

        /* ========================================
           SWEETALERT2 CUSTOM
           ======================================== */
        .swal2-popup {
            background: var(--bg-tertiary) !important;
            border: 1px solid rgba(148, 163, 184, 0.2) !important;
            border-radius: 16px !important;
            color: var(--text-primary) !important;
        }

        .swal2-title {
            color: var(--text-primary) !important;
        }

        .swal2-html-container {
            color: var(--text-secondary) !important;
        }

        .swal2-confirm,
        .swal2-cancel {
            border-radius: 8px !important;
            font-weight: 600 !important;
            padding: 10px 24px !important;
            font-family: 'Outfit', sans-serif !important;
        }

        .swal2-input,
        .swal2-textarea,
        .swal2-select {
            background: var(--bg-secondary) !important;
            border: 1px solid rgba(148, 163, 184, 0.2) !important;
            color: var(--text-primary) !important;
        }

        .swal2-input:focus,
        .swal2-textarea:focus,
        .swal2-select:focus {
            border-color: var(--accent-primary) !important;
            box-shadow: 0 0 0 3px rgba(0, 212, 255, 0.1) !important;
        }
    </style>
</head>

<body>
    <!-- Cosmic Background -->
    <div class="cosmic-bg" id="cosmicBg">
        @for ($i = 0; $i < 20; $i++)
            <div class="particle"
                style="left: {{ rand(0, 100) }}%; animation-delay: {{ rand(0, 20) }}s; animation-duration: {{ rand(15, 25) }}s;">
            </div>
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
                    <a href="{{ route('service.index') }}" class="btn btn-secondary">
                        <i class='bx bx-arrow-back'></i> Kembali
                    </a>
                </div>
            </div>
        </div>

        <div class="page-content">
            <!-- Ticket Header Card -->
            <div class="ticket-header-card animate-fade-in">
                <div class="ticket-header">
                    <div class="ticket-header-content">
                        <div class="ticket-number-large">#{{ $ticket->ticket_number }}</div>
                        <div class="ticket-title-large">{{ $ticket->issue_title }}</div>

                        <div class="ticket-badges-container">
                            <div class="badge-group">
                                @php
                                    $statusColors = [
                                        'Open' => 'rgba(59, 130, 246, 0.2)',
                                        'Pending' => 'rgba(245, 158, 11, 0.2)',
                                        'Approved' => 'rgba(0, 212, 255, 0.2)',
                                        'Assigned' => 'rgba(124, 58, 237, 0.2)',
                                        'In Progress' => 'rgba(0, 212, 255, 0.2)',
                                        'Resolved' => 'rgba(16, 185, 129, 0.2)',
                                        'Closed' => 'rgba(100, 116, 139, 0.2)',
                                        'Rejected' => 'rgba(239, 68, 68, 0.2)',
                                    ];
                                    $statusTextColors = [
                                        'Open' => '#60a5fa',
                                        'Pending' => '#fbbf24',
                                        'Approved' => '#00d4ff',
                                        'Assigned' => '#a78bfa',
                                        'In Progress' => '#00d4ff',
                                        'Resolved' => '#34d399',
                                        'Closed' => '#94a3b8',
                                        'Rejected' => '#f87171',
                                    ];
                                @endphp
                                <span class="badge"
                                    style="background: {{ $statusColors[$ticket->ticket_status] ?? 'rgba(148, 163, 184, 0.2)' }}; color: {{ $statusTextColors[$ticket->ticket_status] ?? '#94a3b8' }};">
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
                                        Deadline:
                                        {{ \Carbon\Carbon::parse($ticket->sla_deadline)->format('d M Y, H:i') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="action-bar">
                    @role('superadmin|admin')
                        @if ($ticket->validation_status === 'pending' && in_array($ticket->ticket_status, ['Open', 'Pending']))
                            <button type="button" class="btn btn-success" id="btn-approve">
                                <i class='bx bx-check-circle'></i> Approve
                            </button>
                            <button type="button" class="btn btn-danger" id="btn-reject">
                                <i class='bx bx-x-circle'></i> Reject
                            </button>
                        @endif

                        @if (in_array($ticket->ticket_status, ['Open', 'Pending', 'Approved']) && !$ticket->assigned_to)
                            <button type="button" class="btn btn-primary" id="btn-assign">
                                <i class='bx bx-user-check'></i> Assign
                            </button>
                        @endif

                        @if ($ticket->assigned_to && in_array($ticket->ticket_status, ['Assigned', 'In Progress']))
                            <button type="button" class="btn btn-info" id="btn-reassign">
                                <i class='bx bx-transfer'></i> Re-assign
                            </button>
                        @endif

                        @if (!in_array($ticket->ticket_status, ['Closed', 'Rejected']))
                            <button type="button" class="btn btn-warning" id="btn-update-status">
                                <i class='bx bx-edit'></i> Update Status
                            </button>
                        @endif

                        @if (in_array($ticket->ticket_status, ['Resolved']))
                            <button type="button" class="btn btn-dark" id="btn-close">
                                <i class='bx bx-check-double'></i> Close Ticket
                            </button>
                        @endif
                    @endrole

                    @role('teknisi')
                        @if ($ticket->assigned_to === auth()->id() && !in_array($ticket->ticket_status, ['Closed', 'Rejected']))
                            <button type="button" class="btn btn-warning" id="btn-update-status">
                                <i class='bx bx-edit'></i> Update Status
                            </button>
                        @endif
                    @endrole

                    @role('superadmin|admin|teknisi')
                        @if (!in_array($ticket->ticket_status, ['Closed', 'Rejected']))
                            <a href="{{ route('service.edit', $ticket->ticket_number) }}" class="btn btn-secondary">
                                <i class='bx bx-edit-alt'></i> Edit
                            </a>
                        @endif
                    @else
                        @if ($ticket->validation_status === 'pending')
                            <a href="{{ route('service.edit', $ticket->ticket_number) }}" class="btn btn-secondary">
                                <i class='bx bx-edit-alt'></i> Edit
                            </a>
                        @endif
                    @endrole
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
                                                style="background: rgba(148, 163, 184, 0.2); color: #94a3b8;">
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
                                                    'HW' => 'rgba(59, 130, 246, 0.2)',
                                                    'SW' => 'rgba(0, 212, 255, 0.2)',
                                                    'NET' => 'rgba(245, 158, 11, 0.2)',
                                                    'PABX' => 'rgba(148, 163, 184, 0.2)',
                                                    'CCTV' => 'rgba(100, 116, 139, 0.2)',
                                                    'AC' => 'rgba(16, 185, 129, 0.2)',
                                                    'DEV' => 'rgba(239, 68, 68, 0.2)',
                                                    'EMAIL' => 'rgba(59, 130, 246, 0.2)',
                                                ];
                                                $badgeColor =
                                                    $categoryBadges[$ticket->problemCategory->category_code] ??
                                                    'rgba(148, 163, 184, 0.2)';
                                            @endphp
                                            <span class="badge" style="background: {{ $badgeColor }};">
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
                                        <i class='bx bx-file' style="font-size: 24px;"></i>
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
                                    <div class="info-item" style="margin-top: var(--spacing-md);">
                                        <div class="info-label">Notes</div>
                                        <div
                                            style="background: {{ $ticket->validation_status === 'approved' ? 'rgba(16, 185, 129, 0.1)' : 'rgba(239, 68, 68, 0.1)' }};
                                                    border-left: 3px solid {{ $ticket->validation_status === 'approved' ? 'var(--accent-success)' : 'var(--accent-danger)' }};
                                                    padding: var(--spacing-md);
                                                    border-radius: var(--radius-sm);">
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
                                            style="background: {{ $item['color'] === 'primary' ? 'var(--gradient-cosmic)' : ($item['color'] === 'success' ? 'var(--gradient-success)' : ($item['color'] === 'warning' ? 'var(--gradient-warning)' : 'var(--gradient-danger)')) }};">
                                            <i class='bx {{ $item['icon'] }}'></i>
                                        </div>
                                        <div class="timeline-content">
                                            <div class="timeline-title">{{ $item['title'] }}</div>
                                            <div class="timeline-description">{{ $item['description'] }}</div>
                                            <div class="timeline-meta">
                                                <i class='bx bx-time'></i>
                                                {{ $item['timestamp']->format('d M Y, H:i') }}
                                                @if (isset($item['user']) && $item['user'])
                                                    <br><i class='bx bx-user'></i>
                                                    {{ $item['user']->name ?? '-' }}
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

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Main Script -->
    <script>
        $(function() {
            // ============================================
            // APPROVE TICKET
            // ============================================
            $('#btn-approve').click(function() {
                $('#modal-approve').modal('show');
            });

            $('#form-approve').submit(function(e) {
                e.preventDefault();

                $.ajax({
                    url: '/service-request/ticket/{{ $ticket->ticket_number }}/approve',
                    type: 'POST',
                    data: $(this).serialize(),
                    beforeSend: function() {
                        $('#btn-submit-approve').prop('disabled', true).html(
                            '<i class="bx bx-loader-alt bx-spin"></i> Processing...');
                    },
                    success: function(response) {
                        $('#modal-approve').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message,
                            iconColor: 'var(--accent-success)',
                            confirmButtonColor: 'var(--accent-primary)'
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: xhr.responseJSON?.message || 'Something went wrong',
                            iconColor: 'var(--accent-danger)',
                            confirmButtonColor: 'var(--accent-primary)'
                        });
                    },
                    complete: function() {
                        $('#btn-submit-approve').prop('disabled', false).html(
                            '<i class="bx bx-check-circle"></i> Approve');
                    }
                });
            });

            // ============================================
            // REJECT TICKET
            // ============================================
            $('#btn-reject').click(function() {
                $('#modal-reject').modal('show');
            });

            $('#form-reject').submit(function(e) {
                e.preventDefault();

                $.ajax({
                    url: '/service-request/ticket/{{ $ticket->ticket_number }}/reject',
                    type: 'POST',
                    data: $(this).serialize(),
                    beforeSend: function() {
                        $('#btn-submit-reject').prop('disabled', true).html(
                            '<i class="bx bx-loader-alt bx-spin"></i> Processing...');
                    },
                    success: function(response) {
                        $('#modal-reject').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message,
                            iconColor: 'var(--accent-success)',
                            confirmButtonColor: 'var(--accent-primary)'
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: xhr.responseJSON?.message || 'Something went wrong',
                            iconColor: 'var(--accent-danger)',
                            confirmButtonColor: 'var(--accent-primary)'
                        });
                    },
                    complete: function() {
                        $('#btn-submit-reject').prop('disabled', false).html(
                            '<i class="bx bx-x-circle"></i> Reject');
                    }
                });
            });

            // ============================================
            // ASSIGN TICKET
            // ============================================
            $('#btn-assign, #btn-reassign').click(function() {
                $('#modal-assign').modal('show');
            });

            $('#form-assign').submit(function(e) {
                e.preventDefault();

                $.ajax({
                    url: '/service-request/ticket/{{ $ticket->ticket_number }}/assign',
                    type: 'POST',
                    data: $(this).serialize(),
                    beforeSend: function() {
                        $('#btn-submit-assign').prop('disabled', true).html(
                            '<i class="bx bx-loader-alt bx-spin"></i> Processing...');
                    },
                    success: function(response) {
                        $('#modal-assign').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message,
                            iconColor: 'var(--accent-success)',
                            confirmButtonColor: 'var(--accent-primary)'
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: xhr.responseJSON?.message || 'Something went wrong',
                            iconColor: 'var(--accent-danger)',
                            confirmButtonColor: 'var(--accent-primary)'
                        });
                    },
                    complete: function() {
                        $('#btn-submit-assign').prop('disabled', false).html(
                            '<i class="bx bx-user-check"></i> Assign');
                    }
                });
            });

            // ============================================
            // UPDATE STATUS
            // ============================================
            $('#btn-update-status').click(function() {
                $('#modal-update-status').modal('show');
            });

            $('#form-update-status').submit(function(e) {
                e.preventDefault();

                $.ajax({
                    url: '/service-request/ticket/{{ $ticket->ticket_number }}/update-status',
                    type: 'POST',
                    data: $(this).serialize(),
                    beforeSend: function() {
                        $('#btn-submit-status').prop('disabled', true).html(
                            '<i class="bx bx-loader-alt bx-spin"></i> Processing...');
                    },
                    success: function(response) {
                        $('#modal-update-status').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message,
                            iconColor: 'var(--accent-success)',
                            confirmButtonColor: 'var(--accent-primary)'
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: xhr.responseJSON?.message || 'Something went wrong',
                            iconColor: 'var(--accent-danger)',
                            confirmButtonColor: 'var(--accent-primary)'
                        });
                    },
                    complete: function() {
                        $('#btn-submit-status').prop('disabled', false).html(
                            '<i class="bx bx-edit"></i> Update');
                    }
                });
            });

            // ============================================
            // CLOSE TICKET
            // ============================================
            $('#btn-close').click(function() {
                $('#modal-close').modal('show');
            });

            $('#form-close').submit(function(e) {
                e.preventDefault();

                $.ajax({
                    url: '/service-request/ticket/{{ $ticket->ticket_number }}/update-status',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        status: 'Closed',
                        notes: $('#close_notes').val()
                    },
                    beforeSend: function() {
                        $('#btn-submit-close').prop('disabled', true).html(
                            '<i class="bx bx-loader-alt bx-spin"></i> Processing...');
                    },
                    success: function(response) {
                        $('#modal-close').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Ticket closed successfully',
                            iconColor: 'var(--accent-success)',
                            confirmButtonColor: 'var(--accent-primary)'
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: xhr.responseJSON?.message || 'Something went wrong',
                            iconColor: 'var(--accent-danger)',
                            confirmButtonColor: 'var(--accent-primary)'
                        });
                    },
                    complete: function() {
                        $('#btn-submit-close').prop('disabled', false).html(
                            '<i class="bx bx-check-double"></i> Close');
                    }
                });
            });

            // ============================================
            // INITIALIZATION LOG
            // ============================================
            // console.log('%c🎫 Ticket Detail - {{ $ticket->ticket_number }}',
            //     'font-size: 20px; font-weight: bold; color: #00d4ff;');
            // console.log('%c✨ Cosmic Theme Active', 'font-size: 14px; color: #7c3aed;');
        });
    </script>
</body>

</html>
