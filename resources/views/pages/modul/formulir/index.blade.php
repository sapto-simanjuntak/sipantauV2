@extends('layouts.default')

@push('after-style')
    <style>
        /* Custom CSS for row background colors */
        .row-not-started {
            background-color: #bfdaff;
            /* Light blue for Not Started */
        }

        .row-in-progress {
            background-color: #ffd396;
            /* Light yellow for In Progress */
        }

        .row-completed {
            background-color: #74b383;
            /* Light green for Completed */
        }

        .row-cancelled {
            background-color: #fbadb3;
            /* Light red for Cancelled */
        }

        .row-default {
            background-color: #f0f0f0;
            /* Light gray for Default */
        }
    </style>
@endpush

@section('content')
    <div class="page-wrapper">
        <div class="page-content">
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                <div class="ms-auto">
                    <a href="#" id="new_data" class="btn btn-primary btn-sm"><i class="bx bx-plus mr-1"></i>Add
                        Formulir Pengajuan
                    </a>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="crudTable">
                            <thead>
                                <tr>
                                    <th width="20">No </th>
                                    <th width="200">Name</th>
                                    <th width="200">Description</th>
                                    <th width="">Status</th>
                                    <th width="">Pemohon</th>
                                    <th width="">Validated</th>
                                    <th width="">Validated By</th>
                                    <th width="">Validated Date</th>
                                    <th width="">Start Date</th>
                                    <th width="">End Date</th>
                                    <th width="">PIC IT</th>
                                    <th width="150">Action</th>
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
        'modalId' => 'modal-formulir',
        'modalTitle' => 'Form Pembuatan/Pengembangan Sistem',
        'size' => 'lg',
        'content' => view('partials.modals.content.formulir.create'),
    ])

    {{--
    @include('partials.modals.index', [
        'modalId' => 'modal-project-edit',
        'modalTitle' => 'Edit Master Project',
        'size' => 'lg',
        'content' => view('partials.modals.content.project.edit'),
    ])

    @include('partials.modals.index', [
        'modalId' => 'modal-pic',
        'modalTitle' => 'Add PIC Project',
        'size' => '',
        'content' => view('partials.modals.content.project.pic'),
    ]) --}}

    @include('partials.notification.index')

    {{-- @include('partials.select2.index') --}}
@endsection


