<!--
/* Hallmark · macrostructure: centered-password-reset · theme: Tactile Sage & Deep Rail · genre: modern-minimal
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
    <title>Set New Password &bull; {{ config('app.name', 'INVOIRY') }}</title>

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
            <h1 class="text-2xl font-black text-[#111111] tracking-tight">Set New Password</h1>
            <p class="text-xs sm:text-sm text-[#666666]">
                Choose a secure password for your terminal account.
            </p>
        </div>

        <!-- Error Alert -->
        @if($errors->any())
            <div class="p-3.5 bg-[#FEF2F2] border border-[#FECDCA] text-[#B42318] rounded-2xl text-xs font-semibold flex items-start gap-2.5">
                <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <!-- Form -->
        <form action="{{ route('password.update') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div>
                <label for="email" class="block text-xs font-bold text-[#344054] mb-1.5">Account Email</label>
                <input type="email" name="email" id="email" required readonly value="{{ old('email', $email) }}"
                    class="w-full px-4 py-2.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm font-medium text-[#666666] cursor-not-allowed min-h-[44px]">
            </div>

            <div>
                <label for="password" class="block text-xs font-bold text-[#344054] mb-1.5">New Password *</label>
                <div class="relative">
                    <input type="password" name="password" id="password" required oninput="checkPasswordStrength(this.value)"
                        placeholder="Minimum 6 characters"
                        class="w-full px-4 py-2.5 pl-10 pr-10 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm font-medium text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111] transition min-h-[44px]">
                    <svg class="w-4 h-4 text-[#929292] absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    <x-password-toggle-btn target="password" />
                </div>

                <!-- Dynamic Password Strength Meter -->
                <div class="mt-2 space-y-1">
                    <div class="flex items-center gap-1.5">
                        <div id="strBar1" class="h-1 flex-1 rounded-full bg-[#E6E6E6] transition-colors"></div>
                        <div id="strBar2" class="h-1 flex-1 rounded-full bg-[#E6E6E6] transition-colors"></div>
                        <div id="strBar3" class="h-1 flex-1 rounded-full bg-[#E6E6E6] transition-colors"></div>
                    </div>
                    <div class="flex justify-between items-center text-[10px] text-[#666666] font-mono">
                        <span id="strLabel">Enter password</span>
                        <span>Min 6 chars</span>
                    </div>
                </div>
            </div>

            <div>
                <label for="password_confirmation" class="block text-xs font-bold text-[#344054] mb-1.5">Confirm New Password *</label>
                <div class="relative">
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                        placeholder="Repeat new password"
                        class="w-full px-4 py-2.5 pl-10 pr-10 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl text-xs sm:text-sm font-medium text-[#111111] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#111111] transition min-h-[44px]">
                    <svg class="w-4 h-4 text-[#929292] absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    <x-password-toggle-btn target="password_confirmation" />
                </div>
            </div>

            <button type="submit" class="w-full py-3 bg-[#B7FF00] hover:bg-[#a6ec00] text-[#101010] rounded-xl text-xs sm:text-sm font-black transition shadow-xs whitespace-nowrap active:scale-[0.98] min-h-[46px] flex items-center justify-center gap-2 border border-[#a6ea00]">
                <span>Update Password & Sign In</span>
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

    <script>
        function checkPasswordStrength(val) {
            const b1 = document.getElementById('strBar1');
            const b2 = document.getElementById('strBar2');
            const b3 = document.getElementById('strBar3');
            const lbl = document.getElementById('strLabel');

            if (!val || val.length === 0) {
                b1.className = 'h-1 flex-1 rounded-full bg-[#E6E6E6] transition-colors';
                b2.className = 'h-1 flex-1 rounded-full bg-[#E6E6E6] transition-colors';
                b3.className = 'h-1 flex-1 rounded-full bg-[#E6E6E6] transition-colors';
                lbl.textContent = 'Enter password';
                lbl.className = 'text-[10px] text-[#666666] font-mono';
                return;
            }

            let score = 0;
            if (val.length >= 6) score++;
            if (val.length >= 8 && /[A-Z]/.test(val)) score++;
            if (/[0-9]/.test(val) && /[^A-Za-z0-9]/.test(val)) score++;

            if (score <= 1) {
                b1.className = 'h-1 flex-1 rounded-full bg-[#DC2626] transition-colors';
                b2.className = 'h-1 flex-1 rounded-full bg-[#E6E6E6] transition-colors';
                b3.className = 'h-1 flex-1 rounded-full bg-[#E6E6E6] transition-colors';
                lbl.textContent = 'Weak Password';
                lbl.className = 'text-[10px] text-[#DC2626] font-mono font-bold';
            } else if (score === 2) {
                b1.className = 'h-1 flex-1 rounded-full bg-[#D97706] transition-colors';
                b2.className = 'h-1 flex-1 rounded-full bg-[#D97706] transition-colors';
                b3.className = 'h-1 flex-1 rounded-full bg-[#E6E6E6] transition-colors';
                lbl.textContent = 'Medium Password';
                lbl.className = 'text-[10px] text-[#D97706] font-mono font-bold';
            } else {
                b1.className = 'h-1 flex-1 rounded-full bg-[#079455] transition-colors';
                b2.className = 'h-1 flex-1 rounded-full bg-[#079455] transition-colors';
                b3.className = 'h-1 flex-1 rounded-full bg-[#079455] transition-colors';
                lbl.textContent = 'Strong Password';
                lbl.className = 'text-[10px] text-[#079455] font-mono font-bold';
            }
        }
    </script>
</body>
</html>

