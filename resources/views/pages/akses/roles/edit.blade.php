@extends('layouts.default')
@section('content')
    <div class="page-wrapper">
        <div class="page-content">
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                <div class="ms-auto">
                    <a href="{{ route('role.index') }}" class="btn btn-primary"><i class="bx bx-undo mr-1"></i></a>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <form action="{{ url('role/' . $role->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="nama">Role Name</label>
                            <input type="text" class="form-control" id="nama" name="name"
                                value="{{ $role->name }}">
                        </div>
                        <div class="form-group mt-2">
                            <button type="submit" class="btn btn-warning">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
