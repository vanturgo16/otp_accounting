<div class="btn-group">
    <button class="btn btn-sm btn-primary action-btn" data-id="{{ $data->id }}">
        Action <i class="mdi mdi-chevron-down"></i>
    </button>
</div>

<div id="action-menu-{{ $data->id }}" class="floating-dropdown d-none">
    <a href="javascript:void(0)" class="dropdown-item-floating openAjaxModal d-flex align-items-center gap-2"
        data-id="info_{{ $data->id }}" data-size="lg" data-url="{{ route('bankaccount.modal.info', encrypt($data->id)) }}">
        <i class="mdi mdi-information"></i>
        <div class="dropdown-item-floating-divider"></div>
        <span>Info</span>
    </a>
    <a href="javascript:void(0)" class="dropdown-item-floating openAjaxModal d-flex align-items-center gap-2"
        data-id="edit_{{ $data->id }}" data-size="lg" data-url="{{ route('bankaccount.modal.edit', encrypt($data->id)) }}">
        <i class="mdi mdi-file-edit"></i>
        <div class="dropdown-item-floating-divider"></div>
        <span>Edit</span>
    </a>
    @if($data->is_active == 0)
        <a href="javascript:void(0)" class="dropdown-item-floating success openAjaxModal d-flex align-items-center gap-2"
            data-id="activate_{{ $data->id }}" data-size="md" data-url="{{ route('bankaccount.modal.activate', encrypt($data->id)) }}">
            <i class="mdi mdi-check-circle"></i>
            <div class="dropdown-item-floating-divider"></div>
            <span>Activate</span>
        </a>
    @else
        <a href="javascript:void(0)" class="dropdown-item-floating danger openAjaxModal d-flex align-items-center gap-2"
            data-id="deactivate_{{ $data->id }}" data-size="md" data-url="{{ route('bankaccount.modal.deactivate', encrypt($data->id)) }}">
            <i class="mdi mdi-close-circle"></i>
            <div class="dropdown-item-floating-divider"></div>
            <span>Deactivate</span>
        </a>
    @endif
</div>