<div class="modal fade common-modal modal-size-l" id="NnotificationSettings" tabindex="-1" aria-labelledby="NnotificationSettingsLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-820">
        <div class="modal-content">
            <div class="modal-header justify-content-center {{ request()->route()->getName() == 'staffs.createNotification' ? 'blue-bg' : 'green-bg' }}">
                <h5 class="modal-title text-center" id="NnotificationSettingsLabel">
                    @if(request()->route()->getName() == 'staffs.createNotification')
                        @lang('cruds.notification.fields.notification_settings')
                    @else
                        @lang('cruds.notification.fields.new_message')
                    @endif
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body inner-size-l">
                <form class="msg-form" id="addNotificationForm" action="" {{-- method="POST" --}} enctype='multipart/form-data'>
                    @csrf
                    <div class="form-label">
                        <label>@lang('cruds.notification.fields.staff')</label>
                        <div class="right-sidebox-small modal-dropdown">
                            <div class="select-box">
                                <span class="selected-options">@lang('global.select') ...</span>
                            </div>
                            <div class="options" style="display: none;">
                                <p class="selectAllStaff">@lang('cruds.notification.fields.all_staff')</p>
                                <input type="text" id="searchInput" placeholder="Search...">
                                <ul class="custom-check">
                                    @foreach ($staffsNotify as $key=>$item)
                                        <li class="select-option">
                                            <label>
                                                <input type="checkbox" name="staffs[]" class="checkboxes staff-checkbox" value="{{ $item->uuid }}" data-company="{{ $item->company->uuid }}">
                                                <span>{{ ucwords($item->name) }} ({{ $item->email }})</span>
                                            </label>
                                        </li>
                                    @endforeach
                                </ul>
                                <input type="hidden" name="companies[]" id="companyUUId">
                            </div>                          
                        </div>
                        <span class="staffs_error"></span>
                    </div>
                    <div class="form-label select-label">
                        <label for="notification_subject">@lang('cruds.notification.fields.section'):</label>
                        <select class="select2" name="section" id="section">
                            <option value="">@lang('global.select')  ...</option>
                            @foreach (config('constant.notification_subject') as $key=>$val)
                                <option value="{{$key}}">{{ $val }}</option>
                            @endforeach
                        </select>
                        <span class="section_error"></span>
                    </div>
                    <div class="form-label">
                        <label class="text-end px-2">@lang('cruds.notification.fields.subject'): </label>
                        <input type="text" name="subject" value="" placeholder="@lang('cruds.notification.fields.type')......." required>
                    </div>
                    
                    @if(request()->route()->getName() == 'staffs.createNotification')
                        <div class="form-label with-textarea">
                            <label>@lang('cruds.notification.fields.message'):</label>
                            <textarea placeholder="@lang('cruds.notification.fields.type')......." name="message" required></textarea>
                        </div>
                    @else
                        <div class="form-label bottom-textarea">
                            <textarea placeholder="@lang('cruds.notification.fields.type_message').........." name="message" required></textarea>
                        </div>
                    @endif
                    <div class="form-label justify-content-center">
                        <input type="submit" value="@lang('global.send')" id="" class="cbtn submitBtn">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>