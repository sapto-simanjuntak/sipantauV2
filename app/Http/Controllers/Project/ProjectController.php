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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Modul\Project as ModulProject;

class ProjectController extends Controller
{
    public function index()
    {
        $users = User::all();
        $statuses = Project::$statuses; // Ambil data status untuk proyek
        $validasies = Project::$validated; // Ambil data validasi untuk proyek

        if (request()->ajax()) {
            $query = Project::with('users'); // Sertakan relasi users

            return DataTables::of($query)
                ->addColumn('action', function ($pro) {
                    $deletePicButton = $pro->users->isNotEmpty()
                        ? '<li><a href="#" class="dropdown-item delete_pic" data-obj="' . htmlspecialchars(json_encode($pro), ENT_QUOTES, 'UTF-8') . '">Hapus PIC</a></li>'
                        : '';

                    // Cek apakah pengguna adalah Superadmin dan proyek belum divalidasi
                    $validateButton = '';
                    if (Auth::user()->hasRole('Superadmin') && !$pro->validated_by) {
                        $validateButton = '<li><a href="#" class="dropdown-item show_modal_validasi" data-obj="' . htmlspecialchars(json_encode($pro), ENT_QUOTES, 'UTF-8') . '">Validasi</a></li>';
                    }
                    return '
                        <div class="d-none d-sm-flex">
                            <a class="btn btn-sm m-1 btn btn-warning" href="'  . url('project/' . $pro->id . '/give-task') . '"><i class="lni lni-eye"></i></a>
                            <div class="dropdown m-1">
                                <button class="btn btn-sm btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"> <b>AKSI</b> </button>
                                <ul class="dropdown-menu">
                                    ' . ($pro->users->isNotEmpty() && Auth::user()->hasRole('Superadmin') ? $deletePicButton : '') . '

                                    ' . (Auth::user()->hasAnyRole(['Superadmin', 'User']) ? '<li><a href="#" class="dropdown-item show_modal_edit" data-obj="' . htmlspecialchars(json_encode($pro), ENT_QUOTES, 'UTF-8') . '">Edit</a></li>' : '') . '

                                    ' . (Auth::user()->hasRole('Superadmin') ? '<li><a href="#" class="dropdown-item delete" data-id="' . $pro->id . '">Hapus</a></li>' : '') . '

                                    ' . $validateButton . '
                                </ul>
                            </div>
                        </div>';
                })
                ->addColumn('pic', function ($pro) {
                    if ($pro->users->isNotEmpty()) {
                        return $pro->users->pluck('name')->implode(', ');
                    } elseif (Auth::user()->hasRole('Superadmin')) {
                        return '<a href="#" class="btn btn-success btn-sm show_modal_pic" data-obj="' . htmlspecialchars(json_encode($pro), ENT_QUOTES, 'UTF-8') . '">PIC</a>';
                    } else {
                        return '-'; // Untuk pengguna selain Superadmin
                    }
                })
                ->addColumn('created_user', function ($project) {
                    return $project->user_created ? $project->user_created->name : '-'; // Menampilkan nama pengguna
                })
                ->addColumn('validated_by', function ($project) {
                    if ($project->validatedBy) {
                        return $project->validatedBy->name; // Menampilkan nama pengguna jika ada
                    } else {
                        // Menampilkan status berdasarkan peran pengguna
                        if (Auth::user()->hasRole('Superadmin')) {
                            return '<a href="#" class="btn btn-primary btn-sm show_modal_validasi" data-obj="' . htmlspecialchars(json_encode($project), ENT_QUOTES, 'UTF-8') . '">Menunggu Validasi</a>';
                        } else {
                            return 'Menunggu Validasi';
                        }
                    }
                })
                ->rawColumns(['action', 'pic', 'validated_by'])
                ->make();
        }

        return view('pages.modul.project.index', compact('statuses', 'users', 'validasies'));
    }




    // public function index()
    // {
    //     $users = User::all();
    //     $statuses = Project::$statuses; // Ambil data status untuk proyek
    //     $validasies = Project::$validated; // Ambil data validasi untuk proyek

    //     if (request()->ajax()) {
    //         $query = Project::with('users'); // Include users relation

    //         return DataTables::of($query)
    //             ->addColumn('action', function ($pro) {
    //                 $deletePicButton = $pro->users->isNotEmpty()
    //                     ? '<li><a href="#" class="dropdown-item delete_pic" data-obj="' . htmlspecialchars(json_encode($pro), ENT_QUOTES, 'UTF-8') . '">Delete PIC</a></li>'
    //                     : '';

    //                 // Cek apakah pengguna adalah Superadmin
    //                 $validateButton = '';
    //                 if (Auth::user()->hasRole('Superadmin') && !$pro->validated_by) {
    //                     $validateButton = '<li><a href="#" class="dropdown-item show_modal_validasi" data-obj="' . htmlspecialchars(json_encode($pro), ENT_QUOTES, 'UTF-8') . '">Validate</a></li>';
    //                 }

    //                 return '
    //             <div class="d-none d-sm-flex">
    //                 <a class="btn btn-sm m-1 btn btn-warning" href="'  . url('project/' . $pro->id . '/give-task') . '"><i class="lni lni-eye"></i></a>
    //                 <div class="dropdown m-1">
    //                     <button class="btn btn-sm btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"> <b>AKSI</b> </button>
    //                     <ul class="dropdown-menu">
    //                         ' . $deletePicButton . '
    //                         <li><a href="#" class="dropdown-item show_modal_edit" data-obj="' . htmlspecialchars(json_encode($pro), ENT_QUOTES, 'UTF-8') . '">Edit</a></li>
    //                         <li><a href="#" class="dropdown-item delete" data-id="' . $pro->id . '">Hapus</a></li>
    //                         ' . $validateButton . '
    //                     </ul>
    //                 </div>
    //             </div>';
    //             })
    //             ->addColumn('pic', function ($pro) {
    //                 if ($pro->users->isNotEmpty()) {
    //                     return $pro->users->pluck('name')->implode(', ');
    //                 } else {
    //                     return '<a href="#" class="btn btn-success btn-sm show_modal_pic" data-obj="' . htmlspecialchars(json_encode($pro), ENT_QUOTES, 'UTF-8') . '">PIC</a>';
    //                 }
    //             })
    //             ->addColumn('created_user', function ($project) {
    //                 return $project->user_created ? $project->user_created->name : '-'; // Menampilkan nama pengguna
    //             })
    //             ->addColumn('validated_by', function ($project) {
    //                 if ($project->validatedBy) {
    //                     return $project->validatedBy->name; // Menampilkan nama pengguna jika ada
    //                 } else {
    //                     // Menampilkan status berdasarkan peran pengguna
    //                     if (Auth::user()->hasRole('Superadmin')) {
    //                         return '<a href="#" class="btn btn-success btn-sm show_modal_validasi" data-obj="' . htmlspecialchars(json_encode($project), ENT_QUOTES, 'UTF-8') . '">Validate</a>';
    //                     } else {
    //                         return 'Belum Divalidasi';
    //                     }
    //                 }
    //             })
    //             ->rawColumns(['action', 'pic', 'validated_by'])
    //             ->make();
    //     }

    //     return view('pages.modul.project.index', compact('statuses', 'users', 'validasies'));
    // }



    // public function index()
    // {
    //     $users = User::all();
    //     $statuses = Project::$statuses; // Ambil data status untuk proyek
    //     $validasies = Project::$validated; // Ambil data validasi untuk proyek

    //     if (request()->ajax()) {
    //         $query = Project::with('users'); // Include users relation

    //         return DataTables::of($query)
    //             ->addColumn('action', function ($pro) {
    //                 $deletePicButton = $pro->users->isNotEmpty()
    //                     ? '<li><a href="#" class="dropdown-item delete_pic" data-obj="' . htmlspecialchars(json_encode($pro), ENT_QUOTES, 'UTF-8') . '">Delete PIC</a></li>'
    //                     : '';

