<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Buat Tiket IT - {{ config('app.name') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/favicon.ico">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@600&display=swap"
        rel="stylesheet">

    <!-- Boxicons -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

    <style>
        /* ========================================
           RESET & BASE STYLES
           ======================================== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-tap-highlight-color: transparent;
        }

        html,
        body {
            height: 100%;
            overflow-x: hidden;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #f8fafc;
            color: #0f172a;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* ========================================
           CSS VARIABLES
           ======================================== */
        :root {
            --primary: #0ea5e9;
            --primary-dark: #0284c7;
            --secondary: #8b5cf6;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;

            --bg-primary: #f8fafc;
            --bg-card: #ffffff;
            --bg-input: #f1f5f9;

            --text-primary: #0f172a;
            --text-secondary: #475569;
            --text-muted: #64748b;

            --border-color: #e2e8f0;
            --border-medium: #cbd5e1;

            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 16px;
            --radius-full: 9999px;

            --shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 16px rgba(0, 0, 0, 0.08);
            --shadow-glow: 0 0 20px rgba(14, 165, 233, 0.3);

            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* ========================================
           BACKGROUND
           ======================================== */
        .cosmic-bg {
            position: fixed;
            inset: 0;
            z-index: 0;
            background: linear-gradient(135deg, #e0f2fe 0%, #f1f5f9 50%, #fae8ff 100%);
            pointer-events: none;
        }

        .cosmic-bg::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at 20% 30%, rgba(14, 165, 233, 0.08) 0%, transparent 50%),
                radial-gradient(circle at 80% 70%, rgba(139, 92, 246, 0.08) 0%, transparent 50%);
            animation: float 20s ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translate(0, 0);
            }

            50% {
                transform: translate(15px, -15px);
            }
        }

        /* ========================================
           CONTAINER
           ======================================== */
        .app-container {
            position: relative;
            z-index: 1;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ========================================
           APP BAR
           ======================================== */
        .app-bar {
            position: sticky;
            top: 0;
            z-index: 100;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px) saturate(180%);
            border-bottom: 2px solid var(--border-color);
            padding: 14px 16px;
            box-shadow: var(--shadow);
        }

        .app-bar-content {
            max-width: 640px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .back-btn {
            width: 36px;
            height: 36px;
            border-radius: var(--radius-full);
            background: var(--bg-input);
            border: 2px solid var(--border-color);
            color: var(--text-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: var(--transition);
            flex-shrink: 0;
        }

        .back-btn:active {
            transform: scale(0.92);
            background: var(--border-medium);
            border-color: var(--primary);
        }

        .app-title {
            flex: 1;
        }

        .app-title h1 {
            font-size: 17px;
            font-weight: 700;
            margin: 0;
            line-height: 1.2;
            color: var(--text-primary);
        }

        .app-title p {
            font-size: 11px;
            color: var(--text-secondary);
            margin: 2px 0 0 0;
        }

        /* ========================================
           MAIN CONTENT
           ======================================== */
        .main-content {
            flex: 1;
            max-width: 640px;
            margin: 0 auto;
            width: 100%;
            padding: 16px;
            padding-bottom: 90px;
        }

        /* ========================================
           PROGRESS INDICATOR
           ======================================== */
        .progress-card {
            background: var(--bg-card);
            border: 2px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: 16px;
            margin-bottom: 20px;
            box-shadow: var(--shadow);
        }

        .progress-dots {
            display: flex;
            justify-content: space-between;
            position: relative;
            margin-bottom: 10px;
        }

        .progress-dots::before {
            content: '';
            position: absolute;
            top: 14px;
            left: 0;
            right: 0;
            height: 2px;
            background: var(--border-medium);
            z-index: 0;
        }

        .progress-line {
            position: absolute;
            top: 14px;
            left: 0;
            height: 2px;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            transition: width 0.4s ease;
            z-index: 1;
        }

        .progress-dot {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: white;
            border: 2px solid var(--border-medium);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 700;
            position: relative;
            z-index: 2;
            transition: var(--transition);
            color: var(--text-muted);
        }

        .progress-dot.active {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-color: transparent;
            box-shadow: var(--shadow-glow);
            color: white;
        }

        .progress-dot.done {
            background: var(--success);
            border-color: transparent;
            color: white;
        }

        .progress-labels {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 8px;
            text-align: center;
        }

        .progress-label {
            font-size: 10px;
            color: var(--text-secondary);
            font-weight: 600;
        }

        /* ========================================
           SECTION
           ======================================== */
        .section {
            display: none;
        }

        .section.active {
            display: block;
            animation: slideUp 0.3s ease;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(15px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ========================================
           CARD
           ======================================== */
        .card {
            background: var(--bg-card);
            border: 2px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: 16px;
            margin-bottom: 14px;
            box-shadow: var(--shadow);
        }

        .card-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 14px;
        }

        .card-icon {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
            color: white;
            box-shadow: 0 4px 12px rgba(14, 165, 233, 0.3);
        }

        .card-title {
            flex: 1;
        }

        .card-title h3 {
            font-size: 14px;
            font-weight: 700;
            margin: 0;
            line-height: 1.3;
            color: var(--text-primary);
        }

        .card-title p {
            font-size: 11px;
            color: var(--text-secondary);
            margin: 2px 0 0 0;
        }

        .card-title-simple {
            margin-bottom: 16px;
        }

        .card-title-simple h3 {
            font-size: 15px;
            font-weight: 700;
            margin: 0 0 4px 0;
            color: var(--text-primary);
        }

        .card-title-simple p {
            font-size: 12px;
            color: var(--text-secondary);
            margin: 0;
        }

        /* UPLOAD TITLE - SIMPLE & CLEAN */
        .upload-title {
            margin-bottom: 16px;
            text-align: center;
        }

        .upload-title h3 {
            font-size: 15px;
            font-weight: 700;
            color: var(--text-primary);
            margin: 0 0 4px 0;
        }

        .upload-title p {
            font-size: 12px;
            color: var(--text-secondary);
            margin: 0;
        }

        /* ========================================
           CATEGORY GRID
           ======================================== */
        .category-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }

        .category-item {
            background: var(--bg-input);
            border: 2px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 18px 12px;
            text-align: center;
            cursor: pointer;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .category-item::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            opacity: 0;
            transition: opacity 0.3s;
        }

        .category-item:active {
            transform: scale(0.96);
        }

        .category-item.selected {
            border-color: var(--primary);
            box-shadow: 0 0 15px rgba(14, 165, 233, 0.25);
            background: white;
        }

        .category-item.selected::before {
            opacity: 0.05;
        }

        .category-icon {
            width: 48px;
            height: 48px;
            border-radius: var(--radius-md);
            margin: 0 auto 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            position: relative;
            z-index: 1;
            color: white;
        }

        /* Category Colors */
        .cat-hw {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        .cat-sw {
            background: linear-gradient(135deg, #8b5cf6, #7c3aed);
            box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
        }

        .cat-net {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
        }

        .cat-pabx {
            background: linear-gradient(135deg, #10b981, #059669);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .cat-cctv {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        }

        .cat-ac {
            background: linear-gradient(135deg, #06b6d4, #0891b2);
            box-shadow: 0 4px 12px rgba(6, 182, 212, 0.3);
        }

        .cat-dev {
            background: linear-gradient(135deg, #ec4899, #db2777);
            box-shadow: 0 4px 12px rgba(236, 72, 153, 0.3);
        }

        .cat-email {
            background: linear-gradient(135deg, #6366f1, #4f46e5);
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
        }

        .category-name {
            font-size: 13px;
            font-weight: 600;
            position: relative;
            z-index: 1;
            color: var(--text-primary);
        }

        .category-desc {
            font-size: 10px;
            color: var(--text-secondary);
            margin-top: 3px;
            position: relative;
            z-index: 1;
        }

        /* ========================================
           CHIP GRID
           ======================================== */
        .chip-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 8px;
        }

        .chip {
            padding: 11px 14px;
            background: var(--bg-input);
            border: 2px solid var(--border-color);
            border-radius: var(--radius-md);
            font-size: 12px;
            font-weight: 600;
            color: var(--text-secondary);
            text-align: center;
            cursor: pointer;
            transition: var(--transition);
        }

        .chip:active {
            transform: scale(0.96);
        }

        .chip.selected {
            background: white;
            border-color: var(--primary);
            color: var(--primary);
            box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.1);
        }

        /* ========================================
           FORM ELEMENTS
           ======================================== */
        .form-group {
            margin-bottom: 14px;
        }

        .form-label {
            font-size: 12px;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 7px;
            display: block;
        }

        .form-input,
        .form-select,
        .form-textarea {
            width: 100%;
            padding: 13px 15px;
            background: var(--bg-input);
            border: 2px solid var(--border-color);
            border-radius: var(--radius-md);
            color: var(--text-primary);
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            transition: var(--transition);
            font-weight: 500;
        }

        .form-input:focus,
        .form-select:focus,
        .form-textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.08);
            background: white;
        }

        .form-input::placeholder,
        .form-textarea::placeholder {
            color: var(--text-muted);
        }

        .form-textarea {
            min-height: 110px;
            resize: vertical;
        }

        .form-select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%2364748b' d='M6 8L2 4h8z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 14px center;
            padding-right: 40px;
        }

        .form-hint {
            font-size: 11px;
            color: var(--text-muted);
            margin-top: 5px;
        }

        /* ========================================
           SEVERITY CARDS
           ======================================== */
        .severity-grid {
            display: grid;
            gap: 10px;
        }

        .severity-item {
            padding: 14px;
            background: var(--bg-input);
            border: 2px solid var(--border-color);
            border-radius: var(--radius-md);
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .severity-item:active {
            transform: scale(0.98);
        }

        .severity-item.selected {
            border-color: currentColor;
            background: white;
            box-shadow: 0 0 0 3px currentColor;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .severity-emoji {
            font-size: 28px;
            line-height: 1;
        }

        .severity-text h4 {
            font-size: 14px;
            font-weight: 700;
            margin: 0 0 2px 0;
        }

        .severity-text p {
            font-size: 11px;
            opacity: 0.7;
            margin: 0;
        }

        .severity-low {
            color: #10b981;
        }

        .severity-med {
            color: #3b82f6;
        }

        .severity-high {
            color: #f59e0b;
        }

        .severity-critical {
            color: #ef4444;
        }

        /* ========================================
           TOGGLE SWITCH
           ======================================== */
        .toggle-card {
            padding: 14px;
            background: var(--bg-input);
            border: 2px solid var(--border-color);
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            transition: var(--transition);
        }

        .toggle-card.active {
            border-color: var(--danger);
            background: rgba(239, 68, 68, 0.08);
        }

        .toggle-left {
            display: flex;
            align-items: center;
            gap: 11px;
        }

        .toggle-icon {
            width: 36px;
            height: 36px;
            border-radius: var(--radius-md);
            background: rgba(239, 68, 68, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: var(--danger);
        }

        .toggle-label {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-primary);
        }

        .toggle-switch {
            position: relative;
            width: 48px;
            height: 28px;
            background: var(--border-medium);
            border-radius: 14px;
            transition: var(--transition);
        }

        .toggle-card.active .toggle-switch {
            background: var(--danger);
        }

        .toggle-switch::before {
            content: '';
            position: absolute;
            top: 3px;
            left: 3px;
            width: 22px;
            height: 22px;
            background: white;
            border-radius: 50%;
            transition: var(--transition);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .toggle-card.active .toggle-switch::before {
            transform: translateX(20px);
        }

        /* ========================================
           CAMERA UPLOAD
           ======================================== */
        .camera-upload {
            width: 100%;
            padding: 42px 16px;
            background: var(--bg-input);
            border: 2px dashed var(--border-medium);
            border-radius: var(--radius-md);
            text-align: center;
            cursor: pointer;
            transition: var(--transition);
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .camera-upload:active {
            transform: scale(0.98);
        }

        .camera-upload.has-file {
            border-style: solid;
            border-color: var(--success);
            background: rgba(16, 185, 129, 0.08);
        }

        .camera-upload i {
            font-size: 42px;
            opacity: 0.4;
            margin-bottom: 10px;
            color: var(--text-muted);
            display: block;
            width: 100%;
            text-align: center;
        }

        .camera-upload.has-file i {
            opacity: 1;
            color: var(--success);
        }

        .camera-upload p {
            font-size: 13px;
            font-weight: 600;
            margin: 0;
            color: var(--text-primary);
            width: 100%;
            text-align: center;
        }

        .camera-upload small {
            font-size: 11px;
            color: var(--text-muted);
            display: block;
            margin-top: 4px;
            width: 100%;
            text-align: center;
        }

        .file-preview {
            margin-top: 14px;
            display: none;
        }

        .file-preview.show {
            display: block;
        }

        .preview-img {
            width: 100%;
            max-height: 280px;
            object-fit: cover;
            border-radius: var(--radius-md);
            border: 2px solid var(--border-color);
        }

        .file-info {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 11px;
            background: var(--bg-input);
            border: 2px solid var(--border-color);
            border-radius: var(--radius-md);
            margin-top: 10px;
            font-size: 12px;
            color: var(--text-primary);
        }

        .file-info button {
            margin-left: auto;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: rgba(239, 68, 68, 0.15);
            border: none;
            color: var(--danger);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        /* ========================================
           SUMMARY
           ======================================== */
        .summary-group {
            background: var(--bg-input);
            border: 2px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 14px;
            margin-bottom: 12px;
        }

        .summary-title {
            font-size: 12px;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 7px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 7px 0;
            font-size: 12px;
            border-bottom: 1px solid var(--border-color);
        }

        .summary-row:last-child {
            border-bottom: none;
        }

        .summary-label {
            color: var(--text-secondary);
            font-weight: 500;
        }

        .summary-value {
            color: var(--text-primary);
            font-weight: 600;
            text-align: right;
            max-width: 55%;
            word-break: break-word;
        }

        /* ========================================
           ALERT
           ======================================== */
        .alert {
            padding: 13px 15px;
            border-radius: var(--radius-md);
            font-size: 12px;
            margin-bottom: 14px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        .alert-warning {
            background: rgba(245, 158, 11, 0.1);
            border: 2px solid rgba(245, 158, 11, 0.3);
            color: #d97706;
        }

        .alert i {
            font-size: 17px;
            flex-shrink: 0;
            margin-top: 1px;
        }

        /* ========================================
           BOTTOM BAR
           ======================================== */
        .bottom-bar {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 100;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px) saturate(180%);
            border-top: 2px solid var(--border-color);
            padding: 14px 16px;
            box-shadow: 0 -4px 12px rgba(0, 0, 0, 0.05);
        }

        .bottom-bar-content {
            max-width: 640px;
            margin: 0 auto;
            display: flex;
            gap: 10px;
        }

        /* ========================================
           BUTTONS
           ======================================== */
        .btn {
            flex: 1;
            padding: 14px 20px;
            border-radius: var(--radius-md);
            font-size: 14px;
            font-weight: 700;
            border: none;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
        }

        .btn:active {
            transform: scale(0.96);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            box-shadow: 0 4px 14px rgba(14, 165, 233, 0.3);
        }

        .btn-secondary {
            background: white;
            color: var(--text-primary);
            border: 2px solid var(--border-medium);
        }

        .btn-secondary:hover {
            background: var(--bg-input);
            border-color: var(--primary);
        }

        .btn-success {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            box-shadow: 0 4px 14px rgba(16, 185, 129, 0.3);
        }

        .btn-block {
            width: 100%;
        }

        /* ========================================
           LOADING
           ======================================== */
        .loading-overlay {
            position: fixed;
            inset: 0;
            background: rgba(248, 250, 252, 0.95);
            backdrop-filter: blur(8px);
            z-index: 999;
            display: none;
            align-items: center;
            justify-content: center;
        }

        .loading-overlay.show {
            display: flex;
        }

        .spinner {
            width: 44px;
            height: 44px;
            border: 4px solid var(--border-medium);
            border-top-color: var(--primary);
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* ========================================
           RESPONSIVE
           ======================================== */
        @media (min-width: 640px) {
            .category-grid {
                grid-template-columns: repeat(4, 1fr);
            }

            .chip-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        /* ========================================
           UTILITY
           ======================================== */
        .hidden {
            display: none !important;
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--border-medium);
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--text-muted);
        }
    </style>
</head>

<body>
    <!-- Background -->
    <div class="cosmic-bg"></div>

    <!-- App Container -->
    <div class="app-container">
        <!-- App Bar -->
        <header class="app-bar">
            <div class="app-bar-content">
                <button class="back-btn" onclick="window.history.back()">
                    <i class='bx bx-arrow-back'></i>
                </button>
                <div class="app-title">
                    <h1>Buat Tiket IT</h1>
                    <p>Laporkan masalah Anda</p>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Progress -->
            <div class="progress-card">
                <div class="progress-dots">
                    <div class="progress-line" id="progressLine"></div>
                    <div class="progress-dot active" id="dot1">1</div>
                    <div class="progress-dot" id="dot2">2</div>
                    <div class="progress-dot" id="dot3">3</div>
                    <div class="progress-dot" id="dot4">4</div>
                </div>
                <div class="progress-labels">
                    <span class="progress-label">Kategori</span>
                    <span class="progress-label">Detail</span>
                    <span class="progress-label">Lokasi</span>
                    <span class="progress-label">Review</span>
                </div>
            </div>

            <!-- Form -->
            <form id="ticketForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="requester_name" value="{{ Auth::user()->name }}">
                <input type="hidden" name="requester_phone" id="hiddenPhone">
                <input type="hidden" name="problem_category_id" id="categoryId">
                <input type="hidden" name="problem_sub_category_id" id="subCategoryId">
                <input type="hidden" name="severity_level" id="severityLevel">
                <input type="hidden" name="impact_patient_care" id="impactCare" value="0">

                <!-- Step 1: Kategori -->
                <section class="section active" id="step1">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-icon">
                                <i class='bx bx-category'></i>
                            </div>
                            <div class="card-title">
                                <h3>Pilih Kategori</h3>
                                <p>Jenis masalah yang dialami</p>
                            </div>
                        </div>
                        <div class="category-grid" id="categoryGrid">
                            <!-- Populated by JS -->
                        </div>
                    </div>
                </section>

                <!-- Step 2: Detail -->
                <section class="section" id="step2">
                    <!-- Sub-category -->
                    <div class="card hidden" id="subCategoryCard">
                        <div class="card-header">
                            <div class="card-icon">
                                <i class='bx bx-list-ul'></i>
                            </div>
                            <div class="card-title">
                                <h3>Detail Masalah</h3>
                                <p>Pilih yang paling sesuai</p>
                            </div>
                        </div>
                        <div class="chip-grid" id="subCategoryGrid"></div>
                    </div>

                    <!-- Judul -->
                    <div class="card">
                        <div class="card-header">
                            <div class="card-icon">
                                <i class='bx bx-text'></i>
                            </div>
                            <div class="card-title">
                                <h3>Judul Masalah</h3>
                                <p>Ringkasan singkat</p>
                            </div>
                        </div>
                        <input type="text" class="form-input" name="issue_title"
                            placeholder="Contoh: PC tidak bisa nyala" required>
                    </div>

                    <!-- Deskripsi -->
                    <div class="card">
                        <div class="card-header">
                            <div class="card-icon">
                                <i class='bx bx-message-detail'></i>
                            </div>
                            <div class="card-title">
                                <h3>Deskripsi</h3>
                                <p>Jelaskan detail masalahnya</p>
                            </div>
                        </div>
                        <textarea class="form-textarea" name="description"
                            placeholder="Apa yang terjadi? Kapan mulai? Apa yang sudah dicoba?" required></textarea>
                        <div class="form-hint">💡 Semakin detail, semakin cepat dibantu</div>
                    </div>

                    <!-- Severity -->
                    <div class="card">
                        <div class="card-header">
                            <div class="card-icon">
                                <i class='bx bx-error-circle'></i>
                            </div>
                            <div class="card-title">
                                <h3>Seberapa Urgent?</h3>
                                <p>Tingkat keparahan</p>
                            </div>
                        </div>
                        <div class="severity-grid">
                            <div class="severity-item severity-low" data-level="Rendah"
                                onclick="selectSeverity('Rendah')">
                                <span class="severity-emoji">🟢</span>
                                <div class="severity-text">
                                    <h4>Rendah</h4>
                                    <p>Tidak mendesak</p>
                                </div>
                            </div>
                            <div class="severity-item severity-med" data-level="Sedang"
                                onclick="selectSeverity('Sedang')">
                                <span class="severity-emoji">🟡</span>
                                <div class="severity-text">
                                    <h4>Sedang</h4>
                                    <p>Ada workaround</p>
                                </div>
                            </div>
                            <div class="severity-item severity-high" data-level="Tinggi"
                                onclick="selectSeverity('Tinggi')">
                                <span class="severity-emoji">🟠</span>
                                <div class="severity-text">
                                    <h4>Tinggi</h4>
                                    <p>Ganggu operasional</p>
                                </div>
                            </div>
                            <div class="severity-item severity-critical" data-level="Kritis"
                                onclick="selectSeverity('Kritis')">
                                <span class="severity-emoji">🔴</span>
                                <div class="severity-text">
                                    <h4>Kritis</h4>
                                    <p>Ganggu pasien</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Impact Toggle -->
                    <div class="card">
                        <div class="toggle-card" id="impactToggle" onclick="toggleImpact()">
                            <div class="toggle-left">
                                <div class="toggle-icon">
                                    <i class='bx bx-heart'></i>
                                </div>
                                <span class="toggle-label">Berdampak pada pasien?</span>
                            </div>
                            <div class="toggle-switch"></div>
                        </div>
                    </div>

                    <!-- Waktu -->
                    <div class="card">
                        <div class="card-header">
                            <div class="card-icon">
                                <i class='bx bx-time'></i>
                            </div>
                            <div class="card-title">
                                <h3>Kapan Terjadi?</h3>
                                <p>Waktu pertama kali muncul</p>
                            </div>
                        </div>
                        <input type="datetime-local" class="form-input" name="occurrence_time" id="occurrenceTime"
                            required>
                    </div>
                </section>

                <!-- Step 3: Lokasi -->
                <section class="section" id="step3">
                    <!-- Unit -->
                    <div class="card">
                        <div class="card-header">
                            <div class="card-icon">
                                <i class='bx bx-building'></i>
                            </div>
                            <div class="card-title">
                                <h3>Unit/Ruangan</h3>
                                <p>Dimana Anda bekerja?</p>
                            </div>
                        </div>
                        <select class="form-select" name="unit_id" id="unitSelect" required>
                            <option value="">Pilih unit...</option>
                        </select>
                    </div>

                    <!-- Lokasi -->
                    <div class="card">
                        <div class="card-header">
                            <div class="card-icon">
                                <i class='bx bx-map-pin'></i>
                            </div>
                            <div class="card-title">
                                <h3>Lokasi Spesifik</h3>
                                <p>Lokasi detail dalam unit</p>
                            </div>
                        </div>
                        <input type="text" class="form-input" name="location" placeholder="Contoh: Meja perawat"
                            required>
                    </div>

                    <!-- Device -->
                    <div class="card">
                        <div class="card-header">
                            <div class="card-icon">
                                <i class='bx bx-laptop'></i>
                            </div>
                            <div class="card-title">
                                <h3>Perangkat (Opsional)</h3>
                                <p>Nama/kode perangkat</p>
                            </div>
                        </div>
                        <input type="text" class="form-input" name="device_affected"
                            placeholder="Contoh: PC-IGD-01">
                    </div>

                    <!-- Network (conditional) -->
                    <div class="card hidden" id="networkCard">
                        <div class="card-header">
                            <div class="card-icon">
                                <i class='bx bx-network-chart'></i>
                            </div>
                            <div class="card-title">
                                <h3>Info Network</h3>
                                <p>Jika diketahui</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <input type="text" class="form-input" name="ip_address"
                                placeholder="IP Address (192.168.x.x)">
                        </div>
                        <select class="form-select" name="connection_status">
                            <option value="">Status koneksi...</option>
                            <option value="Tidak bisa connect">Tidak bisa connect</option>
                            <option value="Koneksi lambat">Koneksi lambat</option>
                            <option value="Putus-putus">Putus-putus</option>
                        </select>
                    </div>

                    <!-- Expected Action -->
                    <div class="card">
                        <div class="card-header">
                            <div class="card-icon">
                                <i class='bx bx-task'></i>
                            </div>
                            <div class="card-title">
                                <h3>Yang Diharapkan</h3>
                                <p>Apa yang perlu dilakukan?</p>
                            </div>
                        </div>
                        <textarea class="form-textarea" name="expected_action" placeholder="Contoh: Perbaiki segera" required></textarea>
                    </div>

                    <!-- Upload -->
                    <div class="card">
                        <div class="card-header">
                            <div class="card-icon">
                                <i class='bx bx-camera'></i>
                            </div>
                            <div class="card-title">
                                <h3>Upload Foto (Opsional)</h3>
                                <p>Screenshot atau foto masalah</p>
                            </div>
                        </div>
                        <input type="file" name="file_path" id="fileInput" accept="image/*" style="display:none"
                            onchange="handleFile(this)">
                        <label for="fileInput" class="camera-upload" id="cameraBtn">
                            <i class='bx bx-camera'></i>
                            <p>Tap untuk Upload Foto</p>
                            <small>Max 5MB • JPG, PNG</small>
                        </label>
                        <div class="file-preview" id="filePreview">
                            <img id="previewImg" class="preview-img">
                            <div class="file-info">
                                <i class='bx bx-file'></i>
                                <span id="fileName"></span>
                                <button type="button" onclick="removeFile()">
                                    <i class='bx bx-x'></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Phone -->
                    <div class="card">
                        <div class="card-header">
                            <div class="card-icon">
                                <i class='bx bx-phone'></i>
                            </div>
                            <div class="card-title">
                                <h3>No. Telepon (Opsional)</h3>
                                <p>Untuk dihubungi teknisi</p>
                            </div>
                        </div>
                        <input type="tel" class="form-input" id="phoneInput" placeholder="08xxxxxxxxxx">
                    </div>
                </section>

                <!-- Step 4: Review -->
                <section class="section" id="step4">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-icon">
                                <i class='bx bx-check-double'></i>
                            </div>
                            <div class="card-title">
                                <h3>Review Data</h3>
                                <p>Pastikan semua benar</p>
                            </div>
                        </div>
                    </div>

                    <div id="summaryContainer"></div>

                    <div class="alert alert-warning">
                        <i class='bx bx-info-circle'></i>
                        <div>
                            <strong>Perhatian!</strong><br>
                            Data akan langsung diteruskan ke tim IT
                        </div>
                    </div>
                </section>
            </form>
        </main>

        <!-- Bottom Bar -->
        <div class="bottom-bar">
            <div class="bottom-bar-content">
                <button type="button" class="btn btn-secondary hidden" id="prevBtn" onclick="prevStep()">
                    <i class='bx bx-arrow-back'></i>
                    Kembali
                </button>
                <button type="button" class="btn btn-primary" id="nextBtn" onclick="nextStep()">
                    Lanjut
                    <i class='bx bx-arrow-to-right'></i>
                </button>
                <button type="button" class="btn btn-success btn-block hidden" id="submitBtn"
                    onclick="submitForm()">
                    <i class='bx bx-send'></i>
                    Kirim Tiket
                </button>
            </div>
        </div>
    </div>

    <!-- Loading -->
    <div class="loading-overlay" id="loading">
        <div class="spinner"></div>
    </div>

    <!-- jQuery & SweetAlert2 -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        let step = 1;
        let selectedCat = null;
        let selectedSub = null;

        $(function() {
            loadCategories();
            loadUnits();
            setTime();
        });

        function loadCategories() {
            $.get('/ajax/problem-categories', data => {
                const icons = {
                    HW: 'bx-chip',
                    SW: 'bx-code-alt',
                    NET: 'bx-wifi',
                    PABX: 'bx-phone',
                    CCTV: 'bx-video',
                    AC: 'bx-wind',
                    DEV: 'bx-rocket',
                    EMAIL: 'bx-envelope'
                };
                const desc = {
                    HW: 'PC, Printer',
                    SW: 'Aplikasi',
                    NET: 'Internet',
                    PABX: 'Telepon',
                    CCTV: 'Kamera',
                    AC: 'AC',
                    DEV: 'Fitur Baru',
                    EMAIL: 'Email'
                };
                let html = '';
                data.forEach(c => {
                    html += `<div class="category-item" data-id="${c.id}" data-code="${c.category_code}" onclick="selectCat(${c.id},'${c.category_code}')">
                        <div class="category-icon cat-${c.category_code.toLowerCase()}"><i class='bx ${icons[c.category_code]}'></i></div>
                        <div class="category-name">${c.category_name}</div>
                        <div class="category-desc">${desc[c.category_code]||''}</div>
                    </div>`;
                });
                $('#categoryGrid').html(html);
            });
        }

        function selectCat(id, code) {
            selectedCat = id;
            $('.category-item').removeClass('selected');
            $(`.category-item[data-id="${id}"]`).addClass('selected');
            $('#categoryId').val(id);

            $.get(`/ajax/sub-categories/${id}`, data => {
                if (data.length > 0) {
                    let html = '';
                    data.forEach(s => {
                        html +=
                            `<div class="chip" data-id="${s.id}" onclick="selectSub(${s.id})">${s.sub_category_name}</div>`;
                    });
                    $('#subCategoryGrid').html(html);
                    $('#subCategoryCard').removeClass('hidden');
                } else {
                    $('#subCategoryCard').addClass('hidden');
                }
            });

            $('#networkCard').toggleClass('hidden', code !== 'NET');
            setTimeout(nextStep, 300);
        }

        function selectSub(id) {
            selectedSub = id;
            $('.chip').removeClass('selected');
            $(`.chip[data-id="${id}"]`).addClass('selected');
            $('#subCategoryId').val(id);
        }

        function selectSeverity(lvl) {
            $('.severity-item').removeClass('selected');
            $(`.severity-item[data-level="${lvl}"]`).addClass('selected');
            $('#severityLevel').val(lvl);
            if (lvl === 'Kritis') {
                $('#impactToggle').addClass('active');
                $('#impactCare').val('1');
            }
        }

        function toggleImpact() {
            $('#impactToggle').toggleClass('active');
            $('#impactCare').val($('#impactToggle').hasClass('active') ? '1' : '0');
        }

        function handleFile(input) {
            const file = input.files[0];
            if (!file) return;
            if (file.size > 5 * 1024 * 1024) {
                Swal.fire({
                    icon: 'error',
                    title: 'File Terlalu Besar',
                    text: 'Max 5MB',
                    background: '#fff',
                    color: '#0f172a'
                });
                input.value = '';
                return;
            }
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = e => {
                    $('#previewImg').attr('src', e.target.result);
                    $('#fileName').text(file.name);
                    $('#filePreview').addClass('show');
                    $('#cameraBtn').addClass('has-file');
                };
                reader.readAsDataURL(file);
            }
        }

        function removeFile() {
            $('#fileInput').val('');
            $('#filePreview').removeClass('show');
            $('#cameraBtn').removeClass('has-file');
        }

        function loadUnits() {
            $.get('/ajax/hospital-units', data => {
                let html = '<option value="">Pilih unit...</option>';
                data.forEach(u => {
                    html += `<option value="${u.id}">${u.unit_code} - ${u.unit_name}</option>`;
                });
                $('#unitSelect').html(html);
            });
        }

        function setTime() {
            const now = new Date();
            const str =
                `${now.getFullYear()}-${String(now.getMonth()+1).padStart(2,'0')}-${String(now.getDate()).padStart(2,'0')}T${String(now.getHours()).padStart(2,'0')}:${String(now.getMinutes()).padStart(2,'0')}`;
            $('#occurrenceTime').val(str);
        }

        function nextStep() {
            if (!validate()) return;
            if (step < 4) {
                step++;
                updateUI();
            }
        }

        function prevStep() {
            if (step > 1) {
                step--;
                updateUI();
            }
        }

        function updateUI() {
            $('.section').removeClass('active');
            $(`#step${step}`).addClass('active');

            const prog = ((step - 1) / 3) * 100;
            $('#progressLine').css('width', prog + '%');

            for (let i = 1; i <= 4; i++) {
                $(`#dot${i}`).removeClass('active done');
                if (i < step) $(`#dot${i}`).addClass('done').html('<i class="bx bx-check"></i>');
                else if (i === step) $(`#dot${i}`).addClass('active').text(i);
                else $(`#dot${i}`).text(i);
            }

            $('#prevBtn').toggleClass('hidden', step === 1);
            $('#nextBtn').toggleClass('hidden', step === 4);
            $('#submitBtn').toggleClass('hidden', step !== 4);

            if (step === 4) generateSummary();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        function validate() {
            if (step === 1 && !selectedCat) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Pilih Kategori',
                    background: '#fff',
                    color: '#0f172a'
                });
                return false;
            }
            if (step === 2) {
                if (!$('[name="issue_title"]').val()) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Judul Kosong',
                        background: '#fff',
                        color: '#0f172a'
                    });
                    return false;
                }
                if (!$('[name="description"]').val() || !$('#severityLevel').val()) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Lengkapi Data',
                        background: '#fff',
                        color: '#0f172a'
                    });
                    return false;
                }
            }
            if (step === 3) {
                if (!$('[name="unit_id"]').val() || !$('[name="location"]').val() || !$('[name="expected_action"]')
                    .val()) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Lengkapi Lokasi',
                        background: '#fff',
                        color: '#0f172a'
                    });
                    return false;
                }
            }
            return true;
        }

        function generateSummary() {
            const catName = $(`.category-item[data-id="${selectedCat}"]`).find('.category-name').text();
            const subName = selectedSub ? $(`.chip[data-id="${selectedSub}"]`).text() : '-';
            const unit = $('#unitSelect option:selected').text();
            const sev = $('#severityLevel').val();
            const impact = $('#impactCare').val() === '1' ? 'Ya' : 'Tidak';

            $('#hiddenPhone').val($('#phoneInput').val());

            let html = `
            <div class="summary-group">
                <div class="summary-title"><i class='bx bx-info-circle'></i> Info Masalah</div>
                <div class="summary-row"><span class="summary-label">Kategori</span><span class="summary-value">${catName}</span></div>
                <div class="summary-row"><span class="summary-label">Detail</span><span class="summary-value">${subName}</span></div>
                <div class="summary-row"><span class="summary-label">Judul</span><span class="summary-value">${$('[name="issue_title"]').val()}</span></div>
                <div class="summary-row"><span class="summary-label">Severity</span><span class="summary-value">${sev}</span></div>
                <div class="summary-row"><span class="summary-label">Impact Pasien</span><span class="summary-value">${impact}</span></div>
            </div>
            <div class="summary-group">
                <div class="summary-title"><i class='bx bx-map'></i> Lokasi</div>
                <div class="summary-row"><span class="summary-label">Unit</span><span class="summary-value">${unit}</span></div>
                <div class="summary-row"><span class="summary-label">Lokasi</span><span class="summary-value">${$('[name="location"]').val()}</span></div>
            </div>`;

            $('#summaryContainer').html(html);
        }

        function submitForm() {
            $('#loading').addClass('show');
            const form = new FormData($('#ticketForm')[0]);

            $.ajax({
                url: '{{ route('service.store') }}',
                type: 'POST',
                data: form,
                processData: false,
                contentType: false,
                success: res => {
                    $('#loading').removeClass('show');
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        html: `<p>Tiket berhasil dibuat</p>
                            <div style="padding:14px;background:rgba(14,165,233,0.1);border-radius:12px;margin-top:14px;">
                                <div style="font-size:11px;color:#64748b;margin-bottom:4px;">No. Tiket</div>
                                <div style="font-size:17px;font-weight:700;font-family:'JetBrains Mono',monospace;color:#0ea5e9;">${res.ticket_number}</div>
                            </div>`,
                        background: '#fff',
                        color: '#0f172a',
                        confirmButtonColor: '#0ea5e9'
                    }).then(() => {
                        window.location.href = res.redirect_url;
                    });
                },
                error: xhr => {
                    $('#loading').removeClass('show');
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: xhr.responseJSON?.message || 'Error',
                        background: '#fff',
                        color: '#0f172a'
                    });
                }
            });
        }
    </script>
</body>

</html>
