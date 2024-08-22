<?php

namespace App\Http\Controllers\Project;

use Exception;
use Illuminate\Http\Request;
use App\Models\Modul\Comment;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        // $validator = Validator::make($request->all(), [
        //     'comment' => 'required|string',
        //     'task_id' => 'required',
        //     'user_id' => Auth::id(),
        // ]);

        $validator = Validator::make($request->all(), [
            'comment' => 'required|string',
            'task_id' => 'required|exists:tasks,id',  // pastikan task_id valid
            'user_id' => 'required|exists:users,id', // pastikan user_id valid
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            Comment::create($request->all());
            return response()->json(['success' => 'Comment berhasil dibuat.'], 200);
        } catch (Exception $err) {
            Log::error($err);
            return response()->json(['error' => 'Terjadi kesalahan saat membuat task.'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