@push('after-js')
    <script>
        $(function() {
            // var statuses = @json($statuses);
            // var users = @json($users);

            $('#new_data').click(function() {
                // var statusSelect = $('#status');
                // statusSelect.empty();

                // $.each(statuses, function(value, label) {
                //     statusSelect.append('<option value="' + value + '">' + label + '</option>');
                // });

                $('#modal-formulir').modal('show');

                $('#form').off('submit').submit(function(event) {
                    event.preventDefault();
                    var formData = $(this).serialize();
                    $.ajax({
                        url: '{{ route('formulir.store') }}',
                        type: 'POST',
                        data: formData,
                        dataType: 'json',
                        success: function(response) {
                            round_success_noti(response.success);
                            $('#modal-formulir').modal('hide');
                            $('#crudTable').DataTable().ajax.reload();
                            $('#form')[0].reset();
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
                    });
                });
            })

            // $(document).on('click', '.show_modal_edit', function() {
            //     var statuses = @json($statuses);
            //     // console.log(statuses); // Pastikan data status tersedia di sini

            //     $('#modal-project-edit').modal('show');

            //     var obj = $(this).data('obj');
            //     // console.log(obj); // Tambahkan log ini untuk memastikan data proyek diterima

            //     $('#id').val(obj.id);
            //     $('#name-edit').val(obj.name);
            //     $('#description-edit').val(obj.description);
            //     $('#roles-edit').val(obj.roles);
            //     $('#start-edit').val(obj.start_date);
            //     $('#end-edit').val(obj.end_date);
            //     $('#status-edit').val(obj.status);

            //     // Ambil elemen select status
            //     var statusSelect = $('#status-edit');
            //     statusSelect.empty(); // Kosongkan pilihan yang ada

            //     // Loop melalui data statuses dan tambahkan pilihan ke dalam select
            //     $.each(statuses, function(key, value) {
            //         var selected = (key === obj.status) ? 'selected' : '';
            //         statusSelect.append('<option value="' + key + '" ' + selected + '>' +
            //             value + '</option>');
            //     });

            //     // Event handler untuk form submit
            //     $('#form-edit').off('submit').on('submit', function(event) {
            //         event.preventDefault();
            //         var formData = $(this).serialize();

            //         $.ajax({
            //             url: '{{ url('projects') }}/' + obj.id,
            //             type: 'POST',
            //             data: formData,
            //             dataType: 'json',
            //             success: function(response) {
            //                 // console.log(response);
            //                 round_success_noti(response.success);
            //                 $('#modal-project-edit').modal('hide');
            //                 $('#crudTable').DataTable().ajax.reload();
            //             },
            //             error: function(xhr, status, error) {
            //                 var errors = xhr.responseJSON.errors;
            //                 $.each(errors, function(key, value) {
            //                     $('#' + key + '-edit').addClass('is-invalid')
            //                         .siblings('.invalid-feedback').html(value);
            //                 });

            //                 // Tambahkan kode penanganan error di sini (misalnya, notifikasi kesalahan)
            //             }
            //         });
            //     });
            // });

            // $(document).on('click', '.delete', function(e) {
            //     e.preventDefault();
            //     var dataId = $(this).data('id');
            //     var url = "{{ route('projects.destroy', ':id') }}";
            //     url = url.replace(':id', dataId);

            //     Swal.fire({
            //         title: 'Are you sure?',
            //         text: "You won't be able to revert this!",
            //         icon: 'warning',
            //         showCancelButton: true,
            //         confirmButtonColor: '#3085d6',
            //         cancelButtonColor: '#d33',
            //         confirmButtonText: 'Yes, delete it!'
            //     }).then((result) => {
            //         if (result.isConfirmed) {
            //             $.ajax({
            //                 url: url,
            //                 type: 'DELETE',
            //                 data: {
            //                     "_token": "{{ csrf_token() }}",
            //                 },
            //                 success: function(response) {
            //                     if (response.success) {
            //                         Swal.fire(
            //                             'Deleted!',
            //                             'Your data has been deleted.',
            //                             'success'
            //                         )
            //                         // Reload DataTable
            //                         $('#crudTable').DataTable().ajax.reload();
            //                     } else {
            //                         Swal.fire(
            //                             'Error!',
            //                             response.error,
            //                             'error'
            //                         )
            //                     }
            //                 }
            //             });
            //         }
            //     })
            // });

            // $(document).on('click', '.show_modal_pic', function() {
            //     $('#modal-pic').modal('show');
            //     var obj = $(this).data('obj');

            //     $('#id').val(obj.id);
            //     $('#name-pic').val(obj.name);

            //     $('#form-add-edit').off('submit').on('submit', function(event) {
            //         event.preventDefault();
            //         var formData = $(this).serialize();

            //         $.ajax({
            //             url: '{{ route('projects.addPic') }}',
            //             type: 'POST',
            //             data: formData,
            //             dataType: 'json',
            //             success: function(response) {
            //                 round_success_noti(response.success);
            //                 $('#modal-pic').modal('hide');
            //                 $('#crudTable').DataTable().ajax.reload();
            //                 $('#form')[0].reset();
            //             },
            //             error: function(xhr, status, error) {
            //                 var errors = xhr.responseJSON.errors;
            //                 console.log(errors);
            //                 $.each(errors, function(key, value) {
            //                     $('#' + key).addClass('is-invalid').siblings(
            //                         '.invalid-feedback').html(value);
            //                 });
            //                 round_error_noti(xhr.status + ' ' + xhr.statusText);
            //             }
            //         });
            //     });
            // });

            // $(document).on('click', '.show_modal_pic', function() {
            //     $('#modal-pic').modal('show');
            //     var obj = $(this).data('obj');

            //     $('#project_id').val(obj.id);
            //     $('#name-pic').val(obj.name);

            //     $('#form-add-edit').off('submit').on('submit', function(event) {
            //         event.preventDefault();
            //         var formData = $(this).serialize();

            //         $.ajax({
            //             url: '{{ route('projects.addPic') }}',
            //             type: 'POST',
            //             data: formData,
            //             dataType: 'json',
            //             success: function(response) {
            //                 round_success_noti(response.success);
            //                 $('#modal-pic').modal('hide');
            //                 $('#crudTable').DataTable().ajax.reload();
            //                 $('#form-add-edit')[0].reset();
            //                 $('#pic').val(null).trigger('change');
            //             },
            //             error: function(xhr, status, error) {
            //                 var errors = xhr.responseJSON.errors;
            //                 console.log(errors);
            //                 $.each(errors, function(key, value) {
            //                     $('#' + key).addClass('is-invalid').siblings(
            //                         '.invalid-feedback').html(value);
            //                 });
            //                 round_error_noti(xhr.status + ' ' + xhr.statusText);
            //             }
            //         });
            //     });
            // });

            // $(document).on('click', '.delete_pic', function(e) {
            //     e.preventDefault();

            //     var projectId = $(this).data('obj').id;
            //     var userIds = $(this).data('obj').users.map(user => user.id);

            //     // Konfirmasi penghapusan
            //     Swal.fire({
            //         title: 'Are you sure?',
            //         text: "You will remove PICs from this project!",
            //         icon: 'warning',
            //         showCancelButton: true,
            //         confirmButtonColor: '#3085d6',
            //         cancelButtonColor: '#d33',
            //         confirmButtonText: 'Yes, remove PICs!'
            //     }).then((result) => {
            //         if (result.isConfirmed) {
            //             $.ajax({
            //                 url: '{{ route('projects.deletePic') }}',
            //                 type: 'POST',
            //                 data: {
            //                     "_token": "{{ csrf_token() }}",
            //                     project_id: projectId,
            //                     pic: userIds
            //                 },
            //                 dataType: 'json',
            //                 success: function(response) {
            //                     if (response.success) {
            //                         Swal.fire(
            //                             'Removed!',
            //                             'PICs have been removed from the project.',
            //                             'success'
            //                         );
            //                         $('#crudTable').DataTable().ajax.reload();
            //                     } else {
            //                         Swal.fire(
            //                             'Error!',
            //                             response.error,
            //                             'error'
            //                         );
            //                     }
            //                 },
            //                 error: function(xhr, status, error) {
            //                     var errors = xhr.responseJSON.errors;
            //                     console.log(errors);
            //                     $.each(errors, function(key, value) {
            //                         round_error_noti(value);
            //                     });
            //                 }
            //             });
            //         }
            //     })
            // });





            // $('#pic').select2({
            //     theme: 'bootstrap-5',
            //     ajax: {
            //         url: "{{ url('get-user') }}", // Pastikan ini adalah route yang benar
            //         dataType: 'json',
            //         tags: true,
            //         delay: 250,
            //         data: function(params) {
            //             return {
            //                 search: params.term
            //             };
            //         },
            //         processResults: function(data) {
            //             return {
            //                 results: $.map(data, function(item) {
            //                     return {
            //                         id: item.id,
            //                         text: item.text
            //                     };
            //                 })
            //             };
            //         },
            //         cache: true
            //     },
            //     placeholder: 'Select PIC', // Tambahkan placeholder untuk tampilan yang lebih baik
            //     allowClear: true
            // });
        })

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
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'description',
                    name: 'description'
                },
                {
                    data: 'status',
                    name: 'status'
                },
                {
                    data: 'created_user',
                    name: 'created_user'
                },
                {
                    data: 'validated',
                    name: 'validated'
                },
                {
                    data: 'validated_by',
                    name: 'validated_by'
                },
                {
                    data: 'validated_date',
                    name: 'validated_date'
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
                    data: 'pic',
                    name: 'pic'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searcable: false,
                    width: '2'
                }
            ],
            rowCallback: function(row, data, index) {
                $(row).addClass(data.row_class); // Menambahkan kelas CSS ke baris
            }
        })
    </script>
@endpush
