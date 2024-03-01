<div class="modal fade common-modal modal-size-l" id="staffDetails" tabindex="-1" aria-labelledby="staffDetailsLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-820">
        <div class="modal-content">
            <div class="modal-header justify-content-center yellow-bg">
                <h5 class="modal-title text-center" id="staffDetailsLabel">@lang('global.view') @lang('cruds.staff.title_singular')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="msg-form">
                    <div class="user-image text-center">
                        <div class="staff-img">
                            <img src="{{ isset($users) && $users->profile_image_url ? $users->profile_image_url : asset(config('constant.default.staff-image')) }}" alt="staff-image">
                        </div>
                        @if(isset($users->name))
                            <p>{{$users->name}}</p>
                        @endif
                        <span>
                            {{ isset($users->profile->occupation->name) ? $users->profile->occupation->name : '' }}
                        </span>
                    </div>
                    {{-- <div class="form-label">
                        <label>@lang('cruds.staff.fields.name'):</label>
                        <div class="right-sidebox d-flex align-items-center justify-content-start">
                            <span class="fw-600">{{$users->name}}</span>
                        </div>
                    </div> --}}
                    <div class="form-label">
                        <label>@lang('cruds.staff.fields.staff_rating'):</label>
                        <div class="form-rating">
                            <ul>
                                <li>
                                    <input type="checkbox" disabled>
                                    <img src="{{asset('images/star-blank.png')}}" class="blank-star" alt="star">
                                    {{-- <img src="{{asset('images/star-fill.png')}}" class="fill-star" alt="star"> --}}
                                </li>
                                <li>
                                    <input type="checkbox" disabled>
                                    <img src="{{asset('images/star-blank.png')}}" class="blank-star" alt="star">
                                    {{-- <img src="{{asset('images/star-fill.png')}}" class="fill-star" alt="star"> --}}
                                </li>
                                <li>
                                    <input type="checkbox" disabled>
                                    <img src="{{asset('images/star-blank.png')}}" class="blank-star" alt="star">
                                    {{-- <img src="{{asset('images/star-fill.png')}}" class="fill-star" alt="star"> --}}
                                </li>
                                <li>
                                    <input type="checkbox" disabled>
                                    <img src="{{asset('images/star-blank.png')}}" class="blank-star" alt="star">
                                    {{-- <img src="{{asset('images/star-fill.png')}}" class="fill-star" alt="star"> --}}
                                </li>
                                <li>
                                    <input type="checkbox" disabled>
                                    <img src="{{asset('images/star-blank.png')}}" class="blank-star" alt="star">
                                    {{-- <img src="{{asset('images/star-fill.png')}}" class="fill-star" alt="star"> --}}
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="form-label">
                        <label>@lang('cruds.staff.fields.email'):</label>
                        <div class="right-sidebox d-flex align-items-center justify-content-center">
                            <span class="fw-600">{{$users->email}}</span>
                        </div>
                    </div>
                    <div class="form-label">
                        <label>@lang('cruds.staff.fields.phone'):</label>
                        <div class="right-sidebox d-flex align-items-center justify-content-center">
                        <span class="fw-600">{{$users->phone}}</span>
                        </div>
                    </div>
                    
                    <div class="form-label">
                        <label>@lang('cruds.staff.fields.dbs_check'):</label>
                        <div class="upload-file">
                            @if(isset($users) && $users->dbs_certificate_url)
                                <a target="_blank" href="{{ $users->dbs_certificate_url }}" class="chose-btn mt-0">
                                    <x-svg-icons icon="help-pdf" />
                                </a>
                            @else
                                <div id="image-preview" class="img-prevarea img-prePro">
                                    <image src="{{asset('images/download-icon.png')}}" alt="">
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="form-label">
                        <label>@lang('cruds.staff.fields.cv'):</label>
                        <div class="upload-file">
                            @if(isset($users) && $users->cv_url)
                                <a target="_blank" href="{{ $users->cv_url }}" class="chose-btn mt-0">
                                    <x-svg-icons icon="help-pdf" />
                                </a>
                            @else
                                <div id="image-preview" class="img-prevarea img-prePro">
                                    <image src="{{asset('images/download-icon.png')}}" alt="">
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="form-label">
                        <label>@lang('cruds.staff.fields.relevant_training_image'):</label>
                        <div class="upload-file">
                            @if(isset($users) && $users->training_document_url)
                                <a target="_blank" href="{{ $users->training_document_url }}" class="chose-btn mt-0">
                                    <x-svg-icons icon="help-pdf" />
                                </a>
                            @else
                                <div id="image-preview" class="img-prevarea img-prePro">
                                    <image src="{{asset('images/download-icon.png')}}" alt="">
                                </div>
                            @endif
                        </div>
                    </div>
                </form>
                <div class="form-label text-center mt-3 pt-1">
                    <button class="cbtn sky-bg mw-160 text-capitalize editStaffBtn" data-href="{{route('staffs.edit', $users->uuid)}}">@lang('global.edit')</button>
                </div>
            </div>
        </div>
    </div>
</div>