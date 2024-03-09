<?php

namespace App\Http\Controllers\Backend;

use App\Models\Location;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $data = [
            'userCount' => 0, 'shiftCount' => 0, 'locationCount' => 0
        ];
        if($user->roles->first()->name == 'Super Admin'){
            $data['userCount'] = User::whereNotNull('company_id')->count();
            $data['shiftCount'] = Shift::Count();
            $data['locationCount'] = Location::count();
        }else/* if($user->roles->first()->name == 'Sub Admin') */{
            $data['userCount'] = User::where('company_id', $user->id)->count();
            $data['shiftCount'] = Shift::where('sub_admin_id', $user->id)->count();
            $data['locationCount'] = $user->locations()->count();
        }

        return view('admin.dashboard', compact('data'));
    }
}
