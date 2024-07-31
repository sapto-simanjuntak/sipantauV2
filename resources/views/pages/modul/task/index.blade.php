// resources/views/pages/modul/task/index.blade.php

@extends('layouts.default')

@section('content')
    <div class="page-wrapper">
        <div class="page-content">
            <h6>Daftar Tugas untuk Proyek: {{ $project->name }}</h6>
            {{-- <a href="{{ route('projects.tasks.create', $project) }}" class="btn btn-primary">Tambah Tugas</a> --}}
            <table class="table mt-4">
                <thead>
                    <tr>
                        <th>Judul</th>
                        <th>Deskripsi</th>
                        <th>Tanggal Mulai</th>
                        <th>Tanggal Selesai</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tasks as $task)
                        <tr>
                            <td>{{ $task->title }}</td>
                            <td>{{ $task->description }}</td>
                            <td>{{ $task->start_date }}</td>
                            <td>{{ $task->end_date }}</td>
                            <td>{{ $task->status }}</td>
                            <td>
                                <a href="{{ route('tasks.edit', $task) }}" class="btn btn-warning">Edit</a>
                                <form action="{{ route('tasks.destroy', $task) }}" method="POST"
                                    style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
