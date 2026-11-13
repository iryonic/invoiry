@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="w-full">
        {{-- Mobile View (< sm: 640px) --}}
        <div class="flex items-center justify-between gap-2 sm:hidden">
            {{-- Mobile Previous --}}
            @if ($paginator->onFirstPage())
                <span aria-disabled="true" aria-label="Previous page" class="inline-flex items-center justify-center gap-1 px-3 py-2 text-xs font-bold text-[#888888] bg-[#FAFAFA] border border-[#EEEEEE] rounded-xl cursor-not-allowed select-none opacity-50 min-h-[38px] shadow-2xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" /></svg>
                    <span>Prev</span>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Previous page" class="inline-flex items-center justify-center gap-1 px-3 py-2 text-xs font-bold text-[#111111] bg-white border border-[#E6E6E6] rounded-xl hover:bg-[#F7F7F6] active:bg-[#ECECEB] transition min-h-[38px] shadow-2xs cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" /></svg>
                    <span>Prev</span>
                </a>
            @endif

            {{-- Mobile Page Indicator --}}
            <span class="text-xs font-mono font-bold text-[#111111] bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl px-3 py-2 select-none shadow-2xs">
                Page {{ $paginator->currentPage() }}
            </span>

            {{-- Mobile Next --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Next page" class="inline-flex items-center justify-center gap-1 px-3 py-2 text-xs font-bold text-[#111111] bg-white border border-[#E6E6E6] rounded-xl hover:bg-[#F7F7F6] active:bg-[#ECECEB] transition min-h-[38px] shadow-2xs cursor-pointer">
                    <span>Next</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" /></svg>
                </a>
            @else
                <span aria-disabled="true" aria-label="Next page" class="inline-flex items-center justify-center gap-1 px-3 py-2 text-xs font-bold text-[#888888] bg-[#FAFAFA] border border-[#EEEEEE] rounded-xl cursor-not-allowed select-none opacity-50 min-h-[38px] shadow-2xs">
                    <span>Next</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" /></svg>
                </span>
            @endif
        </div>

        {{-- Desktop & Tablet View (>= sm: 640px) --}}
        <div class="hidden sm:flex sm:items-center sm:justify-between sm:gap-4">
            <div>
                <p class="text-xs text-[#666666]">
                    Page <span class="font-bold text-[#111111] font-mono">{{ $paginator->currentPage() }}</span>
                </p>
            </div>

            <div class="flex items-center gap-2">
                {{-- Previous Link --}}
                @if ($paginator->onFirstPage())
                    <span aria-disabled="true" aria-label="Previous page" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-[#999999] bg-[#FAFAFA] border border-[#EEEEEE] rounded-xl cursor-not-allowed select-none opacity-50 shadow-2xs min-h-[36px]">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" /></svg>
                        <span>Previous</span>
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Previous page" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-[#111111] bg-white border border-[#E6E6E6] rounded-xl hover:bg-[#F7F7F6] hover:border-[#D0D0D0] hover:text-black focus:outline-none focus:ring-2 focus:ring-black focus:ring-offset-1 transition shadow-2xs cursor-pointer min-h-[36px]">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" /></svg>
                        <span>Previous</span>
                    </a>
                @endif

                {{-- Next Link --}}
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Next page" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-[#111111] bg-white border border-[#E6E6E6] rounded-xl hover:bg-[#F7F7F6] hover:border-[#D0D0D0] hover:text-black focus:outline-none focus:ring-2 focus:ring-black focus:ring-offset-1 transition shadow-2xs cursor-pointer min-h-[36px]">
                        <span>Next</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" /></svg>
                    </a>
                @else
                    <span aria-disabled="true" aria-label="Next page" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-[#999999] bg-[#FAFAFA] border border-[#EEEEEE] rounded-xl cursor-not-allowed select-none opacity-50 shadow-2xs min-h-[36px]">
                        <span>Next</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" /></svg>
                    </span>
                @endif
            </div>
        </div>
    </nav>
@endif
