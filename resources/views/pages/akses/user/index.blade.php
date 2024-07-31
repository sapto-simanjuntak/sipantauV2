@extends('layouts.default')
@section('content')
    <div class="page-wrapper">
        <div class="page-content">

            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                <div class="ms-auto">
                    <a href="#" id="new_data" class="btn btn-primary"><i class="bx bx-plus mr-1"></i>User</a>
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
                                    <th width="200">Email</th>
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
        'modalId' => 'modal-user',
        'modalTitle' => 'Tambah Master User',
        'size' => '',
        'content' => view('partials.modals.content.user.user_create'),
    ])

    @include('partials.modals.index', [
        'modalId' => 'modal-user-edit',
        'modalTitle' => 'Edit Master User',
        'size' => '',
        'content' => view('partials.modals.content.user.user_edit'),
    ])


    @include('partials.select2.index')

    @include('partials.notification.index')
@endsection
@push('after-js')
    <script>
        $(function() {
            $('#new_data').click(function() {
                $('#modal-user').modal('show');
                $('#form').off('submit').submit(function(event) {
                    event.preventDefault();
                    var formData = $(this).serialize();
                    $.ajax({
                        url: '{{ route('user.store') }}',
                        type: 'POST',
                        data: formData,
                        dataType: 'json',
                        success: function(response) {
                            round_success_noti(response.success);
                            $('#modal-user').modal('hide');
                            $('#crudTable').DataTable().ajax.reload();
                            $('#form')[0]
                                .reset(); // Reset the form after successful submission
                            $('#roles').val(null).trigger(
                                'change'); // Clear the select2 field
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


            $('#roles').select2({
                theme: 'bootstrap-5',
                ajax: {
                    url: "{{ url('get-roles') }}", // Pastikan ini adalah route yang benar
                    dataType: 'json',
                    tags: true,
                    delay: 250,
                    data: function(params) {
                        return {
                            search: params.term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: $.map(data, function(item) {
                                return {
                                    id: item.id,
                                    text: item.text
                                };
                            })
                        };
                    },
                    cache: true
                },
                placeholder: 'Select Role', // Tambahkan placeholder untuk tampilan yang lebih baik
                allowClear: true
            });


            $(document).on('click', '.show_modal_edit', function() {
                // $('#modal-user-edit').modal('show');

                var userId = $(this).data('obj').id;
                $.ajax({
                    url: '{{ url('user') }}/' + userId + '/edit',
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        var user = response.user;
                        var roles = response.roles;
                        var userRoles = response.userRoles;
                        console.log(userRoles);

                        $('#modal-user-edit').modal('show');
                        $('#id').val(user.id);
                        $('#name-edit').val(user.name);
                        $('#email-edit').val(user.email);

                        var rolesSelect = $('#roles_edit');
                        rolesSelect.empty(); // Clear previous options

                        $.each(roles, function(key, value) {
                            rolesSelect.append('<option value="' + key + '">' + value +
                                '</option>');
                        });

                        rolesSelect.val(userRoles); // Set selected roles
                        rolesSelect.select2({
                            placeholder: "Select roles",
                            allowClear: true,
                            theme: 'bootstrap-5',
                        });
                    },
                    error: function(xhr, status, error) {
                        console.log(xhr.responseText);
                    }
                });

                var obj = $(this).data('obj');
                $('#id').val(obj.id);
                $('#name-edit').val(obj.name);
                $('#email-edit').val(obj.email);
                $('#roles-edit').val(obj.roles);

                $('#form-edit').off('submit').submit(function(event) {
                    event.preventDefault();
                    var formData = $(this).serialize();

                    $.ajax({
                        url: '{{ url('user') }}/' + obj.id,
                        type: 'POST',
                        data: formData,
                        dataType: 'json',
                        success: function(response) {
                            console.log(response);
                            round_success_noti(response.success);
                            $('#modal-user-edit').modal('hide');
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
                var url = "{{ route('user.destroy', ':id') }}";
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
                    data: 'email',
                    name: 'email'
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
