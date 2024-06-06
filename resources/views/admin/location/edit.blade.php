<div class="modal fade common-modal modal-size-l location-group-modal" id="editLocationModal" tabindex="-1" aria-labelledby="editLocationLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-820">
        <div class="modal-content">
            <div class="modal-header justify-content-center sky-bg">
                <h5 class="modal-title text-center" id="editLocationLabel">@lang('global.edit') @lang('cruds.location.title_singular')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="msg-form" id="editLocationForm" data-action="{{ route('locations.update', $location->uuid) }}">
                    @method('PUT')
                    @csrf
                    @include('admin.location.form') 
                </form>
            </div>
        </div>
    </div>
</div>