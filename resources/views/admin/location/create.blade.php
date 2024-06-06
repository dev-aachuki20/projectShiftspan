<div class="modal fade common-modal modal-size-l location-group-modal" id="addLocationModal" tabindex="-1" aria-labelledby="addLocationLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-820">
        <div class="modal-content">
            <div class="modal-header justify-content-center green-bg">
                <h5 class="modal-title text-center" id="addLocationLabel">+ @lang('global.add') @lang('cruds.location.title_singular')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" >
                <form class="msg-form" id="addLocationForm" action="{{route('locations.store')}}">
                    @csrf
                    @include('admin.location.form') 
                </form>
            </div>
        </div>
    </div>
</div>