<!--
/* Hallmark · macrostructure: split-screen-onboarding-terminal · theme: Tactile Sage & Deep Rail · genre: modern-minimal
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
    <title>Create Store Account &bull; {{ config('app.name', 'INVOIRY') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full bg-[#D5DDC8] selection:bg-[#111111] selection:text-[#B7FF00] flex flex-col justify-center items-center p-3 sm:p-6" style="overflow-x: clip;">
    <x-page-loader />

    <div class="w-full max-w-5xl bg-white rounded-3xl border border-[#E6E6E6] shadow-xl overflow-hidden flex flex-col lg:flex-row min-h-[680px]">

        <!-- Left Showcase Pane (Deep Rail / Obsidian Theme) -->
        <div class="hidden lg:block lg:w-5/12 bg-[#050505] text-[#E7E7E7] p-8 sm:p-10 flex flex-col justify-between border-b lg:border-b-0 lg:border-r border-white/10 relative overflow-hidden">
            <div class="absolute -top-24 -left-24 w-80 h-80 rounded-full bg-[#B7FF00]/10 blur-3xl pointer-events-none"></div>

            <div class="relative space-y-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-[#B7FF00] text-[#101010] flex items-center justify-center font-black text-sm shadow-xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        </div>
                        <span class="font-mono text-xs uppercase tracking-widest font-black text-white">INVOIRY</span>
                    </div>

                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-[#B7FF00]/15 text-[10px] font-mono text-[#B7FF00] border border-[#B7FF00]/25 font-bold">
                        14-DAY FULL TRIAL
                    </span>
                </div>

                <div class="space-y-3">
                    <h2 class="text-2xl sm:text-3xl font-black text-white tracking-tight leading-snug">
                        Launch your retail counter in under 60 seconds.
                    </h2>
                    <p class="text-xs sm:text-sm text-[#929292] leading-relaxed">
                        Instant POS terminal provisioning with isolated database schema, pre-configured GST rules, and complete hardware scanner support.
                    </p>
                </div>
            </div>

            <!-- Value Props List -->
            <div class="relative py-6 space-y-3">
                <div class="flex items-start gap-3 p-3 rounded-xl bg-white/5 border border-white/10">
                    <div class="p-1.5 rounded-lg bg-[#B7FF00]/20 text-[#B7FF00] mt-0.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-white">Zero Setup Fee &bull; No Credit Card Required</h4>
                        <p class="text-[11px] text-[#929292]">Start invoicing immediately on your existing PC, tablet, or phone</p>
                    </div>
                </div>

                <div class="flex items-start gap-3 p-3 rounded-xl bg-white/5 border border-white/10">
                    <div class="p-1.5 rounded-lg bg-[#B7FF00]/20 text-[#B7FF00] mt-0.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-white">Full Hardware Integration</h4>
                        <p class="text-[11px] text-[#929292]">Compatible with 58/80mm ESC/POS thermal printers & USB barcode guns</p>
                    </div>
                </div>

                <div class="flex items-start gap-3 p-3 rounded-xl bg-white/5 border border-white/10">
                    <div class="p-1.5 rounded-lg bg-[#B7FF00]/20 text-[#B7FF00] mt-0.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-white">Dynamic UPI & Customer WhatsApp</h4>
                        <p class="text-[11px] text-[#929292]">Dynamic payment QR printed directly on bills with 1-click PDF dispatch</p>
                    </div>
                </div>
            </div>

            <!-- Footer Compliance Indicators -->
            <div class="relative pt-4 border-t border-white/10 flex items-center justify-between text-[11px] text-[#929292] font-mono">
                <span class="flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-[#B7FF00]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    <span>Bank-Grade Data Isolation</span>
                </span>
                <span>GST Act Ready</span>
            </div>
        </div>

        <!-- Right Form Pane -->
        <div class="flex-1 bg-white p-8 sm:p-12 flex flex-col justify-between">
            <div class="space-y-5 max-w-lg mx-auto w-full">

                <!-- Header -->
                <div class="space-y-1">
                    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-[#F7F7F6] border border-[#E6E6E6] text-[11px] font-bold text-[#111111] mb-2 font-mono">
                        <span class="w-2 h-2 rounded-full bg-[#B7FF00] border border-[#111111]"></span>
                        <span>STORE ONBOARDING</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black text-[#111111] tracking-tight">Create Business Store</h1>
                    <p class="text-xs sm:text-sm text-[#666666]">Enter your store details and administrative credentials.</p>
                </div>

                <!-- Error Alert -->
                @if($errors->any())
                    <div class="p-3.5 bg-[#FEF2F2] border border-[#FECDCA] text-[#B42318] rounded-xl text-xs font-semibold flex items-start gap-2.5">
                        <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                <!-- Registration Form -->
                <form action="{{ route('register') }}" method="POST" class="space-y-3.5">
                    @csrf

                    <!-- SECTION 1: BUSINESS STORE DETAILS -->
                    <div class="space-y-2">
                        <div class="text-[11px] font-bold text-[#666666] uppercase tracking-wider font-mono">1. Store Information</div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="sm:col-span-2">
                                <label for="store_name" class="block text-xs font-bold text-[#344054] mb-1">Store / Business Name *</label>
                                <input type="text" name="store_name" id="store_name" required value="{{ old('store_name') }}"
                                    placeholder="e.g. Apex Mobile & Electronics Hub"
                                    class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm font-medium text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111] transition min-h-[44px]">
                            </div>

                            <div>
                                <label for="state_code" class="block text-xs font-bold text-[#344054] mb-1">State / GST Region *</label>
                                <select name="state_code" id="state_code" required
                                    class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm font-medium text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111] transition min-h-[44px]">
                                    @foreach($states as $code => $state)
                                        <option value="{{ $code }}" {{ old('state_code', '01') == $code ? 'selected' : '' }}>
                                            {{ $code }} - {{ $state }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="gstin" class="block text-xs font-bold text-[#344054] mb-1">GSTIN (Optional)</label>
                                <input type="text" name="gstin" id="gstin" value="{{ old('gstin') }}"
                                    placeholder="e.g. 01AAAAA0000A1Z5" maxlength="15"
                                    class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm font-mono text-[#111111] uppercase focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111] transition min-h-[44px]">
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 2: OWNER CREDENTIALS -->
                    <div class="space-y-2 pt-2 border-t border-[#E6E6E6]">
                        <div class="text-[11px] font-bold text-[#666666] uppercase tracking-wider font-mono">2. Store Administrator</div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label for="name" class="block text-xs font-bold text-[#344054] mb-1">Admin Full Name *</label>
                                <input type="text" name="name" id="name" required value="{{ old('name') }}"
                                    placeholder="e.g. Aaqib Lone"
                                    class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm font-medium text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111] transition min-h-[44px]">
                            </div>

                            <div>
                                <label for="phone" class="block text-xs font-bold text-[#344054] mb-1">Mobile Phone Number *</label>
                                <input type="text" name="phone" id="phone" required value="{{ old('phone') }}"
                                    placeholder="10-digit mobile"
                                    class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm font-mono text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111] transition min-h-[44px]">
                            </div>

                            <div class="sm:col-span-2">
                                <label for="email" class="block text-xs font-bold text-[#344054] mb-1">Work Email Address *</label>
                                <input type="email" name="email" id="email" required value="{{ old('email') }}"
                                    placeholder="admin@yourstore.com"
                                    class="w-full px-3.5 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm font-medium text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111] transition min-h-[44px]">
                            </div>

                            <div>
                                <label for="password" class="block text-xs font-bold text-[#344054] mb-1">Password *</label>
                                <div class="relative">
                                    <input type="password" name="password" id="password" required
                                        placeholder="Minimum 6 characters"
                                        class="w-full px-3.5 py-2.5 pr-10 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm font-medium text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111] transition min-h-[44px]">
                                    <x-password-toggle-btn target="password" />
                                </div>
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-xs font-bold text-[#344054] mb-1">Confirm Password *</label>
                                <div class="relative">
                                    <input type="password" name="password_confirmation" id="password_confirmation" required
                                        placeholder="Repeat password"
                                        class="w-full px-3.5 py-2.5 pr-10 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm font-medium text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111] transition min-h-[44px]">
                                    <x-password-toggle-btn target="password_confirmation" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="w-full py-3 bg-[#B7FF00] hover:bg-[#a6ec00] text-[#101010] rounded-xl text-xs sm:text-sm font-black transition shadow-xs whitespace-nowrap active:scale-[0.98] min-h-[46px] flex items-center justify-center gap-2 border border-[#a6ea00] mt-2">
                        <span>Activate 14-Day Free Trial & Start Invoicing</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </button>
                </form>

                <!-- Footer Sign In Link -->
                <div class="pt-3 border-t border-[#E6E6E6] text-center space-y-2">
                    <p class="text-xs text-[#666666]">
                        Already registered your business?
                        <a href="{{ route('login') }}" class="font-bold text-[#111111] hover:underline inline-flex items-center gap-1">
                            <span>Sign in to terminal</span>
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

