@extends('layouts.default')
@section('content')
    <div class="page-wrapper">
        <div class="page-content">
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                <div class="ms-auto">
                    <a href="{{ route('role.create') }}" class="btn btn-primary"><i class="bx bx-plus mr-1"></i>Roles</a>
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
@endsection
@push('after-js')
    <!-- Accordion js -->
    {{-- <script type="text/javascript" src="{{ asset('theme\files\assets\pages\accordion\accordion.js') }}"></script> --}}
    <script>
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
