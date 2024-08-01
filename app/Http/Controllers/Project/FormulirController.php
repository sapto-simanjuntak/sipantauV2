<?php

namespace App\Http\Controllers\Project;

use Exception;
use App\Models\User;
use App\Models\Modul\Task;
use App\Models\Master\Unit;
use Illuminate\Http\Request;
use App\Models\Modul\Project;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class FormulirController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $unit = Unit::get();
        // dd($unit);

        $users = User::all();
        $statuses = Project::$statuses; // Ambil data status untuk proyek

        if (request()->ajax()) {
            $query = Project::with('users'); // Include users relation
            return DataTables::of($query)
                ->addColumn('action', function ($pro) {
                    $deletePicButton = $pro->users->isNotEmpty()
                        ? '<li><a href="#" class="dropdown-item delete_pic" data-obj="' . htmlspecialchars(json_encode($pro), ENT_QUOTES, 'UTF-8') . '">Delete PIC</a></li>'
                        : '';
                    return '<div class="d-none d-sm-flex">
                 <a class="btn btn-sm m-1 btn btn-warning" href="'  . url('formulir/' . $pro->id . '/view-task') . '"><i class="lni lni-eye"></i></a>
                </div>';
                })
                ->addColumn('pic', function ($pro) {
                    if ($pro->users->isNotEmpty()) {
                        return $pro->users->pluck('name')->implode(', ');
                    } else {
                        return '-';
                        // return '<a href="#" class="btn btn-success btn-sm show_modal_pic" data-obj="' . htmlspecialchars(json_encode($pro), ENT_QUOTES, 'UTF-8') . '">PIC</a>';

                    }
                })
                ->addColumn('created_user', function ($project) {
                    return $project->user_created ? $project->user_created->name : '-'; // Menampilkan nama pengguna
                })
                ->addColumn('validated_by', function ($project) {
                    return $project->validatedBy ? $project->validatedBy->name : '-'; // Menampilkan nama pengguna
                })

                ->addColumn('row_class', function ($project) {
                    return $this->getRowClass($project->status);
                })
                ->rawColumns(['action', 'pic'])
                ->make();
        }
        return view('pages.modul.formulir.index', compact('statuses', 'users'));
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
    // public function store(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'name' => 'required|string|max:255',
    //         'description' => 'required|string',

    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(['errors' => $validator->errors()], 422);
    //     }

    //     try {
    //         $obj = new Project();
    //         $obj->name = $request->name;
    //         $obj->description = $request->description;
    //         $obj->start_date = null; // Atur null jika tidak ada nilai
    //         $obj->end_date =  null; // Atur null jika tidak ada nilai
    //         $obj->status = Project::STATUS_NOT_STARTED; // Default status
    //         $obj->created_user = auth()->id(); // Pastikan kolom ini sesuai dengan tabel Anda
    //         $obj->validated = Project::STATUS_PENDING;
    //         $obj->validated_by = null; // Pastikan kolom ini sesuai dengan tabel Anda
    //         $obj->save();

    //         return response()->json(['success' => 'Proyek berhasil dibuat.'], 200);
    //     } catch (Exception $err) {
    //         Log::error($err);
    //         return response()->json(['error' => 'Terjadi kesalahan saat membuat proyek baru.'], 500);
    //     }
    // }

    public function store(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description_before' => 'required|string',
            'fileUpload' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048', // Validasi file
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            // Simpan data proyek
            $obj = new Project();
            $obj->name = $request->name;
            $obj->description_before = $request->description_before;
            $obj->description_after = $request->description_after;
            $obj->start_date = null; // Atur null jika tidak ada nilai
            $obj->end_date = null; // Atur null jika tidak ada nilai
            $obj->status = Project::STATUS_NOT_STARTED; // Default status
            $obj->created_user = auth()->id(); // Pastikan kolom ini sesuai dengan tabel Anda
            $obj->validated = Project::STATUS_PENDING;
            $obj->validated_by = null; // Pastikan kolom ini sesuai dengan tabel Anda

            // Proses upload file jika ada
            if ($request->hasFile('fileUpload')) {
                $file = $request->file('fileUpload');
                $filePath = $file->store('uploads', 'public'); // Simpan file ke folder 'uploads' dalam disk 'public'
                $obj->file_path = $filePath; // Simpan path file ke kolom yang sesuai
            }

            $obj->save();

            return response()->json(['success' => 'Proyek berhasil dibuat.'], 200);
        } catch (Exception $err) {
            Log::error($err);
            return response()->json(['error' => 'Terjadi kesalahan saat membuat proyek baru.'], 500);
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

    protected function getRowClass($status)
    {
        switch ($status) {
            case Project::STATUS_NOT_STARTED:
                return 'row-not-started'; // Custom CSS class for "Not Started"
            case Project::STATUS_IN_PROGRESS:
                return 'row-in-progress'; // Custom CSS class for "In Progress"
            case Project::STATUS_COMPLETED:
                return 'row-completed'; // Custom CSS class for "Completed"
            case Project::STATUS_CANCELLED:
                return 'row-cancelled'; // Custom CSS class for "Cancelled"
            default:
                return 'row-default'; // Default class
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

        return view('pages.modul.formulir.Task', compact('project', 'statuses'));
    }
}
