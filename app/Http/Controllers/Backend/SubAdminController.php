<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

use App\DataTables\SubAdminDataTable;


class SubAdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(SubAdminDataTable $dataTable)
    {
        abort_if(Gate::denies('sub_admin_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        try {
            // return $dataTable->render('admin.sub_admin.index');
            return view('admin.sub_admin.index');
        } catch (\Exception $e) {
            return abort(500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
