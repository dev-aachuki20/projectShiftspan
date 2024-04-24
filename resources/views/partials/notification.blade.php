@forelse ($notification as $item)
    @php
      $notificationRoute = '';

      if(in_array($item->notification_type,config('constant.notification_routes.shifts'))){
        $notificationRoute = route('shifts.index');
      }else  if(in_array($item->notification_type,config('constant.notification_routes.staffs'))){
        $notificationRoute = route('staffs.index');
      }else  if(in_array($item->notification_type,config('constant.notification_routes.messages'))){
        $notificationRoute = route('messages.index');
      }
    @endphp

    <li class="notificationlist">
        <a href="{{ ($notificationRoute != '') ? $notificationRoute : 'javascript:void(0)' }}" class="un-read {{-- read --}}" {{-- onclick="markAsRead('{{$item->id}}')"--}}>
            <h6>{{$item->subject}}</h6>

            <p class="discription">{{$item->message}}</p>
             <span class="dropdown">
                <x-svg-icons icon="dropdown" />                  
             </span>
            <div class="n-delete-btn text-end mt-3">
                <button class="dash-btn red-bg small-btn icon-btn deleteNotfiyBtn" data-uuid="{{ $item->id }}">
                    <span><x-svg-icons icon="delete" />@lang('global.delete')</span>
                </button>
            </div>
        </a>
    </li>

@empty
    <li>
        <h6></h6>
        <p>@lang('global.data_not_found')</p>
    </li>
@endforelse