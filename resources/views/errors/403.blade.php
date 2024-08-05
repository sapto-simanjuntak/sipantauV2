@extends('layouts.default')

@section('content')
    <div class="page-wrapper">
        <div class="page-content">
            <div class="container">
                <div class="text-center mt-5">
                    <h1 class="display-1 text-danger">403</h1>
                    <h2 class="mt-4">Forbidden</h2>
                    <p><b>User does not have the right roles to access this page.</b></p>
                    <a href="javascript:history.back()" class="btn btn-primary">Back</a>

                </div>
            </div>
        </div>
    </div>
@endsection
