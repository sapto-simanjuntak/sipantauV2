@extends('layouts.default')

@push('after-style')
    <style>
        .ticket-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            border-radius: 10px 10px 0 0;
            margin-bottom: 0;
        }

        .ticket-info-card {
            border: none;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .info-label {
            font-weight: 600;
            color: #6c757d;
            font-size: 0.875rem;
            margin-bottom: 0.25rem;
        }

        .info-value {
            font-size: 1rem;
            color: #212529;
            margin-bottom: 1rem;
        }

        .timeline {
            position: relative;
            padding-left: 30px;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 8px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #dee2e6;
        }

        .timeline-item {
            position: relative;
            padding-bottom: 1.5rem;
        }

        .timeline-item:last-child {
            padding-bottom: 0;
        }

        .timeline-icon {
            position: absolute;
            left: -30px;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            color: white;
            z-index: 1;
        }

        .attachment-item {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 1rem;
            transition: all 0.3s;
        }

        .attachment-item:hover {
            border-color: #667eea;
            background-color: #f8f9fa;
        }

        .action-button {
            min-width: 120px;
        }

        .badge-lg {
            font-size: 1rem;
            padding: 0.5rem 1rem;
        }

        .sla-pulse {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.6;
            }
        }
    </style>
@endpush

@section('content')
    <div class="page-wrapper">
        <div class="page-content">
            {{-- BREADCRUMB --}}
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                <div class="breadcrumb-title pe-3">Service Request</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="bx bx-home-alt"></i></a>
                            </li>
                            <li class="breadcrumb-item"><a href="{{ route('service.index') }}">Daftar Tiket</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $ticket->ticket_number }}</li>
                        </ol>
                    </nav>
                </div>
                <div class="ms-auto">
                    <a href="{{ route('service.print', $ticket->ticket_number) }}" class="btn btn-primary btn-sm me-2"
                        target="_blank">
                        <i class="bx bx-printer me-1"></i>Print Report
                    </a>
                    <a href="{{ route('service.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bx bx-arrow-back me-1"></i>Kembali
                    </a>
                </div>
            </div>

            {{-- TICKET HEADER --}}
            <div class="card ticket-info-card">
                <div class="ticket-header">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h3 class="mb-2">{{ $ticket->ticket_number }}</h3>
                            <h5 class="mb-3">{{ $ticket->issue_title }}</h5>
                            <div class="d-flex gap-2 flex-wrap">
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
                                @endphp
                                <span class="badge bg-{{ $statusColor }} badge-lg">{{ $ticket->ticket_status }}</span>

                                @php
                                    $priorityColors = [
                                        'Critical' => 'danger',
                                        'High' => 'warning',
                                        'Medium' => 'info',
                                        'Low' => 'secondary',
                                    ];
                                    $priorityColor = $priorityColors[$ticket->priority] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $priorityColor }} badge-lg">{{ $ticket->priority }}</span>

                                @if ($ticket->impact_patient_care)
                                    <span class="badge bg-danger badge-lg sla-pulse">
                                        <i class="bx bx-error-circle me-1"></i>Patient Impact
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4 text-md-end mt-3 mt-md-0">
                            <div class="text-white-50 mb-2">SLA Status</div>
                            <span
                                class="badge bg-{{ $slaStatus['class'] }} badge-lg {{ $slaStatus['status'] === 'overdue' ? 'sla-pulse' : '' }}">
                                <i class="bx bx-time-five me-1"></i>{{ $slaStatus['message'] }}
                            </span>
                            @if ($ticket->sla_deadline)
                                <div class="text-white-50 mt-2 small">
                                    Deadline: {{ \Carbon\Carbon::parse($ticket->sla_deadline)->format('d M Y, H:i') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- ACTION BUTTONS --}}
                {{-- <div class="card-body border-bottom bg-light">
                    <div class="d-flex gap-2 flex-wrap justify-content-center">
                        @role('superadmin|admin')
                            @if ($ticket->validation_status === 'pending' && in_array($ticket->ticket_status, ['Open', 'Pending']))
                                <button type="button" class="btn btn-success action-button" id="btn-approve">
                                    <i class="bx bx-check-circle me-1"></i>Approve
                                </button>
                                <button type="button" class="btn btn-danger action-button" id="btn-reject">
                                    <i class="bx bx-x-circle me-1"></i>Reject
                                </button>
                            @endif

                            @if (in_array($ticket->ticket_status, ['Open', 'Pending', 'Approved']) && !$ticket->assigned_to)
                                <button type="button" class="btn btn-primary action-button" id="btn-assign">
                                    <i class="bx bx-user-check me-1"></i>Assign
                                </button>
                            @endif

                            @if ($ticket->assigned_to && in_array($ticket->ticket_status, ['Assigned', 'In Progress']))
                                <button type="button" class="btn btn-info action-button" id="btn-reassign">
                                    <i class="bx bx-transfer me-1"></i>Re-assign
                                </button>
                            @endif

                            @if (!in_array($ticket->ticket_status, ['Closed', 'Rejected']))
                                <button type="button" class="btn btn-warning action-button" id="btn-update-status">
                                    <i class="bx bx-edit me-1"></i>Update Status
                                </button>
                            @endif

                            @if (in_array($ticket->ticket_status, ['Resolved']))
                                <button type="button" class="btn btn-dark action-button" id="btn-close">
                                    <i class="bx bx-check-double me-1"></i>Close Ticket
                                </button>
                            @endif
                        @endrole

                        @if (!in_array($ticket->ticket_status, ['Closed', 'Rejected']))
                            <a href="{{ route('service.edit', $ticket->ticket_number) }}"
                                class="btn btn-secondary action-button">
                                <i class="bx bx-edit-alt me-1"></i>Edit
                            </a>
                        @endif
                    </div>
                </div> --}}
                <div class="card-body border-bottom bg-light">
                    <div class="d-flex gap-2 flex-wrap justify-content-center">

                        {{-- ADMIN/SUPERADMIN BUTTONS --}}
                        @role('superadmin|admin')
                            @if ($ticket->validation_status === 'pending' && in_array($ticket->ticket_status, ['Open', 'Pending']))
                                <button type="button" class="btn btn-success action-button" id="btn-approve">
                                    <i class="bx bx-check-circle me-1"></i>Approve
                                </button>
                                <button type="button" class="btn btn-danger action-button" id="btn-reject">
                                    <i class="bx bx-x-circle me-1"></i>Reject
                                </button>
                            @endif

                            @if (in_array($ticket->ticket_status, ['Open', 'Pending', 'Approved']) && !$ticket->assigned_to)
                                <button type="button" class="btn btn-primary action-button" id="btn-assign">
                                    <i class="bx bx-user-check me-1"></i>Assign
                                </button>
                            @endif

                            @if ($ticket->assigned_to && in_array($ticket->ticket_status, ['Assigned', 'In Progress']))
                                <button type="button" class="btn btn-info action-button" id="btn-reassign">
                                    <i class="bx bx-transfer me-1"></i>Re-assign
                                </button>
                            @endif

                            @if (!in_array($ticket->ticket_status, ['Closed', 'Rejected']))
                                <button type="button" class="btn btn-warning action-button" id="btn-update-status">
                                    <i class="bx bx-edit me-1"></i>Update Status
                                </button>
                            @endif

                            @if (in_array($ticket->ticket_status, ['Resolved']))
                                <button type="button" class="btn btn-dark action-button" id="btn-close">
                                    <i class="bx bx-check-double me-1"></i>Close Ticket
                                </button>
                            @endif
                        @endrole

                        {{-- TEKNISI BUTTONS --}}
                        @role('teknisi')
                            @if ($ticket->assigned_to === auth()->id() && !in_array($ticket->ticket_status, ['Closed', 'Rejected']))
                                <button type="button" class="btn btn-warning action-button" id="btn-update-status">
                                    <i class="bx bx-edit me-1"></i>Update Status
                                </button>
                            @endif
                        @endrole

                        {{-- EDIT BUTTON --}}
                        @role('superadmin|admin|teknisi')
                            {{-- Admin/Teknisi bisa edit kecuali closed/rejected --}}
                            @if (!in_array($ticket->ticket_status, ['Closed', 'Rejected']))
                                <a href="{{ route('service.edit', $ticket->ticket_number) }}"
                                    class="btn btn-secondary action-button">
                                    <i class="bx bx-edit-alt me-1"></i>Edit
                                </a>
                            @endif
                        @else
                            {{-- User biasa cuma bisa edit kalau validation_status masih pending --}}
                            @if ($ticket->validation_status === 'pending')
                                <a href="{{ route('service.edit', $ticket->ticket_number) }}"
                                    class="btn btn-secondary action-button">
                                    <i class="bx bx-edit-alt me-1"></i>Edit
                                </a>
                            @endif
                        @endrole
                    </div>
                </div>
            </div>

            <div class="row">
                {{-- LEFT COLUMN --}}
                <div class="col-lg-8">
                    {{-- TICKET DETAILS --}}
                    <div class="card ticket-info-card mb-3">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="bx bx-info-circle me-2"></i>Ticket Details</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-label">Requester</div>
                                    <div class="info-value">
                                        <strong>{{ $ticket->requester_name }}</strong>
                                        @if ($ticket->requester_phone)
                                            <br><small class="text-muted"><i
                                                    class="bx bx-phone me-1"></i>{{ $ticket->requester_phone }}</small>
                                        @endif
                                        @if ($ticket->user)
                                            <br><small class="text-muted"><i
                                                    class="bx bx-envelope me-1"></i>{{ $ticket->user->email }}</small>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="info-label">Unit / Location</div>
                                    <div class="info-value">
                                        @if ($ticket->hospitalUnit)
                                            <span class="badge bg-secondary">{{ $ticket->hospitalUnit->unit_code }}</span>
                                            <strong class="ms-2">{{ $ticket->hospitalUnit->unit_name }}</strong>
                                        @endif
                                        @if ($ticket->location)
                                            <br><small class="text-muted"><i
                                                    class="bx bx-map me-1"></i>{{ $ticket->location }}</small>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="info-label">Category</div>
                                    <div class="info-value">
                                        @if ($ticket->problemCategory)
                                            @php
                                                $categoryBadges = [
                                                    'HW' => 'primary',
                                                    'SW' => 'info',
                                                    'NET' => 'warning',
                                                    'PABX' => 'secondary',
                                                    'CCTV' => 'dark',
                                                    'AC' => 'success',
                                                    'DEV' => 'danger',
                                                    'EMAIL' => 'primary',
                                                ];
                                                $badgeClass =
                                                    $categoryBadges[$ticket->problemCategory->category_code] ??
                                                    'secondary';
                                            @endphp
                                            <span
                                                class="badge bg-{{ $badgeClass }}">{{ $ticket->problemCategory->category_name }}</span>

                                            @if ($ticket->problemSubCategory)
                                                <br><small
                                                    class="text-muted">{{ $ticket->problemSubCategory->sub_category_name }}</small>
                                            @endif
                                        @endif
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="info-label">Severity Level</div>
                                    <div class="info-value">
                                        @php
                                            $severityColors = [
                                                'Kritis' => 'danger',
                                                'Tinggi' => 'warning',
                                                'Sedang' => 'info',
                                                'Rendah' => 'secondary',
                                            ];
                                            $severityColor = $severityColors[$ticket->severity_level] ?? 'secondary';
                                        @endphp
                                        <span class="badge bg-{{ $severityColor }}">{{ $ticket->severity_level }}</span>
                                    </div>
                                </div>

                                @if ($ticket->device_affected)
                                    <div class="col-md-6">
                                        <div class="info-label">Device Affected</div>
                                        <div class="info-value">
                                            <i class="bx bx-laptop me-1"></i>{{ $ticket->device_affected }}
                                            @if ($ticket->ip_address)
                                                <br><small class="text-muted"><i
                                                        class="bx bx-network-chart me-1"></i>{{ $ticket->ip_address }}</small>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                @if ($ticket->connection_status)
                                    <div class="col-md-6">
                                        <div class="info-label">Connection Status</div>
                                        <div class="info-value">
                                            {{ $ticket->connection_status }}
                                        </div>
                                    </div>
                                @endif

                                <div class="col-md-6">
                                    <div class="info-label">Occurrence Time</div>
                                    <div class="info-value">
                                        {{ \Carbon\Carbon::parse($ticket->occurrence_time)->format('d M Y, H:i') }}
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="info-label">Created At</div>
                                    <div class="info-value">
                                        {{ $ticket->created_at->format('d M Y, H:i') }}
                                        <small class="text-muted">({{ $ticket->created_at->diffForHumans() }})</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- DESCRIPTION --}}
                    <div class="card ticket-info-card mb-3">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="bx bx-message-detail me-2"></i>Description</h5>
                        </div>
                        <div class="card-body">
                            <p class="mb-0">{{ $ticket->description ?? 'No description provided' }}</p>
                        </div>
                    </div>

                    @if ($ticket->expected_action)
                        <div class="card ticket-info-card mb-3">
                            <div class="card-header bg-light">
                                <h5 class="mb-0"><i class="bx bx-target-lock me-2"></i>Expected Action</h5>
                            </div>
                            <div class="card-body">
                                <p class="mb-0">{{ $ticket->expected_action }}</p>
                            </div>
                        </div>
                    @endif

                    {{-- ATTACHMENTS --}}
                    @if ($ticket->file_path)
                        <div class="card ticket-info-card mb-3">
                            <div class="card-header bg-light">
                                <h5 class="mb-0"><i class="bx bx-paperclip me-2"></i>Attachments</h5>
                            </div>
                            <div class="card-body">
                                <div class="attachment-item">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <i class="bx bx-file font-24 me-2"></i>
                                            <span>{{ basename($ticket->file_path) }}</span>
                                        </div>
                                        <a href="{{ Storage::url($ticket->file_path) }}" target="_blank"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="bx bx-download me-1"></i>Download
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- VALIDATION INFO --}}
                    @if ($ticket->validation_status !== 'pending')
                        <div class="card ticket-info-card mb-3">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">
                                    <i
                                        class="bx {{ $ticket->validation_status === 'approved' ? 'bx-check-circle' : 'bx-x-circle' }} me-2"></i>
                                    Validation {{ ucfirst($ticket->validation_status) }}
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="info-label">Validated By</div>
                                        <div class="info-value">
                                            {{ optional($ticket->validator)->name ?? '-' }}
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-label">Validated At</div>
                                        <div class="info-value">
                                            {{ $ticket->validated_at ? \Carbon\Carbon::parse($ticket->validated_at)->format('d M Y, H:i') : '-' }}
                                        </div>
                                    </div>
                                    @if ($ticket->validation_notes)
                                        <div class="col-12">
                                            <div class="info-label">Notes</div>
                                            <div class="info-value">
                                                <div
                                                    class="alert alert-{{ $ticket->validation_status === 'approved' ? 'success' : 'danger' }} mb-0">
                                                    {{ $ticket->validation_notes }}
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- ASSIGNMENT INFO --}}
                    @if ($ticket->assigned_to)
                        <div class="card ticket-info-card mb-3">
                            <div class="card-header bg-light">
                                <h5 class="mb-0"><i class="bx bx-user-check me-2"></i>Assignment Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="info-label">Assigned To</div>
                                        <div class="info-value">
                                            <strong>{{ optional($ticket->assignedTechnician)->name ?? '-' }}</strong>
                                            @if ($ticket->assignedTechnician)
                                                <br><small
                                                    class="text-muted">{{ $ticket->assignedTechnician->email }}</small>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-label">Assigned At</div>
                                        <div class="info-value">
                                            {{ $ticket->assigned_at ? \Carbon\Carbon::parse($ticket->assigned_at)->format('d M Y, H:i') : '-' }}
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-label">Assigned By</div>
                                        <div class="info-value">
                                            {{ optional($ticket->assignedBy)->name ?? '-' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- RIGHT COLUMN --}}
                <div class="col-lg-4">
                    {{-- TIMELINE --}}
                    <div class="card ticket-info-card">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="bx bx-time-five me-2"></i>Activity Timeline</h5>
                        </div>
                        <div class="card-body">
                            <div class="timeline">
                                @forelse($timeline as $item)
                                    <div class="timeline-item">
                                        <div class="timeline-icon bg-{{ $item['color'] }}">
                                            <i class="bx {{ $item['icon'] }}"></i>
                                        </div>
                                        <div>
                                            <strong>{{ $item['title'] }}</strong>
                                            <p class="mb-1 small text-muted">{{ $item['description'] }}</p>
                                            <small class="text-muted">
                                                <i
                                                    class="bx bx-time me-1"></i>{{ $item['timestamp']->format('d M Y, H:i') }}
                                                @if (isset($item['user']) && $item['user'])
                                                    <br><i class="bx bx-user me-1"></i>{{ $item['user']->name ?? '-' }}
                                                @endif
                                            </small>
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

    {{-- MODALS --}}
    @include('pages.modul.service-request.modals.approve')
    @include('pages.modul.service-request.modals.reject')
    @include('pages.modul.service-request.modals.assign')
    @include('pages.modul.service-request.modals.update-status')
    @include('pages.modul.service-request.modals.close')
