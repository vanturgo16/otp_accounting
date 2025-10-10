<button type="button" class="btn btn-sm btn-primary fw-bold" 
    data-id_ref="{{ $data->id }}" 
    data-ref_number="{{ $data->ref_number }}" 
    data-source="{{ $data->source }}"
    data-bs-toggle="modal" data-bs-target="#showDetail">
    {{ $data->total_transaction ?? 0 }}
</button>