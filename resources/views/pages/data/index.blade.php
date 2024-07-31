@extends('layouts.default')
@section('content')
    @can('Data-View')
        <div class="page-wrapper">
            <div class="page-content">

                <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                    <div class="ms-auto">
                        @canany(['Data-Create'])
                            <a href="#" class="btn btn-primary" id="new_data"><i class="bx bx-plus mr-1"></i>Data</a>
                        @endcanany
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
                                        <th width="200">Deskripsi</th>
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
    @endcan

    @include('partials.modals.index', [
        'modalId' => 'modal-bidang',
        'modalTitle' => 'Tambah Master Bidang',
        'size' => '',
        'content' => view('partials.modals.content.bidang.mstr_bidang_create'),
    ])

    @include('partials.modals.index', [
        'modalId' => 'modal-bidang-edit',
        'modalTitle' => 'Edit Master Bidang',
        'size' => '',
        'content' => view('partials.modals.content.bidang.mstr_bidang_edit'),
    ])
    @include('partials.notification.index')
@endsection
@push('after-js')
    <script>
        $(function() {
            $('#new_data').click(function() {
                $('#modal-bidang').modal('show');
                $('#form').off('submit').submit(function(event) {
                    event.preventDefault();
                    var formData = $(this).serialize();

                    $.ajax({
                        url: '{{ route('data.store') }}',
                        type: 'POST',
                        data: formData,
                        dataType: 'json',
                        success: function(response) {
                            round_success_noti(response.success);
                            $('#modal-bidang').modal('hide');
                            $('#crudTable').DataTable().ajax.reload();
                        },
                        error: function(xhr, status, error) {
                            var errors = xhr.responseJSON.errors;
                            // console.log(errors);
                            $.each(errors, function(key, value) {
                                $('#' + key).addClass('is-invalid').siblings(
                                    '.invalid-feedback').html(value);
                            });
                            round_error_noti(xhr.status + ' ' + xhr.statusText);
                        }
                    });
                });
            })

            $(document).on('click', '.show_modal_edit', function() {
                $('#modal-bidang-edit').modal('show');

                var obj = $(this).data('obj');
                $('#id').val(obj.id);
                $('#name-edit').val(obj.name);
                $('#description-edit').val(obj.deskripsi);

                $('#form-edit').off('submit').submit(function(event) {
                    event.preventDefault();
                    var formData = $(this).serialize();

                    $.ajax({
                        url: '{{ url('data') }}/' + obj.id,
                        type: 'POST',
                        data: formData,
                        dataType: 'json',
                        success: function(response) {
                            round_success_noti(response.success);
                            $('#modal-bidang-edit').modal('hide');
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
                var url = "{{ route('data.delete', ':id') }}";
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
                    data: 'deskripsi',
                    name: 'deskripsi'
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
