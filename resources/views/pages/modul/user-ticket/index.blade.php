<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>My Tickets - Service Request Management</title>

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
           CSS VARIABLES - Design System
           ======================================== */
        :root {
            /* Color Palette - Dark Mode First */
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

            /* Gradients */
            --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --gradient-success: linear-gradient(135deg, #10b981 0%, #059669 100%);
            --gradient-warning: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            --gradient-danger: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            --gradient-cosmic: linear-gradient(135deg, #00d4ff 0%, #7c3aed 100%);

            /* Spacing */
            --spacing-xs: 8px;
            --spacing-sm: 12px;
            --spacing-md: 16px;
            --spacing-lg: 24px;
            --spacing-xl: 32px;

            /* Border Radius */
            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 16px;
            --radius-xl: 24px;
            --radius-full: 9999px;

            /* Shadows */
            --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.1);
            --shadow-md: 0 4px 16px rgba(0, 0, 0, 0.15);
            --shadow-lg: 0 8px 32px rgba(0, 0, 0, 0.2);
            --shadow-glow: 0 0 20px rgba(0, 212, 255, 0.3);
        }

        /* ========================================
           RESET & BASE STYLES
           ======================================== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-tap-highlight-color: transparent;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Outfit', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            overflow-x: hidden;
            line-height: 1.6;
        }

        button,
        a {
            -webkit-user-select: none;
            user-select: none;
        }

        /* ========================================
           ANIMATED BACKGROUND - Cosmic Theme
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
        }

        .page-content {
            padding: 0;
            max-width: 100%;
        }

        /* ========================================
           APP BAR
           ======================================== */
        .app-bar {
            position: sticky;
            top: 0;
            z-index: 100;
            background: rgba(10, 14, 39, 0.95);
            backdrop-filter: blur(20px) saturate(180%);
            border-bottom: 1px solid rgba(148, 163, 184, 0.1);
            padding: var(--spacing-md);
        }

        .app-bar-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            max-width: 1200px;
            margin: 0 auto;
        }

        .app-bar-title {
            display: flex;
            align-items: center;
            gap: var(--spacing-sm);
        }

        .app-bar-title h1 {
            font-size: 22px;
            font-weight: 700;
            background: var(--gradient-cosmic);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .app-bar-actions {
            display: flex;
            gap: var(--spacing-sm);
        }

        .icon-button {
            position: relative;
            width: 40px;
            height: 40px;
            border-radius: var(--radius-full);
            background: rgba(148, 163, 184, 0.1);
            border: none;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .icon-button:active {
            transform: scale(0.95);
            background: rgba(148, 163, 184, 0.2);
        }

        .icon-button .badge {
            position: absolute;
            top: -4px;
            right: -4px;
            width: 20px;
            height: 20px;
            background: var(--accent-danger);
            border-radius: var(--radius-full);
            font-size: 11px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid var(--bg-primary);
        }

        /* ========================================
           HERO SECTION
           ======================================== */
        .hero-section {
            padding: var(--spacing-xl) var(--spacing-md);
            background: var(--gradient-cosmic);
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><rect width="100" height="100" fill="none"/><circle cx="50" cy="50" r="40" fill="none" stroke="white" stroke-width="0.5" opacity="0.1"/></svg>');
            opacity: 0.3;
        }

        .hero-content {
            position: relative;
            z-index: 1;
            max-width: 1200px;
            margin: 0 auto;
        }

        .hero-greeting {
            font-size: 14px;
            font-weight: 500;
            opacity: 0.9;
            margin-bottom: 4px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .hero-title {
            font-size: 28px;
            font-weight: 800;
            margin-bottom: var(--spacing-md);
            line-height: 1.2;
        }

        .hero-subtitle {
            font-size: 15px;
            opacity: 0.8;
            font-weight: 400;
        }

        /* ========================================
           STATS GRID
           ======================================== */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: var(--spacing-md);
            padding: var(--spacing-md);
            margin-top: -40px;
            position: relative;
            z-index: 10;
            max-width: 1200px;
            margin-left: auto;
            margin-right: auto;
        }

        .stat-card {
            background: rgba(21, 25, 50, 0.8);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(148, 163, 184, 0.1);
            border-radius: var(--radius-lg);
            padding: var(--spacing-md);
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, transparent 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .stat-card:active {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
        }

        .stat-card:active::before {
            opacity: 1;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: var(--spacing-sm);
            font-size: 24px;
            position: relative;
        }

        .stat-icon::before {
            content: '';
            position: absolute;
            inset: -4px;
            border-radius: var(--radius-md);
            padding: 1px;
            background: inherit;
            mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            mask-composite: exclude;
            opacity: 0.3;
        }

        .stat-card.active .stat-icon {
            background: var(--gradient-cosmic);
            box-shadow: var(--shadow-glow);
        }

        .stat-card.success .stat-icon {
            background: var(--gradient-success);
        }

        .stat-card.warning .stat-icon {
            background: var(--gradient-warning);
        }

        .stat-card.info .stat-icon {
            background: var(--gradient-primary);
        }

        .stat-label {
            font-size: 12px;
            color: var(--text-secondary);
            margin-bottom: 4px;
            font-weight: 500;
            letter-spacing: 0.3px;
        }

        .stat-value {
            font-size: 32px;
            font-weight: 800;
            font-family: 'Space Mono', monospace;
            line-height: 1;
        }

        /* ========================================
           SEARCH BAR
           ======================================== */
        .search-container {
            padding: 0 var(--spacing-md) var(--spacing-md);
            max-width: 1200px;
            margin: 0 auto;
        }

        .search-box {
            position: relative;
            display: flex;
            align-items: center;
            background: rgba(21, 25, 50, 0.8);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(148, 163, 184, 0.1);
            border-radius: var(--radius-full);
            padding: var(--spacing-sm) var(--spacing-md);
            transition: all 0.3s ease;
        }

        .search-box:focus-within {
            border-color: var(--accent-primary);
            box-shadow: 0 0 0 3px rgba(0, 212, 255, 0.1);
        }

        .search-box i {
            color: var(--text-muted);
            margin-right: var(--spacing-sm);
            font-size: 18px;
        }

        .search-input {
            flex: 1;
            background: none;
            border: none;
            color: var(--text-primary);
            font-size: 15px;
            outline: none;
            font-family: 'Outfit', sans-serif;
        }

        .search-input::placeholder {
            color: var(--text-muted);
        }

        .search-clear {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: rgba(148, 163, 184, 0.2);
            border: none;
            color: var(--text-primary);
            display: none;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 14px;
        }

        .search-box.has-value .search-clear {
            display: flex;
        }

        /* ========================================
           FILTER TABS
           ======================================== */
        .filter-section {
            padding: var(--spacing-md);
            max-width: 1200px;
            margin: 0 auto;
        }

        .filter-tabs {
            display: flex;
            gap: var(--spacing-xs);
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
            padding-bottom: var(--spacing-xs);
        }

        .filter-tabs::-webkit-scrollbar {
            display: none;
        }

        .filter-tab {
            flex-shrink: 0;
            padding: var(--spacing-sm) var(--spacing-md);
            border-radius: var(--radius-full);
            background: rgba(148, 163, 184, 0.1);
            border: 1px solid rgba(148, 163, 184, 0.1);
            color: var(--text-secondary);
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            white-space: nowrap;
        }

        .filter-tab:active,
        .filter-tab.active {
            background: var(--gradient-cosmic);
            border-color: transparent;
            color: var(--text-primary);
            box-shadow: var(--shadow-glow);
            transform: scale(1.05);
        }

        /* ========================================
           TICKETS CONTAINER
           ======================================== */
        .tickets-container {
            padding: var(--spacing-md);
            max-width: 1200px;
            margin: 0 auto;
            padding-bottom: 100px;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: var(--spacing-md);
        }

        .section-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--text-primary);
        }

        .section-count {
            font-size: 14px;
            color: var(--text-secondary);
            font-weight: 600;
        }

        .tickets-list {
            display: flex;
            flex-direction: column;
            gap: var(--spacing-md);
        }

        /* ========================================
           TICKET CARD
           ======================================== */
        .ticket-card {
            background: rgba(21, 25, 50, 0.8);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(148, 163, 184, 0.1);
            border-radius: var(--radius-lg);
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
        }

        .ticket-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--gradient-cosmic);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .ticket-card:active {
            transform: scale(0.98);
        }

        .ticket-card.has-sla-warning::before {
            opacity: 1;
            background: var(--accent-danger);
        }

        .ticket-header {
            padding: var(--spacing-md);
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: var(--spacing-sm);
        }

        .ticket-number {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .ticket-id {
            font-size: 13px;
            color: var(--text-muted);
            font-family: 'Space Mono', monospace;
            font-weight: 700;
        }

        .ticket-date {
            font-size: 12px;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .status-badge {
            padding: 6px 14px;
            border-radius: var(--radius-full);
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .status-badge::before {
            content: '';
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: currentColor;
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
                transform: scale(1);
            }

            50% {
                opacity: 0.5;
                transform: scale(1.2);
            }
        }

        .status-open {
            background: rgba(59, 130, 246, 0.2);
            color: #60a5fa;
        }

        .status-pending {
            background: rgba(245, 158, 11, 0.2);
            color: #fbbf24;
        }

        .status-approved {
            background: rgba(16, 185, 129, 0.2);
            color: #34d399;
        }

        .status-assigned {
            background: rgba(124, 58, 237, 0.2);
            color: #a78bfa;
        }

        .status-in-progress {
            background: rgba(0, 212, 255, 0.2);
            color: #00d4ff;
        }

        .status-resolved {
            background: rgba(16, 185, 129, 0.2);
            color: #34d399;
        }

        .status-closed {
            background: rgba(100, 116, 139, 0.2);
            color: #94a3b8;
        }

        .status-rejected {
            background: rgba(239, 68, 68, 0.2);
            color: #f87171;
        }

        .ticket-body {
            padding: 0 var(--spacing-md) var(--spacing-md);
        }

        .ticket-title {
            font-size: 17px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: var(--spacing-sm);
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .ticket-meta {
            display: flex;
            flex-wrap: wrap;
            gap: var(--spacing-xs);
            margin-bottom: var(--spacing-sm);
        }

        .meta-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            background: rgba(148, 163, 184, 0.1);
            border-radius: var(--radius-full);
            font-size: 12px;
            color: var(--text-secondary);
            font-weight: 500;
        }

        .meta-chip i {
            font-size: 14px;
            opacity: 0.7;
        }

        .priority-badge {
            padding: 4px 12px;
            border-radius: var(--radius-full);
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .priority-critical {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }

        .priority-high {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        }

        .priority-medium {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        }

        .priority-low {
            background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
        }

        .sla-alert {
            display: flex;
            align-items: center;
            gap: var(--spacing-xs);
            padding: var(--spacing-sm);
            background: rgba(239, 68, 68, 0.1);
            border-left: 3px solid var(--accent-danger);
            border-radius: var(--radius-sm);
            font-size: 12px;
            color: var(--accent-danger);
            font-weight: 600;
            margin-top: var(--spacing-sm);
            animation: pulseAlert 2s ease-in-out infinite;
        }

        @keyframes pulseAlert {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.7;
            }
        }

        .sla-alert.warning {
            background: rgba(245, 158, 11, 0.1);
            border-left-color: var(--accent-warning);
            color: var(--accent-warning);
        }

        .ticket-actions {
            padding: var(--spacing-md);
            border-top: 1px solid rgba(148, 163, 184, 0.1);
            display: flex;
            gap: var(--spacing-sm);
        }

        .action-button {
            flex: 1;
            padding: 12px;
            border-radius: var(--radius-md);
            font-size: 14px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            text-decoration: none;
        }

        .action-button:active {
            transform: scale(0.95);
        }

        .action-primary {
            background: var(--gradient-cosmic);
            color: var(--text-primary);
            box-shadow: 0 4px 12px rgba(0, 212, 255, 0.3);
        }

        .action-secondary {
            background: rgba(148, 163, 184, 0.1);
            color: var(--text-primary);
            border: 1px solid rgba(148, 163, 184, 0.2);
        }

        .action-danger {
            background: var(--gradient-danger);
            color: var(--text-primary);
        }

        /* ========================================
           FAB
           ======================================== */
        .fab-container {
            position: fixed;
            bottom: 24px;
            right: 24px;
            z-index: 999;
        }

        .fab {
            width: 64px;
            height: 64px;
            border-radius: var(--radius-full);
            background: var(--gradient-cosmic);
            border: none;
            box-shadow: 0 8px 32px rgba(0, 212, 255, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            color: var(--text-primary);
        }

        .fab i {
            font-size: 28px;
        }

        .fab:active {
            transform: scale(0.9) rotate(90deg);
        }

        .fab::before {
            content: '';
            position: absolute;
            inset: -8px;
            border-radius: var(--radius-full);
            background: inherit;
            opacity: 0.3;
            filter: blur(12px);
            animation: fabGlow 2s ease-in-out infinite;
        }

        @keyframes fabGlow {

            0%,
            100% {
                opacity: 0.3;
                transform: scale(1);
            }

            50% {
                opacity: 0.5;
                transform: scale(1.1);
            }
        }

        /* ========================================
           EMPTY STATE
           ======================================== */
        .empty-state {
            text-align: center;
            padding: var(--spacing-xl) var(--spacing-md);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: var(--spacing-md);
        }

        .empty-icon {
            width: 120px;
            height: 120px;
            border-radius: var(--radius-full);
            background: rgba(148, 163, 184, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            color: var(--text-muted);
            margin-bottom: var(--spacing-md);
        }

        .empty-title {
            font-size: 20px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: var(--spacing-xs);
        }

        .empty-description {
            font-size: 14px;
            color: var(--text-secondary);
            max-width: 300px;
            line-height: 1.6;
        }

        .empty-action {
            padding: 14px 28px;
            border-radius: var(--radius-full);
            background: var(--gradient-cosmic);
            border: none;
            color: var(--text-primary);
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            margin-top: var(--spacing-md);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .empty-action:active {
            transform: scale(0.95);
        }

        /* ========================================
           ANIMATIONS
           ======================================== */
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ========================================
           RESPONSIVE DESIGN
           ======================================== */
        @media (min-width: 768px) {
            .stats-grid {
                grid-template-columns: repeat(4, 1fr);
            }

            .tickets-list {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 480px) {
            .hero-title {
                font-size: 24px;
            }

            .stat-value {
                font-size: 28px;
            }

            .ticket-title {
                font-size: 15px;
            }
        }

        /* ========================================
           SWEETALERT2 CUSTOM STYLES
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
    </style>
</head>

<body>
    <!-- Cosmic Background -->
    <div class="cosmic-bg" id="cosmicBg">
        <!-- Generate floating particles -->
        @for ($i = 0; $i < 20; $i++)
            <div class="particle"
                style="left: {{ rand(0, 100) }}%; animation-delay: {{ rand(0, 20) }}s; animation-duration: {{ rand(15, 25) }}s;">
            </div>
        @endfor
    </div>

    <div class="page-wrapper">
        <div class="page-content">

            <!-- App Bar -->
            <div class="app-bar">
                <div class="app-bar-content">
                    <div class="app-bar-title">
                        <i class='bx bx-grid-alt' style="font-size: 24px; color: var(--accent-primary);"></i>
                        <h1>My Tickets</h1>
                    </div>
                    <div class="app-bar-actions">
                        <button class="icon-button" onclick="location.reload()">
                            <i class='bx bx-refresh'></i>
                        </button>
                        <button class="icon-button" id="notificationBtn">
                            <i class='bx bx-bell'></i>
                            @if ($stats['active'] > 0)
                                <span class="badge" id="notificationBadge">{{ $stats['active'] }}</span>
                            @else
                                <span class="badge" id="notificationBadge" style="display: none;">0</span>
                            @endif
                        </button>
                    </div>
                </div>
            </div>

            <!-- Hero Section -->
            <div class="hero-section">
                <div class="hero-content">
                    <div class="hero-greeting">Selamat Datang Kembali</div>
                    <h2 class="hero-title" id="userName">{{ $user->name }}</h2>
                    <p class="hero-subtitle">Kelola dan pantau semua tiket service request Anda</p>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="stats-grid">
                <div class="stat-card active">
                    <div class="stat-icon">
                        <i class='bx bx-rocket'></i>
                    </div>
                    <div class="stat-label">Active Tickets</div>
                    <div class="stat-value" id="statActive">{{ $stats['active'] }}</div>
                </div>

                <div class="stat-card info">
                    <div class="stat-icon">
                        <i class='bx bx-task'></i>
                    </div>
                    <div class="stat-label">Open</div>
                    <div class="stat-value" id="statOpen">{{ $stats['open'] }}</div>
                </div>

                <div class="stat-card warning">
                    <div class="stat-icon">
                        <i class='bx bx-time-five'></i>
                    </div>
                    <div class="stat-label">In Progress</div>
                    <div class="stat-value" id="statInProgress">{{ $stats['in_progress'] }}</div>
                </div>

                <div class="stat-card success">
                    <div class="stat-icon">
                        <i class='bx bx-check-circle'></i>
                    </div>
                    <div class="stat-label">Resolved</div>
                    <div class="stat-value" id="statResolved">{{ $stats['resolved'] }}</div>
                </div>
            </div>

            <!-- Search Bar -->
            <div class="search-container">
                <div class="search-box" id="searchBox">
                    <i class='bx bx-search'></i>
                    <input type="text" class="search-input" id="searchInput"
                        placeholder="Cari tiket berdasarkan nomor atau judul...">
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
                    <button class="filter-tab" data-status="Open">
                        <i class='bx bx-task'></i> Open
                    </button>
                    <button class="filter-tab" data-status="Pending">
                        <i class='bx bx-time'></i> Pending
                    </button>
                    <button class="filter-tab" data-status="In Progress">
                        <i class='bx bx-loader-circle'></i> In Progress
                    </button>
                    <button class="filter-tab" data-status="Resolved">
                        <i class='bx bx-check-circle'></i> Resolved
                    </button>
                    <button class="filter-tab" data-status="Closed">
                        <i class='bx bx-archive'></i> Closed
                    </button>
                </div>
            </div>

            <!-- Tickets List -->
            <div class="tickets-container">
                <div class="section-header">
                    <h3 class="section-title">Daftar Tiket</h3>
                    <span class="section-count" id="ticketCount">{{ $tickets->count() }} tiket</span>
                </div>

                <div class="tickets-list" id="ticketsList">
                    @forelse($tickets as $ticket)
                        <div class="ticket-card {{ $ticket->sla_deadline && now()->gt($ticket->sla_deadline) ? 'has-sla-warning' : '' }}"
                            data-id="{{ $ticket->id }}" data-status="{{ $ticket->ticket_status }}"
                            data-priority="{{ $ticket->priority }}"
                            data-search="{{ strtolower($ticket->ticket_number . ' ' . $ticket->issue_title . ' ' . ($ticket->hospitalUnit ? $ticket->hospitalUnit->unit_name : '') . ' ' . ($ticket->problemCategory ? $ticket->problemCategory->category_name : '')) }}">

                            <!-- Ticket Header -->
                            <div class="ticket-header">
                                <div class="ticket-number">
                                    <span class="ticket-id">#{{ $ticket->ticket_number }}</span>
                                    <span class="ticket-date">
                                        <i class='bx bx-calendar'></i>
                                        {{ $ticket->created_at->format('d M Y, H:i') }}
                                    </span>
                                </div>
                                <span
                                    class="status-badge status-{{ strtolower(str_replace(' ', '-', $ticket->ticket_status)) }}">
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

                                    <span class="priority-badge priority-{{ strtolower($ticket->priority) }}">
                                        {{ $ticket->priority }}
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
                                <a href="{{ route('ticket.show', $ticket->ticket_number) }}"
                                    class="action-button action-primary">
                                    <i class='bx bx-show'></i>
                                    Detail
                                </a>

                                @if (in_array($ticket->ticket_status, ['Open', 'Pending']))
                                    <a href="{{ route('service.edit', $ticket->ticket_number) }}"
                                        class="action-button action-secondary">
                                        <i class='bx bx-edit'></i>
                                        Edit
                                    </a>

                                    <button class="action-button action-danger delete-ticket"
                                        data-id="{{ $ticket->id }}" data-ticket="{{ $ticket->ticket_number }}">
                                        <i class='bx bx-trash'></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class='bx bx-inbox'></i>
                            </div>
                            <h3 class="empty-title">Belum Ada Tiket</h3>
                            <p class="empty-description">
                                Anda belum memiliki tiket service request. Buat tiket baru untuk melaporkan masalah
                                teknis.
                            </p>
                            <a href="{{ route('service.create') }}" class="empty-action">
                                <i class='bx bx-plus-circle'></i>
                                Buat Tiket Pertama
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Floating Action Button -->
            <div class="fab-container">
                <a href="{{ route('service.create') }}" class="fab">
                    <i class='bx bx-plus'></i>
                </a>
            </div>

        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Main Application Script -->
    <script>
        $(function() {
            // ==========================================
            // CONFIGURATION & STATE
            // ==========================================
            let currentFilter = 'all';
            let currentSearch = '';

            // ==========================================
            // FILTER TABS
            // ==========================================
            $('.filter-tab').on('click', function() {
                const status = $(this).data('status');
                currentFilter = status;

                // Update active state
                $('.filter-tab').removeClass('active');
                $(this).addClass('active');

                // Filter tickets
                filterAndSearch();
            });

            // ==========================================
            // SEARCH FUNCTIONALITY
            // ==========================================
            let searchTimeout;
            $('#searchInput').on('input', function() {
                const keyword = $(this).val();
                $('#searchBox').toggleClass('has-value', keyword.length > 0);

                // Debounce search
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    currentSearch = keyword.toLowerCase();
                    filterAndSearch();
                }, 300);
            });

            $('#searchClear').on('click', function() {
                currentSearch = '';
                $('#searchInput').val('');
                $('#searchBox').removeClass('has-value');
                filterAndSearch();
                $('#searchInput').focus();
            });

            // ==========================================
            // COMBINED FILTER & SEARCH
            // ==========================================
            function filterAndSearch() {
                let visibleCount = 0;

                $('.ticket-card').each(function() {
                    const $card = $(this);
                    const ticketStatus = $card.data('status');
                    const searchData = $card.data('search');

                    // Check filter
                    const matchesFilter = currentFilter === 'all' || ticketStatus === currentFilter;

                    // Check search
                    const matchesSearch = currentSearch === '' || searchData.includes(currentSearch);

                    // Show/hide card
                    if (matchesFilter && matchesSearch) {
                        $card.show();
                        visibleCount++;
                    } else {
                        $card.hide();
                    }
                });

                // Update count
                $('#ticketCount').text(visibleCount + ' tiket');

                // Show/hide empty state
                if (visibleCount === 0) {
                    let message = 'Tidak ada tiket';
                    if (currentSearch) {
                        message = `Tidak ditemukan tiket dengan kata kunci "${currentSearch}"`;
                    } else if (currentFilter !== 'all') {
                        message = `Tidak ada tiket dengan status "${currentFilter}"`;
                    }
                    showEmptyState(message);
                } else {
                    hideEmptyState();
                }
            }

            // ==========================================
            // DELETE TICKET
            // ==========================================
            $(document).on('click', '.delete-ticket', function() {
                const ticketNumber = $(this).data('ticket');
                const ticketId = $(this).data('id');
                const ticketCard = $(this).closest('.ticket-card');

                Swal.fire({
                    title: 'Hapus Tiket?',
                    html: `<p>Tiket <strong style="color: var(--accent-primary);">${ticketNumber}</strong> akan dihapus permanen.</p>
                           <p style="color: var(--text-muted); font-size: 14px;">Tindakan ini tidak dapat dibatalkan.</p>`,
                    icon: 'warning',
                    iconColor: 'var(--accent-danger)',
                    showCancelButton: true,
                    confirmButtonColor: 'var(--accent-danger)',
                    cancelButtonColor: 'var(--text-muted)',
                    confirmButtonText: '<i class="bx bx-trash"></i> Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    showClass: {
                        popup: 'animate__animated animate__shakeX animate__faster'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/service-request/' + ticketNumber,
                            type: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                // Animate removal
                                ticketCard.css({
                                    'transform': 'translateX(-100%)',
                                    'opacity': '0',
                                    'transition': 'all 0.3s ease'
                                });

                                setTimeout(function() {
                                    ticketCard.remove();

                                    // Update stats
                                    updateStats();

                                    // Update count
                                    filterAndSearch();

                                    // Show success
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Terhapus!',
                                        text: 'Tiket berhasil dihapus',
                                        iconColor: 'var(--accent-success)',
                                        confirmButtonColor: 'var(--accent-primary)',
                                        timer: 2000,
                                        showConfirmButton: false
                                    });

                                    // Check if empty
                                    if ($('.ticket-card').length === 0) {
                                        showEmptyState('Belum Ada Tiket');
                                    }
                                }, 300);
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal!',
                                    text: 'Gagal menghapus tiket',
                                    iconColor: 'var(--accent-danger)',
                                    confirmButtonColor: 'var(--accent-primary)'
                                });
                            }
                        });
                    }
                });
            });

            // ==========================================
            // UPDATE STATISTICS WITH ANIMATION
            // ==========================================
            function updateStats() {
                $.get('/user-ticket/stats', function(data) {
                    animateNumber('#statActive', data.active);
                    animateNumber('#statOpen', data.open);
                    animateNumber('#statInProgress', data.in_progress);
                    animateNumber('#statResolved', data.resolved);

                    // Update badge
                    const badge = $('#notificationBadge');
                    if (data.active > 0) {
                        badge.text(data.active).show();
                    } else {
                        badge.hide();
                    }
                });
            }

            function animateNumber(selector, targetNumber) {
                const $element = $(selector);
                const currentNumber = parseInt($element.text()) || 0;

                if (currentNumber === targetNumber) return;

                const duration = 500;
                const steps = 20;
                const increment = (targetNumber - currentNumber) / steps;
                let current = currentNumber;
                let step = 0;

                const timer = setInterval(() => {
                    step++;
                    current += increment;

                    if (step >= steps) {
                        $element.text(targetNumber);
                        clearInterval(timer);
                    } else {
                        $element.text(Math.round(current));
                    }
                }, duration / steps);
            }

            // ==========================================
            // EMPTY STATE HELPERS
            // ==========================================
            function showEmptyState(message) {
                if ($('.empty-state-dynamic').length === 0) {
                    const isSearch = currentSearch.length > 0;
                    const isFilter = currentFilter !== 'all';

                    let actionBtn = '';
                    if (isSearch) {
                        actionBtn = `
                            <button class="empty-action" id="clearSearchBtn" style="background: var(--gradient-primary);">
                                <i class='bx bx-x-circle'></i>
                                Hapus Pencarian
                            </button>
                        `;
                    } else if (isFilter) {
                        actionBtn = `
                            <button class="empty-action" id="clearFilterBtn" style="background: var(--gradient-primary);">
                                <i class='bx bx-grid-alt'></i>
                                Lihat Semua Tiket
                            </button>
                        `;
                    }

                    $('#ticketsList').append(`
                        <div class="empty-state empty-state-dynamic" style="animation: fadeIn 0.5s ease;">
                            <div class="empty-icon">
                                <i class='bx ${isSearch ? 'bx-search-alt' : 'bx-filter-alt'}'></i>
                            </div>
                            <h3 class="empty-title">${message}</h3>
                            <p class="empty-description">Coba filter atau kata kunci lain</p>
                            ${actionBtn}
                        </div>
                    `);
                }
            }

            function hideEmptyState() {
                $('.empty-state-dynamic').remove();
            }

            // Clear search button
            $(document).on('click', '#clearSearchBtn', function() {
                currentSearch = '';
                $('#searchInput').val('');
                $('#searchBox').removeClass('has-value');
                filterAndSearch();
            });

            // Clear filter button
            $(document).on('click', '#clearFilterBtn', function() {
                currentFilter = 'all';
                $('.filter-tab').removeClass('active');
                $('.filter-tab[data-status="all"]').addClass('active');
                filterAndSearch();
            });

            // ==========================================
            // CARD ANIMATIONS
            // ==========================================
            $('.ticket-card').each(function(index) {
                $(this).css({
                    'animation': `slideIn 0.4s ease-out ${index * 0.05}s both`
                });
            });

            // ==========================================
            // PULL TO REFRESH
            // ==========================================
            let startY = 0;
            let pulling = false;

            $(window).on('touchstart', function(e) {
                if (window.scrollY === 0) {
                    startY = e.touches[0].pageY;
                    pulling = true;
                }
            });

            $(window).on('touchmove', function(e) {
                if (!pulling) return;

                const currentY = e.touches[0].pageY;
                const distance = currentY - startY;

                if (distance > 80 && window.scrollY === 0) {
                    pulling = false;
                    location.reload();
                }
            });

            $(window).on('touchend', function() {
                pulling = false;
            });

            // ==========================================
            // KEYBOARD SHORTCUTS
            // ==========================================
            $(document).on('keydown', function(e) {
                // Ctrl/Cmd + K to focus search
                if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                    e.preventDefault();
                    $('#searchInput').focus();
                }

                // ESC to clear search
                if (e.key === 'Escape' && currentSearch) {
                    currentSearch = '';
                    $('#searchInput').val('').blur();
                    $('#searchBox').removeClass('has-value');
                    filterAndSearch();
                }
            });

            // ==========================================
            // INITIALIZATION LOG
            // ==========================================
            console.log('%c🎫 Ticket Management System', 'font-size: 20px; font-weight: bold; color: #00d4ff;');
            console.log('%c✨ Clean, Powerful & Modern', 'font-size: 14px; color: #7c3aed;');
            console.log('%c📋 Total Tickets: ' + $('.ticket-card').length, 'font-size: 12px; color: #10b981;');
            console.log('%c\n💡 Keyboard Shortcuts:', 'font-size: 14px; font-weight: bold; color: #f59e0b;');
            console.log('%c   • Ctrl/Cmd + K : Focus Search', 'font-size: 12px; color: #94a3b8;');
            console.log('%c   • ESC : Clear Search', 'font-size: 12px; color: #94a3b8;');
        });
    </script>
</body>

</html>
