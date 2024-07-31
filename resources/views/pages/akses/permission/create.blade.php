@extends('layouts.default')
@section('content')
    <div class="page-wrapper">
        <div class="page-content">
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                <div class="ms-auto">
                    <a href="{{ route('permission.index') }}" class="btn btn-primary"><i class="bx bx-undo mr-1"></i></a>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <form action="{{ url('permission') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="nama">Permission Name</label>
                            <input type="text" class="form-control" id="nama" name="name" placeholder="Nama">
                        </div>

                        <div class="form-group mb-3 mt-3">
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
