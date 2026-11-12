<!-- Global Accessible App Dialog (App.confirm / App.alert) -->
<div id="appDialogModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs transition-opacity duration-200 opacity-0 pointer-events-none" role="dialog" aria-modal="true" aria-labelledby="appDialogTitle">
    <div id="appDialogCard" class="bg-white rounded-2xl shadow-2xl border border-[#E6E6E6] max-w-md w-full p-5 sm:p-6 transform transition-all duration-200 scale-95 space-y-4">
        <div class="flex items-start gap-3">
            <div id="appDialogIconContainer" class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 bg-red-50 text-red-600">
                <!-- Icon injected dynamically via JS -->
                <svg id="appDialogIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <h3 id="appDialogTitle" class="text-base font-bold text-[#111111] leading-snug">
                    Confirm Action
                </h3>
                <p id="appDialogMessage" class="text-xs sm:text-sm text-[#666666] mt-1 leading-relaxed">
                    Are you sure you want to proceed with this operation?
                </p>
            </div>
        </div>

        <div class="flex items-center justify-end gap-2.5 pt-2 border-t border-[#E6E6E6]">
            <button type="button" id="appDialogCancelBtn" class="btn-secondary px-4 py-2 rounded-lg text-xs font-semibold min-h-[38px]">
                Cancel
            </button>
            <button type="button" id="appDialogConfirmBtn" class="px-5 py-2 rounded-lg text-xs font-bold text-white bg-red-600 hover:bg-red-700 min-h-[38px] transition shadow-xs">
                Confirm
            </button>
        </div>
    </div>
</div>