    //                 // Tampilkan tombol validate jika user adalah Superadmin
    //                 $validateButton = Auth::user()->hasRole('Superadmin') && !$pro->validated_by
    //                     ? '<li><a href="#" class="dropdown-item show_modal_validasi" data-obj="' . htmlspecialchars(json_encode($pro), ENT_QUOTES, 'UTF-8') . '">Validate</a></li>'
    //                     : '';

    //                 return '
    //             <div class="d-none d-sm-flex">
    //                 <a class="btn btn-sm m-1 btn btn-warning" href="'  . url('project/' . $pro->id . '/give-task') . '"><i class="lni lni-eye"></i></a>
    //                 <div class="dropdown m-1">
    //                     <button class="btn btn-sm btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"> <b>AKSI</b> </button>
    //                     <ul class="dropdown-menu">
    //                         ' . $deletePicButton . '
    //                         <li><a href="#" class="dropdown-item show_modal_edit" data-obj="' . htmlspecialchars(json_encode($pro), ENT_QUOTES, 'UTF-8') . '">Edit</a></li>
    //                         <li><a href="#" class="dropdown-item delete" data-id="' . $pro->id . '">Hapus</a></li>
    //                         ' . $validateButton . '
    //                     </ul>
    //                 </div>
    //             </div>';
    //             })
    //             ->addColumn('pic', function ($pro) {
    //                 if ($pro->users->isNotEmpty()) {
    //                     return $pro->users->pluck('name')->implode(', ');
    //                 } else {
    //                     return '<a href="#" class="btn btn-success btn-sm show_modal_pic" data-obj="' . htmlspecialchars(json_encode($pro), ENT_QUOTES, 'UTF-8') . '">PIC</a>';
    //                 }
    //             })
    //             ->addColumn('created_user', function ($project) {
    //                 return $project->user_created ? $project->user_created->name : '-'; // Menampilkan nama pengguna
    //             })
    //             ->addColumn('validated_by', function ($project) {
    //                 if ($project->validatedBy) {
    //                     return $project->validatedBy->name; // Menampilkan nama pengguna jika ada
    //                 } else {
    //                     return '<a href="#" class="btn btn-success btn-sm show_modal_validasi" data-obj="' . htmlspecialchars(json_encode($project), ENT_QUOTES, 'UTF-8') . '">Validate</a>';
    //                 }
    //             })
    //             ->rawColumns(['action', 'pic', 'validated_by'])
    //             ->make();
    //     }
    //     return view('pages.modul.project.index', compact('statuses', 'users', 'validasies'));
    // }


    // public function index()
    // {
    //     $users = User::all();
    //     $statuses = Project::$statuses; // Ambil data status untuk proyek
    //     $validasies = Project::$validated; // Ambil data validasi untuk proyek


    //     if (request()->ajax()) {
    //         $query = Project::with('users'); // Include users relation
    //         return DataTables::of($query)
    //             ->addColumn('action', function ($pro) {
    //                 $deletePicButton = $pro->users->isNotEmpty()
    //                     ? '<li><a href="#" class="dropdown-item delete_pic" data-obj="' . htmlspecialchars(json_encode($pro), ENT_QUOTES, 'UTF-8') . '">Delete PIC</a></li>'
    //                     : '';
    //                 return '
    //             <div class="d-none d-sm-flex">
    //              <a class="btn btn-sm m-1 btn btn-warning" href="'  . url('project/' . $pro->id . '/give-task') . '"><i class="lni lni-eye"></i></a>
    //               <div class="dropdown m-1">
    //                  <button class="btn btn-sm btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"> <b>AKSI</b> </button>
    //                  <ul class="dropdown-menu">
    //                   ' . $deletePicButton . '

    //                  <li><a href="#" class="dropdown-item show_modal_edit" data-obj="' . htmlspecialchars(json_encode($pro), ENT_QUOTES, 'UTF-8') . '">Edit</a></li>

