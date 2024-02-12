<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Occupation;
use Illuminate\Http\Request;

class OccupationController extends Controller
{
    public function AllOccupations(Request $request){

        $request->validate([
            'company_id'=> ['required','numeric','exists:users,id'],
        ]);
        $allOccupations = Occupation::select('id','name')->where('sub_admin_id',$request->company_id)->orderBy('name', 'asc')->get();
        $responseData = [
            'status'    => true,
            'message'   => 'success',
            'data'  => $allOccupations,
        ];

        return response()->json($responseData, 200);
    }
}
