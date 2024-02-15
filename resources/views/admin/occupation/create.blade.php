<div class="modal fade common-modal modal-size-l" id="addOccupationModal" tabindex="-1" aria-labelledby="addOccupationLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-820">
        <div class="modal-content">
            <div class="modal-header justify-content-center green-bg">
                <h5 class="modal-title text-center" id="addOccupationLabel">+ @lang('global.add')   @lang('global.add') @lang('cruds.occupation.title_singular')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" >
                <form class="msg-form" id="addOccupationForm" action="{{route('occupations.store')}}">
                    @csrf
                    @include('admin.occupation.form') 
                </form>
            </div>
        </div>
    </div>
</div>