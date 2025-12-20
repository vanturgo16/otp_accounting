<div class="text-center">
    <button type="button" class="btn btn-sm btn-primary waves-effect btn-label waves-light openAjaxModal mb-2"
        data-id="edit_{{ $data->id }}" data-size="md" data-url="{{ route('ppn.modal.edit', encrypt($data->id)) }}">
        <i class="mdi mdi-update label-icon"></i> Update
    </button>
</div>