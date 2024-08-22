<?php

namespace App\Http\Controllers\Akses;

use Exception;
use Illuminate\Http\Request;
// use App\Models\Akses\Permission;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        if (request()->ajax()) {
            $query = Permission::query();
            return DataTables::of($query)
                ->addColumn('action', function ($per) {
                    return '
                   <div class="d-none d-sm-flex">
                     <div class="dropdown m-1">
						<button class="btn btn-sm btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"> <b>AKSI</b> </button>
							<ul class="dropdown-menu">
							<li>
                                <a href="#" class="dropdown-item show_modal_edit" data-obj="' . htmlspecialchars(json_encode($per), ENT_QUOTES, 'UTF-8') . '">Edit</a>
                            </li>

                            <li>
                                <a href="#" class="dropdown-item delete" data-id="' . $per->id . '">Hapus</a>
                            </li>
							</ul>
						</div>
                    </div>
                   </div>
                    ';
                })
                // <li><a class="dropdown-item" href="' . url('role/' . $role->id . '/give-permissions') . '">Add Permission</a></li>
                // ->addColumn('addPer', function ($adper) {
                //     return '<a class="btn btn-sm btn btn-success" href="' . url('role/' . $adper->id . '/give-permissions') . '">Add Permission</a>';
                // })
                ->rawColumns(['action'])
                ->make();
        }
        return view('pages.akses.permission.index');
    }
    public function create()
    {
        return view('pages.akses.permission.create');
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:permissions',
        ], [
            'name.required' => 'Nama bidang harus diisi.',
            'name.unique' => 'Nama bidang sudah ada.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $obj = new Permission();
            $obj->name = $request->name;
            $obj->save();
            Log::info('Berhasi menyimpan data', [$obj]);
            return response()->json(['success' => 'Data ' . $obj->name . ' berhasil disimpan.'], 200);
        } catch (Exception $err) {
            Log::error($err);
        }
        // dd($request);
        // Permission::create([
        //     'name' => $request->name
        // ]);



        // return redirect()->route('permission.index')
        //     ->with('success', 'Permission created successfully');
    }
    public function show($id)
    {
        //
    }
    public function edit($id)
    {
        $permission = Permission::find($id);

        return view('pages.akses.permission.edit', compact('permission'));
    }
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'name' => 'required|unique:permissions',
        ], [
            'name.required' => 'Nama bidang harus diisi.',
            'name.unique' => 'Nama bidang sudah ada.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $obj = Permission::findOrFail($request->id);
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
        $data = Permission::find($id);

        if ($data) {
            $data->delete();
            return response()->json(['success' => 'Data deleted successfully']);
        } else {
            return response()->json(['error' => 'Data not found'], 404);
        }
    }

    public function delete($id)
    {
        $data = Permission::find($id);

        if ($data) {
            $data->delete();
            return response()->json(['success' => 'Data deleted successfully']);
        } else {
            return response()->json(['error' => 'Data not found'], 404);
        }
    }

    public function permission()
    {
        return view('permission.permission');
    }
    public function role()
    {
        return view('permission.role');
    }
}
