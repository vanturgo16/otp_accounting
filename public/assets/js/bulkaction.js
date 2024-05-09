$("#checkAllRows").click(function () {
    $("input[name='checkbox']:not(:disabled)").prop("checked", this.checked);
});

function bulkDeleted(url) {
    var checkedDatas = [];
    $(":checkbox:checked").each(function () {
        var data = $(this).data("id-data");
        if (data !== undefined) {
            checkedDatas.push(data);
        }
    });

    if (checkedDatas.length > 0) {
        $("#sb-deleteselected").attr("disabled", "disabled");
        $("#sb-deleteselected").html(
            '<i class="mdi mdi-reload label-icon"></i>Please Wait...'
        );
        $.ajax({
            url: url,
            type: "POST",
            data: {
                idChecked: checkedDatas,
                _token: $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                console.log(response);
                if (response.type === "success") {
                    showAlert("success", response.message);
                } else if (response.type === "error") {
                    showAlert("error", response.error);
                }
                $("#deleteselected").modal("hide");
                $("#sb-deleteselected").removeAttr("disabled");
                $("#sb-deleteselected").html(
                    '<i class="mdi mdi-delete label-icon"></i>Delete'
                );

                refreshDataTable();
            },
            error: function (error) {
                showAlert(
                    "error",
                    "Error delete data: " + error.responseJSON.error
                );
            },
        });
    } else {
        $("#deleteselected").modal("hide");
        showAlert("error", "No items selected for bulk delete");
    }
}

function bulkDeactivate(url) {
    var checkedDatas = [];
    $(":checkbox:checked").each(function () {
        var data = $(this).data("id-data");
        if (data !== undefined) {
            checkedDatas.push(data);
        }
    });

    if (checkedDatas.length > 0) {
        $("#sb-deactivateselected").attr("disabled", "disabled");
        $("#sb-deactivateselected").html(
            '<i class="mdi mdi-reload label-icon"></i>Please Wait...'
        );
        $.ajax({
            url: url,
            type: "POST",
            data: {
                idChecked: checkedDatas,
                _token: $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                console.log(response);
                if (response.type === "success") {
                    showAlert("success", response.message);
                } else if (response.type === "error") {
                    showAlert("error", response.error);
                }
                $("#deactivateselected").modal("hide");
                $("#sb-deactivateselected").removeAttr("disabled");
                $("#sb-deactivateselected").html(
                    '<i class="mdi-close-circle label-icon"></i>Deactivate'
                );

                refreshDataTable();
            },
            error: function (error) {
                showAlert(
                    "error",
                    "Error delete data: " + error.responseJSON.error
                );
            },
        });
    } else {
        $("#deactivateselected").modal("hide");
        showAlert("warning", "No items selected for bulk delete");
    }
}

function showAlert(type, message) {
    const alertElement =
        type === "success" ? $("#alertSuccess") : $("#alertWarning");
    alertElement.removeClass("d-none");
    $(".alertMessage").text(message);

    setTimeout(function () {
        alertElement.addClass("d-none");
    }, 3000);
}
function refreshDataTable() {
    $("#server-side-table").DataTable().ajax.reload();
    $("#checkAllRows").prop("checked", false);
}
