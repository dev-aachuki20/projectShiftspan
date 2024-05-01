@if($groups->count() > 0)
<ul id="userGroupList" class="list-group rounded-0">

    @foreach($groups as $group)

    {{-- <li class="list-group-item userporfile activeAccount active" onclick="selectUser('{{ $group->uuid }}')"> --}}
    <li class="list-group-item userporfile groupItem" data-listId="{{ $group->uuid }}" >
        <div class="userimage">
            <img class="userpic" src="{{asset(config('constant.default.user_icon')) }}" alt="Group Image">
        </div>
        <div class="useraccount">
            @php
           
             $latestMessage = $group->messages()->orderBy('created_at','desc')->first();   
            
             $totalUnreadMessage =  $group->messages()->where('group_id',$group->id)->where('user_id','!=',auth()->user()->id)->whereDoesntHave('usersSeen', function ($query) {
                                        $query->where('user_id', auth()->user()->id);
                                    })->count();
            @endphp
            <h5 class="content"><span class="text-truncate">{{ ucwords($group->group_name) }}</span> <span class="time">{{ dateFormat($latestMessage->created_at, config('constant.date_format.time'))}}</span></h5>

           
            <div class="msg-type"><p class="text-truncate content">{{ ($latestMessage->user->id == auth()->user()->id) ? 'You' : explode(' ',$latestMessage->user->name)[0] }} : {{ $latestMessage->content }}</p>
                @if($totalUnreadMessage > 0)
                <span class="chatmsg-number totalUnreadMess">{{ $totalUnreadMessage }}</span>
                @endif
            </div>
        </div>
    </li>

    @endforeach
    
</ul>
@endif