    //                  <li><a href="#" class="dropdown-item delete" data-id="' . $pro->id . '">Hapus</a></li>
    //                  </ul>
    //               </div>
    //             </div>';
    //             })
    //             ->addColumn('pic', function ($pro) {
    //                 if ($pro->users->isNotEmpty()) {
    //                     return $pro->users->pluck('name')->implode(', ');
    //                 } else {
    //                     return '<a href="#" class="btn btn-success btn-sm show_modal_pic" data-obj="' . htmlspecialchars(json_encode($pro), ENT_QUOTES, 'UTF-8') . '">PIC</a>';
    //                 }
    //             })
    //             ->addColumn('created_user', function ($project) {
    //                 return $project->user_created ? $project->user_created->name : '-'; // Menampilkan nama pengguna
    //             })
    //             ->addColumn('validated_by', function ($project) {
    //                 if ($project->validatedBy) {
    //                     return $project->validatedBy->name; // Menampilkan nama pengguna jika ada
    //                 } else {
    //                     return '<a href="#" class="btn btn-success btn-sm show_modal_validasi" data-obj="' . htmlspecialchars(json_encode($project), ENT_QUOTES, 'UTF-8') . '">Validate</a>';
    //                 }
    //                 // return $project->validatedBy ? $project->validatedBy->name : '-'; // Menampilkan nama pengguna
    //             })
    //             ->rawColumns(['action', 'pic', 'validated_by'])
    //             ->make();
    //     }
    //     return view('pages.modul.project.index', compact('statuses', 'users', 'validasies'));
    // }



    // public function create()
    // {

    //     $statuses = Project::$statuses; // Ambil data status untuk proyek
    //     return view('pages.modul.project.create', compact('statuses'));
    // }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description_before' => 'required|string',
            'description_after' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date'
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
            $obj->start_date = $request->start_date; // Atur null jika tidak ada nilai
            $obj->end_date = $request->end_date; // Atur null jika tidak ada nilai
            $obj->status = $request->status;
            $obj->created_user = auth()->id(); // Pastikan kolom ini sesuai dengan tabel Anda
            $obj->validated = Project::STATUS_PENDING;
            $obj->validated_by = null; // Pastikan kolom ini sesuai dengan tabel Anda

            if ($request->hasFile('fileUpload')) {
                $file = $request->file('fileUpload');
                $filePath = $file->store('uploads', 'public');
                if ($filePath) {
                    $obj->file_path = $filePath;
                } else {
                    Log::error("Gagal menyimpan file.");
                }
            }

            $obj->save();

            return response()->json(['success' => 'Proyek berhasil dibuat.'], 200);
        } catch (Exception $err) {
            Log::error($err);
            return response()->json(['error' => 'Terjadi kesalahan saat membuat proyek baru.'], 500);
        }

        // try {

        //     Project::create($request->all());

        //     return response()->json(['success' => 'Proyek  berhasil dibuat.'], 200);
        // } catch (Exception $err) {
        //     Log::error($err);
        //     return response()->json(['error' => 'Terjadi kesalahan saat membuat pengguna baru.'], 500);
        // }
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

    public function addValidasi(Request $request)
    {
        if (!Auth::user()->hasRole('Superadmin')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }


        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'validated' => 'required|in:Pending,Approved,Rejected', // Validasi status validasi
        ]);

        try {
            $projectId = $request->input('project_id');
            $validated = $request->input('validated');
            $validatedBy = auth()->id(); // Mendapatkan ID pengguna yang saat ini login
            $validatedDate = now(); // Mendapatkan waktu saat ini

            $project = Project::findOrFail($projectId);

            // Update status validasi, siapa yang memvalidasi, dan tanggal validasi
            $project->validated = $validated;
            $project->validated_by = $validatedBy;
            $project->validated_date = $validatedDate;
            $project->save();

            return response()->json(['success' => 'Project updated successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update project.'], 500);
        }
    }
}
