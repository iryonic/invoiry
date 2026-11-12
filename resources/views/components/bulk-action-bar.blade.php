@props([
    'action',
    'id' => 'bulk-action-form',
])

<form id="{{ $id }}" method="POST" action="{{ $action }}" class="bulk-action-bar is-hidden bottom-20 lg:bottom-6" data-bulk-form>
    @csrf
    <div class="bulk-inputs-container hidden"></div>
    <input type="hidden" name="action" value="">

    <div class="bg-[#0b0b0b]/95 backdrop-blur-md text-white border border-white/20 shadow-2xl rounded-2xl p-2.5 sm:px-4 sm:py-3 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-2 sm:gap-4 ring-1 ring-black/40">
        <!-- Selection Status -->
        <div class="flex items-center justify-between sm:justify-start gap-2.5 shrink-0 px-1 sm:px-0">
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center justify-center min-w-[22px] h-[22px] px-1.5 rounded-full bg-[#B7FF00] text-black font-black text-[11px] font-mono shadow-xs" data-bulk-count>
                    0
                </span>
                <span class="font-bold text-xs sm:text-sm text-white tracking-tight">
                    selected
                </span>
            </div>
            <span class="text-white/20 hidden sm:inline">|</span>
            <button type="button" class="text-xs text-white/60 hover:text-white underline transition px-1 py-0.5" data-bulk-clear>
                Deselect all
            </button>
        </div>

        <!-- Actions Slot -->
        <div class="flex items-center justify-end gap-1.5 sm:gap-2 flex-wrap">
            {{ $slot }}
        </div>
    </div>
</form>
