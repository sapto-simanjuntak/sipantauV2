@extends('layouts.default')

@section('content')
    <div class="page-wrapper">
        <div class="page-content">

            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                <div class="ms-auto">
                    <a href="#" id="new_data" class="btn btn-primary"><i class="bx bx-plus mr-1"></i>Project</a>
                </div>
            </div>
            <h6>Dashboard Proyek</h6>
            <div class="row">
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-header">Total Proyek</div>
                        <div class="card-body">{{ $totalProjects }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-header">Proyek Selesai</div>
                        <div class="card-body">{{ $completedProjects }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-header">Proyek Dalam Proses</div>
                        <div class="card-body">{{ $inProgressProjects }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-header">Proyek Belum Dimulai</div>
                        <div class="card-body">{{ $notStartedProjects }}</div>
                    </div>
                </div>
            </div>
            <div class="mt-4">
                <h6>Laporan Bulanan</h6>
                <a href="{{ route('reports.monthly') }}" class="btn btn-primary">Lihat Laporan Bulanan</a>
            </div>
            {{-- <div class="mt-4">
                <h2>Proyek per Bulan</h2>
                @foreach ($monthlyReports as $month => $projects)
                    <h3>{{ $month }}</h3>
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
                @endforeach
            </div> --}}

            <div class="row mt-4">
                <div class="col-md-12">
                    <canvas id="taskChart"></canvas>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('after-js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var ctx = document.getElementById('taskChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Completed', 'In Progress', 'Not Started'],
                    datasets: [{
                        label: 'Tugas',
                        data: [{{ $completedTasks }}, {{ $inProgressTasks }},
                            {{ $notStartedTasks }}
                        ],
                        backgroundColor: ['#4caf50', '#ff9800', '#f44336'],
                        borderColor: ['#388e3c', '#f57c00', '#d32f2f'],
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    </script>
@endpush


{{-- @extends('layouts.default')

@section('content')
    <div class="page-wrapper">
        <div class="page-content">

            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                <div class="ms-auto">
                    <a href="#" id="new_data" class="btn btn-primary"><i class="bx bx-plus mr-1"></i>Project</a>
                </div>
            </div>
            <h1>Dashboard Proyek</h1>
            <div class="row">
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-header">Total Proyek</div>
                        <div class="card-body">{{ $totalProjects }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-header">Proyek Selesai</div>
                        <div class="card-body">{{ $completedProjects }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-header">Proyek Dalam Proses</div>
                        <div class="card-body">{{ $inProgressProjects }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-header">Proyek Belum Dimulai</div>
                        <div class="card-body">{{ $notStartedProjects }}</div>
                    </div>
                </div>
            </div>
            <div class="mt-4">
                <h2>Laporan Bulanan</h2>
                <a href="{{ route('reports.monthly') }}" class="btn btn-primary">Lihat Laporan Bulanan</a>
            </div>
            <div class="mt-4">
                <h2>Proyek per Bulan</h2>
                @foreach ($monthlyReports as $month => $projects)
                    <h3>{{ $month }}</h3>
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
                @endforeach
            </div>

            <div class="row mt-4">
                <div class="col-md-12">
                    <canvas id="taskChart"></canvas>
                </div>
            </div>
        </div>
    @endsection

    @push('after-js')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var ctx = document.getElementById('taskChart').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ['Completed', 'In Progress', 'Not Started'],
                        datasets: [{
                            label: 'Tugas',
                            data: [{{ $completedTasks }}, {{ $inProgressTasks }},
                                {{ $notStartedTasks }}
                            ],
                            backgroundColor: ['#4caf50', '#ff9800', '#f44336'],
                            borderColor: ['#388e3c', '#f57c00', '#d32f2f'],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            });
        </script>
    @endpush --}}


{{-- @extends('layouts.default')

@section('content')
    <div class="page-wrapper">
        <div class="page-content">

            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                <div class="ms-auto">
                    <a href="#" id="new_data" class="btn btn-primary"><i class="bx bx-plus mr-1"></i>Project</a>
                </div>
            </div>
            <h1>Dashboard</h1>
            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">Proyek</div>
                        <div class="card-body">
                            <p>Total Proyek: {{ $projects->count() }}</p>
                            <p>Proyek Aktif: {{ $userProjects->count() }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">Tugas</div>
                        <div class="card-body">
                            <p>Total Tugas: {{ $taskCount }}</p>
                            <p>Tugas Selesai: {{ $completedTasks }}</p>
                            <p>Tugas Dalam Proses: {{ $inProgressTasks }}</p>
                            <p>Tugas Belum Dimulai: {{ $notStartedTasks }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">Proyek Saya</div>
                        <div class="card-body">
                            <ul>
                                @foreach ($userProjects as $project)
                                    <li>{{ $project->name }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-12">
                    <canvas id="taskChart"></canvas>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('after-js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var ctx = document.getElementById('taskChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Completed', 'In Progress', 'Not Started'],
                    datasets: [{
                        label: 'Tugas',
                        data: [{{ $completedTasks }}, {{ $inProgressTasks }},
                            {{ $notStartedTasks }}
                        ],
                        backgroundColor: ['#4caf50', '#ff9800', '#f44336'],
                        borderColor: ['#388e3c', '#f57c00', '#d32f2f'],
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    </script>
@endpush --}}
