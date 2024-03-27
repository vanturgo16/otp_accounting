$(document).on("shown.bs.modal", ".modal", function () {
    $(".js-example-basic-single").select2({
        dropdownParent: this,
    });
});

$(".js-example-basic-single").select2();

$(document).on("hidden.bs.modal", ".modal", function () {
    $(".js-example-basic-single").select2();
});
