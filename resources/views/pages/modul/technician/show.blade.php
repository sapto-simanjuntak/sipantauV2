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
            /* Dark Theme - Technician */
            --bg-primary: #0f172a;
            --bg-secondary: #1e293b;
            --bg-tertiary: #334155;
            --bg-card: #1e293b;

            /* Accent colors */
            --accent-primary: #f97316;
            --accent-secondary: #06b6d4;
            --accent-success: #22c55e;
            --accent-warning: #eab308;
            --accent-danger: #ef4444;
            --accent-info: #3b82f6;

            /* Text colors */
            --text-primary: #f1f5f9;
            --text-secondary: #cbd5e1;
            --text-muted: #94a3b8;

            /* Border colors */
            --border-light: #334155;
            --border-medium: #475569;
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

        /* Dark Background */
        .cosmic-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
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
                radial-gradient(circle at 20% 30%, rgba(249, 115, 22, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 80% 70%, rgba(6, 182, 212, 0.15) 0%, transparent 50%);
            animation: cosmicFloat 30s ease-in-out infinite;
        }

        .cosmic-bg::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image:
                repeating-linear-gradient(0deg, transparent, transparent 2px, rgba(249, 115, 22, 0.03) 2px, rgba(249, 115, 22, 0.03) 4px),
                repeating-linear-gradient(90deg, transparent, transparent 2px, rgba(249, 115, 22, 0.03) 2px, rgba(249, 115, 22, 0.03) 4px);
            opacity: 0.3;
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
            width: 4px;
            height: 4px;
            background: var(--accent-primary);
            border-radius: 50%;
            opacity: 0.4;
            animation: particleFloat 25s linear infinite;
            box-shadow: 0 0 10px var(--accent-primary);
        }

        @keyframes particleFloat {
            0% {
                transform: translateY(0);
                opacity: 0;
            }

            10% {
                opacity: 0.4;
            }

            90% {
                opacity: 0.4;
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
            padding: 24px 16px;
            max-width: 1400px;
            margin: 0 auto;
            width: 100%;
            box-sizing: border-box;
        }

        /* Top Bar */
        .top-bar {
            position: sticky;
            top: 0;
            z-index: 100;
            background: rgba(30, 41, 59, 0.95);
            backdrop-filter: blur(20px);
            border-bottom: 2px solid var(--border-light);
            padding: 16px 0;
            margin-bottom: 24px;
            width: 100%;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }

        .top-bar-content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 16px;
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
            background: linear-gradient(135deg, #f97316 0%, #dc2626 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(249, 115, 22, 0.4);
        }

        .btn-primary:hover {
            box-shadow: 0 6px 16px rgba(249, 115, 22, 0.5);
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: var(--bg-tertiary);
            color: var(--text-primary);
            border: 2px solid var(--border-medium);
        }

        .btn-secondary:hover {
            background: var(--border-light);
            border-color: var(--accent-primary);
        }

        .btn-success {
            background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(34, 197, 94, 0.4);
        }

        .btn-danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4);
        }

        .btn-warning {
            background: linear-gradient(135deg, #eab308 0%, #d97706 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(234, 179, 8, 0.4);
        }

        .btn-info {
            background: linear-gradient(135deg, #06b6d4 0%, #0284c7 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(6, 182, 212, 0.4);
        }

        /* Cards */
        .ticket-header-card {
            background: var(--bg-card);
            border: 2px solid var(--border-light);
            border-radius: 16px;
            overflow: hidden;
            margin-bottom: 24px;
            width: 100%;
            max-width: 100%;
            box-sizing: border-box;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4);
        }

        .ticket-header {
            background: linear-gradient(135deg, #f97316 0%, #dc2626 50%, #c026d3 100%);
            padding: 32px 24px;
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

        /* Action Bar - TECHNICIAN FOCUSED */
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

        /* Quick Actions - Prominent for Technician */
        .quick-actions-bar {
            background: rgba(249, 115, 22, 0.1);
            border: 2px solid rgba(249, 115, 22, 0.3);
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 24px;
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .quick-actions-bar .btn {
            flex: 1;
            min-width: 140px;
        }

        .card {
            background: var(--bg-card);
            border: 2px solid var(--border-light);
            border-radius: 16px;
            overflow: hidden;
            margin-bottom: 24px;
            width: 100%;
            max-width: 100%;
            box-sizing: border-box;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
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

        /* Requester Highlight Card */
        .requester-card {
            background: rgba(6, 182, 212, 0.1);
            border: 2px solid rgba(6, 182, 212, 0.3);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 24px;
        }

        .requester-card .card-title {
            color: var(--accent-secondary);
            margin-bottom: 16px;
        }

        .requester-info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
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

        /* Progress Tracker */
        .progress-tracker {
            background: rgba(34, 197, 94, 0.1);
            border: 2px solid rgba(34, 197, 94, 0.3);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 24px;
        }

        .progress-step {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 12px 0;
            position: relative;
        }

        .progress-step:not(:last-child)::after {
            content: '';
            position: absolute;
            left: 20px;
            top: 50px;
            width: 2px;
            height: calc(100% - 30px);
            background: var(--border-medium);
        }

        .progress-step.completed::after {
            background: var(--accent-success);
        }

        .progress-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
            background: var(--bg-tertiary);
            border: 2px solid var(--border-medium);
            color: var(--text-muted);
        }

        .progress-step.completed .progress-icon {
            background: var(--accent-success);
            border-color: var(--accent-success);
            color: white;
        }

        .progress-step.active .progress-icon {
            background: var(--accent-primary);
            border-color: var(--accent-primary);
            color: white;
            animation: pulse 2s ease-in-out infinite;
        }

        .progress-content {
            flex: 1;
        }

        .progress-title {
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 4px;
        }

        .progress-time {
            font-size: 12px;
            color: var(--text-muted);
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
            box-shadow: 0 0 0 4px var(--bg-card);
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
            background: rgba(249, 115, 22, 0.1);
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

            .action-bar,
            .quick-actions-bar {
                flex-direction: column;
                padding: 12px;
            }

            .action-bar .btn,
            .quick-actions-bar .btn {
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

            .action-bar,
            .quick-actions-bar {
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
            background: var(--bg-secondary) !important;
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
        .form-select,
        .form-control:disabled {
            background: var(--bg-tertiary);
            border: 2px solid var(--border-light);
            color: var(--text-primary);
        }

        .form-control:focus,
        .form-select:focus {
            background: var(--bg-card);
            border-color: var(--accent-primary);
            color: var(--text-primary);
            box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.2);
        }

        /* SweetAlert Custom */
        .swal2-popup {
            background: var(--bg-secondary) !important;
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
        @for ($i = 0; $i < 20; $i++)
            <div class="particle" style="left: {{ rand(0, 100) }}%; animation-delay: {{ rand(0, 25) }}s;"></div>
        @endfor
    </div>

    <div class="page-wrapper">
        <!-- Top Bar -->
        <div class="top-bar">
            <div class="top-bar-content">
                <div class="breadcrumb">
                    <a href="{{ route('technician.tickets.index') }}"><i class='bx bx-wrench'></i></a>
                    <i class='bx bx-chevron-right'></i>
                    <a href="{{ route('technician.tickets.index') }}">My Tickets</a>
                    <i class='bx bx-chevron-right'></i>
                    <span class="breadcrumb-active">{{ $ticket->ticket_number }}</span>
                </div>
                <div class="top-bar-actions">
                    <a href="{{ route('technician.tickets.index') }}" class="btn btn-secondary">
                        <i class='bx bx-arrow-back'></i> Back
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
                                    'Assigned' => 'rgba(139, 92, 246, 0.9)',
                                    'In Progress' => 'rgba(249, 115, 22, 0.9)',
                                    'Pending' => 'rgba(234, 179, 8, 0.9)',
                                    'Resolved' => 'rgba(34, 197, 94, 0.9)',
                                    'Closed' => 'rgba(100, 116, 139, 0.9)',
                                ];
                            @endphp
                            <span class="badge"
                                style="background: {{ $statusColors[$ticket->ticket_status] ?? 'rgba(148, 163, 184, 0.9)' }}; color: #ffffff;">
                                {{ $ticket->ticket_status }}
                            </span>

                            @php
                                $priorityColors = [
                                    'Critical' => 'background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);',
                                    'High' => 'background: linear-gradient(135deg, #eab308 0%, #d97706 100%);',
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

                            @php
                                $severityStyles = [
                                    'Kritis' => 'background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);',
                                    'Tinggi' => 'background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);',
                                    'Sedang' => 'background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);',
                                    'Rendah' => 'background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);',
                                ];
                            @endphp
                            <span class="badge" style="{{ $severityStyles[$ticket->severity_level] ?? '' }}">
                                {{ $ticket->severity_level }}
                            </span>
                        </div>

                        <div class="sla-status-box">
                            <div class="sla-label">SLA Status</div>
                            <span class="badge {{ $slaStatus['status'] === 'overdue' ? 'sla-pulse' : '' }}"
                                style="background: {{ $slaStatus['class'] === 'danger' ? 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)' : ($slaStatus['class'] === 'warning' ? 'linear-gradient(135deg, #eab308 0%, #d97706 100%)' : 'linear-gradient(135deg, #22c55e 0%, #16a34a 100%)') }};">
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
            </div>

            <!-- Quick Actions for Technician -->
            @if (in_array($ticket->ticket_status, ['Assigned', 'In Progress', 'Pending']))
                <div class="quick-actions-bar animate-fade-in" style="animation-delay: 0.1s;">
                    @if ($ticket->ticket_status === 'Assigned')
                        <button class="btn btn-primary" id="btn-start-work">
                            <i class='bx bx-play-circle'></i> Start Work
                        </button>
                    @endif

                    @if (in_array($ticket->ticket_status, ['In Progress', 'Pending']))
                        <button class="btn btn-warning" id="btn-add-note">
                            <i class='bx bx-note'></i> Add Progress Note
                        </button>
                        <button class="btn btn-info" id="btn-set-pending">
                            <i class='bx bx-pause-circle'></i> Set Pending
                        </button>
                    @endif

                    @if ($ticket->ticket_status === 'In Progress')
                        <button class="btn btn-success" id="btn-resolve">
                            <i class='bx bx-check-double'></i> Mark as Resolved
                        </button>
                    @endif
                </div>
            @endif

            <!-- Main Content Grid -->
            <div class="detail-grid">
                <!-- Left Column -->
                <div>
                    <!-- Requester Info - Highlighted -->
                    <div class="requester-card animate-fade-in" style="animation-delay: 0.15s;">
                        <h5 class="card-title">
                            <i class='bx bx-user-voice'></i> Requester Information
                        </h5>
                        <div class="requester-info-grid">
                            <div class="info-item">
                                <div class="info-label">Name</div>
                                <div class="info-value">
                                    <strong>{{ $ticket->requester_name }}</strong>
                                </div>
                            </div>
                            @if ($ticket->requester_phone)
                                <div class="info-item">
                                    <div class="info-label">Phone</div>
                                    <div class="info-value">
                                        <a href="tel:{{ $ticket->requester_phone }}"
                                            style="color: var(--accent-secondary); text-decoration: none;">
                                            <i class='bx bx-phone'></i> {{ $ticket->requester_phone }}
                                        </a>
                                    </div>
                                </div>
                            @endif
                            @if ($ticket->user)
                                <div class="info-item">
                                    <div class="info-label">Email</div>
                                    <div class="info-value">
                                        <a href="mailto:{{ $ticket->user->email }}"
                                            style="color: var(--accent-secondary); text-decoration: none;">
                                            <i class='bx bx-envelope'></i> {{ $ticket->user->email }}
                                        </a>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Location & Unit -->
                    <div class="card animate-fade-in" style="animation-delay: 0.2s;">
                        <div class="card-header">
                            <h5 class="card-title">
                                <i class='bx bx-map-pin'></i> Location Details
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="info-row">
                                <div class="info-item">
                                    <div class="info-label">Unit</div>
                                    <div class="info-value">
                                        @if ($ticket->hospitalUnit)
                                            <span class="badge"
                                                style="background: rgba(6, 182, 212, 0.2); color: var(--accent-secondary); font-weight: 700;">
                                                {{ $ticket->hospitalUnit->unit_code }}
                                            </span>
                                            <strong
                                                style="margin-left: 8px;">{{ $ticket->hospitalUnit->unit_name }}</strong>
                                        @endif
                                    </div>
                                </div>

                                @if ($ticket->location)
                                    <div class="info-item">
                                        <div class="info-label">Specific Location</div>
                                        <div class="info-value">
                                            <i class='bx bx-map'></i> {{ $ticket->location }}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Problem Details -->
                    <div class="card animate-fade-in" style="animation-delay: 0.25s;">
                        <div class="card-header">
                            <h5 class="card-title">
                                <i class='bx bx-error-alt'></i> Problem Details
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="info-row">
                                <div class="info-item">
                                    <div class="info-label">Category</div>
                                    <div class="info-value">
                                        @if ($ticket->problemCategory)
                                            @php
                                                $categoryBadges = [
                                                    'HW' => 'background: rgba(59, 130, 246, 0.2); color: #60a5fa;',
                                                    'SW' => 'background: rgba(6, 182, 212, 0.2); color: #22d3ee;',
                                                    'NET' => 'background: rgba(245, 158, 11, 0.2); color: #fbbf24;',
                                                    'PABX' => 'background: rgba(139, 92, 246, 0.2); color: #a78bfa;',
                                                    'CCTV' => 'background: rgba(100, 116, 139, 0.2); color: #94a3b8;',
                                                    'AC' => 'background: rgba(34, 197, 94, 0.2); color: #4ade80;',
                                                    'DEV' => 'background: rgba(239, 68, 68, 0.2); color: #f87171;',
                                                    'EMAIL' => 'background: rgba(59, 130, 246, 0.2); color: #60a5fa;',
                                                ];
                                                $badgeStyle =
                                                    $categoryBadges[$ticket->problemCategory->category_code] ??
                                                    'background: rgba(100, 116, 139, 0.2); color: #94a3b8;';
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

                                @if ($ticket->device_affected)
                                    <div class="info-item">
                                        <div class="info-label">Device</div>
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
                                    <div class="info-label">Reported</div>
                                    <div class="info-value">
                                        {{ $ticket->created_at->format('d M Y, H:i') }}
                                        <small>({{ $ticket->created_at->diffForHumans() }})</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="card animate-fade-in" style="animation-delay: 0.3s;">
                        <div class="card-header">
                            <h5 class="card-title">
                                <i class='bx bx-message-detail'></i> Problem Description
                            </h5>
                        </div>
                        <div class="card-body">
                            <p class="mb-0" style="white-space: pre-wrap;">
                                {{ $ticket->description ?? 'No description provided' }}</p>
                        </div>
                    </div>

                    @if ($ticket->expected_action)
                        <div class="card animate-fade-in" style="animation-delay: 0.35s;">
                            <div class="card-header">
                                <h5 class="card-title">
                                    <i class='bx bx-target-lock'></i> Expected Action
                                </h5>
                            </div>
                            <div class="card-body">
                                <p class="mb-0" style="white-space: pre-wrap;">{{ $ticket->expected_action }}</p>
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
                </div>

                <!-- Right Column -->
                <div>
                    <!-- Progress Tracker -->
                    <div class="progress-tracker animate-fade-in" style="animation-delay: 0.2s;">
                        <h5 class="card-title" style="color: var(--accent-success); margin-bottom: 20px;">
                            <i class='bx bx-check-shield'></i> Progress Status
                        </h5>

                        <div
                            class="progress-step {{ in_array($ticket->ticket_status, ['Assigned', 'In Progress', 'Pending', 'Resolved', 'Closed']) ? 'completed' : '' }}">
                            <div class="progress-icon">
                                <i class='bx bx-check'></i>
                            </div>
                            <div class="progress-content">
                                <div class="progress-title">Assigned</div>
                                <div class="progress-time">
                                    {{ $ticket->assigned_at ? \Carbon\Carbon::parse($ticket->assigned_at)->format('d M, H:i') : '-' }}
                                </div>
                            </div>
                        </div>

                        <div
                            class="progress-step {{ $ticket->ticket_status === 'In Progress' ? 'active' : (in_array($ticket->ticket_status, ['Resolved', 'Closed']) ? 'completed' : '') }}">
                            <div class="progress-icon">
                                <i
                                    class='bx {{ $ticket->ticket_status === 'In Progress' ? 'bx-loader-circle' : 'bx-check' }}'></i>
                            </div>
                            <div class="progress-content">
                                <div class="progress-title">In Progress</div>
                                <div class="progress-time">
                                    {{ $ticket->ticket_status === 'In Progress' ? 'Currently working' : '-' }}
                                </div>
                            </div>
                        </div>

                        <div
                            class="progress-step {{ $ticket->ticket_status === 'Resolved' ? 'active' : ($ticket->ticket_status === 'Closed' ? 'completed' : '') }}">
                            <div class="progress-icon">
                                <i class='bx bx-check-double'></i>
                            </div>
                            <div class="progress-content">
                                <div class="progress-title">Resolved</div>
                                <div class="progress-time">Awaiting completion</div>
                            </div>
                        </div>

                        <div class="progress-step {{ $ticket->ticket_status === 'Closed' ? 'completed' : '' }}">
                            <div class="progress-icon">
                                <i class='bx bx-lock-alt'></i>
                            </div>
                            <div class="progress-content">
                                <div class="progress-title">Closed</div>
                                <div class="progress-time">Task completed</div>
                            </div>
                        </div>
                    </div>

                    <!-- Timeline -->
                    <div class="card animate-fade-in" style="animation-delay: 0.25s;">
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
                                            style="background: {{ $item['color'] === 'primary' ? 'linear-gradient(135deg, #f97316 0%, #dc2626 100%)' : ($item['color'] === 'success' ? 'linear-gradient(135deg, #22c55e 0%, #16a34a 100%)' : ($item['color'] === 'warning' ? 'linear-gradient(135deg, #eab308 0%, #d97706 100%)' : ($item['color'] === 'info' ? 'linear-gradient(135deg, #06b6d4 0%, #0284c7 100%)' : 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)'))) }};">
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
    <!-- Start Work Modal -->
    <div class="modal fade" id="modal-start-work" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class='bx bx-play-circle'></i> Start Working on Ticket</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="form-start-work">
                    @csrf
                    <div class="modal-body">
                        <p>Ready to start working on ticket <strong>#{{ $ticket->ticket_number }}</strong>?</p>
                        <div class="mb-3">
                            <label class="form-label">Initial Note (Optional)</label>
                            <textarea name="note" class="form-control" rows="3" placeholder="Add any initial observations or notes..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="btn-submit-start">
                            <i class='bx bx-play-circle'></i> Start Work
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Progress Note Modal -->
    <div class="modal fade" id="modal-add-note" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class='bx bx-note'></i> Add Progress Note</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="form-add-note">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Progress Note *</label>
                            <textarea name="note" class="form-control" rows="4"
                                placeholder="Describe what you've done or discovered..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning" id="btn-submit-note">
                            <i class='bx bx-save'></i> Add Note
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Set Pending Modal -->
    <div class="modal fade" id="modal-set-pending" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class='bx bx-pause-circle'></i> Set Ticket as Pending</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="form-set-pending">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Reason for Pending *</label>
                            <textarea name="note" class="form-control" rows="3" placeholder="Why is this ticket being set to pending?"
                                required></textarea>
                        </div>
                        <div class="alert"
                            style="background: rgba(234, 179, 8, 0.1); border-left: 3px solid var(--accent-warning); color: var(--text-secondary);">
                            <small><i class='bx bx-info-circle'></i> This will pause the ticket until further action is
                                taken.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-info" id="btn-submit-pending">
                            <i class='bx bx-pause-circle'></i> Set Pending
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Resolve Modal -->
    <div class="modal fade" id="modal-resolve" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class='bx bx-check-double'></i> Mark as Resolved</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="form-resolve">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Resolution Summary *</label>
                            <textarea name="resolution_note" class="form-control" rows="4"
                                placeholder="Describe what was done to resolve the issue..." required></textarea>
                        </div>
                        <div class="alert"
                            style="background: rgba(34, 197, 94, 0.1); border-left: 3px solid var(--accent-success); color: var(--text-secondary);">
                            <small><i class='bx bx-info-circle'></i> This will mark the ticket as resolved and notify
                                the requester.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success" id="btn-submit-resolve">
                            <i class='bx bx-check-double'></i> Mark Resolved
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

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

            // Start Work
            $('#btn-start-work').click(function() {
                new bootstrap.Modal($('#modal-start-work')[0]).show();
            });

            $('#form-start-work').submit(function(e) {
                e.preventDefault();
                const $btn = $('#btn-submit-start');
                $.ajax({
                    url: '/technician/tickets/{{ $ticket->ticket_number }}/update-status',
                    type: 'POST',
                    data: {
                        status: 'In Progress',
                        note: $('textarea[name="note"]', this).val()
                    },
                    beforeSend: () => $btn.prop('disabled', true).html(
                        '<i class="bx bx-loader-alt bx-spin"></i> Processing...'),
                    success: (res) => {
                        bootstrap.Modal.getInstance($('#modal-start-work')[0]).hide();
                        Swal.fire({
                            icon: 'success',
                            title: 'Work Started!',
                            text: 'Ticket status updated to In Progress',
                            confirmButtonColor: 'var(--accent-primary)'
                        }).then(() => location.reload());
                    },
                    error: (xhr) => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: xhr.responseJSON?.message ||
                                'Failed to update status',
                            confirmButtonColor: 'var(--accent-danger)'
                        });
                    },
                    complete: () => $btn.prop('disabled', false).html(
                        '<i class="bx bx-play-circle"></i> Start Work')
                });
            });

            // Add Progress Note
            $('#btn-add-note').click(function() {
                new bootstrap.Modal($('#modal-add-note')[0]).show();
            });

            $('#form-add-note').submit(function(e) {
                e.preventDefault();
                const $btn = $('#btn-submit-note');
                $.ajax({
                    url: '/technician/tickets/{{ $ticket->ticket_number }}/add-note',
                    type: 'POST',
                    data: $(this).serialize(),
                    beforeSend: () => $btn.prop('disabled', true).html(
                        '<i class="bx bx-loader-alt bx-spin"></i> Saving...'),
                    success: (res) => {
                        bootstrap.Modal.getInstance($('#modal-add-note')[0]).hide();
                        Swal.fire({
                            icon: 'success',
                            title: 'Note Added!',
                            text: 'Progress note has been recorded',
                            confirmButtonColor: 'var(--accent-primary)',
                            timer: 2000
                        }).then(() => location.reload());
                    },
                    error: (xhr) => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: xhr.responseJSON?.message || 'Failed to add note',
                            confirmButtonColor: 'var(--accent-danger)'
                        });
                    },
                    complete: () => $btn.prop('disabled', false).html(
                        '<i class="bx bx-save"></i> Add Note')
                });
            });

            // Set Pending
            $('#btn-set-pending').click(function() {
                new bootstrap.Modal($('#modal-set-pending')[0]).show();
            });

            $('#form-set-pending').submit(function(e) {
                e.preventDefault();
                const $btn = $('#btn-submit-pending');
                $.ajax({
                    url: '/technician/tickets/{{ $ticket->ticket_number }}/update-status',
                    type: 'POST',
                    data: {
                        status: 'Pending',
                        note: $('textarea[name="note"]', this).val()
                    },
                    beforeSend: () => $btn.prop('disabled', true).html(
                        '<i class="bx bx-loader-alt bx-spin"></i> Processing...'),
                    success: (res) => {
                        bootstrap.Modal.getInstance($('#modal-set-pending')[0]).hide();
                        Swal.fire({
                            icon: 'info',
                            title: 'Status Updated!',
                            text: 'Ticket set to Pending',
                            confirmButtonColor: 'var(--accent-primary)'
                        }).then(() => location.reload());
                    },
                    error: (xhr) => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: xhr.responseJSON?.message ||
                                'Failed to update status',
                            confirmButtonColor: 'var(--accent-danger)'
                        });
                    },
                    complete: () => $btn.prop('disabled', false).html(
                        '<i class="bx bx-pause-circle"></i> Set Pending')
                });
            });

            // Resolve Ticket
            $('#btn-resolve').click(function() {
                new bootstrap.Modal($('#modal-resolve')[0]).show();
            });

            $('#form-resolve').submit(function(e) {
                e.preventDefault();
                const $btn = $('#btn-submit-resolve');
                $.ajax({
                    url: '/technician/tickets/{{ $ticket->ticket_number }}/update-status',
                    type: 'POST',
                    data: {
                        status: 'Resolved',
                        note: $('textarea[name="resolution_note"]', this).val()
                    },
                    beforeSend: () => $btn.prop('disabled', true).html(
                        '<i class="bx bx-loader-alt bx-spin"></i> Processing...'),
                    success: (res) => {
                        bootstrap.Modal.getInstance($('#modal-resolve')[0]).hide();
                        Swal.fire({
                            icon: 'success',
                            title: 'Ticket Resolved!',
                            text: 'Great job! The ticket has been marked as resolved.',
                            confirmButtonColor: 'var(--accent-success)',
                            iconColor: 'var(--accent-success)'
                        }).then(() => location.reload());
                    },
                    error: (xhr) => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: xhr.responseJSON?.message ||
                                'Failed to resolve ticket',
                            confirmButtonColor: 'var(--accent-danger)'
                        });
                    },
                    complete: () => $btn.prop('disabled', false).html(
                        '<i class="bx bx-check-double"></i> Mark Resolved')
                });
            });

            // Cleanup modals
            $('.modal').on('hidden.bs.modal', function() {
                $('body').removeClass('modal-open');
                $('.modal-backdrop').remove();
                $(this).find('form')[0]?.reset();
            });
        });
    </script>
</body>

</html>
