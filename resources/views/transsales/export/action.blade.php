{{-- <a href="{{ route('transsales.export.info', encrypt($data->id)) }}" class="btn btn-sm btn-info mt-1 mb-1">
    <i class="mdi mdi-information label-icon" title="Detail"></i>
</a>
<a href="{{ route('transsales.export.print', encrypt($data->id)) }}" target="_blank" class="btn btn-sm btn-danger mt-1 mb-1">
    <i class="mdi mdi-printer label-icon" title="Print PDF"></i>
</a> --}}

@php
    use Carbon\Carbon;
    $isCurrentMonth = Carbon::parse($data->date_invoice)->isSameMonth(now());
@endphp

<div class="btn-group">
    <button class="btn btn-sm btn-primary action-btn" data-id="{{ $data->id }}">
        Action <i class="mdi mdi-chevron-down"></i>
    </button>
</div>
<div id="action-menu-{{ $data->id }}" class="floating-dropdown d-none">
    <a href="javascript:void(0)" class="dropdown-item-floating openAjaxModal d-flex align-items-center gap-2"
        data-id="info_{{ $data->id }}" data-size="xl" data-url="{{ route('transsales.export.modal.info', encrypt($data->id)) }}">
        <i class="mdi mdi-information"></i>
        <div class="dropdown-item-floating-divider"></div>
        <span>Info</span>
    </a>
    @can('Akunting_master_data')
        @if($isCurrentMonth)
            <hr class="m-0">
            <a href="{{ route('transsales.export.edit', encrypt($data->id)) }}" class="dropdown-item-floating d-flex align-items-center gap-2">
                <i class="mdi mdi-file-edit"></i>
                <div class="dropdown-item-floating-divider"></div>
                <span>Edit</span>
            </a>
            <a href="javascript:void(0)" class="dropdown-item-floating danger openAjaxModal d-flex align-items-center gap-2"
                data-id="delete_{{ $data->id }}" data-size="md" data-url="{{ route('transsales.export.modal.delete', encrypt($data->id)) }}">
                <i class="mdi mdi-delete-alert"></i>
                <div class="dropdown-item-floating-divider"></div>
                <span>Delete</span>
            </a>
            <hr class="m-0">
        @endif
    @endcan
    <a href="{{ route('transsales.export.print', encrypt($data->id)) }}" target="_blank" class="dropdown-item-floating d-flex align-items-center gap-2">
        <i class="mdi mdi-printer"></i>
        <div class="dropdown-item-floating-divider"></div>
        <span>Print to PDF</span>
    </a>
</div>