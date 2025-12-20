document.querySelectorAll(".formLoad").forEach(function (form) {
    form.addEventListener("submit", function (event) {
        if (!this.checkValidity()) {
            event.preventDefault();
            return false;
        }
        var submitButton = this.querySelector('button[type="submit"]');
        if (submitButton) {
            submitButton.disabled = true;
            submitButton.innerHTML =
                '<i class="mdi mdi-loading mdi-spin label-icon"></i>Please Wait...';
        }
        $("#processing").removeClass("hidden");
        $("body").addClass("no-scroll");
        return true;
    });
});


function btnSubmitLoad(button) {
    const form = button.closest("form");

    if (!form) return false; // No form found

    // Validate form before submitting
    if (!form.checkValidity()) {
        form.reportValidity();
        return false;
    }

    // Disable the button
    button.disabled = true;
    button.innerHTML = '<i class="mdi mdi-loading mdi-spin label-icon"></i> Please Wait...';

    // Show overlay and block scroll
    $("#processing").removeClass("hidden");
    $("body").addClass("no-scroll");

    // Submit form
    form.submit();
    return true;
}
