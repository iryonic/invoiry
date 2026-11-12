@props([
    'name',
    'id' => null,
    'value' => '1',
    'checked' => false,
    'disabled' => false,
    'label' => null,
    'description' => null,
    'badge' => null,
    'size' => 'md',
    'onchange' => null,
])

@php
    $inputId = $id ?? 'toggle_' . str_replace(['[', ']', '.'], '_', $name) . '_' . uniqid();
    $isChecked = (bool) $checked;
    $isDisabled = (bool) $disabled;
    
    $trackDimensions = $size === 'sm' ? 'w-8 h-4.5' : 'w-11 h-6';
    $thumbDimensions = $size === 'sm' ? 'w-3.5 h-3.5' : 'w-4.5 h-4.5';
    $thumbTranslate = $size === 'sm' ? 'peer-checked:translate-x-3.5' : 'peer-checked:translate-x-5';
    $thumbOffset = $size === 'sm' ? 'top-[2px] left-[2px]' : 'top-[3px] left-[3px]';
@endphp

<label for="{{ $inputId }}" class="flex items-start justify-between gap-3 group select-none {{ $isDisabled ? 'cursor-not-allowed opacity-60' : 'cursor-pointer' }}">
    @if($label || $description)
        <div class="flex-1 min-w-0 pr-1">
            <div class="flex items-center gap-2 flex-wrap">
                <span class="text-xs font-bold text-[#111111] group-hover:text-black transition">
                    {{ $label }}
                </span>
                @if($badge)
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-mono font-bold uppercase {{ $badge === 'System' || $badge === 'Required' ? 'bg-[#111111] text-[#B7FF00]' : 'bg-[#F2F4F7] text-[#344054]' }}">
                        {{ $badge }}
                    </span>
                @endif
            </div>
            @if($description)
                <p class="text-[11px] text-[#666666] mt-0.5 leading-snug">
                    {{ $description }}
                </p>
            @endif
        </div>
    @endif

    <div class="relative inline-flex items-center flex-shrink-0 mt-0.5">
        <input type="checkbox"
               name="{{ $name }}"
               id="{{ $inputId }}"
               value="{{ $value }}"
               {{ $isChecked ? 'checked' : '' }}
               {{ $isDisabled ? 'disabled' : '' }}
               @if($onchange) onchange="{{ $onchange }}" @endif
               class="sr-only peer">

        <!-- Track Background -->
        <div class="{{ $trackDimensions }} bg-[#E6E6E6] border border-[#D5D5D5] peer-focus-visible:ring-2 peer-focus-visible:ring-[#111111] peer-focus-visible:ring-offset-2 rounded-full peer-checked:bg-[#B7FF00] peer-checked:border-[#9EDC00] transition-colors duration-200 ease-in-out shadow-inner"></div>

        <!-- Sliding Knob Thumb -->
        <div class="absolute {{ $thumbOffset }} {{ $thumbDimensions }} bg-white border border-black/10 rounded-full shadow-xs peer-checked:bg-[#101010] peer-checked:border-black {{ $thumbTranslate }} transition-transform duration-200 ease-in-out"></div>
    </div>
</label>

