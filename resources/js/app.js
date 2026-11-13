/**
 * Application Core JavaScript Bundle
 * - Animated Full-Screen Page Preloader with configurable timings
 * - Accessible Modal Dialog System (window.App.confirm & window.App.alert)
 * - Declarative [data-confirm] confirmation interceptor
 * - Reusable Bulk Selection & Floating Action Bar Controller
 */

// ==========================================================================
// 1. Full-Screen Animated Page Preloader
// ==========================================================================
(function() {
    let navTimer = null;
    const pageStartTime = performance.now();

    function getPreloaderConfig() {
        const loader = document.getElementById('pageLoader');
        if (!loader) return { enabled: false, minTime: 0, maxTime: 3500, fadeDuration: 300 };
        return {
            enabled: true,
            minTime: parseInt(loader.getAttribute('data-min-time') || '0', 10),
            maxTime: parseInt(loader.getAttribute('data-max-time') || '3500', 10),
            fadeDuration: parseInt(loader.getAttribute('data-fade-duration') || '300', 10),
        };
    }

    function hidePageLoader() {
        if (navTimer) {
            clearTimeout(navTimer);
            navTimer = null;
        }

        const loader = document.getElementById('pageLoader');
        if (loader && loader.classList.contains('active')) {
            const config = getPreloaderConfig();
            const elapsed = performance.now() - pageStartTime;
            const remaining = Math.max(0, config.minTime - elapsed);

            setTimeout(() => {
                loader.style.transition = `opacity ${config.fadeDuration}ms ease, visibility ${config.fadeDuration}ms ease`;
                loader.classList.remove('active');
                loader.setAttribute('aria-busy', 'false');

                setTimeout(() => {
                    if (document.body) {
                        document.body.classList.remove('preloader-active');
                    }
                }, config.fadeDuration);
            }, remaining);
        } else if (document.body) {
            document.body.classList.remove('preloader-active');
        }
    }

    function showPageLoader() {
        const loader = document.getElementById('pageLoader');
        if (loader) {
            loader.style.transition = '';
            loader.classList.add('active');
            loader.setAttribute('aria-busy', 'true');
        }

        if (document.body) {
            document.body.classList.add('preloader-active');
        }
    }

    window.hidePageLoader = hidePageLoader;
    window.showPageLoader = showPageLoader;

    // Initial Load dismissal
    if (document.body) {
        const initialLoader = document.getElementById('pageLoader');
        if (initialLoader && initialLoader.classList.contains('active')) {
            document.body.classList.add('preloader-active');
        }
    }

    if (document.readyState === 'complete') {
        requestAnimationFrame(() => hidePageLoader());
    } else {
        window.addEventListener('load', () => hidePageLoader(), { once: true });
        document.addEventListener('DOMContentLoaded', () => {
            requestAnimationFrame(() => hidePageLoader());
        }, { once: true });
    }

    // Handle bfcache
    window.addEventListener('pageshow', (event) => {
        if (event.persisted) {
            hidePageLoader();
        }
    });

    // Fallback safety timeout based on Super Admin config
    const config = getPreloaderConfig();
    setTimeout(() => {
        hidePageLoader();
    }, config.maxTime || 3500);

    // Smooth page-changing transition threshold (Strictly only real page navigations)
    document.addEventListener('click', (e) => {
        // Exclude all buttons, form controls, and pseudo-buttons
        if (e.target.closest('button, input[type="submit"], input[type="button"], [role="button"], [type="reset"]')) {
            return;
        }

        const link = e.target.closest('a');
        if (!link) return;

        // Skip non-standard clicks (new tab, middle click, modified keys)
        if (e.ctrlKey || e.metaKey || e.shiftKey || e.altKey || e.button !== 0) return;

        // Skip if default was already prevented
        if (e.defaultPrevented) return;

        const href = link.getAttribute('href');
        if (!href || href === '#' || href.startsWith('#') || href.startsWith('javascript:')) return;
        if (link.target && link.target !== '_self') return;
        if (link.hasAttribute('download') || link.hasAttribute('data-no-loader')) return;
        if (link.getAttribute('role') === 'button' || link.hasAttribute('onclick') || link.dataset.modal || link.dataset.confirm || link.dataset.action) return;

        // Skip file downloads, exports, prints, thermal slips, and PDFs
        const lowerHref = href.toLowerCase();
        if (
            lowerHref.includes('/pdf') ||
            lowerHref.includes('/print') ||
            lowerHref.includes('/thermal') ||
            lowerHref.includes('/download') ||
            lowerHref.includes('/export') ||
            lowerHref.includes('download') ||
            lowerHref.includes('export') ||
            lowerHref.endsWith('.pdf') ||
            lowerHref.endsWith('.csv') ||
            lowerHref.endsWith('.xlsx') ||
            lowerHref.endsWith('.zip')
        ) {
            return;
        }

        try {
            const url = new URL(link.href, window.location.origin);
            if (url.origin !== window.location.origin) return;

            // ONLY activate for genuine page-to-page transitions (different pathname)
            // Filters, queries, search parameters, pagination on the same page DO NOT trigger loader
            if (url.pathname === window.location.pathname) {
                return;
            }
        } catch {
            return;
        }

        if (navTimer) clearTimeout(navTimer);
        navTimer = setTimeout(() => {
            if (!e.defaultPrevented) {
                showPageLoader();
            }
        }, 200);
    });
})();

