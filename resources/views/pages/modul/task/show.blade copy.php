// resources/views/pages/modul/task/index.blade.php

@extends('layouts.default')
@push('after-style')
    <style>
        .comments {
            border: 1px solid #ddd;
            padding: 10px;
            max-width: 600px;
            margin: 20px auto;
            background-color: #f9f9f9;
            border-radius: 5px;
        }

        .comment {
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }

        .comment:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .comment-left {
            text-align: left;
            background-color: #e0f7fa;
            border-radius: 10px 10px 10px 0;
            padding: 10px;
            margin-right: auto;
            max-width: 80%;
        }

        .comment-right {
            text-align: right;
            background-color: #fff9c4;
            border-radius: 10px 10px 0 10px;
            padding: 10px;
            margin-left: auto;
            max-width: 80%;
        }

        .comment strong {
            display: block;
            margin-bottom: 5px;
        }
    </style>
@endpush
@section('content')
    <div class="page-wrapper">
        <div class="page-content">
            <div class="container py-2">
                <h2 class="font-weight-light text-center text-muted py-3">{{ $task->project->name }}</h2>
                <!-- timeline item 1 -->
                <div class="row">

                    <div class="col py-2">
                        <div class="card radius-15">
                            <div class="card-body">
                                <div class="float-end text-muted">{{ $task->created_at }}</div>
                                <h4 class="card-title text-muted">{{ $task->title }}</h4>
                                <p class="card-text">{{ $task->description }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <!--/row-->

                <div class="row">
                    <div class="col py-2">
                        <div class="card radius-15">
                            <div class="card-body">
                                <h5 class="font-weight-light text-muted">Comments</h5>
                                {{-- <div class="comments"> --}}
                                @foreach ($task->comments as $comment)
                                    @php
                                        $alignClass = $comment->user->id % 2 == 0 ? 'comment-left' : 'comment-right';
                                    @endphp
                                    <div class="comment {{ $alignClass }}">
                                        <strong>{{ $comment->user->name }}:</strong>
                                        <p>{{ $comment->content }}</p>
                                        <small>{{ $comment->created_at }}</small>
                                    </div>
                                @endforeach
                                {{-- </div> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
