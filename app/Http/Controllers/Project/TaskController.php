<?php

namespace App\Http\Controllers\Project;

use Exception;
use App\Models\Modul\Task;
use Illuminate\Http\Request;
use App\Models\Modul\Project;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    public function index(Project $project)
    {
        // $tasks = $project->tasks;  // Ambil semua tugas yang terkait dengan proyek
        $tasks = Task::all();

        return view('pages.modul.task.index', compact('tasks', 'project'));
    }

    public function create(Project $project)
    {
        return view('pages.modul.task.create', compact('project'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|string',
            'project_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            Task::create($request->all());
            return response()->json(['success' => 'Task berhasil dibuat.'], 200);
        } catch (Exception $err) {
            Log::error($err);
            return response()->json(['error' => 'Terjadi kesalahan saat membuat task.'], 500);
        }
    }

    public function edit(Project $project, Task $task)
    {
        return view('pages.modul.task.edit', compact('project', 'task'));
    }


    public function show($id)
    {
        $task = Task::with(['comments.user', 'project'])->findOrFail($id);
        return view('pages.modul.task.show', compact('task'));
    }

    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $obj = Task::findOrFail($request->id);
            $obj->title = $request->title;
            $obj->description = $request->description;
            $obj->start_date = $request->start_date;
            $obj->status = $request->status;
            $obj->save();
            Log::info("Berhasi update data", [$obj]);
            return response()->json(['success' => 'Data ' . $obj->name . ' berhasil diupdate.'], 200);
        } catch (Exception $err) {
            Log::error($err);
        }
    }

    public function destroy($id)
    {
        $data = Task::find($id);

        if ($data) {
            $data->delete();
            return response()->json(['success' => 'Data deleted successfully']);
        } else {
            return response()->json(['error' => 'Data not found'], 404);
        }
    }



    public function addTask()
    {
    }
}
