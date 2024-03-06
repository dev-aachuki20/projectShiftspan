@if($type == 'ClockIn')
    <div class="modal fade common-modal modal-size-l" id="clockIn" tabindex="-1" aria-labelledby="clockInLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-820">
            <div class="modal-content">
                <div class="modal-header justify-content-center green-bg">
                    <h5 class="modal-title text-center" id="clockInLabel">@lang('cruds.shift.fields.clock_in')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="msg-form">
                        @forelse($shiftData as $data)
                            <div class="form-label">
                                <label>@lang('cruds.shift.fields.clock_in_time'):</label>
                                <div class="right-sidebox d-flex align-items-center justify-content-center">
                                    <span class="fw-600">
                                        {{ date("h:i A", strtotime($data['clockin_date'])) ?? '08:00 AM (IST)'}}
                                    </span>
                                </div>
                            </div>
                            <div class="form-label">
                                <label>@lang('cruds.shift.fields.geoloaction'):</label>
                                <div class="right-sidebox">
                                    <div id="map" class="map-container" style="width: 100%; height: 200px;" data-lat="{{ $data['clockin_latitude'] }}" data-lng="{{ $data['clockin_longitude'] }}"></div>
                                </div>
                            </div>
                        @empty
                            <strong>
                                {{ ucfirst(strtolower($type)).' '.trans('global.data_not_found') }}
                            </strong>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@elseif($type == 'ClockOut')
    <!-- ClockOut Modal -->
    <div class="modal fade common-modal modal-size-l" id="clockOut" tabindex="-1" aria-labelledby="clockOutLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-820">
            <div class="modal-content">
                <div class="modal-header justify-content-center green-bg">
                    <h5 class="modal-title text-center" id="clockOutLabel">@lang('cruds.shift.fields.clock_out')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="msg-form">
                        @forelse($shiftData as $data)
                            <div class="form-label">
                                <label>@lang('cruds.shift.fields.clock_out_time'):</label>
                                <div class="right-sidebox d-flex align-items-center justify-content-center">
                                    <span class="fw-600">{{ date("h:i A", strtotime($data['clockout_date'])) ?? '20:00 AM  (IST)'}}</span>
                                </div>
                            </div>
                            <div class="form-label">
                                <label>@lang('cruds.shift.fields.geoloaction'):</label>
                                <div class="right-sidebox">
                                    <div id="map" class="map-container" style="width: 100%; height: 200px;" data-lat="{{ $data['clockout_latitude'] }}" data-lng="{{ $data['clockout_longitude'] }}"></div>
                                </div>
                            </div>
                        @empty
                            <strong>
                                {{ ucfirst(strtolower($type)).' '.trans('global.data_not_found') }}
                            </strong>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@else
    <div class="modal fade common-modal modal-size-l" id="timeSheet" tabindex="-1" aria-labelledby="timeSheetLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-820">
            <div class="modal-content">
                <div class="modal-header justify-content-center green-bg">
                    <h5 class="modal-title text-center" id="timeSheetLabel">@lang('cruds.shift.fields.timesheet')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="msg-form">
                        @if(isset($shiftData))
                            <div class="form-label">
                                <label>@lang('cruds.shift.fields.manager_name'):</label>
                                <div class="right-sidebox d-flex align-items-center justify-content-center">
                                    <span class="fw-600">{{$shiftData->manager_name}}</span>
                                </div>
                            </div>
                            <div class="form-label">
                                <label>@lang('cruds.shift.fields.manager_signature'):</label>
                                <div class="right-sidebox d-flex align-items-center justify-content-center">
                                    <img src="{{isset($shiftData) && !empty($shiftData->authorized_signatur_url) ? $shiftData->authorized_signatur_url : asset('images/manager-sign.png')}}" alt="Manager Signature" class="img-fluid">
                                </div>
                            </div>
                        @else
                            <strong>{{ ucfirst(strtolower($type)).' '.trans('global.data_not_found') }}</strong>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif