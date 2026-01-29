@extends('layouts.default')

@push('after-style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bs-stepper/dist/css/bs-stepper.min.css">
    <style>
        .bs-stepper-header {
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 0.5rem;
        }

        .bs-stepper .line {
            background-color: #dee2e6;
        }

        .bs-stepper .step-trigger {
            padding: 1rem;
            border-radius: 0.5rem;
        }

        .bs-stepper .step-trigger:hover {
            background-color: #e9ecef;
        }

        .bs-stepper .step-trigger.active {
            background-color: #0d6efd;
            color: white;
        }

        .bs-stepper-circle {
            background-color: #6c757d;
        }

        .bs-stepper .step.active .bs-stepper-circle {
            background-color: #0d6efd;
        }

        .bs-stepper .step.completed .bs-stepper-circle {
            background-color: #198754;
        }

        .step-content {
            min-height: 400px;
            padding: 2rem 1rem;
        }

        .section-header {
            border-left: 4px solid #0d6efd;
            padding-left: 1rem;
            margin-bottom: 1.5rem;
        }

        .required-mark {
            color: #dc3545;
            font-weight: bold;
        }

        .form-label {
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .help-text {
            font-size: 0.875rem;
            color: #6c757d;
            margin-top: 0.25rem;
        }

        .preview-image {
            max-height: 200px;
            border-radius: 0.5rem;
            border: 2px dashed #dee2e6;
            padding: 0.5rem;
        }

        .info-box {
            background: #cfe2ff;
            border-left: 4px solid #0d6efd;
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
        }

        .summary-item {
            padding: 0.75rem;
            background: #f8f9fa;
            border-radius: 0.5rem;
            margin-bottom: 0.5rem;
        }

        .summary-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.25rem;
        }

        .summary-value {
            color: #212529;
        }

        @media (max-width: 768px) {
            .bs-stepper-header {
                flex-direction: column;
            }

            .bs-stepper .line {
                display: none;
            }

            .bs-stepper-label {
                display: none;
            }
        }
    </style>
@endpush

@section('content')
    <div class="page-wrapper">
        <div class="page-content">
            {{-- Breadcrumb --}}
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                <div class="breadcrumb-title pe-3">Service Request</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('service.index') }}">
                                    <i class="bx bx-list-ul"></i> Daftar Tiket
                                </a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                Edit Tiket: {{ $ticket->ticket_number }}
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>

            {{-- Main Card --}}
            <div class="row">
                <div class="col-xl-12 mx-auto">
                    <div class="card">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0">
                                <i class="bx bx-edit"></i>
                                Edit Tiket IT - {{ $ticket->ticket_number }}
                            </h5>
                        </div>

                        <div class="card-body">
                            {{-- Alert Info --}}
                            <div class="alert alert-info">
                                <i class="bx bx-info-circle"></i>
                                <strong>Info:</strong> Anda sedang mengedit tiket dengan status
                                <span class="badge bg-primary">{{ $ticket->ticket_status }}</span>
                            </div>

                            {{-- BS Stepper --}}
                            <div id="ticketStepper" class="bs-stepper">
                                {{-- Stepper Header --}}
                                <div class="bs-stepper-header" role="tablist">
                                    <div class="step" data-target="#step-1">
                                        <button type="button" class="step-trigger" role="tab" id="stepTrigger1"
                                            aria-controls="step-1">
                                            <span class="bs-stepper-circle">1</span>
                                            <span class="bs-stepper-label">Info Pelapor</span>
                                        </button>
                                    </div>
                                    <div class="line"></div>

                                    <div class="step" data-target="#step-2">
                                        <button type="button" class="step-trigger" role="tab" id="stepTrigger2"
                                            aria-controls="step-2">
                                            <span class="bs-stepper-circle">2</span>
                                            <span class="bs-stepper-label">Detail Masalah</span>
                                        </button>
                                    </div>
                                    <div class="line"></div>

                                    <div class="step" data-target="#step-3">
                                        <button type="button" class="step-trigger" role="tab" id="stepTrigger3"
                                            aria-controls="step-3">
                                            <span class="bs-stepper-circle">3</span>
                                            <span class="bs-stepper-label">Lokasi & Lampiran</span>
                                        </button>
                                    </div>
                                    <div class="line"></div>

                                    <div class="step" data-target="#step-4">
                                        <button type="button" class="step-trigger" role="tab" id="stepTrigger4"
                                            aria-controls="step-4">
                                            <span class="bs-stepper-circle">4</span>
                                            <span class="bs-stepper-label">Review & Submit</span>
                                        </button>
                                    </div>
                                </div>

                                {{-- Stepper Content --}}
                                <div class="bs-stepper-content">
                                    <form id="ticketForm" enctype="multipart/form-data">
                                        @csrf

                                        {{-- STEP 1: Info Pelapor --}}
                                        <div id="step-1" class="content" role="tabpanel" aria-labelledby="stepTrigger1">
                                            <div class="step-content">
                                                <div class="section-header">
                                                    <h5 class="text-primary mb-0">
                                                        <i class="bx bx-user"></i> Informasi Pelapor
                                                    </h5>
                                                    <small class="text-muted">Masukkan informasi Anda sebagai
                                                        pelapor</small>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label for="requester_name" class="form-label">
                                                            Nama Lengkap <span class="required-mark">*</span>
                                                        </label>
                                                        <input type="text" class="form-control" id="requester_name"
                                                            name="requester_name" value="{{ $ticket->requester_name }}"
                                                            required>
                                                        <div class="help-text">Nama lengkap pelapor</div>
                                                    </div>

                                                    <div class="col-md-6 mb-3">
                                                        <label for="requester_phone" class="form-label">
                                                            No. Telepon
                                                        </label>
                                                        <input type="tel" class="form-control" id="requester_phone"
                                                            name="requester_phone" value="{{ $ticket->requester_phone }}"
                                                            placeholder="08xxxxxxxxxx">
                                                        <div class="help-text">Nomor telepon yang bisa dihubungi</div>
                                                    </div>

                                                    <div class="col-12 mb-3">
                                                        <label for="unit_id" class="form-label">
                                                            Unit/Ruangan <span class="required-mark">*</span>
                                                        </label>
                                                        <select class="form-select" id="unit_id" name="unit_id"
                                                            required>
                                                            <option value="">- Pilih Unit/Ruangan -</option>
                                                        </select>
                                                        <div class="help-text">Unit/ruangan tempat Anda bekerja atau lokasi
                                                            masalah</div>
                                                    </div>
                                                </div>

                                                <div class="info-box">
                                                    <i class="bx bx-info-circle"></i>
                                                    <strong>Informasi:</strong> Pastikan data pelapor sudah benar agar tim
                                                    IT dapat menghubungi Anda.
                                                </div>
                                            </div>

                                            <div class="d-flex justify-content-between">
                                                <a href="{{ route('service.index') }}" class="btn btn-secondary">
                                                    <i class="bx bx-arrow-back"></i> Kembali
                                                </a>
                                                <button type="button" class="btn btn-primary" onclick="stepper.next()">
                                                    Selanjutnya <i class="bx bx-arrow-to-right"></i>
                                                </button>
                                            </div>
                                        </div>

                                        {{-- STEP 2: Detail Masalah --}}
                                        <div id="step-2" class="content" role="tabpanel"
                                            aria-labelledby="stepTrigger2">
                                            <div class="step-content">
                                                <div class="section-header">
                                                    <h5 class="text-primary mb-0">
                                                        <i class="bx bx-error-circle"></i> Detail Masalah
                                                    </h5>
                                                    <small class="text-muted">Jelaskan masalah yang Anda alami</small>
                                                </div>

                                                <div class="row">
                                                    <div class="col-12 mb-3">
                                                        <label for="issue_title" class="form-label">
                                                            Judul Masalah <span class="required-mark">*</span>
                                                        </label>
                                                        <input type="text" class="form-control" id="issue_title"
                                                            name="issue_title" value="{{ $ticket->issue_title }}"
                                                            placeholder="Contoh: PC tidak bisa menyala di IGD" required>
                                                        <div class="help-text">Ringkasan singkat masalah yang Anda alami
                                                        </div>
                                                    </div>

                                                    <div class="col-12 mb-3">
                                                        <label for="description" class="form-label">
                                                            Deskripsi Masalah <span class="required-mark">*</span>
                                                        </label>
                                                        <textarea class="form-control" id="description" name="description" rows="5"
                                                            placeholder="Jelaskan masalah secara detail..." required>{{ $ticket->description }}</textarea>
                                                        <div class="help-text">Berikan informasi selengkap mungkin</div>
                                                    </div>

                                                    <div class="col-md-6 mb-3">
                                                        <label for="problem_category" class="form-label">
                                                            Kategori Masalah <span class="required-mark">*</span>
                                                        </label>
                                                        <select class="form-select" id="problem_category"
                                                            name="problem_category_id" required>
                                                            <option value="">- Pilih Kategori -</option>
                                                        </select>
                                                        <div class="help-text">Pilih kategori yang paling sesuai</div>
                                                    </div>

                                                    <div class="col-md-6 mb-3" id="sub_category_container"
                                                        style="display:none;">
                                                        <label for="problem_sub_category" class="form-label">
                                                            Detail Masalah <span class="required-mark">*</span>
                                                        </label>
                                                        <select class="form-select" id="problem_sub_category"
                                                            name="problem_sub_category_id">
                                                            <option value="">- Pilih Detail Masalah -</option>
                                                        </select>
                                                        <div class="help-text">Pilih masalah yang paling sesuai</div>
                                                    </div>

                                                    {{-- Network Section (Conditional) --}}
                                                    <div class="col-12" id="network_section" style="display:none;">
                                                        <div class="row">
                                                            <div class="col-md-6 mb-3">
                                                                <label for="ip_address" class="form-label">IP
                                                                    Address</label>
                                                                <input type="text" class="form-control"
                                                                    id="ip_address" name="ip_address"
                                                                    value="{{ $ticket->ip_address }}"
                                                                    placeholder="192.168.1.x">
                                                                <div class="help-text">Isi jika diketahui</div>
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label for="connection_status" class="form-label">Status
                                                                    Koneksi</label>
                                                                <select class="form-select" id="connection_status"
                                                                    name="connection_status">
                                                                    <option value="">- Pilih Status -</option>
                                                                    <option value="Tidak bisa connect sama sekali"
                                                                        {{ $ticket->connection_status == 'Tidak bisa connect sama sekali' ? 'selected' : '' }}>
                                                                        Tidak bisa connect
                                                                    </option>
                                                                    <option value="Koneksi lambat"
                                                                        {{ $ticket->connection_status == 'Koneksi lambat' ? 'selected' : '' }}>
                                                                        Koneksi lambat
                                                                    </option>
                                                                    <option value="Putus-putus"
                                                                        {{ $ticket->connection_status == 'Putus-putus' ? 'selected' : '' }}>
                                                                        Putus-putus
                                                                    </option>
                                                                    <option value="Tidak stabil"
                                                                        {{ $ticket->connection_status == 'Tidak stabil' ? 'selected' : '' }}>
                                                                        Tidak stabil
                                                                    </option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6 mb-3">
                                                        <label for="severity_level" class="form-label">
                                                            Tingkat Keparahan <span class="required-mark">*</span>
                                                        </label>
                                                        <select class="form-select" id="severity_level"
                                                            name="severity_level" required>
                                                            <option value="">- Pilih Tingkat Keparahan -</option>
                                                            <option value="Rendah"
                                                                {{ $ticket->severity_level == 'Rendah' ? 'selected' : '' }}>
                                                                🟢 Rendah - Tidak mendesak
                                                            </option>
                                                            <option value="Sedang"
                                                                {{ $ticket->severity_level == 'Sedang' ? 'selected' : '' }}>
                                                                🟡 Sedang - Ada workaround
                                                            </option>
                                                            <option value="Tinggi"
                                                                {{ $ticket->severity_level == 'Tinggi' ? 'selected' : '' }}>
                                                                🟠 Tinggi - Mengganggu operasional
                                                            </option>
                                                            <option value="Kritis"
                                                                {{ $ticket->severity_level == 'Kritis' ? 'selected' : '' }}>
                                                                🔴 Kritis - Ganggu layanan pasien
                                                            </option>
                                                        </select>
                                                    </div>

                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label d-block">Dampak ke Pasien</label>
                                                        <div class="form-check form-switch mt-2">
                                                            <input class="form-check-input" type="checkbox"
                                                                id="impact_patient_care" name="impact_patient_care"
                                                                value="1"
                                                                {{ $ticket->impact_patient_care ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="impact_patient_care">
                                                                <i class="bx bx-heart text-danger"></i> Berdampak pada
                                                                pelayanan pasien
                                                            </label>
                                                        </div>
                                                        <div class="help-text">Centang jika masalah ini mengganggu layanan
                                                            pasien</div>
                                                    </div>

                                                    <div class="col-12 mb-3">
                                                        <label for="occurrence_time" class="form-label">
                                                            Waktu Kejadian <span class="required-mark">*</span>
                                                        </label>
                                                        <input type="datetime-local" class="form-control"
                                                            id="occurrence_time" name="occurrence_time"
                                                            value="{{ \Carbon\Carbon::parse($ticket->occurrence_time)->format('Y-m-d\TH:i') }}"
                                                            required>
                                                        <div class="help-text">Kapan masalah ini pertama kali terjadi?
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="d-flex justify-content-between">
                                                <button type="button" class="btn btn-secondary"
                                                    onclick="stepper.previous()">
                                                    <i class="bx bx-arrow-back"></i> Sebelumnya
                                                </button>
                                                <button type="button" class="btn btn-primary" onclick="stepper.next()">
                                                    Selanjutnya <i class="bx bx-arrow-to-right"></i>
                                                </button>
                                            </div>
                                        </div>

                                        {{-- STEP 3: Lokasi & Lampiran --}}
                                        <div id="step-3" class="content" role="tabpanel"
                                            aria-labelledby="stepTrigger3">
                                            <div class="step-content">
                                                <div class="section-header">
                                                    <h5 class="text-primary mb-0">
                                                        <i class="bx bx-map"></i> Lokasi & Lampiran
                                                    </h5>
                                                    <small class="text-muted">Informasi lokasi dan file pendukung</small>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label for="device_affected" class="form-label">Perangkat yang
                                                            Terdampak</label>
                                                        <input type="text" class="form-control" id="device_affected"
                                                            name="device_affected" value="{{ $ticket->device_affected }}"
                                                            placeholder="Contoh: PC-IGD-01, Printer Kasir">
                                                        <div class="help-text">Nama/kode perangkat yang bermasalah</div>
                                                    </div>

                                                    <div class="col-md-6 mb-3">
                                                        <label for="location" class="form-label">
                                                            Lokasi Spesifik <span class="required-mark">*</span>
                                                        </label>
                                                        <input type="text" class="form-control" id="location"
                                                            name="location" value="{{ $ticket->location }}"
                                                            placeholder="Contoh: IGD - Ruang Triase" required>
                                                        <div class="help-text">Lokasi detail dalam unit</div>
                                                    </div>

                                                    <div class="col-12 mb-3">
                                                        <label for="expected_action" class="form-label">
                                                            Tindakan yang Diharapkan <span class="required-mark">*</span>
                                                        </label>
                                                        <textarea class="form-control" id="expected_action" name="expected_action" rows="3"
                                                            placeholder="Contoh: Perbaikan segera, Penggantian perangkat..." required>{{ $ticket->expected_action }}</textarea>
                                                        <div class="help-text">Apa yang Anda harapkan dari tim IT?</div>
                                                    </div>

                                                    <div class="col-12 mb-3">
                                                        <label for="file_path" class="form-label">Lampiran
                                                            (Screenshot/Foto/Dokumen)</label>

                                                        @if ($ticket->file_path)
                                                            <div class="alert alert-info mb-2">
                                                                <i class="bx bx-file"></i> File saat ini:
                                                                <a href="{{ Storage::url($ticket->file_path) }}"
                                                                    target="_blank">
                                                                    {{ basename($ticket->file_path) }}
                                                                </a>
                                                                <br>
                                                                <small class="text-muted">Upload file baru jika ingin
                                                                    menggantinya</small>
                                                            </div>
                                                        @endif

                                                        <input type="file" class="form-control" id="file_path"
                                                            name="file_path" accept="image/*,.pdf,.doc,.docx">
                                                        <div class="help-text">
                                                            <i class="bx bx-info-circle"></i> Format: JPG, PNG, PDF, DOC
                                                            (Max 5MB)
                                                        </div>
                                                        <div id="file_preview" class="mt-3" style="display:none;">
                                                            <img id="preview_image" src="" class="preview-image">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="info-box">
                                                    <i class="bx bx-camera"></i>
                                                    <strong>Tips:</strong> Screenshot atau foto error message sangat
                                                    membantu teknisi.
                                                </div>
                                            </div>

                                            <div class="d-flex justify-content-between">
                                                <button type="button" class="btn btn-secondary"
                                                    onclick="stepper.previous()">
                                                    <i class="bx bx-arrow-back"></i> Sebelumnya
                                                </button>
                                                <button type="button" class="btn btn-primary" onclick="stepper.next()">
                                                    Review <i class="bx bx-check-circle"></i>
                                                </button>
                                            </div>
                                        </div>

                                        {{-- STEP 4: Review & Submit --}}
                                        <div id="step-4" class="content" role="tabpanel"
                                            aria-labelledby="stepTrigger4">
                                            <div class="step-content">
                                                <div class="section-header">
                                                    <h5 class="text-primary mb-0">
                                                        <i class="bx bx-check-double"></i> Review & Submit
                                                    </h5>
                                                    <small class="text-muted">Periksa kembali data Anda sebelum
                                                        update</small>
                                                </div>

                                                {{-- Summary container --}}
                                                <div id="summary-container"></div>

                                                {{-- Alert Warning --}}
                                                <div class="alert alert-warning mt-3">
                                                    <i class="bx bx-error-circle"></i>
                                                    <strong>Perhatian:</strong>
                                                    <ul class="mb-0 mt-2">
                                                        <li>Pastikan semua informasi sudah benar</li>
                                                        <li>Perubahan data akan tersimpan setelah klik "Update Tiket"</li>
                                                        <li>Status tiket saat ini:
                                                            <strong>{{ $ticket->ticket_status }}</strong>
                                                        </li>
                                                        @if ($ticket->assigned_to)
                                                            <li>Tiket sudah di-assign ke:
                                                                <strong>{{ $ticket->assignedTechnician->name ?? 'Teknisi' }}</strong>
                                                            </li>
                                                        @endif
                                                    </ul>
                                                </div>
                                            </div>

                                            <div class="d-flex justify-content-between">
                                                <button type="button" class="btn btn-secondary"
                                                    onclick="stepper.previous()">
                                                    <i class="bx bx-arrow-back"></i> Sebelumnya
                                                </button>
                                                <button type="submit" class="btn btn-warning btn-lg">
                                                    <i class="bx bx-save"></i> Update Tiket
                                                </button>
                                            </div>
                                        </div>

                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('after-js')
    <script src="https://cdn.jsdelivr.net/npm/bs-stepper/dist/js/bs-stepper.min.js"></script>
    <script>
        // ============================================
        // GLOBAL VARIABLES
        // ============================================
        let stepper;
        const ticketData = @json($ticket);

        // ============================================
        // DOCUMENT READY - MAIN INITIALIZATION
        // ============================================
        $(document).ready(function() {
            // 1. Initialize Stepper
            initializeStepper();

            // 2. Load Master Data
            loadHospitalUnits();
            loadProblemCategories();

            // 3. Setup Event Handlers
            setupEventHandlers();
        });

        // ============================================
        // INITIALIZATION FUNCTIONS
        // ============================================
        function initializeStepper() {
            stepper = new Stepper($('#ticketStepper')[0], {
                linear: false,
                animation: true
            });
        }

        function setupEventHandlers() {
            // Form submit
            $('#ticketForm').on('submit', function(e) {
                e.preventDefault();
                updateTicket();
            });

            // Category change
            $('#problem_category').on('change', handleCategoryChange);

            // Severity level change
            $('#severity_level').on('change', function() {
                if ($(this).val() === 'Kritis') {
                    $('#impact_patient_care').prop('checked', true);
                    Swal.fire({
                        icon: 'warning',
                        title: 'Perhatian!',
                        text: 'Tingkat Kritis akan mendapat prioritas tertinggi',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            });

            // File upload
            $('#file_path').on('change', function() {
                handleFileUpload(this);
            });

            // ✅ FIX: Generate summary saat klik step 4 (biar ambil data terbaru dari form)
            $('#stepTrigger4').on('click', function() {
                generateSummary();
            });
        }

        // ============================================
        // DATA LOADING FUNCTIONS
        // ============================================
        function loadHospitalUnits() {
            $.ajax({
                url: '/ajax/hospital-units',
                type: 'GET',
                success: function(response) {
                    $('#unit_id').empty().append('<option value="">- Pilih Unit/Ruangan -</option>');
                    response.forEach(function(unit) {
                        const selected = unit.id == ticketData.unit_id ? 'selected' : '';
                        const badge = getUnitTypeBadge(unit.unit_type);
                        $('#unit_id').append(
                            `<option value="${unit.id}" ${selected}>${badge} ${unit.unit_code} - ${unit.unit_name}</option>`
                        );
                    });
                },
                error: function() {
                    Swal.fire('Error', 'Gagal memuat data unit', 'error');
                }
            });
        }

        function loadProblemCategories() {
            $.ajax({
                url: '/ajax/problem-categories',
                type: 'GET',
                success: function(response) {
                    $('#problem_category').empty().append('<option value="">- Pilih Kategori -</option>');
                    response.forEach(function(category) {
                        const selected = category.id == ticketData.problem_category_id ? 'selected' :
                            '';
                        $('#problem_category').append(
                            `<option value="${category.id}" data-code="${category.category_code}" ${selected}>
                                ${category.category_name}
                            </option>`
                        );
                    });

                    // Trigger load sub-categories jika ada category
                    if (ticketData.problem_category_id) {
                        handleCategoryChange();
                    }
                }
            });
        }

        function loadSubCategories(categoryId) {
            $.ajax({
                url: '/ajax/sub-categories/' + categoryId,
                type: 'GET',
                success: function(response) {
                    if (response.length > 0) {
                        $('#problem_sub_category').empty().append(
                            '<option value="">- Pilih Detail Masalah -</option>');
                        response.forEach(function(sub) {
                            const selected = sub.id == ticketData.problem_sub_category_id ? 'selected' :
                                '';
                            $('#problem_sub_category').append(
                                `<option value="${sub.id}" ${selected}>${sub.sub_category_name}</option>`
                            );
                        });
                        $('#sub_category_container').show();
                        $('#problem_sub_category').attr('required', true);
                    } else {
                        $('#sub_category_container').hide();
                        $('#problem_sub_category').removeAttr('required');
                    }
                },
                error: function() {
                    $('#sub_category_container').hide();
                    $('#problem_sub_category').removeAttr('required');
                }
            });
        }

        // ============================================
        // EVENT HANDLER FUNCTIONS (UPDATED WITH DEV LOGIC)
        // ============================================
        function handleCategoryChange() {
            const categoryId = $('#problem_category').val();
            const categoryCode = $('#problem_category option:selected').data('code');

            // Reset sections
            $('#sub_category_container').hide();
            $('#network_section').hide();
            $('#problem_sub_category').removeAttr('required');

            // ✅ TAMBAHAN: Show/hide fields untuk DEV
            if (categoryCode === 'DEV') {
                // Hide fields yang tidak perlu untuk DEV
                $('#occurrence_time').closest('.col-12').hide();
                $('#device_affected').closest('.col-md-6').hide();
                $('#location').closest('.col-md-6').hide();

                // Remove required attribute
                $('#occurrence_time').removeAttr('required');
                $('#location').removeAttr('required');
            } else {
                // Show fields untuk kategori lain
                $('#occurrence_time').closest('.col-12').show();
                $('#device_affected').closest('.col-md-6').show();
                $('#location').closest('.col-md-6').show();

                // Add required attribute back
                $('#occurrence_time').attr('required', true);
                $('#location').attr('required', true);
            }

            if (!categoryId) return;

            // Load sub-categories
            loadSubCategories(categoryId);

            // Show network section jika kategori Network
            if (categoryCode === 'NET') {
                $('#network_section').show();
            }
        }

        function handleFileUpload(input) {
            const file = input.files[0];
            if (!file) return;

            // Check file size (5MB max)
            if (file.size > 5 * 1024 * 1024) {
                Swal.fire({
                    icon: 'error',
                    title: 'File Terlalu Besar',
                    text: 'Ukuran file maksimal 5MB'
                });
                $(input).val('');
                $('#file_preview').hide();
                return;
            }

            // Preview image
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#preview_image').attr('src', e.target.result);
                    $('#file_preview').show();
                };
                reader.readAsDataURL(file);
            } else {
                $('#file_preview').hide();
            }
        }

        // ============================================
        // SUMMARY GENERATION (UPDATED WITH DEV LOGIC)
        // ============================================
        function generateSummary() {
            const categoryCode = $('#problem_category option:selected').data('code');
            const isDevCategory = categoryCode === 'DEV';

            // ✅ Ambil data dari FORM (data yang sedang di-edit), BUKAN dari ticketData
            const formData = {
                requester_name: $('#requester_name').val(),
                requester_phone: $('#requester_phone').val(),
                unit: $('#unit_id option:selected').text(),
                issue_title: $('#issue_title').val(),
                description: $('#description').val(),
                category: $('#problem_category option:selected').text(),
                sub_category: $('#problem_sub_category option:selected').text(),
                severity: $('#severity_level option:selected').text(),
                impact_patient: $('#impact_patient_care').is(':checked') ? 'Ya' : 'Tidak',
                occurrence_time: isDevCategory ? null : $('#occurrence_time').val(),
                device_affected: isDevCategory ? null : $('#device_affected').val(),
                location: isDevCategory ? null : $('#location').val(),
                expected_action: $('#expected_action').val(),
                ip_address: $('#ip_address').val(),
                connection_status: $('#connection_status option:selected').text(),
                file: $('#file_path')[0]?.files[0]?.name || (ticketData.file_path ? basename(ticketData.file_path) :
                    '-')
            };

            let html = '<div class="row">';

            // Info Pelapor
            html += '<div class="col-md-6">';
            html += '<h6 class="text-primary mb-3"><i class="bx bx-user"></i> Info Pelapor</h6>';
            html += createSummaryItem('Nama', formData.requester_name);
            html += createSummaryItem('Telepon', formData.requester_phone || '-');
            html += createSummaryItem('Unit', formData.unit);
            html += '</div>';

            // Detail Masalah
            html += '<div class="col-md-6">';
            html += '<h6 class="text-primary mb-3"><i class="bx bx-error-circle"></i> Detail Masalah</h6>';
            html += createSummaryItem('Judul', formData.issue_title);
            html += createSummaryItem('Kategori', formData.category);
            if (formData.sub_category && formData.sub_category !== '- Pilih Detail Masalah -') {
                html += createSummaryItem('Detail Masalah', formData.sub_category);
            }
            html += createSummaryItem('Tingkat Keparahan', formData.severity);
            html += createSummaryItem('Impact Pasien', formData.impact_patient);
            html += '</div>';

            // Deskripsi
            html += '<div class="col-12 mt-3">';
            html += '<h6 class="text-primary mb-3"><i class="bx bx-file-blank"></i> Deskripsi</h6>';
            html += createSummaryItem('Deskripsi Masalah', formData.description);
            html += '</div>';

            // ✅ HANYA tampilkan Lokasi & Perangkat jika BUKAN DEV
            if (!isDevCategory) {
                html += '<div class="col-md-6 mt-3">';
                html += '<h6 class="text-primary mb-3"><i class="bx bx-map"></i> Lokasi & Perangkat</h6>';
                html += createSummaryItem('Perangkat', formData.device_affected || '-');
                html += createSummaryItem('Lokasi', formData.location);
                html += createSummaryItem('Waktu Kejadian', formatDateTime(formData.occurrence_time));
                if (formData.ip_address) {
                    html += createSummaryItem('IP Address', formData.ip_address);
                }
                if (formData.connection_status && formData.connection_status !== '- Pilih Status -') {
                    html += createSummaryItem('Status Koneksi', formData.connection_status);
                }
                html += '</div>';
            }

            // Tindakan & Lampiran
            html += '<div class="col-md-6 mt-3">';
            html += '<h6 class="text-primary mb-3"><i class="bx bx-task"></i> Tindakan</h6>';
            html += createSummaryItem('Tindakan Diharapkan', formData.expected_action);
            html += createSummaryItem('Lampiran', formData.file);
            html += '</div>';

            html += '</div>';

            $('#summary-container').html(html);
        }

        function createSummaryItem(label, value) {
            return `
                <div class="summary-item">
                    <div class="summary-label">${label}</div>
                    <div class="summary-value">${value}</div>
                </div>
            `;
        }

        // ============================================
        // UPDATE TICKET FUNCTION (UPDATED WITH DEV LOGIC)
        // ============================================
        function updateTicket() {
            const form = $('#ticketForm')[0];
            const formData = new FormData(form);
            const categoryCode = $('#problem_category option:selected').data('code');

            // Remove network fields jika bukan kategori Network
            if (categoryCode !== 'NET') {
                formData.delete('ip_address');
                formData.delete('connection_status');
            }

            // ✅ Remove DEV-specific fields jika kategori DEV
            if (categoryCode === 'DEV') {
                formData.delete('occurrence_time');
                formData.delete('device_affected');
                formData.delete('location');
            }

            // Remove empty values
            for (let [key, value] of Array.from(formData.entries())) {
                if (value === '' || value === 'null') {
                    formData.delete(key);
                }
            }

            Swal.fire({
                title: 'Mengupdate...',
                text: 'Mohon tunggu',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            $.ajax({
                url: '{{ route('service.update', $ticket->ticket_number) }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        html: `
                            <p>Tiket berhasil diupdate</p>
                            <div class="alert alert-info mt-3">
                                <strong>No Tiket: ${response.ticket_number}</strong>
                            </div>
                        `,
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.href = response.redirect_url;
                    });
                },
                error: function(xhr) {
                    console.error('Error response:', xhr.responseJSON);

                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: xhr.responseJSON?.message || 'Terjadi kesalahan'
                    });

                    // Handle validation errors
                    if (xhr.status === 422 && xhr.responseJSON?.errors) {
                        $('.is-invalid').removeClass('is-invalid');
                        $('.invalid-feedback').remove();

                        $.each(xhr.responseJSON.errors, function(key, value) {
                            const input = $('[name="' + key + '"]');
                            input.addClass('is-invalid');
                            input.after('<div class="invalid-feedback">' + value[0] + '</div>');
                        });

                        // Go to first error step
                        const firstErrorInput = $('.is-invalid').first();
                        if (firstErrorInput.length) {
                            const stepId = firstErrorInput.closest('.content').attr('id');
                            const stepNumber = stepId.replace('step-', '');
                            stepper.to(parseInt(stepNumber));
                        }
                    }
                }
            });
        }

        // ============================================
        // HELPER FUNCTIONS
        // ============================================
        function getUnitTypeBadge(type) {
            const badges = {
                'Critical': '🔴',
                'Clinical': '🔵',
                'Support': '🟢',
                'Administrative': '⚪'
            };
            return badges[type] || '';
        }

        function formatDateTime(datetime) {
            if (!datetime) return '-';
            const dt = new Date(datetime);
            return dt.toLocaleString('id-ID', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        function basename(path) {
            return path.split('/').pop();
        }
    </script>
@endpush
