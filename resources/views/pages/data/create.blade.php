@extends('layouts.default')
@section('content')
    <div class="page-wrapper">
        <div class="page-content">

            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                <div class="ms-auto">
                    <a href="{{ route('data.index') }}" class="btn btn-primary"><i class="bx bx-undo mr-1"></i>User</a>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <form action="{{ url('data') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="nama"> Name</label>
                            <input type="text" class="form-control" name="name" placeholder="Nama">
                        </div>
                        <div class="form-group">
                            <label for="nama">Deskripsi</label>
                            <input type="text" class="form-control" name="deskripsi">
                        </div>


                        <div class="form-group mb-3">
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('after-js')
@endpush
