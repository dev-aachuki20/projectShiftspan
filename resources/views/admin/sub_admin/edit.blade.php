<div class="modal fade common-modal modal-size-l" id="editSubAdminModal" tabindex="-1" aria-labelledby="editSubAdminLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-820">
        <div class="modal-content">
            <div class="modal-header justify-content-center sky-bg">
                <h5 class="modal-title text-center" id="editSubAdminLabel">@lang('global.edit') @lang('cruds.client_admin.title_singular')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="msg-form" id="editSubAdminForm" data-action="{{ route('client-admins.update', $subAdmin->uuid) }}">
                    @method('PUT')
                    @csrf
                    @include('admin.sub_admin.form') 
                </form>
            </div>
        </div>
    </div>
</div>