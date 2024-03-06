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
                    @if(isset($subAdmins) && !empty($subAdmins->name))
                        <div class="form-label">
                            <label>@lang('cruds.staff.fields.staff_admin'):</label>
                            <div class="right-sidebox d-flex align-items-center justify-content-center">
                                <span class="fw-600">{{$subAdmins->name}}</span>
                            </div>
                        </div>
                    @endif
                    <div class="form-label">
                        <label>@lang('cruds.staff.fields.title'):</label>
                        <div class="right-sidebox d-flex align-items-center justify-content-center">
                            <span class="fw-600">{{$users->title}}</span>
                        </div>
                    </div>
                    <div class="form-label">
                        <label>@lang('cruds.staff.fields.username'):</label>
                        <div class="right-sidebox d-flex align-items-center justify-content-center">
                            <span class="fw-600">{{$users->name}}</span>
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
                        <label>@lang('cruds.staff.fields.dob'):</label>
                        <div class="right-sidebox d-flex align-items-center justify-content-center">
                        <span class="fw-600">{{date('d-m-Y', strtotime($users->profile->dob))}}</span>
                        </div>
                    </div>

                    @if(isset($users->profile) && !empty($users->profile->previous_name))
                        <div class="form-label">
                            <label>@lang('cruds.staff.fields.previous_name'):</label>
                            <div class="right-sidebox d-flex align-items-center justify-content-center">
                            <span class="fw-600">{{$users->profile->previous_name}}</span>
                            </div>
                        </div>
                    @endif
                    
                    @if(isset($users->profile) && !empty($users->profile->national_insurance_number))
                        <div class="form-label">
                            <label>@lang('cruds.staff.fields.national_insurance_number'):</label>
                            <div class="right-sidebox d-flex align-items-center justify-content-center">
                            <span class="fw-600">{{$users->profile->national_insurance_number}}</span>
                            </div>
                        </div>
                    @endif

                    @if(isset($users->profile) && !empty($users->profile->address))
                        <div class="form-label">
                            <label>@lang('cruds.staff.fields.address'):</label>
                            <div class="right-sidebox d-flex align-items-center justify-content-center">
                            <span class="fw-600">{{$users->profile->address}}</span>
                            </div>
                        </div>
                    @endif

                    @if(isset($users->profile) && !empty($users->profile->education))
                        <div class="form-label">
                            <label>@lang('cruds.staff.fields.education'):</label>
                            <div class="right-sidebox d-flex align-items-center justify-content-center">
                            <span class="fw-600">{{$users->profile->education}}</span>
                            </div>
                        </div>
                    @endif

                    @if(isset($users->profile) && !empty($users->profile->prev_emp_1))
                        <div class="form-label">
                            <label>@lang('cruds.staff.fields.prev_emp_1'):</label>
                            <div class="right-sidebox d-flex align-items-center justify-content-center">
                            <span class="fw-600">{{$users->profile->prev_emp_1}}</span>
                            </div>
                        </div>
                    @endif

                    @if(isset($users->profile) && !empty($users->profile->prev_emp_2))
                        <div class="form-label">
                            <label>@lang('cruds.staff.fields.prev_emp_2'):</label>
                            <div class="right-sidebox d-flex align-items-center justify-content-center">
                            <span class="fw-600">{{$users->profile->prev_emp_2}}</span>
                            </div>
                        </div>
                    @endif

                    @if(isset($users->profile) && !empty($users->profile->reference_1))
                        <div class="form-label">
                            <label>@lang('cruds.staff.fields.reference_1'):</label>
                            <div class="right-sidebox d-flex align-items-center justify-content-center">
                            <span class="fw-600">{{$users->profile->reference_1}}</span>
                            </div>
                        </div>
                    @endif

                    @if(isset($users->profile) && !empty($users->profile->reference_2))
                        <div class="form-label">
                            <label>@lang('cruds.staff.fields.reference_2'):</label>
                            <div class="right-sidebox d-flex align-items-center justify-content-center">
                            <span class="fw-600">{{$users->profile->reference_2}}</span>
                            </div>
                        </div>
                    @endif

                    @if(isset($users->profile) && !empty($users->profile->date_sign))
                        <div class="form-label">
                            <label>@lang('cruds.staff.fields.date_sign'):</label>
                            <div class="right-sidebox d-flex align-items-center justify-content-center">
                            <span class="fw-600">{{date('d-m-Y', strtotime($users->profile->date_sign))}}</span>
                            </div>
                        </div>
                    @endif

                    
                    <div class="form-label">
                        <label>@lang('cruds.staff.fields.criminal'):</label>
                        <div class="right-sidebox d-flex align-items-center justify-content-center">
                            @if(isset(config('constant.staff_info')[$users->profile->is_criminal]))
                                <span class="fw-600">{{ config('constant.staff_info')[$users->profile->is_criminal] }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="form-label">
                        <label>@lang('cruds.staff.fields.rehabilitation_of_offenders'):</label>
                        <div class="right-sidebox d-flex align-items-center justify-content-center">
                            @if(isset(config('constant.staff_info')[$users->profile->is_rehabilite]))
                                <span class="fw-600">{{ config('constant.staff_info')[$users->profile->is_rehabilite] }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="form-label">
                        <label>@lang('cruds.staff.fields.enquires'):</label>
                        <div class="right-sidebox d-flex align-items-center justify-content-center">
                            @if(isset(config('constant.staff_info')[$users->profile->is_enquire]))
                                <span class="fw-600">{{ config('constant.staff_info')[$users->profile->is_enquire] }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="form-label">
                        <label>@lang('cruds.staff.fields.health_issue'):</label>
                        <div class="right-sidebox d-flex align-items-center justify-content-center">
                            @if(isset(config('constant.staff_info')[$users->profile->is_health_issue]))
                                <span class="fw-600">{{ config('constant.staff_info')[$users->profile->is_health_issue] }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="form-label">
                        <label>@lang('cruds.staff.fields.statement'):</label>
                        <div class="right-sidebox d-flex align-items-center justify-content-center">
                            @if(isset(config('constant.staff_info')[$users->profile->is_statement]))
                                <span class="fw-600">{{ config('constant.staff_info')[$users->profile->is_statement] }}</span>
                            @endif
                        </div>
                    </div>


                    <div class="form-label">
                        <label>@lang('cruds.staff.fields.staff_image'):</label>
                        <div class="upload-file">
                            <div id="image-preview" class="img-prevarea img-prePro">
                                <image src="{{isset($users) && $users->profile_image_url ? $users->profile_image_url : asset('images/download-icon.png')}}" alt="">
                            </div>
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

                    <div class="form-label">
                        <label>@lang('cruds.staff.fields.dbs_certificate'):</label>
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
                        <label>@lang('cruds.staff.fields.staff_budge'):</label>
                        <div class="upload-file">
                            @if(isset($users) && $users->staff_budge_url)
                                <a target="_blank" href="{{ $users->staff_budge_url }}" class="chose-btn mt-0">
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
                        <label>@lang('cruds.staff.fields.dbs_check'):</label>
                        <div class="upload-file">
                            @if(isset($users) && $users->dbs_check_url)
                                <a target="_blank" href="{{ $users->dbs_check_url }}" class="chose-btn mt-0">
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
                        <label>@lang('cruds.staff.fields.training_check'):</label>
                        <div class="upload-file">
                            @if(isset($users) && $users->training_check_url)
                                <a target="_blank" href="{{ $users->training_check_url }}" class="chose-btn mt-0">
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