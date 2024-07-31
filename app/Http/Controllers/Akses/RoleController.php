<?php

namespace App\Http\Controllers\Akses;

use Illuminate\Http\Request;
use App\Utils\PermissionHelper;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Exception;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $query = Role::query();
            return DataTables::of($query)
                ->addColumn('action', function ($role) {
                    return '
                   <div class="d-none d-sm-flex">
                     <a class="btn btn-sm btn btn-success m-1" href="' . url('role/' . $role->id . '/give-permissions') . '">Add Permission</a>
                     <div class="dropdown m-1">
						<button class="btn btn-sm btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"><b><i class="bx bx-cog fs-5 bx-spin"></i></b></button>
							<ul class="dropdown-menu">
                                <li>
                                    <a href="#" class="dropdown-item show_modal_edit" data-obj="' . htmlspecialchars(json_encode($role), ENT_QUOTES, 'UTF-8') . '">Edit</a>
                                </li>
                                <li>
                                    <a href="#" class="dropdown-item delete" data-id="' . $role->id . '">Hapus</a>
                                </li>
							</ul>
						</div>
                    </div>
                   </div>
                    ';
                })
                // <li><a class="dropdown-item" href="' . url('role/' . $role->id . '/give-permissions') . '">Add Permission</a></li>

                ->addColumn('permission', function ($roles) {
                    return $roles->permissions->count() . ' Permissions';
                })
                // ->addColumn('addPer', function ($adper) {
                //     return '<a class="btn btn-sm btn btn-success" href="' . url('role/' . $adper->id . '/give-permissions') . '">Add Permission</a>';
                // })
                ->rawColumns(['action', 'permission', 'addPer'])
                ->make();
        }
        return view('pages.akses.roles.index');

        // $roles = Role::all();
        // return view('role.index', compact('roles'));
    }
    public function create()
    {
        return view('pages.akses.roles.create');
    }
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:roles|max:255',
        ], [
            'name.required' => 'Nama Roles harus diisi.',
            'name.unique' => 'Nama Roles sudah ada.',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $obj = new Role();
            $obj->name = $request->name;
            $obj->save();
            Log::info('Berhasi menyimpan data', [$obj]);
            return response()->json(['success' => 'Data ' . $obj->name . 'berhasil disimpan.'], 200);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Data ' . $th . 'Gagal.']);
        }

        // $this->validate($request, [
        //     'name' => 'required|unique:roles|max:255',
        // ]);
        // $role = new Role;
        // $role->name = $request->name;
        // $role->save();
        // return redirect()->route('role.index');
    }


    public function show($id)
    {
        //
    }
    public function edit($id)
    {
        $role = Role::findOrFail($id);
        return view('pages.akses.roles.edit', compact('role'));
    }


    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'name' => 'required|unique:roles',
        ], [
            'name.required' => 'Nama bidang harus diisi.',
            'name.unique' => 'Nama bidang sudah ada.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }


        try {
            $obj = Role::findOrFail($request->id);
            $obj->name = $request->name;
            $obj->save();
            Log::info("Berhasi update data", [$obj]);
            return response()->json(['success' => 'Data ' . $obj->name . ' berhasil diupdate.'], 200);
        } catch (Exception $err) {
            Log::error($err);
        }
    }


    public function destroy($id)
    {
        $data = Role::find($id);

        if ($data) {
            $data->delete();
            return response()->json(['success' => 'Data deleted successfully']);
        } else {
            return response()->json(['error' => 'Data not found'], 404);
        }
    }

    // public function addPermissionToRole($roleId)
    // {
    //     $permissions = Permission::get();

    //     $role = Role::findOrFail($roleId);

    //     $rolePermissions = DB::table('role_has_permissions')
    //         ->where('role_has_permissions.role_id', $role->id)
    //         ->pluck('role_has_permissions.permission_id', 'role_has_permissions.permission_id')
    //         ->all();

    //     return view('pages.akses.roles.add-permission', compact('role', 'permissions', 'rolePermissions'));
    // }

    public function addPermissionToRole($roleId)
    {
        $role = Role::findOrFail($roleId);
        $permissions = Permission::all();
        $rolePermissions = $role->permissions->pluck('name')->toArray();

        // dd(compact('role', 'permissions', 'rolePermissions'));

        return view('pages.akses.roles.add-permission', compact('role', 'permissions', 'rolePermissions'));
    }


    public function givePermissionToRole(Request $request, $roleId)
    {
        $role = Role::findOrFail($roleId);
        $role->syncPermissions($request->permission);
        return redirect()->route('role.index');
    }
}
