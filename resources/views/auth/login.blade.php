<!--
/* Hallmark · macrostructure: split-screen-industrial-terminal · theme: Tactile Sage & Deep Rail · genre: modern-minimal
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
    <title>Sign In &bull; {{ $platformName ?? config('app.name', 'INVOIRY') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full bg-[#D5DDC8] selection:bg-[#111111] selection:text-[#B7FF00] flex flex-col justify-center items-center p-3 sm:p-6" style="overflow-x: clip;">
    <x-page-loader />

    <div class="w-full max-w-5xl bg-white rounded-3xl border border-[#E6E6E6] shadow-xl overflow-hidden flex flex-col lg:flex-row min-h-[620px]">

        <!-- Left Showcase Pane (Deep Rail / Obsidian Theme) -->
        <div class="hidden lg:block lg:w-5/12 bg-[#050505] text-[#E7E7E7] p-8 sm:p-10 flex flex-col justify-between border-b lg:border-b-0 lg:border-r border-white/10 relative overflow-hidden">
            <!-- Subtle Radial Glow in Brand Lime -->
            <div class="absolute -top-24 -left-24 w-80 h-80 rounded-full bg-[#B7FF00]/10 blur-3xl pointer-events-none"></div>

            <!-- Top Header & Brand -->
            <div class="relative space-y-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-[#B7FF00] text-[#101010] flex items-center justify-center font-black text-sm shadow-xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        </div>
                        <span class="font-mono text-xs uppercase tracking-widest font-black text-white">INVOIRY</span>
                    </div>

                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white/10 text-[10px] font-mono text-[#B7FF00] border border-white/5">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#B7FF00] animate-pulse"></span>
                        <span>INVOIRY {{ \App\Models\SystemSetting::get('app_version', 'v1.0.0') }}</span>
                    </span>
                </div>

                <div class="space-y-3">
                    <h2 class="text-2xl sm:text-3xl font-black text-white tracking-tight leading-snug">
                        Point-of-Sale &amp; Multi-Store Retail Intelligence.
                    </h2>
                    <p class="text-xs sm:text-sm text-[#929292] leading-relaxed">
                        Engineered for modern electronics, mobile hardware, and retail chains with real-time inventory, thermal printing, and automated GST compliance.
                    </p>
                </div>
            </div>

            <!-- Feature Badges Grid -->
            <div class="relative py-6 space-y-3">
                <div class="flex items-start gap-3 p-3 rounded-xl bg-white/5 border border-white/10">
                    <div class="p-1.5 rounded-lg bg-[#B7FF00]/20 text-[#B7FF00] mt-0.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-white">Thermal & Laser Invoice Generation</h4>
                        <p class="text-[11px] text-[#929292]">ESC/POS 58mm/80mm receipts, dynamic UPI QR & PDF downloads</p>
                    </div>
                </div>

                <div class="flex items-start gap-3 p-3 rounded-xl bg-white/5 border border-white/10">
                    <div class="p-1.5 rounded-lg bg-[#B7FF00]/20 text-[#B7FF00] mt-0.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-white">Full GST Compliance Engine</h4>
                        <p class="text-[11px] text-[#929292]">Automatic intra-state (CGST+SGST) and inter-state (IGST) taxation</p>
                    </div>
                </div>

                <div class="flex items-start gap-3 p-3 rounded-xl bg-white/5 border border-white/10">
                    <div class="p-1.5 rounded-lg bg-[#B7FF00]/20 text-[#B7FF00] mt-0.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-white">Dynamic RBAC & Form Customization</h4>
                        <p class="text-[11px] text-[#929292]">Tailor input fields and staff capabilities per business store</p>
                    </div>
                </div>
            </div>

            <!-- Footer Compliance Indicators -->
            <div class="relative pt-4 border-t border-white/10 flex items-center justify-between text-[11px] text-[#929292] font-mono">
                <span class="flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-[#B7FF00]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    <span>256-bit AES Tenant Isolation</span>
                </span>
                <span>99.98% SLA</span>
            </div>
        </div>

        <!-- Right Interactive Authentication Pane -->
        <div class="flex-1 bg-white p-8 sm:p-12 flex flex-col justify-between">
            <div class="space-y-6 max-w-md mx-auto w-full">

                <!-- Header -->
                <div class="space-y-1">
                    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-[#F7F7F6] border border-[#E6E6E6] text-[11px] font-bold text-[#111111] mb-2 font-mono">
                        <span class="w-2 h-2 rounded-full bg-[#B7FF00] border border-[#111111]"></span>
                        <span>SECURE TERMINAL ACCESS</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black text-[#111111] tracking-tight">Sign In</h1>
                    <p class="text-xs sm:text-sm text-[#666666]">Enter your registered credentials or select an evaluation role.</p>
                </div>

                <!-- 1-Click Evaluation Roles Container -->
                @if($showDemoCredentials ?? true)
                    <div class="p-3.5 bg-[#F7F7F6] rounded-2xl border border-[#E6E6E6] space-y-2.5">
                        <div class="flex items-center justify-between text-xs font-bold text-[#111111]">
                            <span class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-[#111111]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                <span>Quick Evaluation Roles</span>
                            </span>
                            <span class="text-[10px] text-[#666666] font-mono">1-Click Sign In</span>
                        </div>

                        <div class="grid grid-cols-2 gap-1.5">
                            <a href="{{ route('demo.login', 'freetrial') }}" title="Shop Admin: Full counter and inventory controls"
                               class="px-3 py-2 bg-[#050505] hover:bg-black text-[#B7FF00] rounded-xl text-xs font-bold text-center transition flex items-center justify-center gap-1.5 min-h-[38px] shadow-2xs">
                                <span>Shop Admin</span>
                            </a>
                            <a href="{{ route('demo.login', 'cashier') }}" title="Store Cashier: POS invoice billing counter"
                               class="px-3 py-2 bg-white hover:bg-[#F2F4F7] text-[#111111] border border-[#E6E6E6] rounded-xl text-xs font-bold text-center transition flex items-center justify-center gap-1.5 min-h-[38px]">
                                <span>Store Cashier</span>
                            </a>
                            <a href="{{ route('demo.login', 'superadmin') }}" title="Super Admin: Multi-tenant platform master"
                               class="px-3 py-2 bg-white hover:bg-[#F2F4F7] text-[#111111] border border-[#E6E6E6] rounded-xl text-xs font-bold text-center transition flex items-center justify-center gap-1.5 min-h-[38px]">
                                <span>Super Admin</span>
                            </a>
                            <a href="{{ route('demo.login', 'apex') }}" title="Apex Electronics: Enterprise tenant"
                               class="px-3 py-2 bg-white hover:bg-[#F2F4F7] text-[#111111] border border-[#E6E6E6] rounded-xl text-xs font-bold text-center transition flex items-center justify-center gap-1.5 min-h-[38px]">
                                <span>Apex Store</span>
                            </a>
                        </div>
                    </div>
                @endif

                <!-- Flash Status Banners -->
                @if($errors->any())
                    <div class="p-3.5 bg-[#FEF2F2] border border-[#FECDCA] text-[#B42318] rounded-xl text-xs font-semibold flex items-start gap-2.5">
                        <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                @if(session('success'))
                    <div class="p-3.5 bg-[#ECFDF3] border border-[#D1FADF] text-[#079455] rounded-xl text-xs font-semibold flex items-start gap-2.5">
                        <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                <!-- Sign In Form -->
                <form action="{{ route('login') }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label for="login" class="block text-xs font-bold text-[#344054] mb-1.5">Email or Phone Number</label>
                        <div class="relative">
                            <input type="text" name="login" id="login" required value="{{ old('login', 'freetrial@gmail.com') }}"
                                placeholder="name@store.com or 10-digit mobile"
                                class="w-full px-4 py-2.5 pl-10 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm font-medium text-[#111111] placeholder-[#929292] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111] transition min-h-[44px]">
                            <svg class="w-4 h-4 text-[#929292] absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path></svg>
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="password" class="block text-xs font-bold text-[#344054]">Password</label>
                            <a href="{{ route('password.request') }}" class="text-xs text-[#666666] hover:text-[#111111] font-semibold hover:underline">
                                Forgot password?
                            </a>
                        </div>
                        <div class="relative">
                            <input type="password" name="password" id="password" required value="freetrial"
                                placeholder="••••••••"
                                class="w-full px-4 py-2.5 pl-10 pr-12 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm font-medium text-[#111111] placeholder-[#929292] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111] transition min-h-[44px]">
                            <svg class="w-4 h-4 text-[#929292] absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            <x-password-toggle-btn target="password" />
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-1 text-xs">
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <input type="checkbox" name="remember" class="w-4 h-4 rounded text-[#111111] border-[#D0D5DD] focus:ring-[#111111]">
                            <span class="text-xs text-[#344054] font-medium">Keep terminal session active</span>
                        </label>
                    </div>

                    <button type="submit" class="w-full py-3 bg-[#B7FF00] hover:bg-[#a6ec00] text-[#101010] rounded-xl text-xs sm:text-sm font-black transition shadow-xs whitespace-nowrap active:scale-[0.98] min-h-[46px] flex items-center justify-center gap-2 border border-[#a6ea00]">
                        <span>Sign In to Terminal</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </button>
                </form>

                <!-- Onboarding Callout -->
                <div class="pt-4 border-t border-[#E6E6E6] text-center space-y-2">
                    <p class="text-xs text-[#666666]">
                        Setting up a new counter?
                        <a href="{{ route('register') }}" class="font-bold text-[#111111] hover:underline inline-flex items-center gap-1">
                            <span>Start 14-day free store trial</span>
                        </a>
                    </p>
                </div>
            </div>

            <!-- Terminal Footnote -->
            <div class="mt-8 text-center text-[11px] text-[#929292] font-mono">
                <span>Hardware Barcode Scanner &bull; Thermal ESC/POS &bull; Dynamic UPI QR</span>
            </div>
        </div>
    </div>

    <script>
        function togglePasswordVisibility() {
            const pwd = document.getElementById('password');
            const txt = document.getElementById('toggleText');
            if (pwd.type === 'password') {
                pwd.type = 'text';
                txt.textContent = 'Hide';
            } else {
                pwd.type = 'password';
                txt.textContent = 'Show';
            }
        }
    </script>
</body>
</html>
