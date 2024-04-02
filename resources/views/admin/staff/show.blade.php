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
                            <img src="{{ isset($user) && $user->profile_image_url ? $user->profile_image_url : asset(config('constant.default.staff-image')) }}" alt="staff-image">
                        </div>
                        @if(isset($user->name))
                            <p>{{$user->name}}</p>
                        @endif
                        <span>
                            {{ isset($user->profile->occupation->name) ? $user->profile->occupation->name : '' }}
                        </span>
                    </div>
                    {{-- <div class="form-label">
                        <label>@lang('cruds.staff.fields.name'):</label>
                        <div class="right-sidebox d-flex align-items-center justify-content-start">
                            <span class="fw-600">{{$user->name}}</span>
                        </div>
                    </div> --}}
                    <div class="form-label">
                        <label>@lang('cruds.staff.fields.staff_rating'):</label>
                        <div class="form-rating">
                            <ul>
                                @if(isset($rating))

                                    @if($rating <= 5)
                                        @for($r=1; $r<=$rating; $r++)
                                        <li>
                                            <input type="checkbox" disabled>
                                            <img src="{{asset('images/star-fill.png')}}" class="fill-star" alt="star">
                                        </li>
                                        @endfor

                                        @for($r=1; $r<=5-$rating; $r++)
                                        <li>
                                            <input type="checkbox" disabled>
                                            <img src="{{asset('images/star-blank.png')}}" class="blank-star" alt="star">
                                        </li>
                                        @endfor
                                    
                                    @endif

                                @else

                                    @for($r=1; $r<=5; $r++)
                                    <li>
                                        <input type="checkbox" disabled>
                                        <img src="{{asset('images/star-blank.png')}}" class="blank-star" alt="star">
                                    </li>
                                    @endfor

                                @endif
                                
                            </ul>
                        </div>
                    </div>
                    @if(isset($user->company) && !empty($user->company->name))
                        <div class="form-label">
                            <label>@lang('cruds.staff.fields.staff_admin'):</label>
                            <div class="right-sidebox d-flex align-items-center justify-content-center">
                                <span class="fw-600">{{ ucwords($user->company->name) }}</span>
                            </div>
                        </div>
                    @endif
                    <div class="form-label">
                        <label>@lang('cruds.staff.fields.title'):</label>
                        <div class="right-sidebox d-flex align-items-center justify-content-center">
                            <span class="fw-600">{{$user->title}}</span>
                        </div>
                    </div>
                    <div class="form-label">
                        <label>@lang('cruds.staff.fields.username'):</label>
                        <div class="right-sidebox d-flex align-items-center justify-content-center">
                            <span class="fw-600">{{$user->name}}</span>
                        </div>
                    </div>
                    <div class="form-label">
                        <label>@lang('cruds.staff.fields.email'):</label>
                        <div class="right-sidebox d-flex align-items-center justify-content-center">
                            <span class="fw-600">{{$user->email}}</span>
                        </div>
                    </div>
                    <div class="form-label">
                        <label>@lang('cruds.staff.fields.phone'):</label>
                        <div class="right-sidebox d-flex align-items-center justify-content-center">
                        <span class="fw-600">{{$user->phone}}</span>
                        </div>
                    </div>
                    <div class="form-label">
                        <label>@lang('cruds.staff.fields.dob'):</label>
                        <div class="right-sidebox d-flex align-items-center justify-content-center">
                        <span class="fw-600">{{date('d-m-Y', strtotime($user->profile->dob))}}</span>
                        </div>
                    </div>

                    @if(isset($user->profile) && !empty($user->profile->previous_name))
                        <div class="form-label">
                            <label>@lang('cruds.staff.fields.previous_name'):</label>
                            <div class="right-sidebox d-flex align-items-center justify-content-center">
                            <span class="fw-600">{{$user->profile->previous_name}}</span>
                            </div>
                        </div>
                    @endif
                    
                    @if(isset($user->profile) && !empty($user->profile->national_insurance_number))
                        <div class="form-label">
                            <label>@lang('cruds.staff.fields.national_insurance_number'):</label>
                            <div class="right-sidebox d-flex align-items-center justify-content-center">
                            <span class="fw-600">{{$user->profile->national_insurance_number}}</span>
                            </div>
                        </div>
                    @endif

                    @if(isset($user->profile) && !empty($user->profile->address))
                        <div class="form-label">
                            <label>@lang('cruds.staff.fields.address'):</label>
                            <div class="right-sidebox d-flex align-items-center justify-content-center">
                            <span class="fw-600">{{$user->profile->address}}</span>
                            </div>
                        </div>
                    @endif

                    @if(isset($user->profile) && !empty($user->profile->education))
                        <div class="form-label">
                            <label>@lang('cruds.staff.fields.education'):</label>
                            <div class="right-sidebox d-flex align-items-center justify-content-center">
                            <span class="fw-600">{{$user->profile->education}}</span>
                            </div>
                        </div>
                    @endif

                    @if(isset($user->profile) && !empty($user->profile->prev_emp_1))
                        <div class="form-label">
                            <label>@lang('cruds.staff.fields.prev_emp_1'):</label>
                            <div class="right-sidebox d-flex align-items-center justify-content-center">
                            <span class="fw-600">{{$user->profile->prev_emp_1}}</span>
                            </div>
                        </div>
                    @endif

                    @if(isset($user->profile) && !empty($user->profile->prev_emp_2))
                        <div class="form-label">
                            <label>@lang('cruds.staff.fields.prev_emp_2'):</label>
                            <div class="right-sidebox d-flex align-items-center justify-content-center">
                            <span class="fw-600">{{$user->profile->prev_emp_2}}</span>
                            </div>
                        </div>
                    @endif

                    @if(isset($user->profile) && !empty($user->profile->reference_1))
                        <div class="form-label">
                            <label>@lang('cruds.staff.fields.reference_1'):</label>
                            <div class="right-sidebox d-flex align-items-center justify-content-center">
                            <span class="fw-600">{{$user->profile->reference_1}}</span>
                            </div>
                        </div>
                    @endif

                    @if(isset($user->profile) && !empty($user->profile->reference_2))
                        <div class="form-label">
                            <label>@lang('cruds.staff.fields.reference_2'):</label>
                            <div class="right-sidebox d-flex align-items-center justify-content-center">
                            <span class="fw-600">{{$user->profile->reference_2}}</span>
                            </div>
                        </div>
                    @endif

                    @if(isset($user->profile) && !empty($user->profile->date_sign))
                        <div class="form-label">
                            <label>@lang('cruds.staff.fields.date_sign'):</label>
                            <div class="right-sidebox d-flex align-items-center justify-content-center">
                            <span class="fw-600">{{date('d-m-Y', strtotime($user->profile->date_sign))}}</span>
                            </div>
                        </div>
                    @endif

                    
                    <div class="form-label">
                        <label>@lang('cruds.staff.fields.criminal'):</label>
                        <div class="right-sidebox d-flex align-items-center justify-content-center">
                            @if(isset(config('constant.staff_info')[$user->profile->is_criminal]))
                                <span class="fw-600">{{ config('constant.staff_info')[$user->profile->is_criminal] }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="form-label">
                        <label>@lang('cruds.staff.fields.rehabilitation_of_offenders'):</label>
                        <div class="right-sidebox d-flex align-items-center justify-content-center">
                            @if(isset(config('constant.staff_info')[$user->profile->is_rehabilite]))
                                <span class="fw-600">{{ config('constant.staff_info')[$user->profile->is_rehabilite] }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="form-label">
                        <label>@lang('cruds.staff.fields.enquires'):</label>
                        <div class="right-sidebox d-flex align-items-center justify-content-center">
                            @if(isset(config('constant.staff_info')[$user->profile->is_enquire]))
                                <span class="fw-600">{{ config('constant.staff_info')[$user->profile->is_enquire] }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="form-label">
                        <label>@lang('cruds.staff.fields.health_issue'):</label>
                        <div class="right-sidebox d-flex align-items-center justify-content-center">
                            @if(isset(config('constant.staff_info')[$user->profile->is_health_issue]))
                                <span class="fw-600">{{ config('constant.staff_info')[$user->profile->is_health_issue] }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="form-label">
                        <label>@lang('cruds.staff.fields.statement'):</label>
                        <div class="right-sidebox d-flex align-items-center justify-content-center">
                            @if(isset(config('constant.staff_info')[$user->profile->is_statement]))
                                <span class="fw-600">{{ config('constant.staff_info')[$user->profile->is_statement] }}</span>
                            @endif
                        </div>
                    </div>


                    <div class="form-label">
                        <label>@lang('cruds.staff.fields.staff_image'):</label>
                        <div class="upload-file">
                            <div id="image-preview" class="img-prevarea img-prePro">
                                <image src="{{isset($user) && $user->profile_image_url ? $user->profile_image_url : asset('images/download-icon.png')}}" alt="">
                            </div>
                        </div>
                    </div>

                    <div class="form-label">
                        <label>@lang('cruds.staff.fields.relevant_training_image'):</label>
                        <div class="upload-file">
                            @if(isset($user) && $user->training_document_url)
                                <a target="_blank" href="{{ $user->training_document_url }}" class="chose-btn mt-0">
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
                            @if(isset($user) && $user->dbs_certificate_url)
                                <a target="_blank" href="{{ $user->dbs_certificate_url }}" class="chose-btn mt-0">
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
                            @if(isset($user) && $user->cv_url)
                                <a target="_blank" href="{{ $user->cv_url }}" class="chose-btn mt-0">
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
                            @if(isset($user) && $user->staff_budge_url)
                                <a target="_blank" href="{{ $user->staff_budge_url }}" class="chose-btn mt-0">
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
                            @if(isset($user) && $user->dbs_check_url)
                                <a target="_blank" href="{{ $user->dbs_check_url }}" class="chose-btn mt-0">
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
                            @if(isset($user) && $user->training_check_url)
                                <a target="_blank" href="{{ $user->training_check_url }}" class="chose-btn mt-0">
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
                @if(isset($type) && $type == 'staff')
                <div class="form-label text-center mt-3 pt-1">
                    <button class="cbtn sky-bg mw-160 text-capitalize editStaffBtn" data-href="{{route('staffs.edit', $user->uuid)}}">@lang('global.edit')</button>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>