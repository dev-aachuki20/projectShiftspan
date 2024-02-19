<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class SubAdminController extends Controller
{
    public function AllSubAdmins(){
        $roleid= config('constant.roles.sub_admin');
        $allSubAdmins = User::select('id','name')->whereHas('roles', function ($query) use ($roleid) {
            $query->where('id', $roleid);
        })->orderBy('name', 'asc')->get();

        $responseData = [
            'status'    => true,
            'message'   => 'success',
            'data'  => $allSubAdmins,
        ];

        return response()->json($responseData, 200);
    }
}
