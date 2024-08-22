<?php

namespace App\Http\Controllers\Project;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Modul\Task;
use Illuminate\Http\Request;
use App\Models\Modul\Project;
use Yajra\DataTables\DataTables;
// use Illuminate\Validation\Validator;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\Modul\Project as ModulProject;

class ProjectController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }


    public function index()
    {
        $users = User::all();
        $statuses = Project::$statuses; // Ambil data status untuk proyek
        $validasies = Project::$validated; // Ambil data validasi untuk proyek

        if (request()->ajax()) {
            //
            $currentUser = Auth::user(); // Ambil pengguna yang sedang login
            $query = Project::with('users'); // Sertakan relasi users

            // Filter proyek berdasarkan peran pengguna
            if ($currentUser->hasRole('User')) {
                // Jika peran adalah 'User', hanya ambil proyek yang terkait dengan pengguna
                $query->whereHas('users', function ($q) use ($currentUser) {
                    $q->where('user_id', $currentUser->id);
                });
            } elseif ($currentUser->hasRole('Superadmin')) {
                // Jika peran adalah 'Superadmin', ambil semua proyek
                // Tidak perlu filter di sini
                // $query sudah berisi semua proyek tanpa batasan
            }

            return DataTables::of($query)
                ->addColumn('action', function ($pro) {
                    $deletePicButton = $pro->users->isNotEmpty()
                        ? '<li><a href="#" class="dropdown-item delete_pic" data-obj="' . htmlspecialchars(json_encode($pro), ENT_QUOTES, 'UTF-8') . '">Delete PIC</a></li>'
                        : '';

                    // Cek apakah pengguna adalah Superadmin dan proyek belum divalidasi
                    $validateButton = '';
                    if (Auth::user()->hasRole('Superadmin') && !$pro->validated_by) {
                        $validateButton = '<li><a href="#" class="dropdown-item show_modal_validasi" data-obj="' . htmlspecialchars(json_encode($pro), ENT_QUOTES, 'UTF-8') . '">Validasi</a></li>';
                    }
                    // Tombol "Set Start Date"
                    $setStartDateButton = '';
                    if ($pro->users->isNotEmpty() && Auth::user()->hasAnyRole(['User', 'Superadmin', 'Admin'])) {
                        $setStartDateButton = '<li><a href="#" class="dropdown-item edit_set_start_date" data-obj="' . htmlspecialchars(json_encode($pro), ENT_QUOTES, 'UTF-8') . '">Set Start Date</a></li>';
                    }


                    return '
                        <div class="d-none d-sm-flex">
                            <a class="btn btn-sm m-1 btn btn-warning" href="'  . url('project/' . $pro->id . '/give-task') . '"><i class="lni lni-eye"></i></a>
                            <div class="dropdown m-1">
                                <button class="btn btn-sm btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"> <b>AKSI</b> </button>
                                <ul class="dropdown-menu">
                                    ' . ($pro->users->isNotEmpty() && Auth::user()->hasRole('Superadmin') ? $deletePicButton : '') . '

                                    ' . (Auth::user()->hasAnyRole(['Superadmin', 'User']) ? '<li><a href="#" class="dropdown-item show_modal_edit" data-obj="' . htmlspecialchars(json_encode($pro), ENT_QUOTES, 'UTF-8') . '">Edit</a></li>' : '') . '

                                    ' . (Auth::user()->hasRole('Superadmin') ? '<li><a href="#" class="dropdown-item delete" data-id="' . $pro->id . '">Delete Project</a></li>' : '') . '

                                    ' . $validateButton . '
                                     ' . $setStartDateButton . '
                                </ul>
                            </div>
                        </div>';
                })

                ->addColumn('start_date', function ($pro) {
                    // Format tanggal jika sudah diisi
                    if ($pro->start_date) {
                        try {
                            // Konversi string tanggal menjadi objek Carbon
                            $startDate = Carbon::parse($pro->start_date);
                            // Format tanggal menjadi "Tanggal Bulan Tahun"
                            return $startDate->format('d F Y');
                        } catch (\Exception $e) {
                            // Tangani kesalahan jika parsing gagal
                            return 'Tanggal Tidak Valid';
                        }
                    }
                    // Cek apakah pengguna memiliki peran 'User' atau 'Superadmin' dan `pic` terisi
                    if ($pro->users->isNotEmpty() && Auth::user()->hasAnyRole(['User', 'Superadmin'])) {
                        return '<a href="#" class="btn btn-info btn-sm set_start_date" data-obj="' . htmlspecialchars(json_encode($pro), ENT_QUOTES, 'UTF-8') . '">Set Start Date</a>';
                    }
                    return '-'; // Jika `pic` tidak terisi atau pengguna tidak memiliki peran yang sesuai
                })
                ->addColumn('end_date', function ($pro) {
                    if ($pro->end_date) {
                        $endDate = Carbon::parse($pro->end_date);
                        // Format tanggal menjadi "Tanggal Bulan Tahun"
                        return $endDate->format('d F Y');
                    }
                    return '-'; // Jika `pic` tidak terisi
                })
                ->addColumn('pic', function ($pro) {
                    if ($pro->users->isNotEmpty()) {
                        // return $pro->users->pluck('name')->implode(', ');
                        return $pro->users->map(function ($user) {
                            return '<span class="badge bg-gradient-moonlit me-1">' . htmlspecialchars($user->name, ENT_QUOTES, 'UTF-8') . '</span>';
                        })->implode(' ');
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
                ->rawColumns(['action', 'start_date', 'end_date', 'pic', 'validated_by'])
                ->make();
        }

        return view('pages.modul.project.index', compact('statuses', 'users', 'validasies'));
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description_before' => 'required|string',
            'description_after' => 'required|string',
            // 'start_date' => 'required|date',
            // 'end_date' => 'required|date|after:start_date'
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
            'description_before' => 'required|string',
            'description_after' => 'required|string',
            // 'start_date' => 'required|date',
            // 'end_date' => 'required|date|after:start_date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }



        try {
            $obj = Project::findOrFail($request->id);
            $obj->name = $request->name;
            $obj->description_before = $request->description_before;
            $obj->description_after = $request->description_after;
            // $obj->start_date = $request->start_date;
            // $obj->status = $request->status;

            // Proses upload file jika ada
            if ($request->hasFile('fileUpload')) {
                // Hapus file lama jika ada
                if ($obj->file_path && Storage::disk('public')->exists($obj->file_path)) {
                    Storage::disk('public')->delete($obj->file_path);
                }

                $file = $request->file('fileUpload');
                $filePath = $file->store('uploads', 'public');
                if ($filePath) {
                    $obj->file_path = $filePath; // Simpan path file ke kolom yang sesuai
                } else {
                    Log::error("Gagal menyimpan file.");
                    return response()->json(['error' => 'Gagal menyimpan file.'], 500);
                }
            }


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
        $project = Project::with('tasks', 'user_created')->find($id);
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
                ->addColumn('comment_count', function ($task) {
                    return $task->comments->count() . ' comments';
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

    public function setStatusproject(Request $request)
    {
        if (!Auth::user()->hasRole('Superadmin')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'id' => 'required|exists:projects,id',
        ]);

        try {
            $projectId = $request->input('id');
            $start_date = $request->input('start_date');
            $end_date = $request->input('end_date');
            $status = $request->input('status');

            $project = Project::findOrFail($projectId);

            // Update status validasi, siapa yang memvalidasi, dan tanggal validasi
            $project->start_date = $start_date;
            $project->end_date = $end_date;
            $project->status = $status;
            $project->save();

            return response()->json(['success' => 'Set Status updated successfully.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update project.'], 500);
        }
    }
}
