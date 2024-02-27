<div class="modal fade common-modal modal-size-l" id="addStaffModal" tabindex="-1" aria-labelledby="addStaffLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-820">
        <div class="modal-content">
            <div class="modal-header justify-content-center green-bg">
                <h5 class="modal-title text-center" id="addStaffLabel">+ @lang('global.add') @lang('cruds.staff.title_singular')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" >
                <form class="msg-form" id="addStaffForm" action="{{route('staffs.store')}}" enctype='multipart/form-data'>
                    @csrf
                    @include('admin.staff.form') 
                </form>
            </div>
        </div>
    </div>
</div>