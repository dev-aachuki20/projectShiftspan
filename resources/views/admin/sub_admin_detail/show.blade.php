<div class="modal fade common-modal modal-size-l" id="viewSubAdminDetailModal" tabindex="-1" aria-labelledby="viewSubAdminDetailLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-820">
        <div class="modal-content">
            <div class="modal-header justify-content-center yellow-bg">
                <h5 class="modal-title text-center" id="viewSubAdminDetailLabel">@lang('global.view') @lang('cruds.client_detail.title_singular')</h5>
                <button type="button" class="btn-close " data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body inner-size-l">
                <form class="msg-form">
                    @if(auth()->user()->is_super_admin)
                        <div class="form-label">
                            <label>@lang('cruds.client_detail.fields.client_admin_name'):</label>
                            <div class="right-sidebox d-flex align-items-center justify-content-start">
                                <span class="fw-600">{{$subAdminDetail->client->name}}</span>
                            </div>
                        </div>
                    @endif
                    <div class="form-label">
                        <label>@lang('cruds.client_detail.fields.name'):</label>
                        <div class="right-sidebox d-flex align-items-center justify-content-start">
                            <span class="fw-600">{{$subAdminDetail->name}}</span>
                        </div>
                    </div>
                    <div class="form-label">
                        <label>@lang('cruds.client_detail.fields.address'):</label>
                        <div class="right-sidebox d-flex align-items-center justify-content-start">
                            <span class="fw-600">{{$subAdminDetail->address}}</span>
                        </div>
                    </div>
                    <div class="form-label">
                        <label>@lang('cruds.client_detail.fields.shop_description'):</label>
                        <div class="right-sidebox d-flex align-items-center justify-content-start">
                            <span class="fw-600">{{$subAdminDetail->shop_description}}</span>
                        </div>
                    </div>
                    <div class="form-label">
                        <label>@lang('cruds.client_detail.fields.travel_info'):</label>
                        <div class="right-sidebox d-flex align-items-center justify-content-start">
                            <span class="fw-600">{{$subAdminDetail->travel_info}}</span>
                        </div>
                    </div>
                    <div class="form-label">
                        <label>@lang('cruds.client_detail.fields.building_image'):</label>
                        <div class="upload-file">
                            <input type="file" disabled>
                            <div id="image-preview" class="img-prevarea img-prePro">
                                <img src="{{ $subAdminDetail->building_image_url ? $subAdminDetail->building_image_url : asset(config('constant.default.building-image'))}}" alt="">
                            </div>
                        </div>
                    </div>
                </form>
                <div class="form-label text-center mt-3 pt-1">
                    <button class="cbtn sky-bg mw-160 text-capitalize editSubAdminDetailBtn" data-href="{{route('client-details.edit', $subAdminDetail->uuid)}}">@lang('global.edit')</button>
                </div>
            </div>
        </div>
    </div>
</div>