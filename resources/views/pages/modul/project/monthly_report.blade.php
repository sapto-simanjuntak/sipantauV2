@extends('layouts.default')

@section('content')
    <div class="page-wrapper">
        <div class="page-content">
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">

            </div>

            <h1>Laporan Proyek</h1>
            <div class="card">
                <div class="card-body">
                    @foreach ($monthlyReports as $month => $projects)
                        <h2>{{ $month }}</h2>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Nama Proyek</th>
                                        <th>Deskripsi</th>
                                        <th>Jumlah Tugas</th>
                                        <th>Tugas Selesai</th>
                                        <th>Tugas Dalam Proses</th>
                                        <th>Tugas Belum Dimulai</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($projects as $project)
                                        <tr>
                                            <td>{{ $project->name }}</td>
                                            <td>{{ $project->description }}</td>
                                            <td>{{ $project->tasks->count() }}</td>
                                            <td>{{ $project->tasks->where('status', 'completed')->count() }}</td>
                                            <td>{{ $project->tasks->where('status', 'in_progress')->count() }}</td>
                                            <td>{{ $project->tasks->where('status', 'not_started')->count() }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection


{{-- @extends('layouts.default')

@section('content')
    <div class="page-wrapper">
        <div class="page-content">
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">

            </div>

            <h1>Laporan Proyek</h1>
            <div class="card">
                <div class="card-body">
                    <h1>Laporan Bulanan</h1>
                    @foreach ($monthlyReports as $month => $projects)
                        <h2>{{ $month }}</h2>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Nama Proyek</th>
                                        <th>Deskripsi</th>
                                        <th>Jumlah Tugas</th>
                                        <th>Tugas Selesai</th>
                                        <th>Tugas Dalam Proses</th>
                                        <th>Tugas Belum Dimulai</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($projects as $project)
                                        <tr>
                                            <td>{{ $project->name }}</td>
                                            <td>{{ $project->description }}</td>
                                            <td>{{ $project->tasks->count() }}</td>
                                            <td>{{ $project->tasks->where('status', 'completed')->count() }}</td>
                                            <td>{{ $project->tasks->where('status', 'in_progress')->count() }}</td>
                                            <td>{{ $project->tasks->where('status', 'not_started')->count() }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection --}}
