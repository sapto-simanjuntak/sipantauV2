@extends('layouts.default')

@push('after-style')
    <style>
        /* Custom CSS for ticket status */
        .row-open {
            background-color: #d1ecf1;
            /* Light blue for Open */
        }

        .row-pending {
            background-color: #fff3cd;
            /* Light yellow for Pending */
        }

        .row-approved {
            background-color: #d4edda;
            /* Light green for Approved */
        }

        .row-assigned {
            background-color: #cfe2ff;
            /* Light blue for Assigned */
        }

        .row-in-progress {
            background-color: #ffd396;
            /* Orange for In Progress */
        }

        .row-resolved {
            background-color: #74b383;
            /* Green for Resolved */
        }

        .row-closed {
            background-color: #e2e3e5;
            /* Gray for Closed */
        }

        .row-rejected {
            background-color: #fbadb3;
            /* Light red for Rejected */
        }

        /* Badge untuk impact patient care */
        .impact-patient {
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

        /* SLA Warning */
        .sla-warning {
            animation: blink 1s infinite;
        }

        @keyframes blink {

            0%,
            50%,
            100% {
                opacity: 1;
            }

            25%,
            75% {
                opacity: 0.5;
            }
        }

        /* Table Enhancement */
        #crudTable {
            font-size: 0.875rem;
        }

        #crudTable thead th {
            background-color: #f8f9fa;
            font-weight: 600;
            border-bottom: 2px solid #dee2e6;
            vertical-align: middle;
            text-align: center;
        }

        #crudTable tbody td {
            vertical-align: middle;
        }

        /* Compact Badge */
        .badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }

        /* Text Truncate Helper */
        .text-truncate {
            max-width: 200px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: inline-block;
        }

        /* Button Group Compact */
        .btn-group .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }

        /* Tooltip for truncated text */
        [title] {
            cursor: help;
        }
    </style>
@endpush

@section('content')
    <div class="page-wrapper">
        <div class="page-content">
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                <div class="breadcrumb-title pe-3">Service Request</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a></li>
                            <li class="breadcrumb-item active" aria-current="page">Daftar Tiket</li>
                        </ol>
                    </nav>
                </div>
                <div class="ms-auto">
                    <a href="{{ route('service.create') }}" class="btn btn-primary btn-sm">
                        <i class="bx bx-plus me-1"></i>Ajukan Tiket
                    </a>
                </div>
            </div>

            {{-- FILTER CARD (OPTIONAL) --}}
            <div class="card">
                <div class="card-body">
                    <form id="filterForm" class="row g-3">
                        <div class="col-md-3">
                            <label for="filter_unit" class="form-label fw-bold">Unit</label>
                            <select id="filter_unit" name="filter_unit" class="form-select">
                                <option value="">Semua Unit</option>
                                <!-- Akan diisi via AJAX -->
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="filter_category" class="form-label fw-bold">Kategori</label>
                            <select id="filter_category" name="filter_category" class="form-select">
                                <option value="">Semua Kategori</option>
                                <!-- Akan diisi via AJAX -->
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="filter_status" class="form-label fw-bold">Status Tiket</label>
                            <select id="filter_status" name="filter_status" class="form-select">
                                <option value="">Semua Status</option>
                                <option value="Open">Open</option>
                                <option value="Pending">Pending</option>
                                <option value="Approved">Approved</option>
                                <option value="Assigned">Assigned</option>
                                <option value="In Progress">In Progress</option>
                                <option value="Resolved">Resolved</option>
                                <option value="Closed">Closed</option>
                                <option value="Rejected">Rejected</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="filter_priority" class="form-label fw-bold">Prioritas</label>
                            <select id="filter_priority" name="filter_priority" class="form-select">
                                <option value="">Semua Prioritas</option>
                                <option value="Low">Low</option>
                                <option value="Medium">Medium</option>
                                <option value="High">High</option>
                                <option value="Critical">Critical</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-success">
                                <i class="bx bx-filter-alt me-1"></i>Filter
                            </button>
                            <button type="button" class="btn btn-secondary" id="reset_filter">
                                <i class="bx bx-reset me-1"></i>Reset
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- STATISTICS CARDS (OPTIONAL) --}}
            <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 mb-3">
                <div class="col">
                    <div class="card radius-10 border-start border-0 border-3 border-info">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div>
                                    <p class="mb-0 text-secondary">Open Tickets</p>
                                    <h4 class="my-1 text-info" id="stat-open">0</h4>
                                </div>
                                <div class="widgets-icons-2 rounded-circle bg-gradient-scooter text-white ms-auto">
                                    <i class='bx bx-task'></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card radius-10 border-start border-0 border-3 border-warning">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div>
                                    <p class="mb-0 text-secondary">In Progress</p>
                                    <h4 class="my-1 text-warning" id="stat-progress">0</h4>
                                </div>
                                <div class="widgets-icons-2 rounded-circle bg-gradient-bloody text-white ms-auto">
                                    <i class='bx bx-time'></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card radius-10 border-start border-0 border-3 border-success">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div>
                                    <p class="mb-0 text-secondary">Resolved</p>
                                    <h4 class="my-1 text-success" id="stat-resolved">0</h4>
                                </div>
                                <div class="widgets-icons-2 rounded-circle bg-gradient-ohhappiness text-white ms-auto">
                                    <i class='bx bx-check-circle'></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card radius-10 border-start border-0 border-3 border-danger">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div>
                                    <p class="mb-0 text-secondary">Critical Priority</p>
                                    <h4 class="my-1 text-danger" id="stat-critical">0</h4>
                                </div>
                                <div class="widgets-icons-2 rounded-circle bg-gradient-burning text-white ms-auto">
                                    <i class='bx bx-error'></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- MAIN TABLE --}}
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-sm" id="crudTable">
                            <thead class="table-light">
                                <tr>
                                    <th width="3%">No</th>
                                    <th width="10%">No Tiket</th>
                                    <th width="12%">Pelapor</th>
                                    <th width="10%">Unit</th>
                                    <th width="15%">Judul Masalah</th>
                                    <th width="8%">Kategori</th>
                                    <th width="10%">Device</th>
                                    <th width="8%">Severity</th>
                                    <th width="6%">Priority</th>
                                    <th width="8%">Status</th>
                                    <th width="8%">SLA</th>
                                    <th width="8%">Waktu Kejadian</th>
                                    <th width="8%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>


    @include('partials.notification.index')
