@extends('layouts.default')
@section('content')
    <div class="page-wrapper">
        <div class="page-content">
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                <div class="ms-auto">
                    <a href="{{ route('user.index') }}" class="btn btn-primary"><i class="bx bx-undo mr-1"></i></a>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <form action="{{ url('user') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="nama">User Name</label>
                            <input type="text" class="form-control" name="name" placeholder="Nama">
                        </div>
                        <div class="form-group">
                            <label for="nama">Email</label>
                            <input type="text" class="form-control" name="email">
                        </div>
                        <div class="form-group">
                            <label for="nama">Password</label>
                            <input type="text" class="form-control" name="password">
                        </div>
                        <div class="form-group">
                            <label for="nama">Roles</label>
                            <select class="form-control" name="roles[]" id="roles" multiple></select>
                        </div>

                        <div class="form-group mt-2">
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @include('partials.select2.index')
@endsection

@push('after-js')
    <script>
        $(function() {
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
        });
    </script>
@endpush
