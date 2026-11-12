<!--
/* Hallmark · macrostructure: centered-security-recovery · theme: Tactile Sage & Deep Rail · genre: modern-minimal
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
    <title>Forgot Password &bull; {{ config('app.name', 'INVOIRY') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full bg-[#D5DDC8] selection:bg-[#111111] selection:text-[#B7FF00] flex flex-col justify-center items-center p-3 sm:p-6" style="overflow-x: clip;">
    <x-page-loader />

    <div class="w-full max-w-md bg-white rounded-3xl border border-[#E6E6E6] shadow-xl p-8 sm:p-10 space-y-6">

        <!-- Brand Icon & Header -->
        <div class="text-center space-y-2">
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-[#050505] text-[#FFFFFF] shadow-xs mb-1">
                <span class="w-2 h-2 rounded-full bg-[#B7FF00] animate-pulse"></span>
                <span class="font-mono text-[11px] tracking-wider uppercase font-black">Security Center</span>
            </div>
            <h1 class="text-2xl font-black text-[#111111] tracking-tight">Forgot Password?</h1>
            <p class="text-xs sm:text-sm text-[#666666]">
                Enter your registered email address and we'll generate password recovery instructions for your terminal account.
            </p>
        </div>

        <!-- Success Status Notice -->
        @if(session('status'))
            <div class="p-3.5 bg-[#ECFDF3] border border-[#D1FADF] text-[#079455] rounded-2xl text-xs font-semibold space-y-2">
                <div class="flex items-start gap-2">
                    <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>{{ session('status') }}</span>
                </div>

                @if(session('demo_reset_link'))
                    <div class="pt-2 border-t border-[#D1FADF]/60">
                        <span class="text-[10px] text-[#079455] uppercase tracking-wider font-mono block font-bold mb-1.5">Evaluation Direct Reset Link</span>
                        <a href="{{ session('demo_reset_link') }}" class="btn-lime w-full text-center text-xs py-2 shadow-2xs font-mono">
                            Click here to reset password
                        </a>
                    </div>
                @endif
            </div>
        @endif

        <!-- Error Alert -->
        @if($errors->any())
            <div class="p-3.5 bg-[#FEF2F2] border border-[#FECDCA] text-[#B42318] rounded-2xl text-xs font-semibold flex items-start gap-2.5">
                <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <!-- Form -->
        <form action="{{ route('password.email') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label for="email" class="block text-xs font-bold text-[#344054] mb-1.5">Registered Account Email *</label>
                <div class="relative">
                    <input type="email" name="email" id="email" required value="{{ old('email', 'freetrial@gmail.com') }}"
                        placeholder="admin@yourstore.com"
                        class="w-full px-4 py-2.5 pl-10 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm font-medium text-[#111111] placeholder-[#929292] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111] transition min-h-[44px]">
                    <svg class="w-4 h-4 text-[#929292] absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                </div>
            </div>

            <button type="submit" class="w-full py-3 bg-[#B7FF00] hover:bg-[#a6ec00] text-[#101010] rounded-xl text-xs sm:text-sm font-black transition shadow-xs whitespace-nowrap active:scale-[0.98] min-h-[46px] flex items-center justify-center gap-2 border border-[#a6ea00]">
                <span>Send Password Reset Link</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </button>
        </form>

        <!-- Back Link -->
        <div class="pt-4 border-t border-[#E6E6E6] text-center">
            <a href="{{ route('login') }}" class="text-xs font-bold text-[#666666] hover:text-[#111111] inline-flex items-center gap-1.5 transition">
                <span>Back to Sign In</span>
            </a>
        </div>
    </div>
</body>
</html>

