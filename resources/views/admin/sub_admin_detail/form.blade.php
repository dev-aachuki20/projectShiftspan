@if(auth()->user()->is_super_admin)
    <div class="form-label select-label">
        <label>@lang('cruds.client_detail.fields.client_name'):</label>
        <select name="sub_admin_id" id="client_name" class="select2" required>
            <option value="">@lang('global.select') @lang('cruds.client_detail.fields.client_admin')</option>
            @foreach ($subAdmins as $key => $value)
                <option value="{{$key}}" {{ isset($subAdminDetail) && $subAdminDetail->client->uuid == $key ? 'selected' : ''  }}>{{ $value}}</option>
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
    <div class="upload-file">
        <h6>@lang('global.upload')</h6>
        <input type="file" name="building_image" accept="image/jpeg,image/png,image/jpg'">
        <img src="{{asset('default/upload-icon.png')}}" alt="upload-img"/>
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