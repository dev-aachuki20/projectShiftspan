<div class="col-12">
    <div class="chat-header p-3 d-flex justify-content-between align-items-center">
        <div class="userporfile">
            <div class="userimage">
                <img class="userpic" src="{{asset(config('constant.default.user_icon')) }}" alt="">
            </div>
            <div class="useraccount text-truncate">
                <h4 class="m-0 text-truncate" id="chatHeader">{{ ucwords($group->group_name) }}</h4>
                <p class="text-truncate content m-0 activeuser"></p>
            </div>
        </div>
    </div>
</div>
<div class="col-12 h-100 flex-fill overflow-y-auto">
    <div class="message-container px-3" id="messageContainer">

        @if($allMessages->count() > 0)
          @foreach($allMessages as $message)

          @if($message->type == 'text')
            @if($message->user_id == auth()->user()->id)
                <div class="message outgoing">
                    <div class="message-content">{{ $message->content }}<span class="message_time">{{ dateFormat($message->created_at, config('constant.date_format.time'))}}</span></div>
                </div>
            @else
                <div class="message incoming">
                    <div class="message-content">{{ $message->content }}<span class="message_time">{{ dateFormat($message->created_at, config('constant.date_format.time'))}}</span></div>
                </div>
            @endif
          @endif

          @endforeach
        @endif
       

        {{-- <div class="datemention"><span>24 April 2024</span></div> --}}

        {{-- <div class="datemention"><span>Monday</span></div> --}}

    </div>
</div>
<div class="col-12">
    <div class="message-input p-3">
        <form id="messageInputForm" method="POST" action="{{ route('messages.send',$group->uuid) }}">
            @csrf
            <div class="row gx-2 align-items-center">
                <div class="col">
                    <div class="addimg d-none mb-2 rounded-3" id="addimg">
                        <img id="imagePreview" class="img-fluid" alt="Image Preview">
                        <span class="closeBtn" id="closeFile">
                            <x-svg-icons icon="closeBtn"></x-svg-icons>                                                
                        </span>
                    </div>
                    <textarea id="messageInput" name="message_content" rows="1" class="form-control shadow-none" placeholder="Type your message..." style="height: 48px;"></textarea>
                </div>
                <div class="col-auto d-flex">
                    <div class="d-flex gap-2">
                        {{-- <label for="fileInput" class="file-label">
                            <x-svg-icons icon="fileInput"></x-svg-icons>                                                                                      
                        </label>
                        <input type="file" id="fileInput" class="form-control-file d-none" accept=".jpg, .jpeg, .png" onchange="previewImage()">
                        <button class="btn btn-secondary rounded-circle shadow-none" onclick="toggleEmojiPicker()">😀</button> --}}
                        <button class="add_btn dash-btn green-bg w-115 m-0 submitBtn" type="submit">
                           <x-svg-icons icon="messageSendButton"></x-svg-icons>                                                   
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>