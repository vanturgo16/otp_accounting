$(document).on("shown.bs.modal", ".modal", function () {
    $(".js-example-basic-single").select2({
        dropdownParent: this,
    });
});

$(".js-example-basic-single").select2();

$(document).on("hidden.bs.modal", ".modal", function () {
    $(".js-example-basic-single").select2();
    $(".select2").select2({
        width: '100%'
    });
});

// Format Rupiah
function formatCurrencyInput(event) {
    let value = event.target.value;
    value = value.replace(/[^\d,]/g, "");
    let parts = value.split(",");
    let integerPart = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    if (parts.length > 1) {
        let decimalPart = parts[1].slice(0, 3);
        value = `${integerPart},${decimalPart}`;
    } else {
        value = integerPart;
    }
    event.target.value = value;
}

document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".rupiah-input").forEach((input) => {
        input.addEventListener("input", formatCurrencyInput);
    });
});

function numberFormat(number, decimals, decPoint, thousandsSep) {
    // Fix for NaN cases
    if (!isFinite(number)) {
        return "0";
    }

    number = number || 0;
    decimals = isNaN((decimals = Math.abs(decimals))) ? 2 : decimals;
    decPoint = decPoint === undefined ? "." : decPoint;
    thousandsSep = thousandsSep === undefined ? "," : thousandsSep;

    var sign = number < 0 ? "-" : "";
    number = Math.abs(+number || 0).toFixed(decimals);

    var intPart = parseInt(number, 10) + "";
    var j = intPart.length > 3 ? intPart.length % 3 : 0;

    var result =
        sign +
        (j ? intPart.substr(0, j) + thousandsSep : "") +
        intPart.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousandsSep) +
        (decimals
            ? decPoint +
              Math.abs(number - intPart)
                  .toFixed(decimals)
                  .slice(2)
            : "");

    return result;
}
