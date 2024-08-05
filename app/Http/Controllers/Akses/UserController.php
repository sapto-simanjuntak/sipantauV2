<?php

namespace App\Http\Controllers\Akses;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $query =  User::with('roles');
            return DataTables::of($query)
                ->addColumn('roles', function ($user) {
                    return $user->roles->pluck('name')->implode(', ');
                })
                ->addColumn('action', function ($user) {
                    return '
                    <div class="dropdown">
						<button class="btn btn-sm btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"> <b>AKSI</b> </button>
							<ul class="dropdown-menu">
                                <a href="#" class="dropdown-item show_modal_edit" data-obj="' . htmlspecialchars(json_encode($user), ENT_QUOTES, 'UTF-8') . '">Edit</a>
								    <li>
                                <a href="#" class="dropdown-item delete" data-id="' . $user->id . '">Hapus</a>
                            </li>
							</ul>
						</div>
                    </div>';
                })

                ->rawColumns(['action', 'status', 'roles'])
                ->make();
        }
        return view('pages.akses.user.index');


        // ->addColumn('status', function ($stat) {
        //     return $stat->status == 'active' ? '<div class="badge rounded-pill text-success bg-light-success p-2 text-uppercase px-3">Active</div>' : '<div class="badge rounded-pill text-danger bg-light-danger p-2 text-uppercase px-3">Non Active</div>';
        // })

        // $users = User::all();
        // return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::pluck('name', 'name')->all();
        // return view('users.create', compact('roles'));
        return view('pages.akses.user.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:users|max:255',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|min:6',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);

            $roles = Role::whereIn('id', $request->roles)->get();
            $user->syncRoles($roles);

            return response()->json(['success' => 'User ' . $user->name . ' berhasil dibuat.'], 200);
        } catch (Exception $err) {
            Log::error($err);
            return response()->json(['error' => 'Terjadi kesalahan saat membuat pengguna baru.'], 500);
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
        // $user = User::findOrFail($id);
        // $roles = Role::pluck('name', 'name')->all();
        // $userRole = $user->roles->pluck('name', 'name')->all();

        // return view('pages.akses.user.edit', compact('user', 'roles', 'userRole'));
        $user = User::findOrFail($id);
        $roles = Role::pluck('name', 'name')->all();
        $userRoles = $user->roles->pluck('name')->toArray();

        return response()->json([
            'user' => $user,
            'roles' => $roles,
            'userRoles' => $userRoles
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'nullable|min:8',
            'roles' => 'required|array'
        ], [
            'name.required' => 'Nama bidang harus diisi.',
            'email.required' => 'Email bidang harus diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.min' => 'Password minimal harus terdiri dari 8 karakter.',
            'roles.required' => 'Roles harus diisi.',
            'roles.array' => 'Roles harus berupa array.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $user = User::findOrFail($id);

            $data = [
                'name' => $user->name,
                'email' => $user->email
            ];

            if (!empty($request->password)) {
                $data['password'] = Hash::make($request->password);
            }

            $user->update($data);
            $user->syncRoles($request->roles);

            Log::info("Berhasil update data", [$user]);
            return response()->json(['success' => 'Data ' . $user->name . ' berhasil diupdate.'], 200);
        } catch (Exception $err) {
            Log::error($err);
            return response()->json(['error' => 'Terjadi kesalahan saat mengupdate data.'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $data = User::find($id);

        if ($data) {
            $data->delete();
            return response()->json(['success' => 'Data deleted successfully']);
        } else {
            return response()->json(['error' => 'Data not found'], 404);
        }
    }
}
