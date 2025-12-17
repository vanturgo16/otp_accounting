<div class="modal fade" id="dynamicAjaxModal" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog" id="dynamicAjaxModalSize">
        <div class="modal-content">
            <!-- Loading -->
            <div id="dynamicAjaxModalLoading" class="p-5 text-center">
                <div class="spinner-border text-primary mt-3"></div>
                <div class="mt-2 mb-3">Loading...</div>
            </div>
            <!-- Content -->
            <div id="dynamicAjaxModalContent" style="display: none;"></div>
        </div>
    </div>
</div>

<script>
    let loadedModals = {};
    let modalSavedState = {}; // keyed by modalId (the data-id you pass when opening)

    $(document).on("click", ".openAjaxModal", function () {
        const url = $(this).data("url");
        const size = $(this).data("size") || "md";
        const modalId = $(this).data("id");

        const modal = $("#dynamicAjaxModal");
        const modalDialog = $("#dynamicAjaxModalSize");
        const contentBox = $("#dynamicAjaxModalContent");
        const loadingBox = $("#dynamicAjaxModalLoading");

        // set size dynamically
        modalDialog.removeClass().addClass(`modal-dialog modal-${size}`);

        // If already loaded once, just show modal (restore from cached HTML)
        if (loadedModals[modalId]) {
            contentBox.html(loadedModals[modalId]);
            // register which modalId is currently shown (used by save on hide)
            modal.data('currentModalId', modalId);

            // restore saved values if exist
            restoreSavedState(modalId, contentBox);

            loadingBox.hide();
            contentBox.show();
            modal.modal("show");
            // init plugins after content is inserted
            initModalPlugins();
            return;
        }

        // show loader for first time loading
        contentBox.hide();
        loadingBox.show();
        modal.modal("show");

        // Fetch content from server
        $.ajax({
            url: url,
            type: "GET",
            success: function (response) {
                // cache raw HTML (unchanged)
                loadedModals[modalId] = response;

                // insert content
                loadingBox.hide();
                contentBox.html(response).show();

                // register which modalId is currently shown (used by save on hide)
                modal.data('currentModalId', modalId);

                // restore saved values (if any were saved before)
                restoreSavedState(modalId, contentBox);

                initModalPlugins();
            },
            error: function () {
                const errorHtml = `
                    <div class="modal-header bg-danger">
                        <h5 class="modal-title" id="staticBackdropLabel">Failed</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center py-4">
                        <div class="mb-2">
                            <i class="mdi mdi-alert-circle-outline text-danger" style="font-size: 48px;"></i>
                        </div>
                        <h4 class="text-danger mb-1">Failed to Load</h4>
                        <p class="text-muted mb-4">Something went wrong while loading the content.</p>
                        <button id="retryLoadModal" class="btn btn-danger waves-effect btn-label waves-light">
                            <i class="mdi mdi-refresh label-icon"></i> Try Again
                        </button>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    </div>
                `;

                contentBox.show().html(errorHtml);
                loadingBox.hide();

                // Reload handler
                $(document).off('click', '#retryLoadModal').on('click', '#retryLoadModal', function () {
                    // show loader again
                    contentBox.hide();
                    loadingBox.show();
                    $.ajax({
                        url: url,
                        type: "GET",
                        success: function (response) {
                            loadedModals[modalId] = response;
                            loadingBox.hide();
                            contentBox.html(response).show();

                            // register current modalId
                            modal.data('currentModalId', modalId);

                            // restore saved state if available
                            restoreSavedState(modalId, contentBox);

                            initModalPlugins();
                        },
                        error: function () {
                            loadingBox.hide();
                            contentBox.show().html(errorHtml);
                        }
                    });
                });
            }
        });
    });

    // SAVE form data when modal closes (hide)
    $('#dynamicAjaxModal').on('hide.bs.modal', function () {
        const modal = $(this);
        const modalId = modal.data('currentModalId');
        if (!modalId) return; // nothing to save

        const contentBox = $("#dynamicAjaxModalContent");
        const saved = {};

        // For radios: we will store by name the checked value (if any)
        // For checkboxes with same name: store array of checked values
        // For selects (multiple): store array or single value
        contentBox.find("input, select, textarea").each(function () {
            const $el = $(this);
            const name = $el.attr('name');
            if (!name) return; // skip inputs without name

            const type = $el.attr('type');

            if ($el.is('select')) {
                const val = $el.val();
                saved[name] = {
                    type: 'select',
                    value: typeof val === 'undefined' ? null : val
                };
            } else if (type === 'checkbox') {
                // handle multiple checkboxes with same name
                const all = contentBox.find(`input[name="${name}"]`);
                if (all.length > 1) {
                    // collect array of checked values
                    const checked = [];
                    all.each(function () {
                        if ($(this).is(':checked')) checked.push($(this).val());
                    });
                    saved[name] = { type: 'checkbox-group', value: checked };
                } else {
                    // single checkbox
                    saved[name] = { type: 'checkbox-single', value: $el.is(':checked') };
                }
            } else if (type === 'radio') {
                // only store once per radio name
                if (saved.hasOwnProperty(name)) return;
                const checked = contentBox.find(`input[name="${name}"]:checked`).val() || null;
                saved[name] = { type: 'radio', value: checked };
            } else {
                // text, textarea, number, hidden, etc
                saved[name] = { type: 'value', value: $el.val() };
            }
        });

        modalSavedState[modalId] = saved;
    });

    // When modal is shown, re-init plugins just-in-case (safe)
    $('#dynamicAjaxModal').on('shown.bs.modal', function () {
        initModalPlugins();
    });

    // Helper: restore saved state into content container
    function restoreSavedState(modalId, contentBox) {
        if (!modalId) return;
        const saved = modalSavedState[modalId];
        if (!saved) return;

        for (const name in saved) {
            if (!saved.hasOwnProperty(name)) continue;
            const entry = saved[name];
            const targets = contentBox.find(`[name="${name}"]`);
            if (!targets.length) continue;

            if (entry.type === 'select') {
                targets.each(function () {
                    $(this).val(entry.value).trigger('change');
                });
            } else if (entry.type === 'checkbox-group') {
                // set checked for those values
                targets.each(function () {
                    const $t = $(this);
                    $t.prop('checked', entry.value.indexOf($t.val()) !== -1);
                    $t.trigger('change');
                });
            } else if (entry.type === 'checkbox-single') {
                targets.prop('checked', !!entry.value).trigger('change');
            } else if (entry.type === 'radio') {
                // check the matching radio
                contentBox.find(`input[name="${name}"]`).each(function () {
                    $(this).prop('checked', $(this).val() == entry.value);
                }).trigger('change');
            } else { // 'value'
                targets.each(function () {
                    $(this).val(entry.value).trigger('change');
                });
            }
        }
    }

    // Global plugin initializer (keeps select2 & currency re-init safe)
    function initModalPlugins() {
        const modal = $("#dynamicAjaxModal");

        // destroy existing select2s to avoid double-init
        modal.find(".select2").each(function () {
            if ($(this).hasClass('select2-hidden-accessible')) {
                try { $(this).select2('destroy'); } catch (e) { /* ignore */ }
            }
        });

        modal.find(".select2").select2({
            width: "100%",
            dropdownParent: modal
        });

        modal.find(".currency-input").off('input.modalCurrency').on('input.modalCurrency', function () {
            try { formatCurrencyInput(this); } catch (e) { /* if function missing, ignore */ }
        });
    }
</script>
