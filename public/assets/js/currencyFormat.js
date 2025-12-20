// Format Rupiah
function formatCurrencyInput(event) {
    let value = event.target.value;
    value = value.replace(/[^\d,]/g, "");
    let parts = value.split(",");
    let integerPart = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    if (parts.length > 1) {
        let decimalPart = parts[1].slice(0, 2);
        value = `${integerPart},${decimalPart}`;
    } else {
        value = integerPart;
    }
    event.target.value = value;
}
$(document).on("input", ".currency-input", function () {
    formatCurrencyInput({ target: this });
});


function formatPrice(value) {
    if (!value) return '0';
    // format with 3 decimals first
    let formatted = Number(value).toLocaleString('id-ID', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
    // remove trailing zeros after comma
    formatted = formatted.replace(/,?0+$/, '');
    return formatted;
}
function formatPriceWithStyle(value) {
    // Format with 3 decimals, comma as decimal sep, dot as thousand sep
    let formatted = new Intl.NumberFormat('de-DE', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(value);
    // Split integer and decimal part
    let parts = formatted.split(',');
    let before = parts[0]; // integer part with thousand separator
    let after = parts[1] ? ',' + parts[1] : '';
    return `<span class="fw-bold">${before}</span><span class="text-muted">${after}</span>`;
}