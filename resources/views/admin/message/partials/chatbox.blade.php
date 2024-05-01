@if(isset($group))

<div class="col-12">
    <div class="chat-header p-3 d-flex justify-content-between align-items-center">
        <div class="userporfile">
            <div class="userimage">
                <img class="userpic" src="{{asset(config('constant.default.group_icon')) }}" alt="">
            </div>
            <div class="useraccount text-truncate">
                <h4 class="m-0 text-truncate" id="chatHeader">{{ ucwords($group->group_name) }}</h4>
                @php
                  $groupUserList = $group->users()->where('id','!=',auth()->user()->id)->pluck('name')->toArray();
                  $groupUser = 'You, '.implode(', ',$groupUserList);
                @endphp
                <p class="text-truncate content m-0 activeuser">{{ $groupUser }}</p>
            </div>
        </div>
    </div>
</div>
<div class="col-12 h-100 flex-fill overflow-hidden">
    <div class="message-container px-3 h-100 overflow-y-auto" id="messageContainer">

        @if($allMessages->count() > 0)
          @foreach($allMessages as $message)

          @include('admin.message.partials.message-view')

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
                        <button class="add_btn dash-btn green-bg w-115 m-0 submitBtn" id="send-button" type="submit">
                           <x-svg-icons icon="messageSendButton"></x-svg-icons>                                                   
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@else

<div class="col-12">
    <div class="welcome-screen">
        <div class="userporfile">
            <div class="userimage">
                <img class="userpic" src="{{ auth()->user()->profile_image_url ? auth()->user()->profile_image_url : asset(config('constant.default.user_icon')) }}" alt="{{ auth()->user()->name }}">
            </div>
            <div class="useraccount text-truncate">
                <h3>Welcome!</h3>
                <h4 class="m-0 text-truncate" id="chatHeader">{{ ucwords(auth()->user()->name) }}</h4>
                <!-- <p class="text-truncate content m-0 activeuser"></p> -->
            </div>
        </div>
    </div>
</div>

@endif