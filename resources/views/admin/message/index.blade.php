@extends('layouts.app')
@section('title', trans('cruds.message.title_singular'))

@section('customCss')
<link href="{{asset('plugins/jquery-ui/jquery.ui.min.css')}}" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('main-content')
    <div class="animate__animated animate__fadeInUp">
        <div class="msg-content white-bg radius-50 space-30 d-flex align-items-center">
            <h2 class="mb-md-0">@lang('cruds.message.fields.message_center')</h2>
            @can('message_create')
                <a href="javascript:void(0)" class="add_btn dash-btn green-bg" data-bs-toggle="modal" data-bs-target="#NnotificationSettings">+ @lang('cruds.message.fields.new_message') </a><div class="d-sm-none w-100"></div>
            @endcan

            @can('message_delete')
                <button class="del_btn dash-btn red-bg" id="deleteAllMessage">@lang('cruds.message.fields.delete_message')(s)</button>
            @endcan
        </div>
        {{-- <div class="c-admin position-relative">
            <div class="table-responsive">
                {{$dataTable->table(['class' => 'table common-table', 'style' => 'width:100%;'])}}
            </div>
        </div> --}}


    </div>
            {{-- Start Chat System --}}
        <div class="container-fluid px-0 pt-4 chatboxGroup" id="chatboxGroup">
            <div class="row h-100 gx-3">
                <div class="col-xxl-3 col-md-4 h-100 animate__animated animate__fadeInUp">
                    <div class="sidebar h-100 p-3 rounded-4">
                        <div class="user-list h-100">
                            <div class="row h-100 overflow-hidden flex-column flex-nowrap">
                                <div class="col-12 mb-3">
                                    <div class="input-group searchbar">
                                        <input type="text" class="form-control rounded-pill" placeholder="Username" aria-label="Username" aria-describedby="basic-addon1">
                                        <button class="shadow-none btn add_btn dash-btn green-bg w-115 m-0 border-0 rounded-pill">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M20.71 19.29L17.31 15.9C18.407 14.5025 19.0022 12.7767 19 11C19 9.41775 18.5308 7.87103 17.6518 6.55544C16.7727 5.23985 15.5233 4.21447 14.0615 3.60897C12.5997 3.00347 10.9911 2.84504 9.43928 3.15372C7.88743 3.4624 6.46197 4.22433 5.34315 5.34315C4.22433 6.46197 3.4624 7.88743 3.15372 9.43928C2.84504 10.9911 3.00347 12.5997 3.60897 14.0615C4.21447 15.5233 5.23985 16.7727 6.55544 17.6518C7.87103 18.5308 9.41775 19 11 19C12.7767 19.0022 14.5025 18.407 15.9 17.31L19.29 20.71C19.383 20.8037 19.4936 20.8781 19.6154 20.9289C19.7373 20.9797 19.868 21.0058 20 21.0058C20.132 21.0058 20.2627 20.9797 20.3846 20.9289C20.5064 20.8781 20.617 20.8037 20.71 20.71C20.8037 20.617 20.8781 20.5064 20.9289 20.3846C20.9797 20.2627 21.0058 20.132 21.0058 20C21.0058 19.868 20.9797 19.7373 20.9289 19.6154C20.8781 19.4936 20.8037 19.383 20.71 19.29ZM5 11C5 9.81332 5.3519 8.65328 6.01119 7.66658C6.67047 6.67989 7.60755 5.91085 8.7039 5.45673C9.80026 5.0026 11.0067 4.88378 12.1705 5.11529C13.3344 5.3468 14.4035 5.91825 15.2426 6.75736C16.0818 7.59648 16.6532 8.66558 16.8847 9.82946C17.1162 10.9933 16.9974 12.1997 16.5433 13.2961C16.0892 14.3925 15.3201 15.3295 14.3334 15.9888C13.3467 16.6481 12.1867 17 11 17C9.4087 17 7.88258 16.3679 6.75736 15.2426C5.63214 14.1174 5 12.5913 5 11Z" fill="white"/>
                                            </svg>                                            
                                        </button>
                                    </div>
                                </div>
                                <div class="col-12 h-100 flex-fill overflow-y-auto">
                                    <ul id="userGroupList" class="list-group rounded-0">
                                        <li class="list-group-item userporfile activeAccount active" onclick="selectUser('Shift Cancel')">
                                            <div class="userimage">
                                                <img class="userpic" src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?q=80&w=1740&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="">
                                            </div>
                                            <div class="useraccount">
                                                <h5 class="content"><span class="text-truncate">Shift Cancel</span> <span class="time">11:41 AM</span></h5>
                                                <div class="msg-type"><p class="text-truncate content">Sent To : Vikram Singh Shekhawat</p><span class="chatmsg-number">4</span></div>
                                            </div>
                                        </li>
                                        <li class="list-group-item userporfile" onclick="selectUser('Shift1')">
                                            <div class="userimage">
                                                <img class="userpic" src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?q=80&w=1740&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="">
                                            </div>
                                            <div class="useraccount">
                                                <h5 class="content"><span class="text-truncate">Shift1</span> <span class="time">11:41 AM</span></h5>
                                                <div class="msg-type"><p class="text-truncate content">Sent To : Vikram Singh Shekhawat</p></div>
                                            </div>
                                        </li>
                                        <li class="list-group-item userporfile" onclick="selectUser('HIPL')">
                                            <div class="userimage">
                                                <img class="userpic" src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?q=80&w=1740&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="">
                                            </div>
                                            <div class="useraccount">
                                                <h5 class="content"><span class="text-truncate">HIPL</span> <span class="time">11:41 AM</span></h5>
                                                <div class="msg-type"><p class="text-truncate content">Sent To : Vikram Singh Shekhawat</p></div>
                                            </div>
                                        </li>
                                        <li class="list-group-item userporfile" onclick="selectUser('Help Full Insight')">
                                            <div class="userimage">
                                                <img class="userpic" src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?q=80&w=1740&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="">
                                            </div>
                                            <div class="useraccount">
                                                <h5 class="content"><span class="text-truncate">Help Full Insight</span> <span class="time">11:41 AM</span></h5>
                                                <div class="msg-type"><p class="text-truncate content">Sent To : Vikram Singh Shekhawat</p></div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xxl-9 col-md-8 chat-panel h-100 ">
                    <div class="card chatcard h-100 overflow-hidden animate__animated animate__fadeInUp">
                        <div class="row h-100 flex-column flex-nowrap overflow-hidden">
                            <div class="col-12">
                                <div class="chat-header p-3 d-flex justify-content-between align-items-center">
                                    <div class="userporfile activeAccount">
                                        <div class="userimage">
                                            <img class="userpic" src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?q=80&w=1740&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="">
                                        </div>
                                        <div class="useraccount text-truncate">
                                            <h4 class="m-0 text-truncate" id="chatHeader">User/Group</h4>
                                            <p class="text-truncate content m-0 activeuser">Active</p>
                                        </div>
                                    </div>
                                    <div class="usersetting d-flex align-items-center gap-2">
                                        <div class="dropdown">
                                            <button class="btn btn-secondary border-0 shadow-none editbtn" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                                                <svg class="editicon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M12 7C12.3956 7 12.7822 6.8827 13.1111 6.66294C13.44 6.44318 13.6964 6.13082 13.8478 5.76537C13.9991 5.39992 14.0387 4.99778 13.9616 4.60982C13.8844 4.22186 13.6939 3.86549 13.4142 3.58579C13.1345 3.30608 12.7781 3.1156 12.3902 3.03843C12.0022 2.96126 11.6001 3.00087 11.2346 3.15224C10.8692 3.30362 10.5568 3.55996 10.3371 3.88886C10.1173 4.21776 10 4.60444 10 5C10 5.53043 10.2107 6.03914 10.5858 6.41421C10.9609 6.78929 11.4696 7 12 7ZM12 17C11.6044 17 11.2178 17.1173 10.8889 17.3371C10.56 17.5568 10.3036 17.8692 10.1522 18.2346C10.0009 18.6001 9.96126 19.0022 10.0384 19.3902C10.1156 19.7781 10.3061 20.1345 10.5858 20.4142C10.8655 20.6939 11.2219 20.8844 11.6098 20.9616C11.9978 21.0387 12.3999 20.9991 12.7654 20.8478C13.1308 20.6964 13.4432 20.44 13.6629 20.1111C13.8827 19.7822 14 19.3956 14 19C14 18.4696 13.7893 17.9609 13.4142 17.5858C13.0391 17.2107 12.5304 17 12 17ZM12 10C11.6044 10 11.2178 10.1173 10.8889 10.3371C10.56 10.5568 10.3036 10.8692 10.1522 11.2346C10.0009 11.6001 9.96126 12.0022 10.0384 12.3902C10.1156 12.7781 10.3061 13.1345 10.5858 13.4142C10.8655 13.6939 11.2219 13.8844 11.6098 13.9616C11.9978 14.0387 12.3999 13.9991 12.7654 13.8478C13.1308 13.6964 13.4432 13.44 13.6629 13.1111C13.8827 12.7822 14 12.3956 14 12C14 11.4696 13.7893 10.9609 13.4142 10.5858C13.0391 10.2107 12.5304 10 12 10Z" fill="black"/>
                                                </svg>                                                
                                            </button>
                                            <ul class="dropdown-menu border-0" aria-labelledby="dropdownMenuButton1">
                                                <li><button type="button" class="dropdown-item del_btn">Delete</button></li>
                                            </ul>
                                        </div>
                                        <button class="btn close-btn d-md-none d-flex shadow-none border-0">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M13.4099 11.9999L19.7099 5.70994C19.8982 5.52164 20.004 5.26624 20.004 4.99994C20.004 4.73364 19.8982 4.47825 19.7099 4.28994C19.5216 4.10164 19.2662 3.99585 18.9999 3.99585C18.7336 3.99585 18.4782 4.10164 18.2899 4.28994L11.9999 10.5899L5.70994 4.28994C5.52164 4.10164 5.26624 3.99585 4.99994 3.99585C4.73364 3.99585 4.47824 4.10164 4.28994 4.28994C4.10164 4.47825 3.99585 4.73364 3.99585 4.99994C3.99585 5.26624 4.10164 5.52164 4.28994 5.70994L10.5899 11.9999L4.28994 18.2899C4.19621 18.3829 4.12182 18.4935 4.07105 18.6154C4.02028 18.7372 3.99414 18.8679 3.99414 18.9999C3.99414 19.132 4.02028 19.2627 4.07105 19.3845C4.12182 19.5064 4.19621 19.617 4.28994 19.7099C4.3829 19.8037 4.4935 19.8781 4.61536 19.9288C4.73722 19.9796 4.86793 20.0057 4.99994 20.0057C5.13195 20.0057 5.26266 19.9796 5.38452 19.9288C5.50638 19.8781 5.61698 19.8037 5.70994 19.7099L11.9999 13.4099L18.2899 19.7099C18.3829 19.8037 18.4935 19.8781 18.6154 19.9288C18.7372 19.9796 18.8679 20.0057 18.9999 20.0057C19.132 20.0057 19.2627 19.9796 19.3845 19.9288C19.5064 19.8781 19.617 19.8037 19.7099 19.7099C19.8037 19.617 19.8781 19.5064 19.9288 19.3845C19.9796 19.2627 20.0057 19.132 20.0057 18.9999C20.0057 18.8679 19.9796 18.7372 19.9288 18.6154C19.8781 18.4935 19.8037 18.3829 19.7099 18.2899L13.4099 11.9999Z" fill="black"/>
                                            </svg>                                        
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 h-100 flex-fill overflow-y-auto">
                                {{-- <div class="message-container p-3" id="messageContainer">
                                    <!-- Chat messages will be dynamically added here -->
                                </div> --}}
                                <div class="message-container px-3" id="messageContainer">
                                    <div class="message incoming">
                                        <div class="message-content">Hello! <span class="message_time">12:05 PM</span></div>
                                    </div>
                                    <div class="datemention"><span>24 April 2024</span></div>
                                    <div class="message outgoing">
                                        <div class="message-content">Hi there! <span class="message_time">12:05 PM</span></div>
                                    </div>
                                    <div class="datemention"><span>Monday</span></div>
                                    <div class="message incoming">
                                        <div class="message-content">How are you? <span class="message_time">12:05 PM</span></div>
                                    </div>
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- End Chat System --}}

    {{-- Notification model --}}
    @include('admin.staff.notification.create')
