<div class="modal-header">
    <h5 class="modal-title" id="staticBackdropLabel">List Transaction of <b>{{ $data->invoice_number }}</b></h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body md-body-scroll">
    <table class="table table-bordered small w-100" id="tblPagination">
        <thead class="table-light">
            <tr>
                <th class="align-top text-center">#</th>
                <th class="align-top text-center">Account Code</th>
                <th class="align-top text-center">Account Name</th>
                <th class="align-top text-center">Nominal</th>
                <th class="align-top text-center">Debit / Kredit</th>
                <th class="align-top text-center">Note</th>
            </tr>
        </thead>
        <tbody>
            @foreach($datas as $item)
                <tr>
                    <td class="align-top text-center fw-bold">
                        {{ $loop->iteration }}
                    </td>
                    <td class="align-top text-center fw-bold">
                        {{ $item->account_code ?? '-' }}
                    </td>
                    <td class="align-top">
                        {{ $item->account_name ?? '-' }}
                    </td>
                    <td class="align-top text-end">
                        @php
                            $formatted = number_format($item->amount, 2, ',', '.');
                            [$before, $after] = explode(',', $formatted);
                        @endphp
                        <span class="fw-bold">{{ $before }}</span><span class="text-muted">,{{ $after }}</span>
                    </td>
                    <td class="align-top text-center">
                        @if($item->transaction == 'D')
                            <span class="badge bg-success text-white"><span class="mdi mdi-plus-circle"></span> | Debit</span>
                        @else
                            <span class="badge bg-danger text-white"><span class="mdi mdi-minus-circle"></span> | Kredit</span>
                        @endif
                    </td>
                    <td class="align-top">
                        {{ $item->note ?? '-' }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <script>
        $(document).ready(function() {
            var table = $('#tblPagination').DataTable({
                pageLength: 5,
                lengthMenu: [5, 10, 20, 50],
                ordering: true,
                searching: true
            });
        });
    </script>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
</div>