// ==========================================================================
// 2. Accessible Promise-Based Dialog Modal System (App.confirm & App.alert)
// ==========================================================================
(function() {
    let activeResolve = null;

    const modal = document.getElementById('appDialogModal');
    const card = document.getElementById('appDialogCard');
    const titleEl = document.getElementById('appDialogTitle');
    const msgEl = document.getElementById('appDialogMessage');
    const iconContainer = document.getElementById('appDialogIconContainer');
    const iconEl = document.getElementById('appDialogIcon');
    const cancelBtn = document.getElementById('appDialogCancelBtn');
    const confirmBtn = document.getElementById('appDialogConfirmBtn');

    const variantStyles = {
        danger: {
            container: 'bg-red-50 text-red-600',
            button: 'bg-red-600 hover:bg-red-700 text-white shadow-xs',
            path: 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
        },
        warning: {
            container: 'bg-amber-50 text-amber-600',
            button: 'bg-amber-500 hover:bg-amber-600 text-black font-black shadow-xs',
            path: 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
        },
        primary: {
            container: 'bg-[#B7FF00]/20 text-[#111111]',
            button: 'btn-lime',
            path: 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        },
        success: {
            container: 'bg-green-50 text-green-600',
            button: 'bg-green-600 hover:bg-green-700 text-white shadow-xs',
            path: 'M5 13l4 4L19 7',
        },
        info: {
            container: 'bg-slate-100 text-slate-700',
            button: 'bg-[#111111] hover:bg-black text-white shadow-xs',
            path: 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        },
    };

    function openDialog({ title = 'Confirm Action', message = 'Are you sure?', confirmText = 'Confirm', cancelText = 'Cancel', variant = 'danger', isAlert = false }) {
        if (!modal) {
            // Fallback if modal container is not rendered
            return Promise.resolve(isAlert ? alert(message) : confirm(message));
        }

        return new Promise((resolve) => {
            activeResolve = resolve;

            titleEl.textContent = title;
            msgEl.textContent = message;
            confirmBtn.textContent = confirmText;
            cancelBtn.textContent = cancelText;

            if (isAlert) {
                cancelBtn.classList.add('hidden');
            } else {
                cancelBtn.classList.remove('hidden');
            }

            const style = variantStyles[variant] || variantStyles.danger;
            iconContainer.className = `w-10 h-10 rounded-xl flex items-center justify-center shrink-0 ${style.container}`;
            confirmBtn.className = `px-5 py-2 rounded-lg text-xs font-bold min-h-[38px] transition ${style.button}`;
            if (iconEl) {
                iconEl.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${style.path}"></path>`;
            }

            // Show dialog
            modal.classList.remove('opacity-0', 'pointer-events-none');
            modal.classList.add('opacity-100', 'pointer-events-auto');
            card.classList.remove('scale-95');
            card.classList.add('scale-100');

            setTimeout(() => confirmBtn.focus(), 50);
        });
    }

    function closeDialog(result) {
        if (!modal) return;
        modal.classList.add('opacity-0', 'pointer-events-none');
        modal.classList.remove('opacity-100', 'pointer-events-auto');
        card.classList.add('scale-95');
        card.classList.remove('scale-100');

        if (activeResolve) {
            const res = activeResolve;
            activeResolve = null;
            res(result);
        }
    }

    if (confirmBtn) {
        confirmBtn.addEventListener('click', () => closeDialog(true));
    }
    if (cancelBtn) {
        cancelBtn.addEventListener('click', () => closeDialog(false));
    }

    // Close on backdrop click
    if (modal) {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                closeDialog(false);
            }
        });
    }

    // Keyboard support: Escape cancels, Enter on button
    document.addEventListener('keydown', (e) => {
        if (modal && !modal.classList.contains('pointer-events-none')) {
            if (e.key === 'Escape') {
                e.preventDefault();
                closeDialog(false);
            }
        }
    });

    window.App = window.App || {};
    window.App.confirm = (options) => {
        if (typeof options === 'string') {
            options = { message: options };
        }
        return openDialog({ ...options, isAlert: false });
    };

    window.App.alert = (options) => {
        if (typeof options === 'string') {
            options = { message: options, confirmText: 'OK' };
        }
        return openDialog({ ...options, isAlert: true });
    };

    // Declarative Interception of [data-confirm] (excluding bulk action buttons)
    document.addEventListener('click', async (e) => {
        const trigger = e.target.closest('[data-confirm]');
        if (!trigger) return;

        // Bulk action buttons handle their own confirmation & input hydration
        if (trigger.hasAttribute('data-bulk-action') || trigger.closest('[data-bulk-action]')) {
            return;
        }

        // If it's a submit button in a form, intercept form submission
        const form = trigger.closest('form');
        const message = trigger.getAttribute('data-confirm') || 'Are you sure you want to proceed?';
        const title = trigger.getAttribute('data-confirm-title') || 'Confirm Action';
        const variant = trigger.getAttribute('data-confirm-variant') || 'danger';
        const confirmText = trigger.getAttribute('data-confirm-btn') || 'Proceed';

        e.preventDefault();
        e.stopImmediatePropagation();

        const confirmed = await window.App.confirm({ title, message, confirmText, variant });
        if (confirmed) {
            if (trigger.tagName === 'A') {
                window.location.href = trigger.href;
            } else if (form) {
                form.submit();
            }
        }
    }, true);
})();

