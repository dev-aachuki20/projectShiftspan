<div class="modal fade common-modal modal-size-l" id="editShiftModal" tabindex="-1" aria-labelledby="editShiftLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-820">
        <div class="modal-content">
            <div class="modal-header justify-content-center sky-bg">
                <h5 class="modal-title text-center" id="editShiftLabel">@lang('global.edit') @lang('cruds.shift.title_singular')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="msg-form" id="editShiftForm" data-action="{{ route('shifts.update', $shift->uuid) }}">
                    @method('PUT')
                    @csrf
                    @include('admin.shift.form') 
                </form>
            </div>
        </div>
    </div>
</div>