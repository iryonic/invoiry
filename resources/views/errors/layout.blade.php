<!--
/* Hallmark · macrostructure: branded-terminal-error-screen · theme: Tactile Sage & Deep Rail · genre: modern-minimal
 * pre-emit critique: P5 H5 E5 S5 R5 V5
 * slop test: pass
 */
-->
<!DOCTYPE html>
<html lang="en" class="h-full antialiased" style="overflow-x: clip;">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover">
    @include('layouts.partials.head-meta')
    <title>@yield('title', 'Terminal Notice') &bull; {{ config('app.name', 'INVOIRY') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full @yield('body-bg', 'bg-white') selection:bg-[#111111] selection:text-[#B7FF00] flex flex-col justify-center items-center p-4 sm:p-6" style="overflow-x: clip;">
    <x-page-loader />

    <div class="w-full max-w-lg bg-white rounded-3xl border border-[#E6E6E6] shadow-xl p-8 sm:p-10 space-y-6 text-center">

        <!-- Top Status Pill -->
        <div class="flex items-center justify-center">
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-[#050505] text-[#FFFFFF] shadow-xs">
                <span class="w-2 h-2 rounded-full @yield('dot-color', 'bg-[#B7FF00]') animate-pulse"></span>
                <span class="font-mono text-[11px] font-bold tracking-wider uppercase">@yield('code-label', 'HTTP NOTICE')</span>
            </div>
        </div>

        <!-- Visual Icon Graphic -->
        <div class="mx-auto w-16 h-16 rounded-2xl bg-[#F7F7F6] border border-[#E6E6E6] flex items-center justify-center text-[#111111] shadow-2xs">
            @yield('icon')
        </div>

        <!-- Typography Heading & Description -->
        <div class="space-y-2 max-w-md mx-auto">
            <h1 class="text-2xl sm:text-3xl font-black text-[#111111] tracking-tight">
                @yield('heading', 'Something went wrong')
            </h1>
            <p class="text-xs sm:text-sm text-[#666666] leading-relaxed">
                @yield('message', 'The requested terminal operation could not be completed.')
            </p>
        </div>

        <!-- Optional Diagnostic Guidance Box -->
        @hasSection('guidance')
            <div class="p-3.5 bg-[#F7F7F6] rounded-2xl border border-[#E6E6E6] text-left text-xs text-[#666666] space-y-1">
                @yield('guidance')
            </div>
        @endif

        <!-- Action CTAs (Responsive) -->
        @hasSection('custom-actions')
            <div class="flex flex-col sm:flex-row items-center justify-center gap-2.5 pt-2">
                @yield('custom-actions')
            </div>
        @else
            <div class="flex flex-col sm:flex-row items-center justify-center gap-2.5 pt-2">
                @hasSection('primary-action')
                    @yield('primary-action')
                @else
                    <a href="{{ auth()->check() ? (auth()->user()->isSuperAdmin() ? route('superadmin.index') : route('dashboard')) : route('login') }}"
                       class="btn-lime w-full sm:w-auto min-h-[44px] text-xs font-bold inline-flex items-center justify-center gap-2 shadow-xs">
                        <svg class="w-4 h-4 text-[#101010]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        <span>Return to Dashboard</span>
                    </a>
                @endif

                <button type="button" onclick="window.history.back()"
                    class="btn-secondary w-full sm:w-auto min-h-[44px] text-xs inline-flex items-center justify-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-[#111111]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    <span>Previous Screen</span>
                </button>
            </div>
        @endif

        <!-- Terminal Diagnostics Footnote -->
        <div class="pt-4 border-t border-[#E6E6E6] text-[11px] text-[#929292] font-mono flex items-center justify-between">
            <span>Terminal Diagnostics &bull; Protected State</span>
            <span>Ref: {{ substr(md5(request()->fullUrl() . now()->toDateString()), 0, 8) }}</span>
        </div>
    </div>
</body>
</html>

