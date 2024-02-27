<div class="modal fade common-modal modal-size-l" id="editStaffModal" tabindex="-1" aria-labelledby="editStaffLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-820">
        <div class="modal-content">
            <div class="modal-header justify-content-center sky-bg">
                <h5 class="modal-title text-center" id="editStaffLabel">@lang('global.edit') @lang('cruds.staff.title_singular')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="msg-form" id="editStaffForm" data-action="{{ route('staffs.update', $staff->uuid) }}">
                    @method('PUT')
                    @csrf
                    @include('admin.staff.form') 
                </form>
            </div>
        </div>
    </div>
</div>