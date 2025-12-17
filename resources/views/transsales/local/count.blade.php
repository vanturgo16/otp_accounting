<button type="button"
    class="btn btn-sm btn-light border fw-bold rounded-pill btn-outline-primary position-relative openAjaxModal"
    data-id="transaction_{{ $data->id }}"
    data-size="xl"
    data-url="{{ route('transsales.local.modal.listTT', encrypt($data->id)) }}"
    title="View transaction details">
    <i class="mdi mdi-format-list-bulleted"></i>
    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary">
        {{ $data->total_transaction ?? 0 }}
    </span>
</button>
