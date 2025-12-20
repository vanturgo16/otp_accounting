function exportToExcel(route, filename, requestData) {
    $("#datatable-buttons")
        .DataTable({
            paging: false,
            info: false,
            lengthChange: !1,
            buttons: [
                {
                    extend: "excel",
                    text: '<i class="fas fa-file-excel"></i> | Export to Excel',
                    action: function (e, dt, button, config) {
                        $.ajax({
                            url: route,
                            method: "GET",
                            data: requestData,
                            success: function (response) {
                                generateExcel(response, filename);
                            },
                            error: function (error) {
                                console.error(
                                    "Error sending data to server:",
                                    error
                                );
                            },
                        });
                    },
                },
            ],
        })
        .buttons()
        .container()
        .appendTo("#datatable-buttons_wrapper .col-md-6:eq(0)");

    $(".dataTables_length select").addClass("form-select form-select-sm");

    function generateExcel(data, filename) {
        var wb = XLSX.utils.book_new();
        var ws = XLSX.utils.json_to_sheet(data);
        XLSX.utils.book_append_sheet(wb, ws, "Sheet1");
        var blobData = XLSX.write(wb, {
            bookType: "xlsx",
            mimeType:
                "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
            type: "binary",
        });
        var blob = new Blob([s2ab(blobData)], {
            type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
        });
        var link = document.createElement("a");
        link.href = URL.createObjectURL(blob);
        link.download = filename;
        link.click();
    }

    function s2ab(s) {
        var buf = new ArrayBuffer(s.length);
        var view = new Uint8Array(buf);
        for (var i = 0; i < s.length; i++) view[i] = s.charCodeAt(i) & 0xff;
        return buf;
    }
}

function generateExcel(data, filename) {
    var wb = XLSX.utils.book_new();
    var ws = XLSX.utils.json_to_sheet(data);
    XLSX.utils.book_append_sheet(wb, ws, "Sheet1");
    var blobData = XLSX.write(wb, {
        bookType: "xlsx",
        mimeType:
            "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
        type: "binary",
    });
    var blob = new Blob([s2ab(blobData)], {
        type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
    });
    var link = document.createElement("a");
    link.href = URL.createObjectURL(blob);
    link.download = filename;
    link.click();
}

function s2ab(s) {
    var buf = new ArrayBuffer(s.length);
    var view = new Uint8Array(buf);
    for (var i = 0; i < s.length; i++) view[i] = s.charCodeAt(i) & 0xff;
    return buf;
}


function handleExport(url, requestData, fileName) {
    $("#processing").removeClass("hidden");
    $("body").addClass("no-scroll");

    $.ajax({
        url: url,
        method: "GET",
        data: requestData,
        success: function (response) {
            generateExcel(response, fileName);

            setTimeout(() => {
                $("#processing").addClass("hidden");
                $("body").removeClass("no-scroll");
            }, 300);
        },
        error: function (error) {
            console.error("Error:", error);
            $("#processing").addClass("hidden");
            $("body").removeClass("no-scroll");
        }
    });
}


function initDTUI({
    idTable = "#ssTable",
    columns,
    showExport = false,
    showFilter = false,
    url,
    requestData = {},
    customDrawCallback = null
}) {

    const $table = $(idTable);
    let dt;

    // ----------- INIT DATATABLE -----------
    function initTable() {
        if ($.fn.DataTable.isDataTable(idTable)) {
            $table.DataTable().destroy();
        }

        // const needScrollX = $table.find('thead').outerWidth() > $table.parent().width();

        dt = $table.DataTable({
            responsive: false,
            processing: true,
            serverSide: true,
            pageLength: 5,
            ajax: { url, type: 'GET', data: requestData },
            columns: columns,
            // scrollX: needScrollX
            scrollX: true,
            drawCallback: function(settings) {
                if (typeof customDrawCallback === "function") {
                    customDrawCallback(this.api(), settings);
                }
                // --- Auto adjust columns AFTER DOM stable (header-body alignment)
                setTimeout(() => {
                    this.api().columns.adjust();
                }, 30);
            }
        });

        buildCustomUI();
        attachListeners();
    }

    // ----------- BUILD CUSTOM UI -----------
    function buildCustomUI() {
        const exportBtn = showExport ? `
            <button id="btnExport" class="btn btn-light waves-effect btn-label waves-light">
                <i class="mdi mdi-file-excel label-icon"></i> Export to Excel
            </button>` : "";

        const filterBtn = showFilter ? `
            <button class="btn btn-sm btn-light me-1 flex-shrink-0" 
                id="custom-button" data-bs-toggle="modal" data-bs-target="#sort">
                <i class="mdi mdi-filter label-icon"></i> Sort & Filter
            </button>` : "";

        $('.dataTables_wrapper .dataTables_length').html(`
            <div class="d-flex gap-2 mt-1 mt-sm-0">
                <select class="form-select" id="lengthDT">
                    <option value="5" selected>5</option>
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="-1">All</option>
                </select>
                ${exportBtn}
            </div>
        `).show();

        $('.dataTables_filter').html(`
            <div class="d-flex justify-content-end mt-1 mt-sm-0 w-100 flex-nowrap">
                <div class="input-group flex-nowrap" style="width: auto;">
                    ${filterBtn}
                    <input class="form-control" id="custom-search-input" placeholder="Search...">
                    <span class="input-group-text bg-light flex-shrink-0">
                        <i class="mdi mdi-magnify"></i>
                    </span>
                </div>
            </div>
        `);

        $('.dataTables_scrollBody').css('min-height', '25vh');
    }

    // ----------- EVENT LISTENERS (ONLY ONCE) -----------
    function attachListeners() {
        // Handle Length
        $(document).on('change', '#lengthDT', function () {
            dt.page.len($(this).val()).draw();
        });

        // Handle Search
        $(document).on('keyup', '#custom-search-input', function () {
            dt.search(this.value).draw();
        });

        // Add margin to pagination after load
        $table.on('init.dt', function () {
            $('.dataTables_paginate').addClass('mt-2');
        });

        // Handle window resize & sidebar toggle
        function reSize() {
            dt.columns.adjust().draw(false);
        }
        $(window).on('resize', reSize);
        $(document).on('click', '#vertical-menu-btn', () => setTimeout(reSize, 10));
    }

    // FIRST INIT
    initTable();
}