@extends('layouts.default')
@section('content')
    <div class="page-wrapper">
        <div class="page-content">
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                <div class="ms-auto">
                    <a href="#" class="btn btn-primary" id="new-data"><i class="bx bx-plus mr-1"></i>Roles</a>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="crudTable">
                            <thead>
                                <tr>
                                    <th width="20">No </th>
                                    <th width="200">Roles</th>
                                    <th width="80">Permission</th>
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
        'modalId' => 'modal-roles',
        'modalTitle' => 'Tambah Master Roles',
        'size' => '',
        'content' => view('partials.modals.content.roles.roles_create'),
    ])

    @include('partials.modals.index', [
        'modalId' => 'modal-roles-edit',
        'modalTitle' => 'Edit Master Roles',
        'size' => '',
        'content' => view('partials.modals.content.roles.roles_edit'),
    ])

    @include('partials.notification.index')
@endsection
@push('after-js')
    <script>
        $(function() {
            $('#new-data').click(function() {
                $('#modal-roles').modal('show');

                $('#form').off('submit').submit(function(event) {
                    event.preventDefault();
                    var formData = $(this).serialize();

                    // Hapus pesan error sebelumnya
                    $('.is-invalid').removeClass('is-invalid');
                    $('.invalid-feedback').html('');


                    $.ajax({
                        url: '{{ route('role.store') }}',
                        type: 'POST',
                        data: formData,
                        dataType: 'json',
                        success: function(response) {
                            round_success_noti(response.success);
                            $('#modal-roles').modal('hide');
                            $('#crudTable').DataTable().ajax.reload();
                            $('#form')[0].reset();
                        },
                        error: function(xhr, status, error) {
                            var errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                $('#' + key).addClass('is-invalid').siblings(
                                    '.invalid-feedback').html(value);
                            });
                            round_error_noti(xhr.status + ' ' + xhr.statusText);
                        }

                    })
                })
            });

            $(document).on('click', '.show_modal_edit', function() {
                $('#modal-roles-edit').modal('show');

                var obj = $(this).data('obj');
                $('#id').val(obj.id);
                $('#name-edit').val(obj.name);

                $('#form-edit').off('submit').submit(function(event) {
                    event.preventDefault();
                    var formData = $(this).serialize();

                    $.ajax({
                        url: '{{ url('role') }}/' + obj.id,
                        type: 'POST',
                        data: formData,
                        dataType: 'json',
                        success: function(response) {
                            round_success_noti(response.success);
                            $('#modal-roles-edit').modal('hide');
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
                var url = "{{ route('role.delete', ':id') }}";
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
                    data: 'permission',
                    name: 'permission'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    width: '150px' // Adjust the width accordingly
                },
            ]
        })
    </script>
@endpush