// ==========================================================================
// 3. Reusable Bulk Selection & Floating Action Bar Controller
// ==========================================================================
(function() {
    function initBulkActions() {
        const bulkForms = document.querySelectorAll('[data-bulk-form]');
        if (!bulkForms.length) return;

        function getUniqueItemIds() {
            const set = new Set();
            document.querySelectorAll('[data-bulk-item]').forEach(cb => {
                if (cb.value) set.add(String(cb.value));
            });
            return Array.from(set);
        }

        function getSelectedUniqueIds() {
            const set = new Set();
            document.querySelectorAll('[data-bulk-item]:checked').forEach(cb => {
                if (cb.value) set.add(String(cb.value));
            });
            return Array.from(set);
        }

        function highlightElement(cb, isChecked) {
            const tr = cb.closest('tr');
            if (tr) {
                tr.classList.toggle('bulk-selected-row', isChecked);
            }
            const card = cb.closest('[data-card-item]') || cb.closest('.card-surface');
            if (card && !tr) {
                card.classList.toggle('bulk-selected-card', isChecked);
            }
        }

        function syncCheckboxState(val, isChecked) {
            document.querySelectorAll(`[data-bulk-item][value="${val}"]`).forEach(cb => {
                cb.checked = isChecked;
                highlightElement(cb, isChecked);
            });
        }

        function updateBulkUI() {
            const selectedIds = getSelectedUniqueIds();
            const allIds = getUniqueItemIds();
            const count = selectedIds.length;
            const total = allIds.length;

            // Sync all master select-all checkboxes
            document.querySelectorAll('[data-bulk-select-all]').forEach(master => {
                master.checked = total > 0 && count === total;
                master.indeterminate = count > 0 && count < total;
            });

            // Update counter badges
            document.querySelectorAll('[data-bulk-count]').forEach(el => {
                el.textContent = count;
            });

            // Toggle floating bulk action dock visibility
            bulkForms.forEach(form => {
                const inputsContainer = form.querySelector('.bulk-inputs-container');
                if (count > 0) {
                    form.classList.remove('is-hidden');
                    form.classList.add('is-visible');

                    if (inputsContainer) {
                        inputsContainer.innerHTML = selectedIds
                            .map(id => `<input type="hidden" name="ids[]" value="${id}">`)
                            .join('');
                    }
                } else {
                    form.classList.remove('is-visible');
                    form.classList.add('is-hidden');

                    if (inputsContainer) {
                        inputsContainer.innerHTML = '';
                    }
                }
            });
        }

        // Delegate change event for item checkboxes (handles both mobile cards & desktop tables)
        document.addEventListener('change', (e) => {
            const itemCb = e.target.closest('[data-bulk-item]');
            if (itemCb) {
                syncCheckboxState(itemCb.value, itemCb.checked);
                updateBulkUI();
                return;
            }

            const masterCb = e.target.closest('[data-bulk-select-all]');
            if (masterCb) {
                const shouldCheck = masterCb.checked;
                document.querySelectorAll('[data-bulk-item]').forEach(cb => {
                    cb.checked = shouldCheck;
                    highlightElement(cb, shouldCheck);
                });
                updateBulkUI();
            }
        });

        // Clear all button click
        document.addEventListener('click', (e) => {
            const clearBtn = e.target.closest('[data-bulk-clear]');
            if (!clearBtn) return;
            e.preventDefault();

            document.querySelectorAll('[data-bulk-item]').forEach(cb => {
                cb.checked = false;
                highlightElement(cb, false);
            });
            document.querySelectorAll('[data-bulk-select-all]').forEach(master => {
                master.checked = false;
                master.indeterminate = false;
            });
            updateBulkUI();
        });

        // Bulk action execution click handler
        document.addEventListener('click', async (e) => {
            const btn = e.target.closest('[data-bulk-action]');
            if (!btn) return;

            e.preventDefault();
            e.stopImmediatePropagation();

            const action = btn.getAttribute('data-bulk-action');
            const form = btn.closest('form') || document.querySelector('[data-bulk-form]');
            if (!form) return;

            const selectedIds = getSelectedUniqueIds();
            if (!selectedIds.length) {
                await window.App.alert({
                    title: 'No Records Selected',
                    message: 'Please select one or more records using the checkboxes to perform this bulk action.',
                    variant: 'info'
                });
                return;
            }

            // Hydrate action and selected IDs
            const actionInput = form.querySelector('input[name="action"]');
            if (actionInput) {
                actionInput.value = action;
            }
            const inputsContainer = form.querySelector('.bulk-inputs-container');
            if (inputsContainer) {
                inputsContainer.innerHTML = selectedIds
                    .map(id => `<input type="hidden" name="ids[]" value="${id}">`)
                    .join('');
            }

            // Confirmation modal
            const confirmMsg = btn.getAttribute('data-confirm');
            if (confirmMsg) {
                const count = selectedIds.length;
                const dynamicMsg = confirmMsg.replace(':count', count);
                const title = btn.getAttribute('data-confirm-title') || 'Confirm Bulk Action';
                const variant = btn.getAttribute('data-confirm-variant') || 'danger';
                const confirmText = btn.getAttribute('data-confirm-btn') || (`Confirm (${count})`);

                const confirmed = await window.App.confirm({
                    title,
                    message: dynamicMsg,
                    confirmText,
                    variant
                });

                if (!confirmed) return;
            }

            // Visual feedback & submission
            btn.disabled = true;
            btn.style.opacity = '0.7';
            form.submit();
        }, true);

        // Initial sync
        updateBulkUI();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initBulkActions);
    } else {
        initBulkActions();
    }

    window.App = window.App || {};
    window.App.bulk = { init: initBulkActions };
})();

