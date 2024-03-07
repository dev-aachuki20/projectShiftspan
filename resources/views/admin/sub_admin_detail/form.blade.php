@if(auth()->user()->is_super_admin)
    <div class="form-label select-label">
        <label>@lang('cruds.client_detail.fields.client_admin_name'):</label>
        <select name="sub_admin_id" id="client_name" class="select2" required>
            <option value="">@lang('global.select') @lang('cruds.client_detail.fields.client_admin')</option>
            @foreach ($subAdmins as $key => $value)
                <option value="{{$key}}" @selected(isset($subAdminDetail) && $subAdminDetail->client->uuid == $key )>{{ $value}}</option>
            @endforeach
        </select>
        <div class="client_name_error" style="width: 100%"></div>
    </div>
@endif
<div class="form-label">
    <label>@lang('cruds.client_detail.fields.name'):</label>
    <input type="text" name="name" value="{{ isset($subAdminDetail) ? $subAdminDetail->name : '' }}" required />
</div>
<div class="form-label">
    <label>@lang('cruds.client_detail.fields.address'):</label>
    <input type="text" name="address" value="{{ isset($subAdminDetail) ? $subAdminDetail->address : '' }}" required />
</div>
<div class="form-label">
    <label>@lang('cruds.client_detail.fields.shop_description'):</label>
    <textarea name="shop_description" maxlength="{{config('constant.shop_description_length')}}" required >{{ isset($subAdminDetail) ? $subAdminDetail->shop_description : '' }}</textarea>
</div>
<div class="form-label">
    <label>@lang('cruds.client_detail.fields.travel_info'):</label>
    <textarea name="travel_info" maxlength="{{config('constant.travel_info_length')}}" required >{{ isset($subAdminDetail) ? $subAdminDetail->travel_info : '' }}</textarea>
</div>
<div class="form-label">
    <label>@lang('cruds.client_detail.fields.building_image'):</label>    
    <div class="right-sidebox">
        <div class="img-prevarea img-prePro">
            <img src="{{ isset($subAdminDetail) && $subAdminDetail->building_image_url ? $subAdminDetail->building_image_url : asset(config('constant.default.building-image'))}}" >
        </div>
        <div class="chose-btn-area position-relative">
            <a href="javascript:void(0)" class="chose-btn">@lang('global.choose') @lang('cruds.user.admin_profile.fields.image')</a>
            <input type="file" id="image-input" name="building_image" class="fileInputBoth" accept="image/jpeg,image/png,image/jpg'">
        </div>
    </div>
    <div class="building_image_error" style="width: 100%"></div>
</div>
<div class="form-label justify-content-center">
    <button type="submit" class="cbtn submitBtn">
        @if(isset($subAdminDetail))
            @lang('global.update') @lang('cruds.client_detail.title_singular')            
        @else
            @lang('global.add') @lang('cruds.client_detail.title_singular')
        @endif
    </button>
</div>