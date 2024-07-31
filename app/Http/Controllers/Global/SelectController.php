<?php

namespace App\Http\Controllers\Global;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use App\Models\User;

class SelectController extends Controller
{
    public function selectRoles(Request $request)
    {
        $search = $request->input('search');
        $data = _select2(new Role, 'name', $search);

        $result = [];
        foreach ($data as $value) {
            $result[] = ["id" => $value->id, "text" => $value->name];
        }

        return response()->json($result);
    }

    public function selectUser(Request $request)
    {
        $search = $request->input('search');
        $data = _select2(new User(), 'name', $search);

        $result = [];
        foreach ($data as $value) {
            $result[] = ["id" => $value->id, "text" => $value->name];
        }

        return response()->json($result);
    }
}
