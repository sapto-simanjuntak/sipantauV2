@extends('layouts.default')
@php
    use Carbon\Carbon;
@endphp
@push('after-style')
    <style>
        .custom-table {
            border-collapse: collapse;
            width: 100%;
        }

        .custom-table th {
            background-color: #3d6b9c;
            /* Warna latar belakang header tabel */
            color: #fff;
            /* Warna teks header tabel */
            text-align: left;
            padding: 12px;
        }

        .custom-table td {
            padding: 12px;
            vertical-align: top;
        }

        .custom-table tr:nth-child(even) {
            background-color: #f9f9f9;
            /* Warna latar belakang baris genap */
        }

        .custom-table tr:hover {
            background-color: #e9ecef;
            /* Warna latar belakang saat hover */
        }

        .card-header {
            background-color: #007bff;
            /* Warna latar belakang header kartu */
            color: #fff;
            /* Warna teks header kartu */
            font-weight: bold;
            padding: 15px;
            border-bottom: 1px solid #ddd;
        }

        .card-body {
            background-color: #ffffff;
            /* Warna latar belakang kartu */
            padding: 15px;
        }

        .card {
            margin-bottom: 20px;
            /* Jarak antara kartu */
            border: 1px solid #ddd;
            /* Border kartu */
            border-radius: 5px;
            /* Sudut kartu membulat */
        }

        .table-bordered td,
        .table-bordered th {
            border: 1px solid #dee2e6;
            /* Warna border tabel */
        }

        .badge {
            padding: 5px 10px;
            border-radius: 4px;
            color: #fff;
            font-size: 0.875em;
            font-weight: 600;
        }

        .badge-success {
            background-color: #28a745;
        }

        .badge-info {
            background-color: #17a2b8;
        }

        .badge-not-started {
            color: #fff;
            background-color: #ffc107;
            /* Warna kuning untuk status 'Belum Dimulai' */
        }

        /* Kelas untuk status 'in_progress' */
        .badge-in-progress {
            color: #fff;
            background-color: #17a2b8;
            /* Warna biru untuk status 'Dalam Proses' */
        }

        /* Kelas untuk status 'completed' */
        .badge-completed {
            color: #fff;
            background-color: #28a745;
            /* Warna hijau untuk status 'Selesai' */
        }
    </style>
