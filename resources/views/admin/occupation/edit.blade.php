<div class="modal fade common-modal modal-size-l" id="editOccupationModal" tabindex="-1" aria-labelledby="editOccupationLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-820">
        <div class="modal-content">
            <div class="modal-header justify-content-center sky-bg">
                <h5 class="modal-title text-center" id="editOccupationLabel">@lang('global.edit') @lang('cruds.occupation.title_singular')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="msg-form" id="editOccupationForm" data-action="{{ route('occupations.update', $occupation->uuid) }}">
                    @method('PUT')
                    @csrf
                    @include('admin.occupation.form') 
                </form>
            </div>
        </div>
    </div>
</div>