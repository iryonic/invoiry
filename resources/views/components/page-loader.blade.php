@php
    $preloaderEnabled = \App\Models\SystemSetting::isEnabled('preloader_enabled', true);
    $minTime = (int) \App\Models\SystemSetting::get('preloader_min_time', 0);
    $maxTime = (int) \App\Models\SystemSetting::get('preloader_max_time', 3500);
    $fadeDuration = (int) \App\Models\SystemSetting::get('preloader_fade_duration', 300);
@endphp
@if($preloaderEnabled)
    {{-- Animated Full-Screen Page Preloader: Lime Green Bouncing Dot (#80F644) & White Rotating Square (#fff) on Black (#111111) --}}
    <div id="pageLoader" class="page-loader-overlay active"
        data-min-time="{{ $minTime }}"
        data-max-time="{{ $maxTime }}"
        data-fade-duration="{{ $fadeDuration }}"
        aria-hidden="true" role="status">
        <span class="loader" aria-label="Loading..."></span>
    </div>
@endif