@endpush
@section('content')
    <div class="page-wrapper">
        <div class="page-content">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    Detail Pengajuan Proyek : {{ $project->name }}
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered custom-table">
                                        <tr>
                                            <th>Deskripsi Sebelumnya</th>
                                            <td>: </td>
                                            <td>{{ $project->description_before }}</td>
                                        </tr>
                                        <tr>
                                            <th>Deskripsi Setelahnya</th>
                                            <td>: </td>
                                            <td>{{ $project->description_after }}</td>
                                        </tr>
                                        <tr>
                                            <th>Pengaju</th>
                                            <td>: </td>
                                            <td>{{ $project->user_created->name ?? 'User tidak diketahui' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Tanggal Pengajuan</th>
                                            <td>: </td>
                                            <td>{{ Carbon::parse($project->created_at)->format('d F Y') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Status</th>
                                            <td>: </td>
                                            <td>
                                                <span
                                                    class="badge badge-info">{{ \App\Models\Modul\Project::$statuses[$project->status] }}</span>
                                            </td>
                                        </tr>
                                    </table>
                                    @php
                                        $filePath = $project->file_path ?? $project->file_path;
                                        $fileUrl = $filePath ? asset('storage/' . $filePath) : null;
                                    @endphp

                                    @if ($fileUrl)
                                        <a href="{{ $fileUrl }}" target="_blank" class="btn btn-primary btn-sm">
                                            <i class="bi bi-file-earmark-text"></i> Lihat Dokumen
                                        </a>
                                    @else
                                        <p>Tidak ada file yang tersedia.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    Informasi Validasi
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered custom-table">
                                        <tr>
                                            <th>Di Validasi Oleh</th>
                                            <td>:</td>
                                            <td>{{ $project->validatedBy->name ?? 'User tidak diketahui' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Tanggal Validasi</th>
                                            <td>:</td>
                                            <td>{{ Carbon::parse($project->validated_date)->format('d F Y') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Status Validasi</th>
                                            <td>:</td>
                                            <td>
                                                <span
                                                    class="badge badge-success">{{ \App\Models\Modul\Project::$validated[$project->validated] }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Mulai Project</th>
                                            <td>:</td>
                                            <td>{{ Carbon::parse($project->start_date)->format('d F Y') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Selesai Project</th>
                                            <td>:</td>
                                            <td>{{ Carbon::parse($project->end_date)->format('d F Y') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-lg-flex align-items-center mb-4 gap-3">
                        {{-- <div class="position-relative">
                            <input type="text" class="form-control ps-5 radius-30" placeholder="Search Order">
                            <span class="position-absolute top-50 product-show translate-middle-y">
                                <i class="bx bx-search"></i>
                            </span>
                        </div> --}}
                        <div class="ms-auto">
                            <a href="#" id="new_data" data-obj="{{ json_encode($project) }}"
                                class="btn btn-primary radius-30 mt-2 mt-lg-0">
                                <i class="bx bxs-plus-square"></i>Add Task
                            </a>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered" id="crudTable">
                            <thead>
                                <tr>
                                    <th width="30px">#</th>
                                    <th width="100px">Task Name</th>
                                    <th width="100px">Status</th>
                                    <th width="100px">Start Date</th>
                                    <th width="100px">End Date</th>
                                    {{-- <th>View Details</th> --}}
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('partials.modals.index', [
        'modalId' => 'modal-task',
        'modalTitle' => 'Add Task ',
        'size' => 'lg',
        'content' => view('partials.modals.content.task.create'),
    ])

    @include('partials.modals.index', [
        'modalId' => 'modal-task-edit',
        'modalTitle' => 'Edit Task ',
        'size' => 'lg',
        'content' => view('partials.modals.content.task.edit'),
    ])

    @include('partials.modals.index', [
        'modalId' => 'modal-task-comment',
        'modalTitle' => 'Add Comment ',
        'size' => 'lg',
        'content' => view('partials.modals.content.comment.create'),
    ])

    @include('partials.notification.index')

    @include('partials.select2.index')
@endsection

@push('after-js')
    <script>
        $(function() {
            var statuses = @json($statuses);


            // function populateStatusDropdown() {
            var statusSelect = $('#status');
            statusSelect.empty();

            $.each(statuses, function(value, label) {
                statusSelect.append('<option value="' + value + '">' + label + '</option>');
            });
            // }

            $('#new_data').click(function() {
                // Ambil data proyek dari atribut data-obj
                var project = $(this).data('obj');
                // Set nilai pada form modal
                $('#project_id').val(project.id);
                $('#project_name').val(project.name);
                // populateStatusDropdown();
                // Tampilkan modal
                $('#modal-task').modal('show');

                $('#form-add-task').off('submit').on('submit', function(event) {
                    event.preventDefault();
                    var formData = $(this).serialize();
                    // console.log(formData);
                    $.ajax({
                        url: '{{ route('tasks.store') }}',
                        type: 'POST',
                        data: formData,
                        dataType: 'json',
                        success: function(response) {
                            round_success_noti(response.success);
                            $('#modal-task').modal('hide');
                            $('#crudTable').DataTable().ajax.reload();
                            $('#form-add-task')[0]
                                .reset();
                        },
                        error: function(xhr, status, error) {
                            var errors = xhr.responseJSON.errors;
                            console.log(errors);
                            $.each(errors, function(key, value) {
                                $('#' + key).addClass('is-invalid').siblings(
                                    '.invalid-feedback').html(value);
                            });
                            round_error_noti(xhr.status + ' ' + xhr.statusText);
                        }
                    })
                });
            });


            $(document).on('click', '.show_edit_task', function() {
                $('#modal-task-edit').modal('show');
                var obj = $(this).data('obj');
                $('#id').val(obj.id);
                $('#title-edit').val(obj.title);
                $('#description-edit').val(obj.description);
                $('#status-edit').val(obj.status);

                // Ambil elemen select status
                var statusSelect = $('#status-edit');
                statusSelect.empty(); // Kosongkan pilihan yang ada

                // Loop melalui data statuses dan tambahkan pilihan ke dalam select
                $.each(statuses, function(key, value) {
                    var selected = (key === obj.status) ? 'selected' : '';
                    statusSelect.append('<option value="' + key + '" ' + selected + '>' +
                        value + '</option>');
                });

                $('#form-edit').off('submit').submit(function(event) {
                    event.preventDefault();
                    var formData = $(this).serialize();

                    $.ajax({
                        url: '{{ url('tasks') }}/' + obj.id,
                        type: 'POST',
                        data: formData,
                        dataType: 'json',
                        success: function(response) {
                            // console.log(response);
                            round_success_noti(response.success);
                            $('#modal-task-edit').modal('hide');
                            $('#crudTable').DataTable().ajax.reload();
                        },
                        error: function(xhr, status, error) {
                            var errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                $('#' + key + '-edit').addClass('is-invalid')
                                    .siblings(
                                        '.invalid-feedback').html(value);
                            });

                            round_error_noti(xhr.status + ' ' + xhr.statusText);
                        }
                    });
                });
            })

            $(document).on('click', '.delete', function(e) {
                e.preventDefault();
                var dataId = $(this).data('id');
                var url = "{{ route('tasks.destroy', ':id') }}";
                url = url.replace(':id', dataId);

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: url,
                            type: 'DELETE',
                            data: {
                                "_token": "{{ csrf_token() }}",
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire(
                                        'Deleted!',
                                        'Your data has been deleted.',
                                        'success'
                                    )
                                    // Reload DataTable
                                    $('#crudTable').DataTable().ajax.reload();
                                } else {
                                    Swal.fire(
                                        'Error!',
                                        response.error,
                                        'error'
                                    )
                                }
                            }
                        });
                    }
                })
            });

            $(document).on('click', '.add-comment', function() {
                $('#modal-task-comment').modal('show');
                var obj = $(this).data('obj');
                $('#id_task_comment').val(obj.id);
                $('#title_task').val(obj.title);

                $('#form-comment').off('submit').on('submit', function(event) {
                    // alert('berhasil');
                    event.preventDefault();
                    var formData = $(this).serialize();
                    $.ajax({
                        url: '{{ route('comments.store') }}',
                        type: 'POST',
                        data: formData,
                        dataType: 'json',
                        success: function(response) {
                            // console.log(response);
                            round_success_noti(response.success);
                            $('#modal-task-comment').modal('hide');
                            $('#crudTable').DataTable().ajax.reload();
                            $('#form-comment')[0].reset();
                        },
                        error: function(xhr, status, error) {
                            var errors = xhr.responseJSON.errors;
                            console.log(errors);
                            $.each(errors, function(key, value) {
                                $('#' + key).addClass('is-invalid').siblings(
                                    '.invalid-feedback').html(value);
                            });
                            round_error_noti(xhr.status + ' ' + xhr.statusText);
                        }
                    })
                });
            });
        });


        var datatable = $('#crudTable').DataTable({
            processing: true,
            serverSide: true,
            ordering: true,
            ajax: {
                url: '{!! url()->current() !!}',
            },
            columns: [{
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'title',
                    name: 'title'
                },
                {
                    data: 'status',
                    name: 'status'
                },
                {
                    data: 'start_date',
                    name: 'start_date'
                },
                {
                    data: 'end_date',
                    name: 'end_date'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searcable: false,
                    width: '2'
                },
            ]
        })
    </script>
@endpush
