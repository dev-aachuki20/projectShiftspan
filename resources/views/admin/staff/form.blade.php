@if(auth()->user()->is_super_admin)
    <div class="form-label select-label">
        <label>@lang('cruds.staff.fields.staff_admin'):</label>
        <select name="company_id" id="company_id" class="select2">
            <option value="">@lang('global.select') @lang('cruds.staff.fields.staff_admin')</option>
            @foreach ($subAdmins as $key => $value)
                <option value="{{$key}}" {{ isset($staff) && $staff->company_id == $key ? 'selected' : ''  }}>{{ $value}}</option>
            @endforeach
        </select>
        <div class="sub_admin_error" style="width: 100%"></div>
    </div>
@endif
    <div class="form-label">
        <label>@lang('cruds.staff.fields.title'):</label>
        <input type="text" name="title" value="{{ (isset($staff) && !empty($staff->title)) ? $staff->title : ''}}">
    </div>
    <div class="form-label">
        <label>@lang('cruds.staff.fields.full_name'):</label>
        <input type="text" name="name" value="{{ (isset($staff) && !empty($staff->name)) ? $staff->name : ''}}" @if(isset($staff) && !empty($staff->name)) {{-- readonly --}} @endif>
    </div>
    {{-- <div class="form-label">
        <label>@lang('cruds.staff.fields.name'):</label>
        <input type="text" name="name" value="{{ (isset($staff) && !empty($staff->name)) ? $staff->name : ''}}">
    </div>
    <div class="form-label">
        <label>@lang('cruds.staff.fields.username'):</label>
        <input type="text" name="username" value="{{ (isset($staff) && !empty($staff->username)) ? $staff->username : ''}}" @if(isset($staff) && !empty($staff->username)) readonly @endif>
    </div> --}}
    <div class="form-label">
        <label>@lang('cruds.staff.fields.email'):</label>
        <input type="email" name="email" value="{{ (isset($staff) && !empty($staff->email)) ? $staff->email : ''}}" @if(isset($staff) && !empty($staff->username)) {{-- readonly --}} @endif autofocus="false">
    </div>
    @if(!isset($staff))    
        <div class="form-label password-area">
            <label>@lang('cruds.staff.fields.password'):</label>
            <input type="password" name="password" value="" autofocus="false">
            
            <span class="toggle-password close-eye">
                <x-svg-icons icon="close-eye" />
                <x-svg-icons icon="open-eye" />
            </span>
        </div>
    @endif
    <div class="form-label">
        <label>@lang('cruds.staff.fields.phone'):</label>
        <input type="text" name="phone" value="{{ (isset($staff) && !empty($staff->phone)) ? $staff->phone : ''}}">
    </div>
    <div class="form-label">
        <label>@lang('cruds.staff.fields.dob'):</label>
        <input type="text" name="dob" id="birthday" class="datepicker" value="{{ (isset($staff) && !empty($staff->profile)) ? dateFormat($staff->profile->dob, 'Y-m-d') : '' }}" readonly/>
    </div>
    <div class="form-label">
        <label>@lang('cruds.staff.fields.previous_name'):</label>
        <input type="text" name="previous_name" value="{{ (isset($staff) && !empty($staff->profile)) ? $staff->profile->previous_name : ''}}">
    </div>
    <div class="form-label">
        <label>@lang('cruds.staff.fields.national_insurance_number'):</label>
        <input type="text" name="national_insurance_number" value="{{ (isset($staff) && !empty($staff->profile)) ? $staff->profile->national_insurance_number : ''}}"/>
    </div>
    <div class="form-label">
        <label>@lang('cruds.staff.fields.address'):</label>
        <textarea name="address">{{ (isset($staff) && !empty($staff->profile)) ? $staff->profile->address : '' }}</textarea>
    </div>
    {{-- <div class="form-label">
        <label>@lang('cruds.staff.fields.education'):</label>
        <input type="text" name="education" value="{{ (isset($staff) && !empty($staff->profile)) ? $staff->profile->education : '' }}">
    </div> --}}
    <div class="form-label">
        <label>@lang('cruds.staff.fields.prev_emp_1'):</label>
        <input type="text" name="prev_emp_1" value="{{ (isset($staff) && !empty($staff->profile)) ? $staff->profile->prev_emp_1 : '' }}">
    </div>
    <div class="form-label">
        <label>@lang('cruds.staff.fields.prev_emp_2'):</label>
        <input type="text" name="prev_emp_2" value="{{ (isset($staff) && !empty($staff->profile)) ? $staff->profile->prev_emp_2 : '' }}">
    </div>
    <div class="form-label">
        <label>@lang('cruds.staff.fields.reference_1'):</label>
        <input type="text" name="reference_1" value="{{ (isset($staff) && !empty($staff->profile)) ? $staff->profile->reference_1 : '' }}">
    </div>
    <div class="form-label">
        <label>@lang('cruds.staff.fields.reference_2'):</label>
        <input type="text" name="reference_2" value="{{ (isset($staff) && !empty($staff->profile)) ? $staff->profile->reference_2 : '' }}">
    </div>
    {{-- <div class="form-label">
        <label>@lang('cruds.staff.fields.date_sign'):</label>
        <input type="text" name="date_sign" id="date_sign" class="datepicker" value="{{ (isset($staff) && !empty($staff->profile)) ? dateFormat($staff->profile->date_sign, 'Y-m-d') : '' }}" readonly/>
    </div> --}}

    <div class="form-label select-label">
        <label>@lang('cruds.staff.fields.criminal'):</label>
        <select class="select2" name="is_criminal" id="is_criminal" title="@lang('cruds.staff.fields.criminal')" required>
            @foreach (config('constant.staff_info') as $key=>$val)
                <option value="{{$key}}" @if(isset($staff) && !empty($staff->profile) && $staff->profile->is_criminal == $key) selected @endif>{{ $val }}</option>
            @endforeach
        </select>
    </div>

    <div class="form-label select-label">
        <label for="rehabilitation_of_offenders">@lang('cruds.staff.fields.rehabilitation_of_offenders'):</label>      
        <select class="select2" name="is_rehabilite" id="is_rehabilite" title="@lang('cruds.staff.fields.rehabilitation_of_offenders')" required>
            @foreach (config('constant.staff_info') as $key=>$val)
                <option value="{{$key}}" @if(isset($staff) && !empty($staff->profile) && $staff->profile->is_rehabilite == $key) selected @endif>{{ $val }}</option>
            @endforeach
        </select>
    </div>

    {{-- <div class="form-label select-label">
        <label for="is_enquire">@lang('cruds.staff.fields.enquires'):</label>      
        <select class="select2" name="is_enquire" id="is_enquire" title="@lang('cruds.staff.fields.enquires')" required>
            @foreach (config('constant.staff_info') as $key=>$val)
                <option value="{{$key}}" @if(isset($staff) && !empty($staff->profile) && $staff->profile->is_enquire == $key) selected @endif>{{ $val }}</option>
            @endforeach
        </select>
    </div> --}}

    <div class="form-label select-label">
        <label for="is_health_issue">@lang('cruds.staff.fields.health_issue'):</label>      
        <select class="select2" name="is_health_issue" id="is_health_issue" title="@lang('cruds.staff.fields.health_issue')" required>
            @foreach (config('constant.staff_info') as $key=>$val)
                <option value="{{$key}}" @if(isset($staff) && !empty($staff->profile) && $staff->profile->is_health_issue == $key) selected @endif>{{ $val }}</option>
            @endforeach
        </select>
    </div>

    {{-- <div class="form-label select-label">
        <label for="is_statement">@lang('cruds.staff.fields.statement'):</label>      
        <select class="select2" name="is_statement" id="is_statement" title="@lang('cruds.staff.fields.statement')" required>
            @foreach (config('constant.staff_info') as $key=>$val)
                <option value="{{$key}}" @if(isset($staff) && !empty($staff->profile) && $staff->profile->is_statement == $key) selected @endif>{{ $val }}</option>
            @endforeach
        </select>
    </div> --}}

    <div class="form-label">
        <label>@lang('cruds.staff.fields.staff_image'):</label>
        <div class="right-sidebox">
            <div id="imagePreviewContainer1_1" class="img-prevarea imagePreviewContainer">
                <img id="image" src="{{ isset($staff) && $staff->profile_image_url ? $staff->profile_image_url : asset("images/dummy-image-square.jpg")}}">
            </div>
            <div class="chose-btn-area position-relative">
                <a href="javascript:void(0)" class="chose-btn">@lang('global.choose') @lang('global.image')</a>
                <input type="file" name="image" id="fileInput1_1" class="fileInput" accept="image/*" onchange="document.getElementById('image').src = window.URL.createObjectURL(this.files[0])" multiple>
            </div>
        </div>
    </div>
    <div class="form-label">
        <label>@lang('cruds.staff.fields.relevant_training_image'):</label>
        <div class="right-sidebox">
            @if(isset($staff) && $staff->training_document_url)
                <a target="_blank" href="{{ $staff->training_document_url }}" class="chose-btn mt-0">
                    <x-svg-icons icon="help-pdf" />
                </a>
            @endif
            <div class="chose-btn-area position-relative">
                <a href="javascript:void(0)" class="chose-btn">@lang('global.choose') @lang('global.file')</a>
                <input type="file" name="relevant_training" id="fileInput1_2" class="fileInput" accept=".pdf" multiple>
            </div>
        </div>
    </div>
    
    <div class="form-label">
        <label>@lang('cruds.staff.fields.dbs_certificate'):</label>
        <div class="right-sidebox">
            @if(isset($staff) && $staff->dbs_certificate_url)
                <a target="_blank" href="{{ $staff->dbs_certificate_url }}" class="chose-btn mt-0">
                    <x-svg-icons icon="help-pdf" />
                </a>
            @endif
            <div class="chose-btn-area position-relative">
                <a href="javascript:void(0)" class="chose-btn">@lang('global.choose') @lang('global.file')</a>
                <input type="file" name="dbs_certificate" id="fileInput1_3" class="fileInput" accept=".pdf" multiple>
            </div>
        </div>
    </div>
    <div class="form-label">
        <label>@lang('cruds.staff.fields.cv'):</label>
        <div class="right-sidebox">
            @if(isset($staff) && $staff->cv_url)
                <a target="_blank" href="{{ $staff->cv_url }}" class="chose-btn mt-0">
                    <x-svg-icons icon="help-pdf" />
                </a>
            @endif
            <div class="chose-btn-area position-relative">
                <a href="javascript:void(0)" class="chose-btn">@lang('global.choose') @lang('global.file')</a>
                <input type="file" name="cv_image" id="fileInput1_4" class="fileInput" accept=".pdf" multiple>
            </div>
        </div>
    </div>
    <div class="form-label">
        <label>@lang('cruds.staff.fields.staff_budge'):</label>
        <div class="right-sidebox">
            @if(isset($staff) && $staff->staff_budge_url)
                <a target="_blank" href="{{ $staff->staff_budge_url }}" class="chose-btn mt-0">
                    <x-svg-icons icon="help-pdf" />
                </a>
            @endif
            <div class="chose-btn-area position-relative">
                <a href="javascript:void(0)" class="chose-btn">@lang('global.choose') @lang('global.file')</a>
                <input type="file" name="staff_budge" id="fileInput1_5" class="fileInput" accept=".pdf" multiple>
            </div>
        </div>
    </div>
    <div class="form-label">
        <label>@lang('cruds.staff.fields.dbs_check'):</label>
        <div class="right-sidebox">
            @if(isset($staff) && $staff->dbs_check_url)
                <a target="_blank" href="{{ $staff->dbs_check_url }}" class="chose-btn mt-0">
                    <x-svg-icons icon="help-pdf" />
                </a>
            @endif
            <div class="chose-btn-area position-relative">
                <a href="javascript:void(0)" class="chose-btn">@lang('global.choose') @lang('global.file')</a>
                <input type="file" name="dbs_check" id="fileInput1_6" class="fileInput" accept=".pdf" multiple>
            </div>
        </div>
    </div>
    <div class="form-label">
        <label>@lang('cruds.staff.fields.training_check'):</label>
        <div class="right-sidebox">
            @if(isset($staff) && $staff->training_check_url)
                <a target="_blank" href="{{ $staff->training_check_url }}" class="chose-btn mt-0">
                    <x-svg-icons icon="help-pdf" />
                </a>
            @endif
            <div class="chose-btn-area position-relative">
                <a href="javascript:void(0)" class="chose-btn">@lang('global.choose') @lang('global.file')</a>
                <input type="file" name="training_check" id="fileInput1_7" class="fileInput" accept=".pdf" multiple>
            </div>
        </div>
    </div>
     <div class="form-label">
        <label>@lang('cruds.staff.fields.previous_name'):</label>
        <input type="text" name="previous_name" value="{{ (isset($staff) && !empty($staff->profile)) ? $staff->profile->previous_name : ''}}">
    </div>
    <div class="form-group form-check">
        <input type="checkbox" class="form-check-input" id="is_agreement" @if(isset($staff) && $staff->is_agreement) checked @endif required>
        <label class="form-check-label" for="is_agreement">{{ config('constant.staff_document_agreement_text') }}</label>
    </div>
<div class="form-label justify-content-center">
    <button type="submit" class="cbtn submitBtn">
        @if(isset($staff))
            @lang('global.update') @lang('cruds.staff.title_singular')            
        @else
            @lang('global.add') @lang('cruds.staff.title_singular')
        @endif
    </button>
</div>
