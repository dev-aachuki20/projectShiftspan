<?php

namespace App\Http\Controllers\Backend;

use App\Models\Location;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $locationCount = Location::where('status', 1)->count();
        $shiftCount = Shift::count();
        /* $users = User::whereNotNull('company_id')->count(); */
        
        // $location = User::where('status', 1)->count();
        return view('admin.dashboard', compact('locationCount', 'shiftCount'));
    }
}
