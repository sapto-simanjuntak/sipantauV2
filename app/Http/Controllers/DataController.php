<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Data;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;


class DataController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $query = Data::query();
            return DataTables::of($query)
                ->addColumn('action', function ($data) {
                    $actions = '<div class="dropdown">';
                    $actions .= '<button class="btn btn-sm btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"><b>AKSI</b></button>';
                    $actions .= '<ul class="dropdown-menu">';
                    if (auth()->user()->can('Data-Edit')) {
                        $actions .= '<li>';
                        $actions .= '<a href="#" class="dropdown-item show_modal_edit" data-obj="' . htmlspecialchars(json_encode($data), ENT_QUOTES, 'UTF-8') . '">Edit</a>';
                        $actions .= '</li>';
                    }
                    if (auth()->user()->can('Data-Delete')) {
                        $actions .= '<li>';
                        $actions .= '<a href="#" class="dropdown-item delete" data-id="' . $data->id . '">Hapus</a>';
                        $actions .= '</li>';
                    }

                    $actions .= '</ul></div>';

                    return $actions;

                    // return '
                    // <div class="dropdown">
                    //     <button class="btn btn-sm btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"> <b>AKSI</b> </button>
                    //     <ul class="dropdown-menu">

                    //         <li>
                    //             <a href="#" class="dropdown-item show_modal_edit" data-obj="' . htmlspecialchars(json_encode($data), ENT_QUOTES, 'UTF-8') . '">Edit</a>
                    //         </li>
                    //         <li>
                    //             <a href="#" class="dropdown-item delete" data-id="' . $data->id . '">Hapus</a>
                    //         </li>
                    //     </ul>
                    // </div>';
                })
                ->rawColumns(['action', 'status'])
                ->make();
        }
        return view('pages.data.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        return view('pages.data.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:data',
            'deskripsi' => 'required',
        ], [
            'name.required' => 'Nama bidang harus diisi.',
            'name.unique' => 'Nama bidang sudah ada.',
            'deskripsi.required' => 'Deskripsi bidang harus diisi.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $obj = new Data();
            $obj->name = $request->name;
            $obj->deskripsi = $request->deskripsi;
            $obj->save();
            Log::info("Berhasi menyimpan data", [$obj]);
            return response()->json(['success' => 'Data ' . $obj->name . ' berhasil disimpan.'], 200);
        } catch (Exception $err) {
            Log::error($err);
        }
    }

    /**
     * Display the specified resource.g
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
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'name' => 'required',
            'deskripsi' => 'required',
        ], [
            'name.required' => 'Nama bidang harus diisi.',
            'deskripsi.required' => 'Deskripsi bidang harus diisi.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $obj = Data::findOrFail($request->id);
            $obj->name = $request->name;
            $obj->deskripsi = $request->deskripsi;
            $obj->save();
            Log::info("Berhasi update data", [$obj]);
            return response()->json(['success' => 'Data ' . $obj->name . ' berhasil diupdate.'], 200);
        } catch (Exception $err) {
            Log::error($err);
        }
    }

    /**
     * Remove the specified resource from storage.
     */

    public function destroy($id)
    {
        $data = Data::find($id);

        if ($data) {
            $data->delete();
            return response()->json(['success' => 'Data deleted successfully']);
        } else {
            return response()->json(['error' => 'Data not found'], 404);
        }
    }



    // public function delete($id)
    // {
    //     $data = Data::find($id);
    //     if ($data) {
    //         $data->delete();
    //         return response()->json(['success' => 'Data deleted successfully']);
    //     } else {
    //         return response()->json(['error' => 'Data not found'], 404);
    //     }
    // }
}
