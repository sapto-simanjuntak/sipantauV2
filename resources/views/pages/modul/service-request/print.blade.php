<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Service Request - {{ $ticket->ticket_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', 'Arial', sans-serif;
            font-size: 10pt;
            line-height: 1.5;
            color: #2c3e50;
        }

        .container {
            padding: 15px;
        }

        /* HEADER SECTION */
        .header {
            border-bottom: 3px solid #3498db;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .header-content {
            display: table;
            width: 100%;
        }

        .header-left {
            display: table-cell;
            width: 58%;
            vertical-align: top;
            padding-right: 15px;
        }

        .header-right {
            display: table-cell;
            width: 42%;
            text-align: center;
            vertical-align: top;
        }

        .logo {
            font-size: 22pt;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .subtitle {
            color: #34495e;
            font-size: 12pt;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .hospital-info {
            color: #7f8c8d;
            font-size: 8pt;
            line-height: 1.6;
            margin-top: 5px;
        }

        /* QR CODE BOX */
        .qr-box {
            background: #f8f9fa;
            border: 2px solid #3498db;
            border-radius: 8px;
            padding: 12px;
            display: inline-block;
        }

        .qr-title {
            font-size: 8pt;
            color: #2c3e50;
            font-weight: bold;
            text-align: center;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .qr-code-container {
            background: white;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
        }

        .qr-info {
            margin-top: 8px;
            padding-top: 8px;
            border-top: 1px dashed #bdc3c7;
            text-align: center;
        }

        .qr-ticket-number {
            font-family: 'Courier New', monospace;
            font-weight: bold;
            font-size: 9pt;
            color: #2c3e50;
            letter-spacing: 1px;
        }

        .qr-date {
            font-size: 7pt;
            color: #7f8c8d;
            margin-top: 3px;
        }

        /* TICKET TITLE SECTION */
        .ticket-title-section {
            background: #ecf0f1;
            border-left: 5px solid #3498db;
            padding: 15px;
            margin: 20px 0;
        }

        .ticket-number {
            font-size: 16pt;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 8px;
        }

        .issue-title {
            font-size: 12pt;
            color: #34495e;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .badges {
            margin-top: 10px;
        }

        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 3px;
            font-size: 8pt;
            font-weight: bold;
            margin-right: 6px;
            margin-bottom: 4px;
            color: white;
        }

        .badge-primary {
            background: #3498db;
        }

        .badge-success {
            background: #27ae60;
        }

        .badge-danger {
            background: #e74c3c;
        }

        .badge-warning {
            background: #f39c12;
            color: #fff;
        }

        .badge-info {
            background: #17a2b8;
        }

        .badge-secondary {
            background: #95a5a6;
        }

        .badge-dark {
            background: #34495e;
        }

        /* SECTION STYLING */
        .section {
            margin-bottom: 18px;
            page-break-inside: avoid;
        }

        .section-title {
            background: #34495e;
            color: white;
            padding: 8px 12px;
            font-weight: bold;
            font-size: 10pt;
            margin-bottom: 10px;
            border-radius: 3px;
        }

        /* INFO GRID */
        .info-grid {
            display: table;
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #bdc3c7;
        }

        .info-row {
            display: table-row;
        }

        .info-row:nth-child(odd) {
            background: #f8f9fa;
        }

        .info-row:nth-child(even) {
            background: #ffffff;
        }

        .info-cell {
            display: table-cell;
            width: 50%;
            padding: 10px 12px;
            vertical-align: top;
            border-bottom: 1px solid #ecf0f1;
            border-right: 1px solid #ecf0f1;
        }

        .info-cell:last-child {
            border-right: none;
        }

        .info-label {
            font-weight: 700;
            color: #34495e;
            font-size: 8pt;
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .info-value {
            font-size: 9pt;
            color: #2c3e50;
            line-height: 1.5;
        }

        /* DESCRIPTION BOX */
        .description-box {
            background: #f8f9fa;
            padding: 12px;
            border-radius: 5px;
            border-left: 3px solid #3498db;
            line-height: 1.6;
            font-size: 9pt;
        }

        /* TIMELINE */
        .timeline {
            position: relative;
            padding-left: 30px;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 9px;
            top: 5px;
            bottom: 0;
            width: 2px;
            background: #bdc3c7;
        }

        .timeline-item {
            position: relative;
            padding-bottom: 15px;
        }

        .timeline-item:last-child {
            padding-bottom: 0;
        }

        .timeline-icon {
            position: absolute;
            left: -30px;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #3498db;
            border: 3px solid white;
            box-shadow: 0 0 0 2px #bdc3c7;
        }

        .timeline-title {
            font-weight: bold;
            font-size: 9pt;
            margin-bottom: 3px;
            color: #2c3e50;
        }

        .timeline-desc {
            font-size: 8pt;
            color: #7f8c8d;
            margin-bottom: 3px;
            line-height: 1.4;
        }

        .timeline-time {
            font-size: 7pt;
            color: #95a5a6;
        }

        /* SIGNATURE SECTION */
        .signature-section {
            margin-top: 40px;
            page-break-inside: avoid;
            border-top: 2px solid #bdc3c7;
            padding-top: 25px;
        }

        .signature-header {
            font-weight: bold;
            font-size: 10pt;
            color: #2c3e50;
            margin-bottom: 20px;
            text-align: center;
        }

        .signature-grid {
            display: table;
            width: 100%;
        }

        .signature-cell {
            display: table-cell;
            width: 33.33%;
            text-align: center;
            vertical-align: top;
            padding: 10px;
        }

        .signature-title {
            font-weight: bold;
            margin-bottom: 10px;
            font-size: 9pt;
            color: #34495e;
        }

        .signature-qr {
            margin: 10px auto;
        }

        .signature-qr img {
            width: 80px;
            height: 80px;
            display: block;
            margin: 0 auto;
            border: 1px solid #bdc3c7;
            border-radius: 4px;
            padding: 3px;
            background: white;
        }

        .signature-name {
            border-top: 1.5px solid #2c3e50;
            padding-top: 6px;
            margin-top: 10px;
            display: inline-block;
            min-width: 140px;
            font-size: 8pt;
            font-weight: 600;
            color: #2c3e50;
        }

        .signature-label {
            font-size: 7pt;
            color: #7f8c8d;
            margin-top: 3px;
        }

        .signature-placeholder {
            width: 80px;
            height: 80px;
            margin: 10px auto;
            border: 2px dashed #bdc3c7;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 7pt;
            color: #95a5a6;
            text-align: center;
            padding: 5px;
        }

        /* FOOTER */
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 10px 15px;
            border-top: 2px solid #3498db;
            background: #f8f9fa;
            font-size: 7pt;
            color: #7f8c8d;
        }

        .footer-content {
            display: table;
            width: 100%;
        }

        .footer-left {
            display: table-cell;
            width: 60%;
        }

        .footer-right {
            display: table-cell;
            width: 40%;
            text-align: right;
        }

        .footer-bold {
            font-weight: bold;
            color: #34495e;
        }

        .page-number {
            text-align: center;
            font-size: 7pt;
            color: #95a5a6;
            margin-top: 5px;
        }

        @page {
            margin: 20mm 15mm;
        }

        /* ALERT BOX */
        .alert {
            padding: 10px 12px;
            border-radius: 4px;
            margin-bottom: 10px;
            font-size: 8pt;
            line-height: 1.5;
        }

        .alert-success {
            background: #d4edda;
            border-left: 3px solid #27ae60;
            color: #155724;
        }

        .alert-danger {
            background: #f8d7da;
            border-left: 3px solid #e74c3c;
            color: #721c24;
        }

        .alert-warning {
            background: #fff3cd;
            border-left: 3px solid #f39c12;
            color: #856404;
        }

        .barcode-lines {
            height: 80px;
            background: repeating-linear-gradient(90deg, #000 0px, #000 2px, #fff 2px, #fff 4px, #000 4px, #000 6px,
                    #fff 6px, #fff 9px, #000 9px, #000 11px, #fff 11px, #fff 13px, #000 13px, #000 16px, #fff 16px, #fff 18px,
                    #000 18px, #000 20px, #fff 20px, #fff 23px, #000 23px, #000 25px, #fff 25px, #fff 28px);
            border: 2px solid #2c3e50;
            border-radius: 4px;
        }

        .verification-id {
            font-size: 7pt;
            color: #7f8c8d;
            margin-top: 5px;
        }
    </style>
</head>

<body>
    <div class="container">
        {{-- HEADER --}}
        <div class="header">
            <div class="header-content">
                <div class="header-left">
                    <div class="logo">RSUD CILINCING</div>
                    <div class="subtitle">SERVICE REQUEST REPORT</div>
                    <div class="hospital-info">
                        IT Department - Technical Support Division<br>
                        Jl. Kesehatan No. 123, Jakarta Utara 14140<br>
                        Telp: (021) 1234-5678 | Email: itsupport@rsudcilincing.go.id
                    </div>
                </div>
                <div class="header-right">
                    <div class="qr-box">
                        <div class="qr-title">Document Verification</div>
                        <div class="qr-code-container">
                            @if (isset($qrCodes['requester']) && $qrCodes['requester'])
                                <img src="data:image/png;base64,{{ $qrCodes['requester'] }}" alt="QR Code"
                                    style="width: 100px; height: 100px; display: block; margin: 0 auto;">
                            @else
                                <div class="barcode-lines"></div>
                            @endif
                        </div>
                        <div class="qr-info">
                            <div class="qr-ticket-number">{{ $ticket->ticket_number }}</div>
                            <div class="qr-date">{{ now()->format('d/m/Y H:i') }} WIB</div>
                            <div class="verification-id">
                                ID: {{ strtoupper(substr(md5($ticket->ticket_number . $ticket->created_at), 0, 8)) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- TICKET TITLE --}}
        <div class="ticket-title-section">
            <div class="ticket-number">Ticket: {{ $ticket->ticket_number }}</div>
            <div class="issue-title">{{ $ticket->issue_title }}</div>
            <div class="badges">
                @php
                    $statusColors = [
                        'Open' => 'primary',
                        'Pending' => 'warning',
                        'Approved' => 'info',
                        'Assigned' => 'secondary',
                        'In Progress' => 'primary',
                        'Resolved' => 'success',
                        'Closed' => 'dark',
                        'Rejected' => 'danger',
                    ];
                    $statusColor = $statusColors[$ticket->ticket_status] ?? 'secondary';

                    $priorityColors = [
                        'Critical' => 'danger',
                        'High' => 'warning',
                        'Medium' => 'info',
                        'Low' => 'secondary',
                    ];
                    $priorityColor = $priorityColors[$ticket->priority] ?? 'secondary';
                @endphp
                <span class="badge badge-{{ $statusColor }}">STATUS: {{ strtoupper($ticket->ticket_status) }}</span>
                <span class="badge badge-{{ $priorityColor }}">PRIORITY: {{ strtoupper($ticket->priority) }}</span>
                @if ($ticket->impact_patient_care)
                    <span class="badge badge-danger">PATIENT IMPACT</span>
                @endif
                <span class="badge badge-{{ $slaStatus['class'] }}">SLA:
                    {{ strtoupper($slaStatus['message']) }}</span>
            </div>
        </div>

        {{-- TICKET DETAILS --}}
        <div class="section">
            <div class="section-title">TICKET INFORMATION</div>
            <div class="info-grid">
                <div class="info-row">
                    <div class="info-cell">
                        <div class="info-label">Requester Name</div>
                        <div class="info-value">
                            <strong>{{ $ticket->requester_name }}</strong>
                        </div>
                    </div>
                    <div class="info-cell">
                        <div class="info-label">Contact Number</div>
                        <div class="info-value">
                            {{ $ticket->requester_phone ?? '-' }}
                        </div>
                    </div>
                </div>

                <div class="info-row">
                    <div class="info-cell">
                        <div class="info-label">Email Address</div>
                        <div class="info-value">
                            {{ $ticket->user->email ?? '-' }}
                        </div>
                    </div>
                    <div class="info-cell">
                        <div class="info-label">Hospital Unit</div>
                        <div class="info-value">
                            @if ($ticket->hospitalUnit)
                                {{ $ticket->hospitalUnit->unit_name }} ({{ $ticket->hospitalUnit->unit_code }})
                            @else
                                -
                            @endif
                        </div>
                    </div>
                </div>

                <div class="info-row">
                    <div class="info-cell">
                        <div class="info-label">Location</div>
                        <div class="info-value">
                            {{ $ticket->location ?? '-' }}
                        </div>
                    </div>
                    <div class="info-cell">
                        <div class="info-label">Problem Category</div>
                        <div class="info-value">
                            @if ($ticket->problemCategory)
                                {{ $ticket->problemCategory->category_name }}
                                @if ($ticket->problemSubCategory)
                                    <br>Sub: {{ $ticket->problemSubCategory->sub_category_name }}
                                @endif
                            @else
                                -
                            @endif
                        </div>
                    </div>
                </div>

                <div class="info-row">
                    <div class="info-cell">
                        <div class="info-label">Severity Level</div>
                        <div class="info-value">
                            <strong>{{ $ticket->severity_level }}</strong>
                        </div>
                    </div>
                    <div class="info-cell">
                        <div class="info-label">Priority Level</div>
                        <div class="info-value">
                            <strong>{{ $ticket->priority }}</strong>
                        </div>
                    </div>
                </div>

                @if ($ticket->device_affected)
                    <div class="info-row">
                        <div class="info-cell">
                            <div class="info-label">Device Affected</div>
                            <div class="info-value">
                                {{ $ticket->device_affected }}
                            </div>
                        </div>
                        <div class="info-cell">
                            <div class="info-label">IP Address</div>
                            <div class="info-value">
                                {{ $ticket->ip_address ?? '-' }}
                            </div>
                        </div>
                    </div>
                @endif

                @if ($ticket->connection_status)
                    <div class="info-row">
                        <div class="info-cell">
                            <div class="info-label">Connection Status</div>
                            <div class="info-value">
                                {{ $ticket->connection_status }}
                            </div>
                        </div>
                        <div class="info-cell">
                            <div class="info-label">SLA Deadline</div>
                            <div class="info-value">
                                {{ $ticket->sla_deadline ? \Carbon\Carbon::parse($ticket->sla_deadline)->format('d M Y, H:i') : '-' }}
                            </div>
                        </div>
                    </div>
                @endif

                <div class="info-row">
                    <div class="info-cell">
                        <div class="info-label">Occurrence Time</div>
                        <div class="info-value">
                            {{ \Carbon\Carbon::parse($ticket->occurrence_time)->format('d M Y, H:i') }} WIB
                        </div>
                    </div>
                    <div class="info-cell">
                        <div class="info-label">Ticket Created</div>
                        <div class="info-value">
                            {{ $ticket->created_at->format('d M Y, H:i') }} WIB
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- DESCRIPTION --}}
        <div class="section">
            <div class="section-title">PROBLEM DESCRIPTION</div>
            <div class="description-box">
                {{ $ticket->description ?? 'No description provided' }}
            </div>
        </div>

        @if ($ticket->expected_action)
            <div class="section">
                <div class="section-title">EXPECTED ACTION</div>
                <div class="description-box">
                    {{ $ticket->expected_action }}
                </div>
            </div>
        @endif

        {{-- VALIDATION INFO --}}
        @if ($ticket->validation_status !== 'pending')
            <div class="section">
                <div class="section-title">VALIDATION STATUS</div>

                @if ($ticket->validation_status === 'approved')
                    <div class="alert alert-success">
                        <strong>APPROVED</strong> - This ticket has been validated and approved for processing.
                    </div>
                @else
                    <div class="alert alert-danger">
                        <strong>REJECTED</strong> - This ticket has been rejected.
                    </div>
                @endif

                <div class="info-grid">
                    <div class="info-row">
                        <div class="info-cell">
                            <div class="info-label">Validated By</div>
                            <div class="info-value">{{ optional($ticket->validator)->name ?? '-' }}</div>
                        </div>
                        <div class="info-cell">
                            <div class="info-label">Validation Date</div>
                            <div class="info-value">
                                {{ $ticket->validated_at ? \Carbon\Carbon::parse($ticket->validated_at)->format('d M Y, H:i') : '-' }}
                            </div>
                        </div>
                    </div>
                    @if ($ticket->validation_notes)
                        <div class="info-row">
                            <div class="info-cell" style="width: 100%;">
                                <div class="info-label">Validation Notes</div>
                                <div class="description-box">{{ $ticket->validation_notes }}</div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        {{-- ASSIGNMENT INFO --}}
        @if ($ticket->assigned_to)
            <div class="section">
                <div class="section-title">ASSIGNMENT INFORMATION</div>
                <div class="info-grid">
                    <div class="info-row">
                        <div class="info-cell">
                            <div class="info-label">Assigned Technician</div>
                            <div class="info-value">
                                <strong>{{ optional($ticket->assignedTechnician)->name ?? '-' }}</strong>
                                @if ($ticket->assignedTechnician && $ticket->assignedTechnician->email)
                                    <br>{{ $ticket->assignedTechnician->email }}
                                @endif
                            </div>
                        </div>
                        <div class="info-cell">
                            <div class="info-label">Assigned By</div>
                            <div class="info-value">
                                {{ optional($ticket->assignedBy)->name ?? '-' }}
                            </div>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-cell">
                            <div class="info-label">Assignment Date</div>
                            <div class="info-value">
                                {{ $ticket->assigned_at ? \Carbon\Carbon::parse($ticket->assigned_at)->format('d M Y, H:i') : '-' }}
                                WIB
                            </div>
                        </div>
                        <div class="info-cell">
                            <div class="info-label">Current Status</div>
                            <div class="info-value">
                                <strong>{{ $ticket->ticket_status }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- TIMELINE --}}
        @if ($timeline->count() > 0)
            <div class="section">
                <div class="section-title">ACTIVITY TIMELINE</div>
                <div class="timeline">
                    @foreach ($timeline as $item)
                        <div class="timeline-item">
                            <div class="timeline-icon"></div>
                            <div class="timeline-title">{{ $item['title'] }}</div>
                            <div class="timeline-desc">{{ $item['description'] }}</div>
                            <div class="timeline-time">
                                {{ $item['timestamp']->format('d M Y, H:i') }} WIB
                                @if (isset($item['user']) && $item['user'])
                                    | By: {{ $item['user']->name }}
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- SIGNATURES WITH QR CODES --}}
        {{-- SIGNATURES WITH QR CODES - UPDATED VERSION --}}
        <div class="signature-section">
            <div class="signature-header">VERIFICATION AND APPROVAL</div>
            <div class="signature-grid">

                {{-- ========================================
            SIGNATURE 1: REQUESTER (SELALU ADA)
        ======================================== --}}
                <div class="signature-cell">
                    <div class="signature-title">REQUESTER</div>
                    <div class="signature-qr">
                        @if (isset($qrCodes['requester']) && $qrCodes['requester'])
                            <img src="data:image/png;base64,{{ $qrCodes['requester'] }}" alt="Requester QR"
                                style="width: 80px; height: 80px; display: block; margin: 0 auto; border: 1px solid #bdc3c7; border-radius: 4px; padding: 3px; background: white;">
                        @else
                            {{-- Fallback jika QR gagal generate --}}
                            <div class="signature-placeholder">
                                QR Code<br>Generation<br>Failed
                            </div>
                        @endif
                    </div>
                    <div class="signature-name">{{ $ticket->requester_name }}</div>
                    <div class="signature-label">{{ $ticket->created_at->format('d M Y, H:i') }} WIB</div>
                </div>

                {{-- ========================================
            SIGNATURE 2: VALIDATOR/ADMIN
        ======================================== --}}
                <div class="signature-cell">
                    <div class="signature-title">VALIDATOR</div>
                    <div class="signature-qr">
                        @if (isset($qrCodes['validator']) && $qrCodes['validator'])
                            {{-- QR Code ada (baik sudah validated atau placeholder) --}}
                            <img src="data:image/png;base64,{{ $qrCodes['validator'] }}" alt="Validator QR"
                                style="width: 80px; height: 80px; display: block; margin: 0 auto; border: 1px solid #bdc3c7; border-radius: 4px; padding: 3px; background: white;">
                        @else
                            {{-- Fallback jika QR gagal generate --}}
                            <div class="signature-placeholder">
                                QR Code<br>Generation<br>Failed
                            </div>
                        @endif
                    </div>
                    <div class="signature-name">
                        @if ($ticket->validator)
                            {{ $ticket->validator->name }}
                        @else
                            ________________
                        @endif
                    </div>
                    <div class="signature-label">
                        @if ($ticket->validated_at)
                            {{ $ticket->validated_at->format('d M Y, H:i') }} WIB
                        @else
                            <span style="color: #95a5a6; font-style: italic;">Menunggu Validasi</span>
                        @endif
                    </div>
                </div>

                {{-- ========================================
            SIGNATURE 3: TECHNICIAN
        ======================================== --}}
                <div class="signature-cell">
                    <div class="signature-title">TECHNICIAN</div>
                    <div class="signature-qr">
                        @if (isset($qrCodes['technician']) && $qrCodes['technician'])
                            {{-- QR Code ada (baik sudah resolved atau placeholder) --}}
                            <img src="data:image/png;base64,{{ $qrCodes['technician'] }}" alt="Technician QR"
                                style="width: 80px; height: 80px; display: block; margin: 0 auto; border: 1px solid #bdc3c7; border-radius: 4px; padding: 3px; background: white;">
                        @else
                            {{-- Fallback jika QR gagal generate --}}
                            <div class="signature-placeholder">
                                QR Code<br>Generation<br>Failed
                            </div>
                        @endif
                    </div>
                    <div class="signature-name">
                        @if ($ticket->assigned_to)
                            {{ optional($ticket->assignedTechnician)->name }}
                        @else
                            ________________
                        @endif
                    </div>
                    <div class="signature-label">
                        @if ($ticket->closed_at)
                            {{ $ticket->closed_at->format('d M Y, H:i') }} WIB
                        @elseif ($ticket->ticket_status === 'Resolved')
                            {{ $ticket->updated_at->format('d M Y, H:i') }} WIB
                        @elseif (in_array($ticket->ticket_status, ['Assigned', 'In Progress']))
                            <span style="color: #f39c12; font-style: italic;">Sedang Ditangani</span>
                        @else
                            <span style="color: #95a5a6; font-style: italic;">Menunggu Assignment</span>
                        @endif
                    </div>
                </div>

            </div>

            {{-- Optional: Verification Notes --}}
            <div
                style="margin-top: 20px; padding: 10px; background: #f8f9fa; border-radius: 4px; font-size: 7pt; color: #7f8c8d; text-align: center;">
                <strong>Verification Note:</strong> QR codes above contain digital signatures and can be scanned to
                verify document authenticity.
                Each QR code is generated based on ticket information and timestamp.
            </div>
        </div>
    </div>

    {{-- FOOTER --}}
    <div class="footer">
        <div class="footer-content">
            <div class="footer-left">
                <span class="footer-bold">Document ID:</span>
                {{ strtoupper(substr(md5($ticket->ticket_number . $ticket->created_at), 0, 10)) }}
                | <span class="footer-bold">Generated:</span> {{ now()->format('d/m/Y H:i:s') }} WIB
            </div>
            <div class="footer-right">
                <span class="footer-bold">Confidential</span> - For Internal Use Only
            </div>
        </div>
        <div class="page-number">
            Page 1 of 1
        </div>
    </div>
</body>

</html>
