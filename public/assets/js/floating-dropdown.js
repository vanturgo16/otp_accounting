$(document).on("click", ".action-btn", function (e) {
    e.preventDefault();
    e.stopPropagation();

    const btn = this;
    const id = btn.dataset.id;
    const menu = document.getElementById("action-menu-" + id);

    if (!menu) return;

    // Toggle close if clicking same button again
    const isOpen = !menu.classList.contains("d-none");
    $(".floating-dropdown").addClass("d-none");
    if (isOpen) return;

    // Make menu visible but hidden to measure
    menu.style.visibility = "hidden";
    menu.classList.remove("d-none");

    const rect = btn.getBoundingClientRect();
    const menuHeight = menu.offsetHeight;
    const menuWidth = menu.offsetWidth;

    const spaceBottom = window.innerHeight - rect.bottom;
    const spaceTop = rect.top;

    let top;

    // ============ AUTO FLIP =============
    if (spaceBottom < menuHeight && spaceTop >= menuHeight) {
        // Open UP
        top = rect.top - menuHeight - 6;
    } else {
        // Open DOWN
        top = rect.bottom + 6;
    }

    // Align menu right side to button right side
    menu.style.left = (rect.right - menuWidth) + "px";
    menu.style.top = top + "px";

    menu.style.visibility = "visible";
});

// Hide menu when clicking a menu item
$(document).on("click", ".floating-dropdown a, .floating-dropdown button, .floating-dropdown .dropdown-item", function () {
    $(".floating-dropdown").addClass("d-none");
});
// Close when clicking outside
$(document).on("click", function () {
    $(".floating-dropdown").addClass("d-none");
});
// Prevent closing when clicking inside menu
$(document).on("click", ".floating-dropdown", function (e) {
    e.stopPropagation();
});
