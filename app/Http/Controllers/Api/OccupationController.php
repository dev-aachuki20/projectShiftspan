<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Occupation;
use Illuminate\Http\Request;

class OccupationController extends Controller
{
    public function AllOccupations(){
        $roleid= config('app.roleid.admin');
        $allOccupations = Occupation::select('id','name')->orderBy('name', 'asc')->get();

        $responseData = [
            'status'    => true,
            'message'   => 'success',
            'data'  => $allOccupations,
        ];

        return response()->json($responseData, 200);
    }
}
