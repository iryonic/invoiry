<!DOCTYPE html>
<html lang="en" class="h-full antialiased" style="overflow-x: clip;">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover">
    @include('layouts.partials.head-meta')

    <title>@yield('title', 'Legal & Documentation') &bull; {{ config('app.name', 'INVOIRY') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full bg-[#F7F7F6] text-[#111111] selection:bg-[#B7FF00] selection:text-[#101010] flex flex-col justify-between" style="overflow-x: clip;">
    <x-page-loader />

    <!-- Public Brand Header -->
    <header class="w-full bg-[#050505] text-[#E7E7E7] border-b border-white/10 px-4 sm:px-8 py-3.5 flex items-center justify-between sticky top-0 z-30 shadow-xs">
        <div class="max-w-5xl mx-auto w-full flex items-center justify-between">
            <a href="{{ route('login') }}" class="flex items-center gap-2.5 group">
                <div class="w-8 h-8 rounded-lg bg-[#B7FF00] text-[#101010] flex items-center justify-center font-black text-sm shadow-xs group-hover:scale-105 transition-transform">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
                <div class="flex items-center gap-2">
                    <span class="font-mono text-xs uppercase tracking-widest font-black text-white">INVOIRY</span>
                    <span class="hidden sm:inline-block px-1.5 py-0.5 rounded text-[10px] font-mono font-bold bg-white/10 text-[#B7FF00]">RETAIL ERP</span>
                </div>
            </a>

            <!-- Quick Action Nav -->
            <div class="flex items-center gap-3 text-xs">
                <a href="{{ route('privacy') }}" class="hover:text-white transition {{ request()->routeIs('privacy') ? 'text-[#B7FF00] font-bold' : 'text-[#929292]' }}">Privacy</a>
                <span class="text-white/20">&bull;</span>
                <a href="{{ route('terms') }}" class="hover:text-white transition {{ request()->routeIs('terms') ? 'text-[#B7FF00] font-bold' : 'text-[#929292]' }}">Terms</a>
                <span class="text-white/20">&bull;</span>
                <a href="{{ route('help') }}" class="hover:text-white transition {{ request()->routeIs('help') || request()->routeIs('docs') ? 'text-[#B7FF00] font-bold' : 'text-[#929292]' }}">Documentation</a>

                @if(auth()->check())
                    <a href="{{ auth()->user()->isSuperAdmin() ? route('superadmin.index') : route('dashboard') }}" class="ml-2 btn-lime text-xs px-3 py-1.5 rounded-lg min-h-[36px] flex items-center gap-1.5 shadow-xs">
                        <span>Terminal Dashboard</span>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="ml-2 btn-lime text-xs px-3 py-1.5 rounded-lg min-h-[36px] flex items-center gap-1.5 shadow-xs">
                        <span>Sign In</span>
                    </a>
                @endif
            </div>
        </div>
    </header>

    <!-- Main Content Container -->
    <main class="flex-1 w-full max-w-5xl mx-auto px-4 sm:px-6 py-8 sm:py-10">
        @yield('content')
    </main>

    <!-- Public Legal Footer -->
    <footer class="w-full bg-white border-t border-[#E6E6E6] py-6 px-4 sm:px-8 text-xs text-[#666666] select-none">
        <div class="max-w-5xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <span class="font-bold text-[#111111]">INVOIRY Retail Intelligence</span>
                <span>&bull;</span>
                <span class="font-mono text-[#888888]">&copy; {{ date('Y') }} All rights reserved.</span>
            </div>
            <div class="flex items-center gap-3 text-[11px] text-[#888888]">
                <a href="{{ route('privacy') }}" class="hover:text-black">Privacy Policy</a>
                <span>&bull;</span>
                <a href="{{ route('terms') }}" class="hover:text-black">Terms of Service</a>
                <span>&bull;</span>
                <a href="{{ route('help') }}" class="hover:text-black">Help & Docs</a>
            </div>
        </div>
    </footer>
</body>
</html>

