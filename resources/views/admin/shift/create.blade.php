<div class="modal fade common-modal modal-size-l" id="addShiftModal" tabindex="-1" aria-labelledby="addShiftLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-820">
        <div class="modal-content">
            <div class="modal-header justify-content-center green-bg">
                <h5 class="modal-title text-center" id="addShiftLabel">+ @lang('global.add') @lang('cruds.shift.title_singular')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" >
                <form class="msg-form" id="addShiftForm" action="{{route('shifts.store')}}">
                    @csrf
                    @include('admin.shift.form') 
                </form>
            </div>
        </div>
    </div>
</div>