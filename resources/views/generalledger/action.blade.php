@php
    $source = str_replace(' ', '', $data->source);
@endphp
<div class="text-center">
    <button type="button"
        class="btn btn-sm btn-info waves-effect btn-label waves-light openAjaxModal mb-2"
        data-id="info_{{ $data->id_ref.$source }}" data-size="xl"
        data-url="{{ route('generalledger.modal.info', [
            'source' => $source,
            'id' => encrypt($data->id_ref)
        ]) }}">
        <i class="mdi mdi-information label-icon"></i> Info
    </button>
</div>
