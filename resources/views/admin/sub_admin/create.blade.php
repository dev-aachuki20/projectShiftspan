<div class="modal fade common-modal modal-size-l" id="addSubAdminModal" tabindex="-1" aria-labelledby="addSubAdminLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-820">
        <div class="modal-content">
            <div class="modal-header justify-content-center green-bg">
                <h5 class="modal-title text-center" id="addSubAdminLabel">+ @lang('global.add') @lang('cruds.client_admin.title_singular')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" >
                <form class="msg-form" id="addSubAdminForm" action="{{route('client-admins.store')}}">
                    @csrf
                    @include('admin.sub_admin.form') 
                </form>
            </div>
        </div>
    </div>
</div>