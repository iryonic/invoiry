<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full antialiased" style="overflow-x: clip;">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @include('layouts.partials.head-meta')

    <title>@yield('title', 'Billing & Inventory') &bull; {{ \App\Services\TenantContext::get()?->name ?? config('app.name', 'INVOIRY') }}</title>

    <!-- Clean Typography: Plus Jakarta Sans & JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- HTML5 QR Code & Barcode Engine -->
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full text-[#111111] selection:bg-[#B7FF00] selection:text-[#101010] bg-[#F7F7F6] lg:bg-[#D5DDC8] flex flex-col justify-center items-center" style="overflow-x: clip;">

    <x-page-loader />

    @php
        $tenant = \App\Services\TenantContext::get();
        $user = auth()->user();
        $isImpersonating = session()->has('active_tenant_id');
    @endphp

    @if($isImpersonating)
        <!-- High-Contrast Impersonation Bar -->
        <aside aria-label="System Notice" class="fixed top-0 left-0 right-0 z-50 bg-[#D97706] text-white px-4 py-1.5 text-xs font-semibold flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2">
                <span class="inline-block w-2 h-2 rounded-full bg-white animate-pulse"></span>
                <span>Viewing <strong>{{ $tenant->name }}</strong> as Platform Super Admin</span>
            </div>
            <a href="{{ route('superadmin.leave-impersonate') }}" class="bg-[#050505] text-white px-3 py-0.5 rounded text-[11px] font-bold hover:bg-[#1a1a1a] transition whitespace-nowrap">
                Return to Super Admin
            </a>
        </aside>
    @endif

    <!-- Mobile Slide-out Drawer Backdrop -->
    <div id="mobileNavBackdrop" onclick="toggleMobileNav()" class="fixed inset-0 bg-black/60 z-50 hidden transition-opacity lg:hidden"></div>

    <!-- Mobile Navigation Drawer -->
    <div id="mobileNavDrawer" class="fixed inset-y-0 left-0 w-72 bg-[#050505] text-[#E7E7E7] z-50 transform -translate-x-full transition-transform duration-200 ease-in-out lg:hidden flex flex-col">
        <!-- Brand Header in Drawer -->
        <div class="p-5 border-b border-white/10 flex items-center justify-between">
            <div class="flex items-center gap-3">
                @if($user?->isSuperAdmin() && !$isImpersonating)
                    <div class="w-8 h-8 rounded-lg bg-[#B7FF00] text-[#101010] flex items-center justify-center font-black text-sm shadow-xs flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-xs font-bold text-white truncate">Platform Master</h3>
                        <p class="text-[11px] text-[#B7FF00] font-mono font-bold">Super Admin Console</p>
                    </div>
                @else
                    <div class="w-8 h-8 rounded-lg bg-[#B7FF00] text-[#101010] flex items-center justify-center font-bold text-sm shadow-xs flex-shrink-0">
                        {{ substr($tenant?->name ?? 'I', 0, 1) }}
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-xs font-bold text-white truncate">{{ $tenant?->name ?? 'INVOIRY Store' }}</h3>
                        <p class="text-[11px] text-[#929292] font-mono">{{ $tenant?->city ?? 'Retail Counter' }}</p>
                    </div>
                @endif
            </div>
            <button type="button" onclick="toggleMobileNav()" class="text-[#929292] hover:text-white p-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- Drawer Links -->
        <div class="flex-1 overflow-y-auto p-4 space-y-4">
            @include('layouts.partials.nav-links', ['isMobile' => true])
        </div>

        <!-- Drawer Footer -->
        <div class="p-4 border-t border-white/10 flex items-center justify-between">
            <a href="{{ route('profile.edit') }}" class="min-w-0 hover:opacity-80 transition" title="My Profile">
                <p class="text-xs font-semibold text-white truncate">{{ $user?->name }}</p>
                <p class="text-[11px] text-[#929292] truncate">{{ $user?->email }}</p>
            </a>
            <div class="flex items-center gap-1">
                <a href="{{ route('lock') }}" title="Lock Terminal" class="p-2 text-[#929292] hover:text-[#B7FF00] rounded-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                </a>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" title="Sign out" class="p-2 text-[#929292] hover:text-white rounded-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Main Outer Container / Desktop Shell -->
    <div class="w-full h-full lg:p-0 flex flex-col {{ $isImpersonating ? 'pt-7' : '' }}">
        
        <!-- Application Frame -->
        <div class="w-full h-full flex flex-row bg-[#F7F7F6] lg:shadow-xl lg:border lg:border-[#E6E6E6] overflow-hidden">

            <!-- Desktop Sidebar: #050505 Fixed-feeling Navigation Rail -->
            <aside class="hidden lg:flex flex-col w-56 xl:w-60 bg-[#050505] text-[#E7E7E7] flex-shrink-0 select-none border-r border-[#1a1a1a]">
                
                <!-- Brand Area -->
                <div class="p-4 xl:p-5 border-b border-white/10 flex items-center gap-3">
                    @if($user?->isSuperAdmin() && !$isImpersonating)
                        <div class="w-8 h-8 rounded-lg bg-[#B7FF00] text-[#101010] flex items-center justify-center font-black text-sm shadow-xs flex-shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h2 class="text-xs font-bold text-white truncate tracking-tight">Platform Master</h2>
                            <p class="text-[11px] text-[#B7FF00] font-mono truncate mt-0.5 font-bold">Super Admin</p>
                        </div>
                    @else
                        <div class="w-8 h-8 rounded-lg bg-[#B7FF00] text-[#101010] flex items-center justify-center font-extrabold text-sm shadow-xs flex-shrink-0">
                            {{ substr($tenant?->name ?? 'I', 0, 1) }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <h2 class="text-xs font-bold text-white truncate tracking-tight">{{ $tenant?->name ?? 'INVOIRY Store' }}</h2>
                            <p class="text-[11px] text-[#929292] truncate mt-0.5">{{ $tenant?->city ? $tenant->city . ' Terminal' : 'Retail Counter' }}</p>
                        </div>
                    @endif
                </div>

                <!-- Navigation Groups -->
                <nav class="flex-1 px-3 py-4 space-y-4 overflow-y-auto custom-scrollbar">
                    @include('layouts.partials.nav-links', ['isMobile' => false])
                </nav>

                <!-- User Footer in Sidebar -->
                <div class="p-3.5 border-t border-white/10 bg-[#050505] flex items-center justify-between">
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 min-w-0 hover:opacity-80 transition group" title="View Profile">
                        <img src="{{ $user?->avatar_url }}" alt="{{ $user?->name }}" class="w-7 h-7 rounded-md object-cover bg-[#1a1a1a] border border-white/10">
                        <div class="min-w-0">
                            <p class="text-xs font-semibold text-[#E7E7E7] truncate group-hover:text-[#B7FF00]">{{ $user?->name }}</p>
                            <p class="text-[10px] text-[#929292] font-mono capitalize truncate">{{ str_replace('_', ' ', $user?->role ?? '') }}</p>
                        </div>
                    </a>
                    <div class="flex items-center gap-1">
                        <a href="{{ route('lock') }}" title="Lock Terminal" class="p-1.5 text-[#929292] hover:text-[#B7FF00] rounded-md hover:bg-white/5 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        </a>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" title="Sign out" class="p-1.5 text-[#929292] hover:text-[#B7FF00] rounded-md hover:bg-white/5 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                            </button>
                        </form>
                    </div>
                </div>
            </aside>

            <!-- Main Workspace Area -->
            <div class="flex-1 flex flex-col min-w-0 h-full overflow-hidden bg-[#F7F7F6]">

                <!-- Top Header: Compact #FFFFFF Bar across workspace -->
                <header class="h-14 lg:h-16 bg-[#FFFFFF] border-b border-[#E6E6E6] px-4 sm:px-6 flex items-center justify-between flex-shrink-0 z-10">
                    <div class="flex items-center gap-3 min-w-0">
                        <!-- Mobile Hamburger Button -->
                        <button type="button" onclick="toggleMobileNav()" class="lg:hidden p-2 text-slate-700 hover:text-black rounded-lg hover:bg-slate-100 min-h-[40px] min-w-[40px] flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                        </button>
                        <div>
                            <h1 class="text-sm sm:text-base font-bold text-[#111111] tracking-tight">@yield('page-title', 'Dashboard')</h1>
                            <p class="text-[11px] text-[#666666] hidden sm:block">@yield('page-subtitle', $user?->isSuperAdmin() && !$isImpersonating ? 'Multi-Tenant Master Control' : ($tenant?->name ?? 'Retail Operations'))</p>
                        </div>
                    </div>

                    <!-- Right Header Utilities -->
                    <div class="flex items-center gap-2 sm:gap-3">
                        @if($user?->isSuperAdmin() && !$isImpersonating)
                            <a href="{{ route('superadmin.tenants.create') }}" class="btn-lime text-xs px-3 py-1.5 rounded-lg min-h-[36px]">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                                <span>Add </span>
                            </a>
                            <div class="h-6 w-px bg-[#E6E6E6] hidden sm:block"></div>
                            <div class="hidden sm:flex items-center gap-2 px-2.5 py-1 rounded-full bg-[#111111] text-[#B7FF00] text-[11px] font-bold font-mono border border-black">
                                <span class="w-2 h-2 rounded-full bg-[#B7FF00] animate-pulse"></span>
                                <span>Root Console</span>
                            </div>
                        @elseif($user?->hasPermission('invoices.create'))
                            <a href="{{ route('invoices.create') }}" class="btn-lime text-xs px-3 py-1.5 sm:px-3.5 sm:py-2 rounded-lg min-h-[36px]">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                                <span>New Bill</span>
                            </a>
                            <div class="h-6 w-px bg-[#E6E6E6] hidden sm:block"></div>
                            <div class="hidden sm:flex items-center gap-2 px-2.5 py-1 rounded-full bg-[#F2F4F7] border border-[#EAECF0] text-[11px] font-medium text-[#344054]">
                                <span class="w-2 h-2 rounded-full bg-[#079455]"></span>
                                <span>Live POS</span>
                            </div>
                        @endif

                        <div class="h-6 w-px bg-[#E6E6E6] hidden sm:block"></div>

                        <!-- Profile Link in Header -->
                        <a href="{{ route('profile.edit') }}" title="My Profile & Security" class="flex items-center gap-2 p-1 pl-1.5 pr-2 rounded-full border border-[#E6E6E6] hover:border-black/30 hover:bg-[#F7F7F6] transition min-h-[36px]">
                            <img src="{{ $user?->avatar_url }}" alt="{{ $user?->name }}" class="w-6 h-6 rounded-full object-cover bg-[#050505]">
                            <span class="text-xs font-semibold text-[#111111] hidden md:inline truncate max-w-[120px]">{{ $user?->name }}</span>
                        </a>
                    </div>
                </header>

                <!-- System Notification Toasts (Industry-Grade Alert System) -->
                <div class="px-4 sm:px-6 mt-3 space-y-2.5 empty:hidden" id="notificationToastContainer">
                    @if(session('success'))
                        <div class="p-3.5 bg-[#ECFDF3] border border-[#A6F4C5] text-[#027A48] rounded-xl text-xs font-semibold flex items-center justify-between shadow-xs auto-dismiss-alert" role="alert">
                            <div class="flex items-center gap-2.5">
                                <div class="w-5 h-5 rounded-full bg-[#079455]/15 flex items-center justify-center shrink-0">
                                    <svg class="w-3.5 h-3.5 text-[#079455]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                                <span class="leading-relaxed">{{ session('success') }}</span>
                            </div>
                            <button type="button" onclick="this.closest('[role=alert]').remove()" class="text-[#027A48] hover:text-black p-1 text-sm font-bold ml-2">&times;</button>
                        </div>
                    @endif

                    @if(session('warning'))
                        <div class="p-3.5 bg-[#FFFBEB] border border-[#FDE68A] text-[#92400E] rounded-xl text-xs font-semibold flex items-center justify-between shadow-xs" role="alert">
                            <div class="flex items-center gap-2.5">
                                <div class="w-5 h-5 rounded-full bg-[#D97706]/15 flex items-center justify-center shrink-0">
                                    <svg class="w-3.5 h-3.5 text-[#D97706]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                </div>
                                <span class="leading-relaxed">{{ session('warning') }}</span>
                            </div>
                            <button type="button" onclick="this.closest('[role=alert]').remove()" class="text-[#92400E] hover:text-black p-1 text-sm font-bold ml-2">&times;</button>
                        </div>
                    @endif

                    @if(session('info'))
                        <div class="p-3.5 bg-[#EFF8FF] border border-[#B2DDFF] text-[#175CD3] rounded-xl text-xs font-semibold flex items-center justify-between shadow-xs auto-dismiss-alert" role="alert">
                            <div class="flex items-center gap-2.5">
                                <div class="w-5 h-5 rounded-full bg-[#175CD3]/15 flex items-center justify-center shrink-0">
                                    <svg class="w-3.5 h-3.5 text-[#175CD3]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <span class="leading-relaxed">{{ session('info') }}</span>
                            </div>
                            <button type="button" onclick="this.closest('[role=alert]').remove()" class="text-[#175CD3] hover:text-black p-1 text-sm font-bold ml-2">&times;</button>
                        </div>
                    @endif

                    @if(session('error') || $errors->any())
                        <div class="p-3.5 bg-[#FEF2F2] border border-[#FECACA] text-[#B91C1C] rounded-xl text-xs font-semibold shadow-xs flex items-start justify-between gap-3" role="alert">
                            <div class="flex items-start gap-2.5 flex-1 min-w-0">
                                <div class="w-5 h-5 rounded-full bg-[#DC2626]/15 flex items-center justify-center shrink-0 mt-0.5">
                                    <svg class="w-3.5 h-3.5 text-[#DC2626]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <div class="space-y-1 min-w-0">
                                    @if(session('error'))
                                        <p class="leading-relaxed">{{ session('error') }}</p>
                                    @endif
                                    @if($errors->any())
                                        <ul class="list-disc list-inside space-y-0.5 text-[#991B1B]">
                                            @foreach($errors->all() as $err)
                                                <li>{{ $err }}</li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                            </div>
                            <button type="button" onclick="this.closest('[role=alert]').remove()" class="text-[#B91C1C] hover:text-black p-1 text-sm font-bold">&times;</button>
                        </div>
                    @endif
                </div>

                <script>
                    setTimeout(() => {
                        document.querySelectorAll('.auto-dismiss-alert').forEach(el => {
                            el.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                            el.style.opacity = '0';
                            el.style.transform = 'translateY(-4px)';
                            setTimeout(() => el.remove(), 300);
                        });
                    }, 6000);
                </script>

                <!-- Workspace Scrollable Body -->
                <main class="flex-1 p-4 sm:p-6 overflow-y-auto custom-scrollbar flex flex-col justify-between pb-24 lg:pb-6" style="overflow-x: clip;">
                    <div class="flex-1 min-w-0">
                        @yield('content')
                    </div>

                    @include('layouts.partials.footer')
                </main>
            </div>
        </div>
    </div>

    <!-- Mobile Tactile Bottom Navigation Bar -->
    <nav aria-label="Mobile Navigation" class="lg:hidden fixed bottom-0 left-0 right-0 z-40 bg-[#FFFFFF] border-t border-[#E6E6E6] px-2 py-1 flex items-center justify-around select-none shadow-sm">
        <a href="{{ route('dashboard') }}" class="flex flex-col items-center justify-center w-14 py-1 text-xs font-medium {{ request()->routeIs('dashboard') ? 'text-[#101010] font-bold' : 'text-[#929292] hover:text-[#111111]' }} whitespace-nowrap">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
            <span class="text-[10px] mt-0.5">Home</span>
        </a>

        <a href="{{ route('items.index') }}" class="flex flex-col items-center justify-center w-14 py-1 text-xs font-medium {{ request()->routeIs('items.*') ? 'text-[#101010] font-bold' : 'text-[#929292] hover:text-[#111111]' }} whitespace-nowrap">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
            <span class="text-[10px] mt-0.5">Stock</span>
        </a>

        <!-- Center Raised Action Button: Vivid Lime Accent -->
        <a href="{{ route('invoices.create') }}" class="flex flex-col items-center justify-center -mt-4 whitespace-nowrap">
            <div class="w-12 h-12 rounded-full bg-[#B7FF00] text-[#101010] flex items-center justify-center shadow-md border-2 border-white active:scale-95 transition-transform">
                <svg class="w-5 h-5 text-[#101010]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
            </div>
            <span class="text-[10px] font-bold text-[#111111] mt-0.5">New Bill</span>
        </a>

        <a href="{{ route('invoices.index') }}" class="flex flex-col items-center justify-center w-14 py-1 text-xs font-medium {{ request()->routeIs('invoices.*') && !request()->routeIs('invoices.create') ? 'text-[#101010] font-bold' : 'text-[#929292] hover:text-[#111111]' }} whitespace-nowrap">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            <span class="text-[10px] mt-0.5">Sales</span>
        </a>

        <a href="{{ route('customers.index') }}" class="flex flex-col items-center justify-center w-14 py-1 text-xs font-medium {{ request()->routeIs('customers.*') ? 'text-[#101010] font-bold' : 'text-[#929292] hover:text-[#111111]' }} whitespace-nowrap">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            <span class="text-[10px] mt-0.5">Khata</span>
        </a>
    </nav>

    @include('layouts.partials.dialog-modal')

    <script>
        function toggleMobileNav() {
            const drawer = document.getElementById('mobileNavDrawer');
            const backdrop = document.getElementById('mobileNavBackdrop');
            if (drawer.classList.contains('-translate-x-full')) {
                drawer.classList.remove('-translate-x-full');
                backdrop.classList.remove('hidden');
            } else {
                drawer.classList.add('-translate-x-full');
                backdrop.classList.add('hidden');
            }
        }
    </script>
    @stack('scripts')
</body>
</html>
