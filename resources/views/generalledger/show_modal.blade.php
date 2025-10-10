<div class="modal fade" id="showDetail" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-top modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">List Transaction</b></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" style="max-height: 65vh; overflow-y: auto;">
                <div id="listContent" class="text-start">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p>Loading data...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const modal = document.getElementById('showDetail');
        const content = document.getElementById('listContent');
        let dataTableInstance = null;
    
        modal.addEventListener('show.bs.modal', function (event) {
            // button that triggered the modal
            const button = event.relatedTarget;
    
            // get data attributes
            const id_ref = button.getAttribute('data-id_ref');
            const ref_number = button.getAttribute('data-ref_number');
            const source = button.getAttribute('data-source');
    
            // reset content with loading
            content.innerHTML = `
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p>Loading data...</p>
                </div>
            `;

            console.log(id_ref, ref_number, source);
    
            // fetch data
            fetch(`/generalledger/getData?id_ref=${id_ref}&ref_number=${encodeURIComponent(ref_number)}&source=${encodeURIComponent(source)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.length === 0) {
                        content.innerHTML = `<p class="text-center text-muted">No PRs found.</p>`;
                        return;
                    }
    
                    let tableHtml = `
                        <table id="tablePR" class="display table table-bordered align-middle w-100">
                            <thead class="table-light">
                                <tr>
                                    <th class="align-top text-center">#</th>
                                    <th class="align-top text-center">Account Code</th>
                                    <th class="align-top text-center">Account Name</th>
                                    <th class="align-top text-center">Nominal</th>
                                    <th class="align-top text-center">Debit / Kredit</th>
                                </tr>
                            </thead>
                            <tbody>
                    `;
    
                    data.forEach((item, index) => {
                        // format amount
                        let amountFormatted = '-';
                        if (item.amount !== null) {
                            let formatted = Number(item.amount).toLocaleString('id-ID', { minimumFractionDigits: 3, maximumFractionDigits: 3 });
                            let [before, after] = formatted.split(',');
                            amountFormatted = `<span class="fw-bold">${before}</span><span class="text-muted">,${after}</span>`;
                        }

                        // transaction badge
                        let transactionFormatted = '-';
                        if (item.transaction === 'D') {
                            transactionFormatted = `<span class="badge bg-success text-white"><span class="mdi mdi-plus-circle"></span> | Debit</span>`;
                        } else if (item.transaction === 'K') {
                            transactionFormatted = `<span class="badge bg-danger text-white"><span class="mdi mdi-minus-circle"></span> | Kredit</span>`;
                        }
                        tableHtml += `
                            <tr>
                                <td class="align-top text-center fw-bold">
                                    ${index + 1}
                                </td>
                                <td class="align-top text-center fw-bold">
                                    ${item.account_code ?? '-'}
                                </td>
                                <td class="align-top text-start">
                                    ${item.account_name ?? '-'}
                                </td>
                                <td class="align-top text-end">
                                    ${amountFormatted}
                                </td>
                                <td class="align-top text-center">
                                    ${transactionFormatted}
                                </td>
                            </tr>
                        `;
                    });
    
                    tableHtml += `</tbody></table>`;
                    content.innerHTML = tableHtml;
    
                    // destroy old instance if exists
                    if (dataTableInstance) {
                        dataTableInstance.destroy();
                    }
    
                    // re-init DataTable
                    dataTableInstance = new DataTable("#tablePR", {
                        pageLength: 5,
                        lengthMenu: [5, 10, 20, 50],
                        ordering: true,
                        searching: true
                    });
                })
                .catch(error => {
                    content.innerHTML = `<p class="text-danger text-center">Failed to load data.</p>`;
                    console.error(error);
                });
        });
    });
</script>