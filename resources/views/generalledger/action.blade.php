<div class="text-center">
    <button type="button" class="btn btn-sm btn-info waves-effect btn-label waves-light openAjaxModal mb-2"
        data-id="info_{{ $data->id }}" data-size="xl" data-url="{{ route('generalledger.modal.info', encrypt($data->id)) }}">
        <i class="mdi mdi-information label-icon"></i> Info
    </button>
</div>