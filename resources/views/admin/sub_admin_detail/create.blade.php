<div class="modal fade common-modal modal-size-l" id="addSubAdminDetailModal" tabindex="-1" aria-labelledby="addSubAdminDetailLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-820">
        <div class="modal-content">
            <div class="modal-header justify-content-center green-bg">
                <h5 class="modal-title text-center" id="addSubAdminDetailLabel">+ @lang('global.add') @lang('cruds.client_detail.title_singular')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body inner-size-l" >
                <form class="msg-form" id="addSubAdminDetailForm" action="{{route('client-details.store')}}">
                    @csrf
                    @include('admin.sub_admin_detail.form') 
                </form>
            </div>
        </div>
    </div>
</div>