@endsection

@push('after-js')
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
                            '<i class="bx bx-loader-alt bx-spin me-1"></i>Processing...');
                    },
                    success: function(response) {
                        $('#modal-approve').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message,
                            confirmButtonColor: '#0d6efd'
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: xhr.responseJSON?.message || 'Something went wrong'
                        });
                    },
                    complete: function() {
                        $('#btn-submit-approve').prop('disabled', false).html(
                            '<i class="bx bx-check-circle me-1"></i>Approve');
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
                            '<i class="bx bx-loader-alt bx-spin me-1"></i>Processing...');
                    },
                    success: function(response) {
                        $('#modal-reject').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message,
                            confirmButtonColor: '#0d6efd'
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: xhr.responseJSON?.message || 'Something went wrong'
                        });
                    },
                    complete: function() {
                        $('#btn-submit-reject').prop('disabled', false).html(
                            '<i class="bx bx-x-circle me-1"></i>Reject');
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
                            '<i class="bx bx-loader-alt bx-spin me-1"></i>Processing...');
                    },
                    success: function(response) {
                        $('#modal-assign').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message,
                            confirmButtonColor: '#0d6efd'
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: xhr.responseJSON?.message || 'Something went wrong'
                        });
                    },
                    complete: function() {
                        $('#btn-submit-assign').prop('disabled', false).html(
                            '<i class="bx bx-user-check me-1"></i>Assign');
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
                            '<i class="bx bx-loader-alt bx-spin me-1"></i>Processing...');
                    },
                    success: function(response) {
                        $('#modal-update-status').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message,
                            confirmButtonColor: '#0d6efd'
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: xhr.responseJSON?.message || 'Something went wrong'
                        });
                    },
                    complete: function() {
                        $('#btn-submit-status').prop('disabled', false).html(
                            '<i class="bx bx-edit me-1"></i>Update');
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
                            '<i class="bx bx-loader-alt bx-spin me-1"></i>Processing...');
                    },
                    success: function(response) {
                        $('#modal-close').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Ticket closed successfully',
                            confirmButtonColor: '#0d6efd'
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: xhr.responseJSON?.message || 'Something went wrong'
                        });
                    },
                    complete: function() {
                        $('#btn-submit-close').prop('disabled', false).html(
                            '<i class="bx bx-check-double me-1"></i>Close');
                    }
                });
            });
        });
    </script>
@endpush
