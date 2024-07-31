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
            padding: 10px;
            border-radius: 10px;
            position: relative;
            clear: both;
            max-width: 80%;
        }

        .comment-left {
            background-color: #e0f7fa;
            border-radius: 10px 10px 10px 0;
            float: left;
            margin-right: auto;
            text-align: left;
        }

        .comment-right {
            background-color: #fff9c4;
            border-radius: 10px 10px 0 10px;
            float: right;
            margin-left: auto;
            text-align: right;
        }

        .comment strong {
            display: block;
            margin-bottom: 5px;
        }

        .comment small {
            display: block;
            margin-top: 5px;
            color: #6c757d;
        }

        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }

        .no-comments {
            text-align: center;
            color: #6c757d;
            font-style: italic;
        }
    </style>
@endpush

@section('content')
    <div class="page-wrapper">
        <div class="page-content">
            <div class="container py-2">
                <h2 class="font-weight-light text-center text-muted py-3">{{ $task->project->name }}</h2>
                <!-- Task Details -->
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
                <!-- Comments Section -->
                <div class="row">
                    <div class="col py-2">
                        <div class="card radius-15">
                            <div class="card-body">
                                <h5 class="font-weight-light text-muted">Comments</h5>
                                <div class="comments clearfix">
                                    @php
                                        // Urutkan komentar berdasarkan tanggal pembuatan
                                        $allComments = $task->comments->sortBy('created_at');
                                        $userId = auth()->user()->id;
                                        $userComments = $allComments->filter(
                                            fn($comment) => $comment->user_id == $userId,
                                        );
                                        $otherComments = $allComments->filter(
                                            fn($comment) => $comment->user_id != $userId,
                                        );
                                    @endphp

                                    @if ($allComments->isEmpty())
                                        <p class="no-comments">Belum ada Komentar</p>
                                    @else
                                        @foreach ($userComments as $comment)
                                            <div class="comment comment-left">
                                                <strong>{{ $comment->user->name }}:</strong>
                                                <p>{{ $comment->comment }}</p>
                                                <small>{{ $comment->created_at->format('d M Y, H:i') }}</small>
                                            </div>
                                        @endforeach

                                        @foreach ($otherComments as $comment)
                                            <div class="comment comment-right">
                                                <strong>{{ $comment->user->name }}:</strong>
                                                <p>{{ $comment->comment }}</p>
                                                <small>{{ $comment->created_at->format('d M Y, H:i') }}</small>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
