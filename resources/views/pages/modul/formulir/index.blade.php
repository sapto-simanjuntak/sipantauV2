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
                    data: 'description_before',
                    name: 'description_before'
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
