<a href="{{ route('transsales.local.info', encrypt($data->id)) }}" class="btn btn-sm btn-info mt-1 mb-1">
    <i class="mdi mdi-information label-icon" title="Detail"></i>
</a>
<a href="{{ route('transsales.local.print', encrypt($data->id)) }}" target="_blank" class="btn btn-sm btn-danger mt-1 mb-1">
    <i class="mdi mdi-printer label-icon" title="Print PDF"></i>
</a>