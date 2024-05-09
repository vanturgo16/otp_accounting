$(document).on("shown.bs.modal", ".modal", function () {
    $(".js-example-basic-single").select2({
        dropdownParent: this,
    });
});

$(".js-example-basic-single").select2();

$(document).on("hidden.bs.modal", ".modal", function () {
    $(".js-example-basic-single").select2();
});

// Format Rupiah
$(document).ready(function () {
    var rupiah_inputs = document.querySelectorAll(".rupiah-input");
    rupiah_inputs.forEach(function (inputElement) {
        inputElement.addEventListener("keyup", function (e) {
            this.value = formatCurrency(this.value, " ");
        });
    });
    function formatCurrency(number, prefix) {
        var number_string = number.replace(/[^.\d]/g, "").toString(),
            split = number_string.split("."),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{1,3}/gi);

        if (ribuan) {
            separator = sisa ? "," : "";
            rupiah += separator + ribuan.join(",");
        }

        rupiah = split[1] != undefined ? rupiah + "." + split[1] : rupiah;
        return prefix == undefined ? rupiah : rupiah ? "" + rupiah : "";
    }
});
