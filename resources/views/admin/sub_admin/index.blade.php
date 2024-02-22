@extends('layouts.app')
@section('title','Client Admin')
@section('customCss')
@endsection

@section('main-content')

    <div class="animate__animated animate__fadeInUp">
        <div class="msg-content white-bg radius-50 space-30 d-flex align-items-center">
            <h2 class="mb-md-0">@lang('cruds.client_admin.title')</h2>
            @can('sub_admin_create')
                <a href="javascript:void(0)" class="add_btn dash-btn green-bg w-115" id="addSubAdminBtn" >+ @lang('global.add') @lang('global.new')</a><div class="d-sm-none w-100"></div>
            @endcan
            @can('sub_admin_delete')
                <button class="del_btn dash-btn red-bg w-115 me-md-1" id="deleteAllSubAdmin">@lang('global.delete')</button>
            @endcan
        </div>
        <div class="c-admin position-relative">
            <div class="table-responsive">
                {{$dataTable->table(['class' => 'table common-table short-table nowrap', 'style' => 'width:100%;'])}}
            </div>
        </div>
    </div>

@endsection
@section('customJS')

@parent
{!! $dataTable->scripts() !!}

<script>
   
  
</script>

@endsection