// ==========================================================================
// 4. Universal Password Visibility Toggle
// ==========================================================================
(function() {
    function toggleField(input, toggleBtn) {
        if (!input) return;
        const isPassword = input.type === 'password';
        input.type = isPassword ? 'text' : 'password';

        if (toggleBtn) {
            const eyeOpen = toggleBtn.querySelector('.eye-open');
            const eyeClosed = toggleBtn.querySelector('.eye-closed');
            if (eyeOpen && eyeClosed) {
                eyeOpen.classList.toggle('hidden', isPassword);
                eyeClosed.classList.toggle('hidden', !isPassword);
            }
            const txt = toggleBtn.querySelector('#toggleText') || toggleBtn.querySelector('.toggle-text');
            if (txt) {
                txt.textContent = isPassword ? 'Hide' : 'Show';
            }
            const newLabel = isPassword ? 'Hide password' : 'Show password';
            toggleBtn.setAttribute('aria-label', newLabel);
            toggleBtn.setAttribute('title', newLabel);
        }
    }

    document.addEventListener('click', (e) => {
        const toggleBtn = e.target.closest('[data-password-toggle]');
        if (!toggleBtn) return;

        e.preventDefault();
        e.stopPropagation();

        const targetId = toggleBtn.getAttribute('data-password-toggle');
        let input = targetId ? document.getElementById(targetId) : null;
        if (!input) {
            const container = toggleBtn.closest('.relative') || toggleBtn.parentElement;
            if (container) {
                input = container.querySelector('input');
            }
        }
        toggleField(input, toggleBtn);
    });

    // Global helper for legacy/inline calls
    window.togglePasswordVisibility = function(targetId = 'password', btn = null) {
        let input = typeof targetId === 'string' ? document.getElementById(targetId) : targetId;
        if (!input) input = document.getElementById('password');
        toggleField(input, btn || (input ? input.parentElement.querySelector('[data-password-toggle], button') : null));
    };
})();
