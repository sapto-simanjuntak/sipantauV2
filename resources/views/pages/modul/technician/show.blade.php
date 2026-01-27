@extends('layouts.default')

@push('after-style')
    <style>
        .detail-card {
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .detail-label {
            font-weight: 600;
            color: #6c757d;
            font-size: 0.875rem;
            margin-bottom: 5px;
        }

        .detail-value {
            font-size: 1rem;
            color: #212529;
            margin-bottom: 20px;
        }

        .status-badge {
            font-size: 0.9rem;
            padding: 8px 15px;
        }

        .attachment-preview {
            max-width: 200px;
            border-radius: 8px;
            cursor: pointer;
            transition: transform 0.2s;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .attachment-preview:hover {
            transform: scale(1.05);
        }

        .info-icon {
            color: #0d6efd;
            margin-right: 8px;
        }

        .header-section {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            color: white;
            padding: 30px;
            border-radius: 10px 10px 0 0;
        }

        .progress-card {
            border-left: 4px solid #4e73df;
        }

        .timeline {
            position: relative;
            padding-left: 30px;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #dee2e6;
        }

        .timeline-item {
            position: relative;
            padding-bottom: 20px;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -35px;
            top: 5px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #4e73df;
            border: 2px solid #fff;
            box-shadow: 0 0 0 3px #dee2e6;
        }
    </style>
@endpush

@section('content')
    <div class="page-wrapper">
        <div class="page-content">
            <!-- Breadcrumb -->
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                <div class="breadcrumb-title pe-3">Detail Tiket</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('technician.dashboard') }}"><i class="bx bx-home-alt"></i></a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $serviceRequest->ticket_number }}</li>
                        </ol>
                    </nav>
                </div>
                <div class="ms-auto">
                    <a href="{{ route('technician.dashboard') }}" class="btn btn-secondary btn-sm">
                        <i class="bx bx-arrow-back"></i> Kembali
                    </a>
                </div>
            </div>

            <div class="row">
                <!-- Main Content -->
                <div class="col-lg-8">
                    <!-- Header Card -->
                    <div class="card detail-card mb-3">
                        <div class="header-section">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h4 class="mb-2 text-white">{{ $serviceRequest->issue_title }}</h4>
                                    <p class="mb-0 opacity-75">
                                        <i class="bx bx-calendar"></i>
                                        Assigned
                                        {{ \Carbon\Carbon::parse($serviceRequest->assigned_at)->locale('id')->diffForHumans() }}
                                        <span class="mx-2">•</span>
                                        <strong>{{ $serviceRequest->ticket_number }}</strong>
                                    </p>
                                </div>
                                <div>
                                    @php
                                        $badges = [
                                            'Rendah' => 'success',
                                            'Sedang' => 'warning',
                                            'Tinggi' => 'danger',
                                            'Mendesak' => 'dark',
                                        ];
                                        $badgeClass = $badges[$serviceRequest->severity_level] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $badgeClass }} status-badge">
                                        <i class="bx bx-error-circle"></i> {{ $serviceRequest->severity_level }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <!-- Status & Kategori -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="detail-label">
                                        <i class="bx bx-category info-icon"></i>Kategori Masalah
                                    </div>
                                    <div class="detail-value">
                                        <span class="badge bg-primary">{{ $category->problem_name ?? '-' }}</span>
                                        @if ($subCategory)
                                            <i class="bx bx-chevron-right"></i>
                                            <span class="badge bg-info">{{ $subCategory->sub_category_name }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="detail-label">
                                        <i class="bx bx-flag info-icon"></i>Status Pekerjaan
                                    </div>
                                    <div class="detail-value">
                                        @php
                                            $statusBadges = [
                                                'Assigned' => [
                                                    'class' => 'info',
                                                    'text' => 'Assigned',
                                                    'icon' => 'bx-task',
                                                ],
                                                'In Progress' => [
                                                    'class' => 'warning',
                                                    'text' => 'In Progress',
                                                    'icon' => 'bx-time',
                                                ],
                                                'Resolved' => [
                                                    'class' => 'success',
                                                    'text' => 'Resolved',
                                                    'icon' => 'bx-check-circle',
                                                ],
                                                'Completed' => [
                                                    'class' => 'secondary',
                                                    'text' => 'Completed',
                                                    'icon' => 'bx-check-double',
                                                ],
                                            ];
                                            $currentStatus =
                                                $statusBadges[$serviceRequest->ticket_status] ??
                                                $statusBadges['Assigned'];
                                        @endphp
                                        <span class="badge bg-{{ $currentStatus['class'] }}">
                                            <i class="bx {{ $currentStatus['icon'] }}"></i> {{ $currentStatus['text'] }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <!-- Deskripsi -->
                            <div class="mb-4">
                                <div class="detail-label">
                                    <i class="bx bx-file-blank info-icon"></i>Deskripsi Masalah
                                </div>
                                <div class="detail-value">
                                    <p class="mb-0">{{ $serviceRequest->description }}</p>
                                </div>
                            </div>

                            <!-- Detail Informasi -->
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="detail-label">
                                        <i class="bx bx-devices info-icon"></i>Perangkat Terdampak
                                    </div>
                                    <div class="detail-value">{{ $serviceRequest->device_affected }}</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="detail-label">
                                        <i class="bx bx-map info-icon"></i>Lokasi Fisik
                                    </div>
                                    <div class="detail-value">{{ $serviceRequest->location }}</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="detail-label">
                                        <i class="bx bx-time info-icon"></i>Waktu Kejadian
                                    </div>
                                    <div class="detail-value">
                                        {{ \Carbon\Carbon::parse($serviceRequest->occurrence_time)->format('d F Y, H:i') }}
                                        WIB
                                    </div>
                                </div>
                                @if ($serviceRequest->connection_status)
                                    <div class="col-md-6 mb-3">
                                        <div class="detail-label">
                                            <i class="bx bx-wifi info-icon"></i>Status Koneksi
                                        </div>
                                        <div class="detail-value">{{ $serviceRequest->connection_status }}</div>
                                    </div>
                                @endif
                            </div>

                            <hr>

                            <!-- Tindakan yang Diharapkan -->
                            <div class="mb-3">
                                <div class="detail-label">
                                    <i class="bx bx-task info-icon"></i>Tindakan yang Diharapkan
                                </div>
                                <div class="detail-value">{{ $serviceRequest->expected_action }}</div>
                            </div>

                            <!-- Lampiran dari User -->
                            @if ($serviceRequest->file_path)
                                <div class="mb-3">
                                    <div class="detail-label">
                                        <i class="bx bx-paperclip info-icon"></i>Lampiran dari Pelapor
                                    </div>
                                    <div class="detail-value">
                                        @php
                                            $extension = pathinfo($serviceRequest->file_path, PATHINFO_EXTENSION);
                                            $isImage = in_array(strtolower($extension), [
                                                'jpg',
                                                'jpeg',
                                                'png',
                                                'gif',
                                                'webp',
                                            ]);
                                        @endphp

                                        @if ($isImage)
                                            <img src="{{ asset('storage/' . $serviceRequest->file_path) }}"
                                                class="attachment-preview img-thumbnail" alt="Lampiran"
                                                data-bs-toggle="modal" data-bs-target="#imageModal">
                                            <p class="text-muted small mt-2">Klik untuk memperbesar</p>
                                        @else
                                            <a href="{{ asset('storage/' . $serviceRequest->file_path) }}" target="_blank"
                                                class="btn btn-outline-primary btn-sm">
                                                <i class="bx bx-download"></i> Download Lampiran
                                                ({{ strtoupper($extension) }})
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Progress Card -->
                    @if ($technicianResponse && ($technicianResponse->diagnosis || $technicianResponse->action_taken))
                        <div class="card detail-card progress-card mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="bx bx-wrench"></i> Progress Perbaikan</h6>
                            </div>
                            <div class="card-body">
                                @if ($technicianResponse->diagnosis)
                                    <div class="mb-3">
                                        <div class="detail-label">
                                            <i class="bx bx-search-alt info-icon"></i>Diagnosis
                                        </div>
                                        <div class="detail-value">{{ $technicianResponse->diagnosis }}</div>
                                    </div>
                                @endif

                                @if ($technicianResponse->action_taken)
                                    <div class="mb-3">
                                        <div class="detail-label">
                                            <i class="bx bx-wrench info-icon"></i>Tindakan yang Dilakukan
                                        </div>
                                        <div class="detail-value">{{ $technicianResponse->action_taken }}</div>
                                    </div>
                                @endif

                                @if ($technicianResponse->technician_notes)
                                    <div class="mb-3">
                                        <div class="detail-label">
                                            <i class="bx bx-note info-icon"></i>Catatan Teknisi
                                        </div>
                                        <div class="detail-value">{{ $technicianResponse->technician_notes }}</div>
                                    </div>
                                @endif

                                @if ($technicianResponse->attachment_path)
                                    <div class="mb-3">
                                        <div class="detail-label">
                                            <i class="bx bx-image info-icon"></i>Foto Hasil Perbaikan
                                        </div>
                                        <div class="detail-value">
                                            @php
                                                $techExtension = pathinfo(
                                                    $technicianResponse->attachment_path,
                                                    PATHINFO_EXTENSION,
                                                );
                                                $techIsImage = in_array(strtolower($techExtension), [
                                                    'jpg',
                                                    'jpeg',
                                                    'png',
                                                    'gif',
                                                    'webp',
                                                ]);
                                            @endphp

                                            @if ($techIsImage)
                                                <img src="{{ asset('storage/' . $technicianResponse->attachment_path) }}"
                                                    class="attachment-preview img-thumbnail" alt="Hasil Perbaikan"
                                                    data-bs-toggle="modal" data-bs-target="#techImageModal">
                                                <p class="text-muted small mt-2">Klik untuk memperbesar</p>
                                            @else
                                                <a href="{{ asset('storage/' . $technicianResponse->attachment_path) }}"
                                                    target="_blank" class="btn btn-outline-primary btn-sm">
                                                    <i class="bx bx-download"></i> Download
                                                    ({{ strtoupper($techExtension) }})
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="card detail-card progress-card mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="bx bx-wrench"></i> Progress Perbaikan</h6>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info mb-0">
                                    <i class="bx bx-info-circle"></i>
                                    Belum ada progress perbaikan. Klik <strong>"Update Progress"</strong> untuk menambahkan
                                    diagnosis dan tindakan.
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Timeline -->
                    <div class="card detail-card">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="bx bx-history"></i> Timeline</h6>
                        </div>
                        <div class="card-body">
                            <div class="timeline">
                                <div class="timeline-item">
                                    <small class="text-muted">
                                        {{ \Carbon\Carbon::parse($serviceRequest->created_at)->format('d M Y, H:i') }}
                                    </small>
                                    <p class="mb-0">
                                        <strong>{{ $user->first_name ?? 'User' }}</strong> membuat tiket
                                    </p>
                                </div>

                                @if ($serviceRequest->assigned_at)
                                    <div class="timeline-item">
                                        <small class="text-muted">
                                            {{ \Carbon\Carbon::parse($serviceRequest->assigned_at)->format('d M Y, H:i') }}
                                        </small>
                                        <p class="mb-0">
                                            Tiket di-assign ke Anda
                                        </p>
                                    </div>
                                @endif

                                @if ($technicianResponse && $technicianResponse->started_at)
                                    <div class="timeline-item">
                                        <small class="text-muted">
                                            {{ \Carbon\Carbon::parse($technicianResponse->started_at)->format('d M Y, H:i') }}
                                        </small>
                                        <p class="mb-0">
                                            Mulai dikerjakan
                                        </p>
                                    </div>
                                @endif

                                @if ($technicianResponse && $technicianResponse->resolved_at)
                                    <div class="timeline-item">
                                        <small class="text-muted">
                                            {{ \Carbon\Carbon::parse($technicianResponse->resolved_at)->format('d M Y, H:i') }}
                                        </small>
                                        <p class="mb-0">
                                            <strong class="text-success">Perbaikan selesai</strong>
                                        </p>
                                    </div>
                                @endif

                                @if ($serviceRequest->ticket_status === 'Completed')
                                    <div class="timeline-item">
                                        <small class="text-muted">
                                            {{ $technicianResponse && $technicianResponse->completion_time
                                                ? \Carbon\Carbon::parse($technicianResponse->completion_time)->format('d M Y, H:i')
                                                : \Carbon\Carbon::parse($serviceRequest->updated_at)->format('d M Y, H:i') }}
                                        </small>
                                        <p class="mb-0">
                                            <strong class="text-secondary">Tiket ditutup</strong>
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- Info Pelapor -->
                    <div class="card detail-card mb-3">
                        <div class="card-header bg-gradient-moonlit text-white">
                            <h6 class="mb-0"><i class="bx bx-user"></i> Pelapor</h6>
                        </div>
                        <div class="card-body text-center">
                            <div class="avatar-circle bg-primary text-white mx-auto mb-3"
                                style="width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 32px;">
                                {{ strtoupper(substr($user->first_name ?? 'U', 0, 1)) }}
                            </div>
                            <h5 class="mb-1">{{ $user->first_name ?? 'User' }} {{ $user->last_name ?? '' }}</h5>
                            <p class="text-muted small mb-3">{{ $user->email ?? '-' }}</p>

                            <hr>

                            <div class="text-start">
                                <div class="mb-2">
                                    <small class="text-muted">Tanggal Lapor</small>
                                    <p class="mb-0">
                                        {{ \Carbon\Carbon::parse($serviceRequest->created_at)->format('d M Y, H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions - FIXED VERSION -->
                    <div class="card detail-card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="bx bx-cog"></i> Aksi</h6>
                        </div>
                        <div class="card-body">
                            @if ($serviceRequest->ticket_status === 'Resolved' || $serviceRequest->ticket_status === 'Completed')
                                {{-- SELESAI / DITUTUP --}}
                                <div class="alert alert-success mb-0">
                                    <i class="bx bx-check-circle"></i>
                                    <strong>Pekerjaan Selesai</strong>
                                    <p class="mb-0 small mt-2">
                                        Tiket ini sudah
                                        {{ $serviceRequest->ticket_status === 'Resolved' ? 'diselesaikan' : 'ditutup' }}.
                                        @if ($serviceRequest->ticket_status === 'Completed')
                                            <br><small class="text-muted">Tiket telah ditutup oleh admin.</small>
                                        @endif
                                    </p>
                                </div>
                            @elseif ($serviceRequest->ticket_status === 'Assigned')
                                {{-- BELUM MULAI - BUTTON "MULAI MENGERJAKAN" --}}
                                <button class="btn btn-warning w-100 mb-2" id="startWorkBtn">
                                    <i class="bx bx-play-circle"></i> Mulai Mengerjakan
                                </button>
                                <small class="text-muted d-block text-center">
                                    <i class="bx bx-info-circle"></i> Klik untuk memulai pekerjaan
                                </small>
                            @else
                                {{-- IN PROGRESS - BUTTON UPDATE & TANDAI SELESAI --}}
                                <button class="btn btn-primary w-100 mb-2" id="updateStatusBtn">
                                    <i class="bx bx-edit"></i> Update Progress
                                </button>

                                @if ($serviceRequest->ticket_status !== 'Resolved')
                                    <button class="btn btn-success w-100" id="markResolvedBtn">
                                        <i class="bx bx-check-circle"></i> Tandai Selesai
                                    </button>
                                @endif
                            @endif
                        </div>
                    </div>

                    <!-- Metadata -->
                    <div class="card detail-card">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="bx bx-info-circle"></i> Info Tiket</h6>
                        </div>
                        <div class="card-body">
                            <small class="text-muted d-block mb-1">No. Tiket</small>
                            <p class="mb-2"><strong>{{ $serviceRequest->ticket_number }}</strong></p>

                            <small class="text-muted d-block mb-1">Assigned</small>
                            <p class="mb-2">
                                {{ \Carbon\Carbon::parse($serviceRequest->assigned_at)->format('d M Y, H:i') }}</p>

                            <small class="text-muted d-block mb-1">Status</small>
                            <p class="mb-0">
                                @php
                                    $statusBadges = [
                                        'Assigned' => 'info',
                                        'In Progress' => 'warning',
                                        'Resolved' => 'success',
                                        'Completed' => 'secondary',
                                    ];
                                    $badgeClass = $statusBadges[$serviceRequest->ticket_status] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $badgeClass }}">{{ $serviceRequest->ticket_status }}</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Preview Image User -->
    @if (
        $serviceRequest->file_path &&
            in_array(strtolower(pathinfo($serviceRequest->file_path, PATHINFO_EXTENSION)), [
                'jpg',
                'jpeg',
                'png',
                'gif',
                'webp',
            ]))
        <div class="modal fade" id="imageModal" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Lampiran dari Pelapor</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center">
                        <img src="{{ asset('storage/' . $serviceRequest->file_path) }}" class="img-fluid"
                            alt="Lampiran">
                    </div>
                    <div class="modal-footer">
                        <a href="{{ asset('storage/' . $serviceRequest->file_path) }}" download class="btn btn-primary">
                            <i class="bx bx-download"></i> Download
                        </a>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal Preview Image Technician -->
    @if (
        $technicianResponse &&
            $technicianResponse->attachment_path &&
            in_array(strtolower(pathinfo($technicianResponse->attachment_path, PATHINFO_EXTENSION)), [
                'jpg',
                'jpeg',
                'png',
                'gif',
                'webp',
            ]))
        <div class="modal fade" id="techImageModal" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Foto Hasil Perbaikan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center">
                        <img src="{{ asset('storage/' . $technicianResponse->attachment_path) }}" class="img-fluid"
                            alt="Hasil">
                    </div>
                    <div class="modal-footer">
                        <a href="{{ asset('storage/' . $technicianResponse->attachment_path) }}" download
                            class="btn btn-primary">
                            <i class="bx bx-download"></i> Download
                        </a>
                        <button type="button" class="btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal Update Status -->
    @include('pages.modul.technician.update-status')
@endsection

@push('after-js')
    <script>
        $(function() {
            var ticketNumber = '{{ $serviceRequest->ticket_number }}';
            var currentStatus = '{{ $serviceRequest->ticket_status }}';

            // ============================================
            // START WORK BUTTON (Assigned → In Progress)
            // ============================================
            $('#startWorkBtn').click(function() {
                Swal.fire({
                    title: 'Mulai Mengerjakan?',
                    text: 'Apakah Anda siap untuk mulai mengerjakan tiket ini?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#ffc107',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Mulai Sekarang!',
                    cancelButtonText: 'Nanti'
                }).then((result) => {
                    if (result.isConfirmed) {
                        var formData = new FormData();
                        formData.append('status', 'In Progress');
                        formData.append('_token', '{{ csrf_token() }}');

                        $.ajax({
                            url: '/technician/tickets/' + ticketNumber + '/update-status',
                            type: 'POST',
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function(response) {
                                console.log('✅ Start work success:', response);

                                Swal.fire({
                                    icon: 'success',
                                    title: 'Pekerjaan Dimulai!',
                                    text: 'Status berhasil diubah ke "In Progress"',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload();
                                });
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal!',
                                    text: 'Gagal memulai pekerjaan'
                                });
                            }
                        });
                    }
                });
            });

            // ============================================
            // UPDATE STATUS BUTTON
            // ============================================
            $('#updateStatusBtn').click(function() {
                $('#updateTicketNumber').val(ticketNumber);
                $('#currentStatusDisplay').text(currentStatus);
                $('#statusSelect').val(currentStatus);
                $('#updateStatusModal').modal('show');
            });

            // ============================================
            // MARK RESOLVED BUTTON
            // ============================================
            $('#markResolvedBtn').click(function() {
                Swal.fire({
                    title: 'Tandai Selesai?',
                    text: 'Apakah perbaikan sudah selesai dilakukan?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Selesai!',
                    cancelButtonText: 'Belum'
                }).then((result) => {
                    if (result.isConfirmed) {
                        var formData = new FormData();
                        formData.append('status', 'Resolved');
                        formData.append('_token', '{{ csrf_token() }}');

                        $.ajax({
                            url: '/technician/tickets/' + ticketNumber + '/update-status',
                            type: 'POST',
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function(response) {
                                console.log('✅ Mark resolved success:', response);

                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: 'Tiket ditandai selesai',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload();
                                });
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal!',
                                    text: 'Gagal update status'
                                });
                            }
                        });
                    }
                });
            });

            // ============================================
            // SUBMIT UPDATE STATUS FORM
            // ============================================
            $('#updateStatusForm').submit(function(e) {
                e.preventDefault();

                var formData = new FormData(this);
                var selectedStatus = $('#statusSelect').val();

                $.ajax({
                    url: '/technician/tickets/' + ticketNumber + '/update-status',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    beforeSend: function() {
                        $('#submitUpdateStatus').prop('disabled', true).html(
                            '<i class="bx bx-loader-alt bx-spin"></i> Memproses...');
                    },
                    success: function(response) {
                        console.log('✅ Update success:', response);

                        $('#updateStatusModal').modal('hide');

                        // Cek apakah status jadi Resolved/Completed
                        if (response.data.status === 'Resolved' || response.data.status ===
                            'Completed') {
                            // RELOAD kalau selesai
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            // Update UI tanpa reload kalau belum selesai
                            updateProgressCard(response.data);
                            updateStatusBadge(response.data.status);
                            updateTimeline(response.data);

                            // Reset form
                            $('#updateStatusForm')[0].reset();
                            $('#imagePreview').hide();
                            $('#previewImg').attr('src', '');

                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                        }
                    },
                    error: function(xhr) {
                        console.error('❌ Update error:', xhr);

                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: xhr.responseJSON?.message || 'Gagal update status'
                        });
                    },
                    complete: function() {
                        $('#submitUpdateStatus').prop('disabled', false).html(
                            '<i class="bx bx-save"></i> Update Status');
                    }
                });
            });

            // ============================================
            // HELPER FUNCTIONS
            // ============================================

            function escapeHtml(text) {
                if (!text) return '';
                var map = {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;'
                };
                return text.replace(/[&<>"']/g, function(m) {
                    return map[m];
                });
            }

            function updateProgressCard(data) {
                if (!data.diagnosis && !data.action_taken && !data.technician_notes && !data.attachment_path) {
                    console.log('⏭️ Skip update progress card (no data changes)');
                    return;
                }

                var html = '';

                if (data.diagnosis) {
                    html += '<div class="mb-3">' +
                        '<div class="detail-label"><i class="bx bx-search-alt info-icon"></i>Diagnosis</div>' +
                        '<div class="detail-value">' + escapeHtml(data.diagnosis) + '</div>' +
                        '</div>';
                }

                if (data.action_taken) {
                    html += '<div class="mb-3">' +
                        '<div class="detail-label"><i class="bx bx-wrench info-icon"></i>Tindakan yang Dilakukan</div>' +
                        '<div class="detail-value">' + escapeHtml(data.action_taken) + '</div>' +
                        '</div>';
                }

                if (data.technician_notes) {
                    html += '<div class="mb-3">' +
                        '<div class="detail-label"><i class="bx bx-note info-icon"></i>Catatan Teknisi</div>' +
                        '<div class="detail-value">' + escapeHtml(data.technician_notes) + '</div>' +
                        '</div>';
                }

                if (data.attachment_path) {
                    var isImage = /\.(jpg|jpeg|png|gif|webp)$/i.test(data.attachment_path);
                    if (isImage) {
                        html += '<div class="mb-3">' +
                            '<div class="detail-label"><i class="bx bx-image info-icon"></i>Foto Hasil Perbaikan</div>' +
                            '<div class="detail-value">' +
                            '<img src="/storage/' + data.attachment_path +
                            '" class="attachment-preview img-thumbnail" alt="Hasil" data-bs-toggle="modal" data-bs-target="#techImageModal">' +
                            '<p class="text-muted small mt-2">Klik untuk memperbesar</p>' +
                            '</div></div>';
                    }
                }

                if ($('.progress-card').length > 0) {
                    $('.progress-card .card-body').html(html ||
                        '<div class="alert alert-info mb-0"><i class="bx bx-info-circle"></i> Belum ada progress</div>'
                        );
                    console.log('✅ Progress card updated');
                } else {
                    if (html) {
                        var newCard = '<div class="card detail-card progress-card mb-3">' +
                            '<div class="card-header bg-light">' +
                            '<h6 class="mb-0"><i class="bx bx-wrench"></i> Progress Perbaikan</h6>' +
                            '</div>' +
                            '<div class="card-body">' + html + '</div>' +
                            '</div>';
                        $('.header-section').closest('.card').after(newCard);
                        console.log('✅ Progress card created');
                    }
                }
            }

            function updateStatusBadge(status) {
                var badges = {
                    'Assigned': {
                        class: 'info',
                        icon: 'bx-task'
                    },
                    'In Progress': {
                        class: 'warning',
                        icon: 'bx-time'
                    },
                    'Resolved': {
                        class: 'success',
                        icon: 'bx-check-circle'
                    },
                    'Completed': {
                        class: 'secondary',
                        icon: 'bx-check-double'
                    }
                };

                var badge = badges[status] || badges['Assigned'];
                var badgeHtml = '<span class="badge bg-' + badge.class + '">' +
                    '<i class="bx ' + badge.icon + '"></i> ' + status + '</span>';

                $('.col-md-6').has('.detail-label:contains("Status Pekerjaan")').find('.detail-value').html(
                    badgeHtml);
                $('.card-body').has('small:contains("Status")').find('.badge').replaceWith(
                    '<span class="badge bg-' + badge.class + '">' + status + '</span>');

                currentStatus = status;
                console.log('✅ Status badge updated');
            }

            function updateTimeline(data) {
                var statusText = '';
                if (data.status === 'In Progress') {
                    statusText = 'Mulai dikerjakan';
                } else if (data.status === 'Resolved') {
                    statusText = 'Perbaikan selesai';
                } else if (data.status === 'Completed') {
                    statusText = 'Tiket ditutup';
                } else {
                    return;
                }

                var existingItem = $('.timeline-item').filter(function() {
                    return $(this).find('p').text().indexOf(statusText) !== -1;
                });

                if (existingItem.length > 0) {
                    console.log('⏭️ Timeline item already exists, skip');
                    return;
                }

                var now = new Date();
                var timeStr = now.toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric'
                }) + ', ' + now.toLocaleTimeString('id-ID', {
                    hour: '2-digit',
                    minute: '2-digit'
                });

                var timelineItem = '<div class="timeline-item">' +
                    '<small class="text-muted">' + timeStr + '</small>' +
                    '<p class="mb-0">';

                if (data.status === 'In Progress') {
                    timelineItem += 'Mulai dikerjakan';
                } else if (data.status === 'Resolved') {
                    timelineItem += '<strong class="text-success">Perbaikan selesai</strong>';
                } else if (data.status === 'Completed') {
                    timelineItem += '<strong class="text-secondary">Tiket ditutup</strong>';
                }

                timelineItem += '</p></div>';
                $('.timeline').append(timelineItem);
                console.log('✅ Timeline updated');
            }
        });
    </script>
@endpush
