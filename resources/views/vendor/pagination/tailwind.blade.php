@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="w-full">
        {{-- Mobile View (< sm: 640px) --}}
        <div class="flex flex-col gap-2.5 sm:hidden">
            <div class="flex items-center justify-between gap-2">
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

                {{-- Mobile Page Selector Dropdown / Indicator --}}
                <div class="relative flex items-center">
                    <label for="mobile-page-select-{{ $paginator->getPageName() }}" class="sr-only">Select Page</label>
                    <select id="mobile-page-select-{{ $paginator->getPageName() }}"
                            onchange="if(this.value) window.location.href = this.value"
                            class="appearance-none cursor-pointer bg-[#F7F7F6] hover:bg-[#F0F0EE] border border-[#E6E6E6] rounded-xl py-2 pl-3 pr-7 text-xs font-mono font-bold text-[#111111] focus:outline-none focus:ring-2 focus:ring-black transition shadow-2xs">
                        @for ($page = 1; $page <= $paginator->lastPage(); $page++)
                            <option value="{{ $paginator->url($page) }}" {{ $page == $paginator->currentPage() ? 'selected' : '' }}>
                                Page {{ $page }} of {{ $paginator->lastPage() }}
                            </option>
                        @endfor
                    </select>
                    <div class="pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-[#666666]">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>

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

            {{-- Mobile Info Subtext --}}
            <div class="text-center">
                <p class="text-[11px] text-[#666666]">
                    Showing <span class="font-bold text-[#111111] font-mono">{{ $paginator->firstItem() ?? 0 }}</span>–<span class="font-bold text-[#111111] font-mono">{{ $paginator->lastItem() ?? 0 }}</span> of <span class="font-bold text-[#111111] font-mono">{{ $paginator->total() }}</span> results
                </p>
            </div>
        </div>

        {{-- Desktop & Tablet View (>= sm: 640px) --}}
        <div class="hidden sm:flex sm:items-center sm:justify-between sm:gap-4">
            {{-- Results Summary --}}
            <div class="shrink-0">
                <p class="text-xs text-[#666666]">
                    Showing
                    <span class="font-bold text-[#111111] font-mono">{{ $paginator->firstItem() ?? 0 }}</span>
                    to
                    <span class="font-bold text-[#111111] font-mono">{{ $paginator->lastItem() ?? 0 }}</span>
                    of
                    <span class="font-bold text-[#111111] font-mono">{{ $paginator->total() }}</span>
                    results
                </p>
            </div>

            {{-- Pagination Controls --}}
            <div class="flex items-center gap-1.5 flex-wrap justify-end">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <span aria-disabled="true" aria-label="Previous page" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-[#999999] bg-[#FAFAFA] border border-[#EEEEEE] rounded-xl cursor-not-allowed select-none opacity-50 shadow-2xs min-h-[36px]">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" /></svg>
                        <span class="hidden md:inline">Previous</span>
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Previous page" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-[#111111] bg-white border border-[#E6E6E6] rounded-xl hover:bg-[#F7F7F6] hover:border-[#D0D0D0] hover:text-black focus:outline-none focus:ring-2 focus:ring-black focus:ring-offset-1 transition shadow-2xs cursor-pointer min-h-[36px]">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" /></svg>
                        <span class="hidden md:inline">Previous</span>
                    </a>
                @endif

                {{-- Page Number Links --}}
                @foreach ($elements as $element)
                    {{-- Ellipsis Separator --}}
                    @if (is_string($element))
                        <span aria-disabled="true" class="inline-flex items-center justify-center min-w-[34px] h-[36px] px-1 text-xs font-mono text-[#999999] select-none">
                            …
                        </span>
                    @endif

                    {{-- Page Links Array --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span aria-current="page" class="inline-flex items-center justify-center min-w-[36px] h-[36px] px-2.5 text-xs font-mono font-bold text-[#B7FF00] bg-[#111111] border border-black rounded-xl shadow-xs select-none">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $url }}" aria-label="{{ __('Go to page :page', ['page' => $page]) }}" class="inline-flex items-center justify-center min-w-[36px] h-[36px] px-2.5 text-xs font-mono font-semibold text-[#333333] bg-white border border-[#E6E6E6] rounded-xl hover:bg-[#F7F7F6] hover:border-[#CCCCCC] hover:text-[#111111] focus:outline-none focus:ring-2 focus:ring-black focus:ring-offset-1 transition shadow-2xs cursor-pointer">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Next page" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-[#111111] bg-white border border-[#E6E6E6] rounded-xl hover:bg-[#F7F7F6] hover:border-[#D0D0D0] hover:text-black focus:outline-none focus:ring-2 focus:ring-black focus:ring-offset-1 transition shadow-2xs cursor-pointer min-h-[36px]">
                        <span class="hidden md:inline">Next</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" /></svg>
                    </a>
                @else
                    <span aria-disabled="true" aria-label="Next page" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-[#999999] bg-[#FAFAFA] border border-[#EEEEEE] rounded-xl cursor-not-allowed select-none opacity-50 shadow-2xs min-h-[36px]">
                        <span class="hidden md:inline">Next</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" /></svg>
                    </span>
                @endif

                {{-- Optional Quick Jump (when > 7 pages) --}}
                @if ($paginator->lastPage() > 7 && ($showJump ?? true))
                    <div class="hidden lg:flex items-center gap-1.5 pl-2 ml-1 border-l border-[#E6E6E6]">
                        <label for="desktop-jump-{{ $paginator->getPageName() }}" class="text-[11px] font-semibold text-[#888888] select-none">
                            Jump:
                        </label>
                        <input type="number"
                               id="desktop-jump-{{ $paginator->getPageName() }}"
                               min="1"
                               max="{{ $paginator->lastPage() }}"
                               value="{{ $paginator->currentPage() }}"
                               onkeydown="if(event.key === 'Enter') { event.preventDefault(); const p = parseInt(this.value); if(p >= 1 && p <= {{ $paginator->lastPage() }}) { const u = new URL(window.location.href); u.searchParams.set('{{ $paginator->getPageName() }}', p); window.location.href = u.toString(); } }"
                               class="w-12 h-[34px] text-center text-xs font-mono font-bold text-[#111111] bg-[#F7F7F6] hover:bg-white focus:bg-white border border-[#E6E6E6] rounded-xl focus:outline-none focus:ring-2 focus:ring-black transition shadow-2xs"
                               title="Type page number and press Enter" />
                    </div>
                @endif
            </div>
        </div>
    </nav>
@endif