@endsection

@push('after-js')
    <script>
        $(function() {
            // ============================================
            // INITIALIZE
            // ============================================
            loadFilterOptions();
            loadStatistics();

            // ============================================
            // HANDLE MODAL & FORM
            // ============================================
            $('#new_data').click(function() {
                console.log('Tombol Ajukan Tiket diklik');
                $('#form')[0].reset();
                $('#sub_category_section').hide().html('');
                $('#hardware_section').hide();
                $('#software_section').hide();
                $('#network_section').hide();
                $('#modal-service').modal('show');
                loadProblemCategories();
                loadHospitalUnits();
            });

            // ============================================
            // HANDLE KATEGORI CHANGE
            // ============================================
            $(document).on('change', '#problem_category', function() {
                var categoryId = $(this).val();
                var categoryCode = $(this).find('option:selected').data('code');

                console.log('Kategori:', categoryId, 'Code:', categoryCode);

                // Reset semua section
                $('#sub_category_section').hide().html('');
                $('#hardware_section').hide();
                $('#software_section').hide();
                $('#network_section').hide();

                if (categoryId && categoryId !== '') {
                    loadSubCategories(categoryId);

                    // Tampilkan section sesuai kategori
                    if (categoryCode === 'HW') {
                        $('#hardware_section').show();
                        loadHardwareTypes();
                    } else if (categoryCode === 'SW') {
                        $('#software_section').show();
                        loadSoftwareTypes();
                    } else if (categoryCode === 'NET') {
                        $('#network_section').show();
                    }
                }
            });

            // ============================================
            // HANDLE FORM SUBMIT
            // ============================================
            $(document).on('submit', '#form', function(event) {
                event.preventDefault();
                var formData = new FormData(this);

                $.ajax({
                    url: '{{ route('service.store') }}',
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    contentType: false,
                    processData: false,
                    beforeSend: function() {
                        $('#form button[type="submit"]').prop('disabled', true).text(
                            'Mengirim...');
                    },
                    success: function(response) {
                        console.log('Success:', response);

                        $('#modal-service').modal('hide');
                        $('#form')[0].reset();
                        datatable.ajax.reload();
                        loadStatistics();

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            html: '<p>Tiket berhasil dibuat</p>' +
                                '<div class="alert alert-info mt-3">' +
                                '<i class="bx bx-info-circle"></i> ' +
                                '<strong>No Tiket: ' + response.ticket_number +
                                '</strong>' +
                                '</div>' +
                                '<p class="small text-muted">Silakan catat nomor tiket untuk tracking</p>',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#0d6efd'
                        });
                    },
                    error: function(xhr) {
                        console.error('Error:', xhr.responseJSON);

                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: xhr.responseJSON?.message || 'Terjadi kesalahan',
                        });

                        // Show validation errors
                        if (xhr.status === 422 && xhr.responseJSON?.errors) {
                            $('.is-invalid').removeClass('is-invalid');
                            $('.invalid-feedback').remove();

                            $.each(xhr.responseJSON.errors, function(key, value) {
                                var input = $('[name="' + key + '"]');
                                input.addClass('is-invalid');
                                input.after('<div class="invalid-feedback">' + value[
                                    0] + '</div>');
                            });
                        }
                    },
                    complete: function() {
                        $('#form button[type="submit"]').prop('disabled', false).text(
                            'Kirim Pengajuan');
                    }
                });
            });

            // ============================================
            // LOAD FILTER OPTIONS
            // ============================================
            function loadFilterOptions() {
                // Load Units
                $.get('/ajax/hospital-units', function(data) {
                    $('#filter_unit').append(
                        data.map(u =>
                            `<option value="${u.id}">${u.unit_code} - ${u.unit_name}</option>`)
                    );
                });

                // Load Categories
                $.get('/ajax/problem-categories', function(data) {
                    $('#filter_category').append(
                        data.map(c => `<option value="${c.id}">${c.category_name}</option>`)
                    );
                });
            }

            // ============================================
            // LOAD STATISTICS
            // ============================================
            function loadStatistics() {
                $.get('/ajax/ticket-statistics', function(data) {
                    $('#stat-open').text(data.open || 0);
                    $('#stat-progress').text(data.in_progress || 0);
                    $('#stat-resolved').text(data.resolved || 0);
                    $('#stat-critical').text(data.critical || 0);
                }).fail(function() {
                    console.log('Failed to load statistics');
                });
            }

            // ============================================
            // LOAD PROBLEM CATEGORIES
            // ============================================
            function loadProblemCategories() {
                $.ajax({
                    url: '/ajax/problem-categories',
                    type: 'GET',
                    success: function(response) {
                        $('#problem_category').empty().append(
                            '<option value="">- Pilih Kategori -</option>');
                        response.forEach(function(category) {
                            $('#problem_category').append(
                                `<option value="${category.id}" data-code="${category.category_code}">
                                    ${category.category_name}
                                </option>`
                            );
                        });
                    },
                    error: function(xhr) {
                        Swal.fire('Error', 'Gagal memuat kategori masalah', 'error');
                    }
                });
            }

            // ============================================
            // LOAD HOSPITAL UNITS
            // ============================================
            function loadHospitalUnits() {
                $.ajax({
                    url: '/ajax/hospital-units',
                    type: 'GET',
                    success: function(response) {
                        $('#unit_id').empty().append('<option value="">- Pilih Unit -</option>');
                        response.forEach(function(unit) {
                            $('#unit_id').append(
                                `<option value="${unit.id}">${unit.unit_code} - ${unit.unit_name}</option>`
                            );
                        });
                    }
                });
            }

            // ============================================
            // LOAD SUB-CATEGORIES
            // ============================================
            function loadSubCategories(categoryId) {
                $.ajax({
                    url: '/ajax/sub-categories/' + categoryId,
                    type: 'GET',
                    beforeSend: function() {
                        $('#sub_category_section').html(
                            '<div class="text-center"><i class="bx bx-loader-alt bx-spin"></i> Loading...</div>'
                        ).show();
                    },
                    success: function(response) {
                        if (response.length > 0) {
                            renderSubCategorySelect(response);
                        } else {
                            $('#sub_category_section').hide().html('');
                        }
                    },
                    error: function() {
                        $('#sub_category_section').hide().html('');
                    }
                });
            }

            // ============================================
            // RENDER SUB-CATEGORY SELECT
            // ============================================
            function renderSubCategorySelect(subCategories) {
                var html = '<div class="mb-3">';
                html += '<label class="form-label fw-bold">Sub Kategori</label>';
                html += '<select class="form-select" name="problem_sub_category_id">';
                html += '<option value="">- Pilih Sub Kategori -</option>';

                subCategories.forEach(function(sub) {
                    html += `<option value="${sub.id}">${sub.sub_category_name}</option>`;
                });

                html += '</select></div>';
                $('#sub_category_section').html(html).show();
            }

            // ============================================
            // LOAD HARDWARE TYPES
            // ============================================
            function loadHardwareTypes() {
                $.get('/ajax/hardware-types', function(data) {
                    $('#hardware_type_id').empty().append('<option value="">- Pilih Hardware -</option>');
                    data.forEach(h => {
                        $('#hardware_type_id').append(
                            `<option value="${h.id}">${h.hardware_name}</option>`);
                    });
                });
            }

            // ============================================
            // LOAD SOFTWARE TYPES
            // ============================================
            function loadSoftwareTypes() {
                $.get('/ajax/software-types', function(data) {
                    $('#software_type_id').empty().append('<option value="">- Pilih Software -</option>');
                    data.forEach(s => {
                        $('#software_type_id').append(
                            `<option value="${s.id}">${s.software_name}</option>`);
                    });
                });
            }

            // ============================================
            // HANDLE DELETE
            // ============================================
            $(document).on('click', '.delete', function() {
                var ticketNumber = $(this).data('ticket');

                Swal.fire({
                    title: 'Hapus Tiket?',
                    text: 'Tiket ' + ticketNumber + ' akan dihapus permanen',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/service-request/' + ticketNumber,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                Swal.fire('Berhasil!', 'Tiket berhasil dihapus',
                                    'success');
                                datatable.ajax.reload();
                                loadStatistics();
                            },
                            error: function(xhr) {
                                Swal.fire('Gagal!', 'Gagal menghapus tiket', 'error');
                            }
                        });
                    }
                });
            });

            // ============================================
            // HANDLE FILTER
            // ============================================
            $('#filterForm').on('submit', function(e) {
                e.preventDefault();
                datatable.ajax.reload();
            });

            $('#reset_filter').on('click', function() {
                $('#filterForm')[0].reset();
                datatable.ajax.reload();
            });

            // ============================================
            // DATATABLE
            // ============================================
            // ============================================
            // DATATABLE
            // ============================================
            var datatable = $('#crudTable').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                ajax: {
                    url: '{!! url()->current() !!}',
                    data: function(d) {
                        d.filter_unit = $('#filter_unit').val();
                        d.filter_category = $('#filter_category').val();
                        d.filter_status = $('#filter_status').val();
                        d.filter_priority = $('#filter_priority').val();
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'ticket_number',
                        name: 'ticket_number'
                    },
                    {
                        data: 'requester',
                        name: 'requester_name'
                    },
                    {
                        data: 'unit',
                        name: 'hospitalUnit.unit_name'
                    },
                    {
                        data: 'issue_title',
                        name: 'issue_title'
                    },
                    {
                        data: 'category',
                        name: 'problemCategory.category_name'
                    },
                    {
                        data: 'device_info',
                        name: 'device_affected',
                        orderable: false
                    },
                    {
                        data: 'severity_level',
                        name: 'severity_level'
                    },
                    {
                        data: 'priority',
                        name: 'priority'
                    },
                    {
                        data: 'status',
                        name: 'ticket_status'
                    },
                    {
                        data: 'sla',
                        name: 'sla_deadline',
                        orderable: false
                    },
                    {
                        data: 'occurrence_time',
                        name: 'occurrence_time'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                order: [
                    [1, 'desc']
                ], // ✅ Sort by ticket_number DESC
                rowCallback: function(row, data) {
                    // Add class based on ticket status
                    var statusClass = 'row-' + data.ticket_status.toLowerCase().replace(/ /g, '-');
                    $(row).addClass(statusClass);
                }
            });
        });
    </script>
@endpush
