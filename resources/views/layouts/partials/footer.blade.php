@php
    $footerEnabled = \App\Models\SystemSetting::isEnabled('footer_enabled', true);
@endphp

@if($footerEnabled)
@php
    $currentYear = date('Y');
    $appVersion = \App\Models\SystemSetting::get('app_version', 'v1.0.0');
    $rawCopyright = \App\Models\SystemSetting::get('footer_copyright', '© {year} INVOIRY');
    $shopName = $tenant?->name ?? 'INVOIRY';
    $copyright = str_replace(['{year}', '{shop}', '{tenant}', '{version}'], [$currentYear, $shopName, $shopName, $appVersion], $rawCopyright);
    $tagline = \App\Models\SystemSetting::get('footer_tagline', '');
    $contactInfo = \App\Models\SystemSetting::get('footer_contact_info', '');
    $badgeText = \App\Models\SystemSetting::get('footer_badge_text', '');

    $link1Text = \App\Models\SystemSetting::get('footer_link_1_text', '');
    $link1Url = \App\Models\SystemSetting::get('footer_link_1_url', '');
    $link2Text = \App\Models\SystemSetting::get('footer_link_2_text', '');
    $link2Url = \App\Models\SystemSetting::get('footer_link_2_url', '');
    $link3Text = \App\Models\SystemSetting::get('footer_link_3_text', '');
    $link3Url = \App\Models\SystemSetting::get('footer_link_3_url', '');
@endphp

<footer class="mt-8 pt-5 pb-6 border-t border-[#E6E6E6] text-xs text-[#666666] select-none" id="globalPageFooter">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
        <!-- Brand & Version -->
        <div class="flex items-center gap-2 flex-wrap">
            <span class="font-medium text-[#111111] tracking-tight">{!! e($copyright) !!}</span>
            @if($badgeText)
                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-[#050505] text-[#B7FF00] text-[10px] font-mono font-bold tracking-wide border border-black/20 shadow-2xs">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#B7FF00] animate-pulse"></span>
                    <span>{{ $badgeText }}</span>
                </span>
            @elseif($appVersion)
                <span class="text-[11px] text-[#888888] font-mono">{{ $appVersion }}</span>
            @endif
            @if(auth()->check() && auth()->user()->isSuperAdmin())
                <a href="{{ route('superadmin.settings.index') }}#tab=footer" title="Manage Global Footer in Super Admin Settings" class="inline-flex items-center gap-1 text-[10px] font-semibold text-[#079455] hover:text-black bg-[#ECFDF3] hover:bg-[#D1FADF] px-1.5 py-0.5 rounded border border-[#A6F4C5] transition shadow-2xs">
                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                    <span>Footer Settings</span>
                </a>
            @endif
        </div>

        @if($tagline || $contactInfo)
            <div class="text-[11px] text-[#888888]">
                {{ $tagline }} {{ $contactInfo ? ($tagline ? '· ' : '') . $contactInfo : '' }}
            </div>
        @endif

        <!-- Links (Optional) -->
        @if(($link1Text && $link1Url) || ($link2Text && $link2Url) || ($link3Text && $link3Url))
            <div class="flex items-center gap-3 sm:gap-4 flex-wrap text-[11px] text-[#888888]">
                @if($link1Text && $link1Url)
                    <a href="{{ $link1Url }}" class="hover:text-[#111111] transition">{{ $link1Text }}</a>
                @endif
                @if($link2Text && $link2Url)
                    @if($link1Text && $link1Url)<span class="text-[#D6D6D6]">&bull;</span>@endif
                    <a href="{{ $link2Url }}" class="hover:text-[#111111] transition">{{ $link2Text }}</a>
                @endif
                @if($link3Text && $link3Url)
                    @if(($link1Text && $link1Url) || ($link2Text && $link2Url))<span class="text-[#D6D6D6]">&bull;</span>@endif
                    <a href="{{ $link3Url }}" class="hover:text-[#111111] transition">{{ $link3Text }}</a>
                @endif
            </div>
        @endif
    </div>
</footer>
@endif

