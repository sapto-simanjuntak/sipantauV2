@extends('layouts.default')

@section('content')
    <div class="page-wrapper">
        <div class="page-content">
            <!-- Header -->
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                <div class="breadcrumb-title pe-3">Dashboard Teknisi</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item">
                                <a href="#"><i class="bx bx-home-alt"></i></a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Tiket Saya</li>
                        </ol>
                    </nav>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <div class="card radius-10">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div>
                                    <p class="mb-0 text-secondary">Assigned</p>
                                    <h4 class="my-1 text-info" id="assignedCount">0</h4>
                                </div>
                                <div class="widgets-icons-2 rounded-circle bg-gradient-info text-white ms-auto">
                                    <i class="bx bx-task"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card radius-10">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div>
                                    <p class="mb-0 text-secondary">In Progress</p>
                                    <h4 class="my-1 text-warning" id="progressCount">0</h4>
                                </div>
                                <div class="widgets-icons-2 rounded-circle bg-gradient-warning text-white ms-auto">
                                    <i class="bx bx-time"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card radius-10">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div>
                                    <p class="mb-0 text-secondary">Resolved</p>
                                    <h4 class="my-1 text-success" id="resolvedCount">0</h4>
                                </div>
                                <div class="widgets-icons-2 rounded-circle bg-gradient-success text-white ms-auto">
                                    <i class="bx bx-check-circle"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card radius-10">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div>
                                    <p class="mb-0 text-secondary">Total</p>
                                    <h4 class="my-1" id="totalCount">0</h4>
                                </div>
                                <div class="widgets-icons-2 rounded-circle bg-gradient-moonlit text-white ms-auto">
                                    <i class="bx bx-list-ul"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="technicianTable">
                            <thead>
                                <tr>
                                    <th width="20">No</th>
                                    <th width="150">No Tiket</th>
                                    <th>Judul Masalah</th>
                                    <th width="120">Pelapor</th>
                                    <th width="120">Kategori</th>
                                    <th width="100">Prioritas</th>
                                    <th width="100">Status</th>
                                    <th width="120">Assigned</th>
                                    <th width="150">Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Update Status -->
    @include('pages.modul.technician.update-status')
@endsection

@push('after-js')
    <script>
        $(function() {
            // ============================================
            // DATATABLE
            // ============================================
            var datatable = $('#technicianTable').DataTable({
                processing: true,
                serverSide: true,
                ordering: true,
                ajax: {
                    url: '{!! url()->current() !!}',
                },
                columns: [{
                        data: null,
                        name: 'auto_no',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row, meta) {
                            var pageInfo = datatable.page.info();
                            return pageInfo.page * pageInfo.length + meta.row + 1;
                        }
                    },
                    {
                        data: 'ticket_number',
                        name: 'ticket_number'
                    },
                    {
                        data: 'issue_title',
                        name: 'issue_title'
                    },
                    {
                        data: 'reporter',
                        name: 'reporter',
                        orderable: false
                    },
                    {
                        data: 'category',
                        name: 'category',
                        orderable: false
                    },
                    {
                        data: 'severity_level',
                        name: 'severity_level'
                    },
                    {
                        data: 'status',
                        name: 'ticket_status'
                    },
                    {
                        data: 'assigned_at',
                        name: 'assigned_at'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                drawCallback: function(settings) {
                    updateStats(settings.json);
                }
            });

            // ============================================
            // UPDATE STATS
            // ============================================
            function updateStats(data) {
                // Hitung dari data yang ada
                // Implementasi sederhana, bisa diperbaiki dengan query terpisah
                var assigned = 0,
                    progress = 0,
                    resolved = 0,
                    total = 0;

                if (data && data.data) {
                    total = data.recordsTotal;
                    // Stats lebih detail bisa ditambahkan via AJAX terpisah
                }

                $('#assignedCount').text(assigned);
                $('#progressCount').text(progress);
                $('#resolvedCount').text(resolved);
                $('#totalCount').text(total);
            }

            // ============================================
            // UPDATE STATUS BUTTON
            // ============================================
            $(document).on('click', '.update-status', function() {
                var ticketNumber = $(this).data('ticket');
                var currentStatus = $(this).data('status');

                $('#updateTicketNumber').val(ticketNumber);
                $('#currentStatusDisplay').text(currentStatus);

                // Reset form
                $('#updateStatusForm')[0].reset();
                $('#statusSelect').val(currentStatus);

                $('#updateStatusModal').modal('show');
            });

            // ============================================
            // SUBMIT UPDATE STATUS
            // ============================================
            $('#updateStatusForm').submit(function(e) {
                e.preventDefault();

                var ticketNumber = $('#updateTicketNumber').val();
                var formData = new FormData(this);

                $.ajax({
                    url: '/technician/tickets/' + ticketNumber + '/update-status',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    beforeSend: function() {
                        $('#submitUpdateStatus').prop('disabled', true).text('Memproses...');
                    },
                    success: function(response) {
                        $('#updateStatusModal').modal('hide');

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message,
                            timer: 2000
                        }).then(() => {
                            datatable.ajax.reload();
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: xhr.responseJSON?.message || 'Gagal update status'
                        });
                    },
                    complete: function() {
                        $('#submitUpdateStatus').prop('disabled', false).text('Update Status');
                    }
                });
            });
        });
    </script>
@endpush
