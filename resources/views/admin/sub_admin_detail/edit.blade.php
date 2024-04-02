<div class="modal fade common-modal modal-size-l" id="editSubAdminDetailModal" tabindex="-1" aria-labelledby="editSubAdminDetailLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-820">
        <div class="modal-content">
            <div class="modal-header justify-content-center sky-bg">
                <h5 class="modal-title text-center" id="editSubAdminDetailLabel">@lang('global.edit') @lang('cruds.listed_businesses.title_singular')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body inner-size-l">
                <form class="msg-form" id="editSubAdminDetailForm" data-action="{{ route('client-details.update', $subAdminDetail->uuid) }}">
                    @method('PUT')
                    @csrf
                    @include('admin.sub_admin_detail.form') 
                </form>
            </div>
        </div>
    </div>
</div>