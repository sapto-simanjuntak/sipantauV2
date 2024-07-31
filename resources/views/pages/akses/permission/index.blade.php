@extends('layouts.default')
@section('content')
    <div class="page-wrapper">
        <div class="page-content">
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                <div class="ms-auto">
                    <a href="#" class="btn btn-primary" id="new_data"><i class="bx bx-plus mr-1"></i>Permission</a>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="crudTable">
                            <thead>
                                <tr>
                                    <th width="20">No </th>
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
        'modalId' => 'modal-permission',
        'modalTitle' => 'Tambah Master Permission',
        'size' => '',
        'content' => view('partials.modals.content.permission.permission_create'),
    ])

    @include('partials.modals.index', [
        'modalId' => 'modal-permission-edit',
        'modalTitle' => 'Edit Master Permission',
        'size' => '',
        'content' => view('partials.modals.content.permission.permission_edit'),
    ])

    @include('partials.notification.index')
@endsection
@push('after-js')
    <script>
        $(function() {
            $('#new_data').click(function() {
                $('#modal-permission').modal('show');
                $('#form').off('submit').submit(function(event) {
                    event.preventDefault();
                    var formData = $(this).serialize();

                    // Hapus pesan error sebelumnya
                    $('.is-invalid').removeClass('is-invalid');
                    $('.invalid-feedback').html('');
                    $.ajax({
                        url: '{{ route('permission.store') }}',
                        type: 'POST',
                        data: formData,
                        dataType: 'json',

                        success: function(response) {
                            round_success_noti(response.success);
                            $('#modal-permission').modal('hide');
                            $('#crudTable').DataTable().ajax.reload();

                            $('#form')[0]
                                .reset(); // Reset the form after successful submission
                            // Clear the select2 field
                        },
                        error: function(xhr, status, error) {
                            var errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                $('#' + key).addClass('is-invalid').siblings(
                                    '.invalid-feedback').html(value);
                            });
                            round_error_noti(xhr.status + ' ' + xhr.statusText);
                        }
                    });
                })
            });


            $(document).on('click', '.show_modal_edit', function() {
                $('#modal-permission-edit').modal('show');

                var obj = $(this).data('obj');
                $('#id').val(obj.id);
                $('#name-edit').val(obj.name);

                $('#form-edit').off('submit').submit(function(event) {
                    event.preventDefault();
                    var formData = $(this).serialize();

                    $.ajax({
                        url: '{{ url('permission') }}/' + obj.id,
                        type: 'POST',
                        data: formData,
                        dataType: 'json',
                        success: function(response) {
                            round_success_noti(response.success);
                            $('#modal-permission-edit').modal('hide');
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
                var url = "{{ route('permission.delete', ':id') }}";
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



{{-- @extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <a href="{{ route('permission.create') }}" class="btn btn-primary">Tambah Permission</a>
                    <div class="card-header">
                        Permission Create

                    </div>

                    <div class="card-body">
                        <table class="table table-bordered" id="" width="100%">
                            <tr>
                                <th>No</th>
                                <th>Name</th>
                                <th>Action</th>
                            </tr>
                            @foreach ($permissions as $key => $permission)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $permission->name }}</td>
                                    <td>
                                        <a href="{{ route('permission.edit', $permission->id) }}"
                                            class="btn btn-primary">Edit</a>
                                        <form action="{{ route('permission.destroy', $permission->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection --}}
