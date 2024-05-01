@if($message)
    
    @if($message->type == 'text')
        @if($message->user_id == auth()->user()->id)
            <div class="message outgoing">
                <div class="message-content">{!!  nl2br($message->content) !!}<span class="message_time">{{ dateFormat($message->created_at, config('constant.date_format.time'))}}</span></div>
            </div>
        @else
            <div class="message incoming">
                <div class="message-content">{!! nl2br($message->content) !!}<span class="message_time"><span class="pe-1">{{ explode(' ',$message->user->name)[0] }},</span> {{ dateFormat($message->created_at, config('constant.date_format.time'))}}</span></div>
            </div>
        @endif
    @endif

@endif