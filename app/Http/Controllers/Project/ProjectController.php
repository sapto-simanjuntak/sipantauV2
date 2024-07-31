<?php

namespace App\Http\Controllers\Project;

use Exception;
use App\Models\User;
use App\Models\Modul\Task;
use Illuminate\Http\Request;
use App\Models\Modul\Project;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Log;
// use Illuminate\Validation\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\Modul\Project as ModulProject;

class ProjectController extends Controller
{

    public function index()
    {
        $users = User::all();
        $statuses = Project::$statuses; // Ambil data status untuk proyek

        if (request()->ajax()) {
            $query = Project::with('users'); // Include users relation
            return DataTables::of($query)
                ->addColumn('action', function ($pro) {
                    $deletePicButton = $pro->users->isNotEmpty()
                        ? '<li><a href="#" class="dropdown-item delete_pic" data-obj="' . htmlspecialchars(json_encode($pro), ENT_QUOTES, 'UTF-8') . '">Delete PIC</a></li>'
                        : '';
                    return '
                <div class="d-none d-sm-flex">
                 <a class="btn btn-sm m-1 btn btn-warning" href="'  . url('project/' . $pro->id . '/give-task') . '"><i class="lni lni-eye"></i></a>
                  <div class="dropdown m-1">
                     <button class="btn btn-sm btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"> <b>AKSI</b> </button>
                     <ul class="dropdown-menu">
                      ' . $deletePicButton . '
                     <li><a href="#" class="dropdown-item show_modal_edit" data-obj="' . htmlspecialchars(json_encode($pro), ENT_QUOTES, 'UTF-8') . '">Edit</a></li>

                     <li><a href="#" class="dropdown-item delete" data-id="' . $pro->id . '">Hapus</a></li>
                     </ul>
                  </div>
                </div>';
                })
                ->addColumn('pic', function ($pro) {
                    if ($pro->users->isNotEmpty()) {
                        return $pro->users->pluck('name')->implode(', ');
                    } else {
                        return '<a href="#" class="btn btn-success btn-sm show_modal_pic" data-obj="' . htmlspecialchars(json_encode($pro), ENT_QUOTES, 'UTF-8') . '">PIC</a>';
                    }
                })
                ->addColumn('created_user', function ($project) {
                    return $project->user_created ? $project->user_created->name : '-'; // Menampilkan nama pengguna
                })
                ->addColumn('validated_by', function ($project) {
                    return $project->validatedBy ? $project->validatedBy->name : '-'; // Menampilkan nama pengguna
                })
                ->rawColumns(['action', 'pic'])
                ->make();
        }
        return view('pages.modul.project.index', compact('statuses', 'users'));
    }



    // public function create()
    // {

    //     $statuses = Project::$statuses; // Ambil data status untuk proyek
    //     return view('pages.modul.project.create', compact('statuses'));
    // }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            // 'status' => 'required|in:' . implode(',', Project::$statuses),
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {

            Project::create($request->all());

            return response()->json(['success' => 'Proyek  berhasil dibuat.'], 200);
        } catch (Exception $err) {
            Log::error($err);
            return response()->json(['error' => 'Terjadi kesalahan saat membuat pengguna baru.'], 500);
        }
    }

    public function show(Project $project)
    {
        return view('pages.modul.project.show', compact('project'));
    }

    public function edit(Project $project)
    {
        return view('pages.modul.project.edit', compact('project'));
    }

    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $obj = Project::findOrFail($request->id);
            $obj->name = $request->name;
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
        $data = Project::find($id);

        if ($data) {
            $data->delete();
            return response()->json(['success' => 'Data deleted successfully']);
        } else {
            return response()->json(['error' => 'Data not found'], 404);
        }
    }

    public function addPic(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'pic' => 'required|array',
            'pic.*' => 'exists:users,id',
        ]);

        try {
            $projectId = $request->input('project_id');
            $userIds = $request->input('pic');

            // Tambahkan user ke proyek menggunakan syncWithoutDetaching
            $project = Project::findOrFail($projectId);
            $project->users()->syncWithoutDetaching($userIds);

            return response()->json(['success' => 'PICs added successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to add PICs.'], 500);
        }
    }

    public function deletePic(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'pic' => 'required|array',
            'pic.*' => 'exists:users,id',
        ]);

        try {
            $projectId = $request->input('project_id');
            $userIds = $request->input('pic');

            $project = Project::findOrFail($projectId);
            $project->users()->detach($userIds);

            return response()->json(['success' => 'PICs deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete PICs.'], 500);
        }
    }

    public function giveTask($id)
    {
        $project = Project::with('tasks')->find($id);
        $statuses = Task::$taskStatuses; // Ambil status dari model Task

        if (request()->ajax()) {
            $tasks = $project ? $project->tasks : collect();

            return DataTables::of($tasks)
                ->addColumn('action', function ($task) {
                    $detailUrl = route('tasks.show', $task->id);
                    return '<div class="d-flex order-actions">
                            <a href="' . $detailUrl . '" class="bg-linkedin"><i class="lni lni-eye text-white"></i></a>
                            <a href="#" class="ms-1 add-comment bg-success" data-obj="' . htmlspecialchars(json_encode($task), ENT_QUOTES, 'UTF-8') . '"><i class="lni lni-comments-alt text-white"></i></a>
                            <a href="#" data-obj="' . htmlspecialchars(json_encode($task), ENT_QUOTES, 'UTF-8') . '" class="ms-1 show_edit_task bg-warning"><i class="bx bxs-edit text-white"></i></a>
                            <a href="#" class="ms-1 delete bg-danger " data-id="' . $task->id . '"><i class="bx bxs-trash text-white "></i></a>

                             </div>';
                })
                ->addColumn('status', function ($task) {
                    return '<div class="badge rounded-pill ' . $task->status_class . ' p-2 text-uppercase px-3">
                            <i class="bx bxs-circle me-1"></i>' . $task->status_description . '
                        </div>';
                })
                ->rawColumns(['action', 'status']) // Izinkan kolom status untuk render HTML
                ->make();
        }

        return view('pages.modul.project.addTask', compact('project', 'statuses'));
    }
}
