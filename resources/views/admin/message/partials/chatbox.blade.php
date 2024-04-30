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
        <div class="row gx-2 align-items-center">
            <div class="col">
                <div class="addimg d-none mb-2 rounded-3" id="addimg">
                    <img id="imagePreview" class="img-fluid" alt="Image Preview">
                    <span class="closeBtn" id="closeFile">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12.0118 2.00391C6.49115 2.00061 2.01306 6.47333 2.00977 11.994C2.00647 17.5147 6.47919 21.9928 11.9999 21.9961C14.6522 22.0004 17.1969 20.9479 19.0712 19.0713C20.9462 17.1979 22.0004 14.6565 22.002 12.006C22.0053 6.48529 17.5325 2.0072 12.0118 2.00391ZM12.0121 20.9961C7.0437 20.9995 3.01331 16.9746 3.00989 12.0061C3.00653 7.03772 7.0315 3.00727 11.9999 3.00391C14.387 2.99964 16.6773 3.94678 18.3641 5.63574C20.0516 7.3216 21.0005 9.60858 21.0021 11.9939C21.0055 16.9623 16.9805 20.9927 12.0121 20.9961ZM12.7069 12L15.8885 8.81836C16.079 8.62396 16.079 8.31287 15.8885 8.11847C15.6953 7.92127 15.3787 7.91809 15.1815 8.11133L11.9999 11.293L8.81824 8.11139C8.62384 7.9209 8.31275 7.9209 8.11835 8.11139C7.92114 8.30463 7.91797 8.62116 8.11121 8.81836L11.2928 12L8.11121 15.1816C8.01746 15.2754 7.96485 15.4025 7.96478 15.5351C7.96478 15.8112 8.1886 16.0351 8.46472 16.0352C8.59735 16.0353 8.72461 15.9826 8.81824 15.8887L11.9999 12.707L15.1815 15.8887C15.2751 15.9826 15.4024 16.0353 15.535 16.0352C15.6676 16.0351 15.7947 15.9825 15.8884 15.8888C16.0837 15.6935 16.0837 15.377 15.8885 15.1816L12.7069 12Z" fill="black"/>
                        </svg>                                                    
                    </span>
                </div>
                <textarea id="messageInput" rows="1" class="form-control shadow-none" placeholder="Type your message..." style="height: 48px;"></textarea>
            </div>
            <div class="col-auto d-flex">
                <div class="d-flex gap-2">
                    {{-- <label for="fileInput" class="file-label">
                        <svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M37.6719 13.6339C38.0365 14.0089 38.349 14.5179 38.6094 15.1607C38.8698 15.8036 39 16.3929 39 16.9286V40.0714C39 40.6071 38.8177 41.0625 38.4531 41.4375C38.0885 41.8125 37.6458 42 37.125 42H10.875C10.3542 42 9.91146 41.8125 9.54688 41.4375C9.18229 41.0625 9 40.6071 9 40.0714V7.92857C9 7.39286 9.18229 6.9375 9.54688 6.5625C9.91146 6.1875 10.3542 6 10.875 6H28.375C28.8958 6 29.4688 6.13393 30.0938 6.40179C30.7188 6.66964 31.2135 6.99107 31.5781 7.36607L37.6719 13.6339ZM29 8.73214V16.2857H36.3438C36.2135 15.8973 36.0703 15.6228 35.9141 15.4621L29.8008 9.17411C29.6445 9.01339 29.3776 8.86607 29 8.73214ZM36.5 39.4286V18.8571H28.375C27.8542 18.8571 27.4115 18.6696 27.0469 18.2946C26.6823 17.9196 26.5 17.4643 26.5 16.9286V8.57143H11.5V39.4286H36.5ZM34 30.4286V36.8571H14V33L17.75 29.1429L20.25 31.7143L27.75 24L34 30.4286ZM17.75 26.5714C16.7083 26.5714 15.8229 26.1964 15.0938 25.4464C14.3646 24.6964 14 23.7857 14 22.7143C14 21.6429 14.3646 20.7321 15.0938 19.9821C15.8229 19.2321 16.7083 18.8571 17.75 18.8571C18.7917 18.8571 19.6771 19.2321 20.4062 19.9821C21.1354 20.7321 21.5 21.6429 21.5 22.7143C21.5 23.7857 21.1354 24.6964 20.4062 25.4464C19.6771 26.1964 18.7917 26.5714 17.75 26.5714Z" fill="black"/>
                        </svg>                                                                                                      
                    </label>
                    <input type="file" id="fileInput" class="form-control-file d-none" accept=".jpg, .jpeg, .png" onchange="previewImage()">
                    <button class="btn btn-secondary rounded-circle shadow-none" onclick="toggleEmojiPicker()">😀</button> --}}
                    <button class="add_btn dash-btn green-bg w-115 m-0" onclick="sendMessage()">
                        <svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#clip0_717_4)"><mask id="mask0_717_4" style="mask-type:luminance" maskUnits="userSpaceOnUse" x="0" y="0" width="48" height="48"><path d="M48 0H0V48H48V0Z" fill="white"/></mask><g mask="url(#mask0_717_4)"><path d="M6.16212 27.2536L20.1639 24.4531C21.4141 24.2033 21.4141 23.7968 20.1639 23.5471L6.16212 20.7466C5.32812 20.5801 4.51587 19.7671 4.34937 18.9338L1.54887 4.93209C1.29837 3.68109 2.03562 3.09984 3.19437 3.63459L45.9211 23.3543C46.6929 23.7106 46.6929 24.2896 45.9211 24.6458L3.19437 44.3656C2.03562 44.9003 1.29837 44.3191 1.54887 43.0681L4.34937 29.0663C4.51587 28.2331 5.32812 27.4201 6.16212 27.2536Z" fill="white"/></g></g><defs><clipPath id="clip0_717_4"><rect width="48" height="48" fill="white"/></clipPath></defs></svg>                                                    
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>