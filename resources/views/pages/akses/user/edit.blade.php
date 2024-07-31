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
                    <form action="{{ url('user/' . $user->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="nama">User Name</label>
                            <input type="text" class="form-control" name="name" value="{{ $user->name }}">
                        </div>
                        <div class="form-group">
                            <label for="nama">Email</label>
                            <input type="text" class="form-control" name="email" value="{{ $user->email }}">
                        </div>
                        <div class="form-group">
                            <label for="nama">Password</label>
                            <input type="text" class="form-control" name="password">
                        </div>
                        <div class="form-group">
                            <label for="nama">Roles</label>
                            <select name="roles[]" class="form-control" multiple id="roles">
                                <option value="">Select Role</option>
                                @foreach ($roles as $key => $role)
                                    <option value="{{ $key }}" {{ in_array($key, $userRole) ? 'selected' : '' }}>
                                        {{ $role }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <button type="submit" class="btn btn-primary">Update</button>
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
        $(document).ready(function() {
            var roles = @json($roles);

            $('#roles').select2({
                theme: 'bootstrap-5',
                data: $.map(roles, function(value, key) {
                    return {
                        id: key,
                        text: value
                    };
                })
            });
        });
    </script>
@endpush
