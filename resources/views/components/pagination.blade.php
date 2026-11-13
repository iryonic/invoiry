@props([
    'paginator',
    'showInfo' => true,
    'showJump' => true,
    'class' => '',
])

@if ($paginator && ($paginator->hasPages() || ($showInfo && $paginator->total() > 0)))
    <div {{ $attributes->merge(['class' => 'w-full ' . $class]) }}>
        @if ($paginator->hasPages())
            {{ $paginator->links('vendor.pagination.tailwind', [
                'showInfo' => $showInfo,
                'showJump' => $showJump,
            ]) }}
        @elseif ($showInfo && $paginator->total() > 0)
            <div class="flex items-center justify-between text-xs text-[#666666]">
                <p>
                    Showing all
                    <span class="font-bold text-[#111111] font-mono">{{ $paginator->total() }}</span>
                    {{ \Illuminate\Support\Str::plural('result', $paginator->total()) }}
                </p>
            </div>
        @endif
    </div>
@endif

