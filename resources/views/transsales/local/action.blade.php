<a href="{{ route('transsales.local.info', encrypt($data->id)) }}" type="button" class="btn btn-sm btn-info waves-effect btn-label waves-light">
    <i class="mdi mdi-information label-icon"></i> Detail
</a>
<a href="{{ route('transsales.local.print', encrypt($data->id)) }}" target="_blank" type="button" class="btn btn-sm btn-danger waves-effect btn-label waves-light">
    <i class="mdi mdi-printer label-icon"></i> Print PDF
</a>