@endsection

@section('customJS')

@parent
{!! $dataTable->scripts() !!}
<script src="{{asset('plugins/jquery-ui/jquery.ui.min.js')}}"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).on('shown.bs.modal', function() {
        $('.select2').each(function() {
            if (this.isConnected) {
                $(this).select2({
                    width: 'calc(100% - 180px)',
                    dropdownParent: $(this).closest('.select-label'),
                    selectOnClose: false
                });
            }
        });


        $('.staff-checkbox').on('change', function() {
            selectedStaffCheckboxes();
        });

        $(document).on('click','.selectAllStaff',function(event) {

            var $checkboxes = $('.options input[type="checkbox"]');
            var allChecked = $checkboxes.filter(':checked').length === $checkboxes.length;
            $checkboxes.prop('checked', !allChecked);

            selectedStaffCheckboxes();

        });

        $(function () {
            $(".datepicker").datepicker({
                maxDate: 0,
                dateFormat: 'yy-mm-dd',
                changeMonth: true,
                changeYear: true,
                yearRange: "-100:+0",
                // showButtonPanel: true,
            });
        });
    });

    $(document).ready(function(){
        $('#searchInput').on('input', function() {
            var searchText = $(this).val().toLowerCase();
            $('.custom-check li').each(function() {
                var text = $(this).text().toLowerCase();
                if (text.includes(searchText)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });
    });

    @can('message_create')
        $(document).on('submit', '#addNotificationForm', function (e) {
            e.preventDefault();
            $('.validation-error-block').remove();
            $(".submitBtn").attr('disabled', true);

            $('.loader-div').show();

            var formData = new FormData(this);

            $.ajax({
                type: 'post',
                url: "{{route('messages.store')}}",
                dataType: 'json',
                contentType: false,
                processData: false,
                data: formData,
                success: function (response) {
                    $('.loader-div').hide();
                    $(".submitBtn").attr('disabled', false);
                    if(response.success) {
                        $('#addNotificationForm')[0].reset();
                        $('#NnotificationSettings').modal('hide');

                        var selected = [];
                        selected.push($(this).closest(".select-option").find('span').text());
                        $('.selected-options').text(selected.length > 0 ? 'Select...' : 'Select...');

                        $('.popup_render_div').html('');
                        $('#message-centre-table').DataTable().ajax.reload(null, false);
                        toasterAlert('success',response.message);
                    }
                },
                error: function (response) {
                    $('.loader-div').hide();
                    $(".submitBtn").attr('disabled', false);

                    if(response.responseJSON.error_type == 'something_error'){
                        toasterAlert('error',response.responseJSON.error);
                    } else {
                        var errorLabelTitle = '';
                        $.each(response.responseJSON.errors, function (key, item) {
                            errorLabelTitle = '<span class="validation-error-block">'+item[0]+'</sapn>';
                            if (key.indexOf('staffs') !== -1) {
                                $(".staffs_error").html(errorLabelTitle);
                            } else if(key== 'section') {
                                $(".section_error").html(errorLabelTitle);
                            } else if(key== 'subject') {
                                $(".subject_error").html(errorLabelTitle);
                            } else{
                                $(document).find('[name='+key+']').after(errorLabelTitle);
                            }
                        });
                    }
                },
                complete: function(res){
                    $(".submitBtn").attr('disabled', false);
                }
            });
        });
    @endcan

    @can('message_delete')
        $(document).on('click', '#deleteAllMessage', function(e){
            e.preventDefault();
            var t = $(this);
            var selectedIds = [];
            $('.dt_cb:checked').each(function() {
                selectedIds.push($(this).data('id'));
            });
            if(selectedIds.length == 0){
                fireWarningSwal('Warning', "{{ trans('messages.warning_select_record') }}");
                return false;
            }
            Swal.fire({
                title: "{{ trans('global.areYouSure') }}",
                text: "{{ trans('global.onceClickedRecordDeleted') }}",
                icon: "warning",
                showDenyButton: true,
                //   showCancelButton: true,
                confirmButtonText: "{{ trans('global.swl_confirm_button_text') }}",
                denyButtonText: "{{ trans('global.swl_deny_button_text') }}",
            })
            .then(function(result) {
                if (result.isConfirmed) {
                    $('.loader-div').show();
                    $.ajax({
                        url: "{{route('messages.massDestroy')}}",
                        type: "POST",
                        data: {
                            ids: selectedIds,
                            _token: "{{ csrf_token() }}",
                        },
                        dataType: 'json',
                        success: function (response) {
                            if(response.success) {
                                $('#message-centre-table').DataTable().ajax.reload(null, false);
                                setTimeout(() => {
                                    $('#dt_cb_all').prop('checked', false);
                                }, 500);
                                toasterAlert('success',response.message);
                            }
                            else {
                                toasterAlert('error',response.message);
                            }
                            $('.loader-div').hide();
                        },
                        error: function(res){
                            toasterAlert('error',res.responseJSON.error);
                            $('.loader-div').hide();
                        }
                    })
                }
            });
        })
    @endcan

    $('.btn-close').click(function() {
        $('.validation-error-block').remove();
    });


    function selectedStaffCheckboxes(){
        var selectedDataArray = [];
        var checkedCheckboxes = $(".staff-checkbox:checked");
        checkedCheckboxes.each(function(){
            var dataValue = $(this).attr("data-company");
            if (!selectedDataArray.includes(dataValue)) {
                selectedDataArray.push(dataValue);
            }
        });

        if(selectedDataArray.length > 0){
            $("#companyUUId").val(selectedDataArray.join(','));
        }else{
            $("#companyUUId").val('');
        }
    }
</script>

@endsection
