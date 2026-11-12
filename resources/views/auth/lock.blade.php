<!DOCTYPE html>
<html lang="en" class="h-full antialiased" style="overflow-x: clip;">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover">
    @include('layouts.partials.head-meta')
    <title>Terminal Locked &bull; {{ config('app.name', 'INVOIRY') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full bg-[#050505] selection:bg-[#B7FF00] selection:text-[#101010] text-[#E7E7E7] flex flex-col justify-between items-center p-4 sm:p-6 relative overflow-hidden" style="overflow-x: clip;">
    <x-page-loader />

    <!-- Ambient Glowing Backdrop -->
    <div class="absolute -top-32 -right-32 w-96 h-96 rounded-full bg-[#B7FF00]/10 blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-32 -left-32 w-96 h-96 rounded-full bg-[#B7FF00]/10 blur-3xl pointer-events-none"></div>

    <!-- Top Status Bar: Live Digital Clock & Store Identifier -->
    <header class="w-full max-w-4xl flex items-center justify-between text-xs font-mono relative z-10">
        <div class="flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/5 border border-white/10 text-[#B7FF00]">
            <span class="w-2 h-2 rounded-full bg-[#B7FF00] animate-pulse"></span>
            <span class="font-bold tracking-wider uppercase">{{ $tenant->name ?? config('app.name', 'INVOIRY') }}</span>
        </div>

        <div class="text-right">
            <span id="liveClock" class="text-sm sm:text-base font-bold text-black tracking-widest font-mono">--:--:--</span>
            <span class="block text-[10px] text-[#929292] uppercase">{{ now()->format('l, d M Y') }}</span>
        </div>
    </header>

    <!-- Center Card: Lock Screen Prompt -->
    <main class="w-full max-w-sm bg-[#111111] rounded-3xl border border-white/10 p-6 sm:p-8 shadow-2xl space-y-5 relative z-10 text-center">

        <!-- User Profile Avatar & Identity -->
        <div class="space-y-3 flex flex-col items-center">
            <div class="relative">
                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}"
                     class="w-20 h-20 rounded-2xl object-cover bg-[#050505] border-2 border-[#B7FF00] shadow-xl">
                <div class="absolute -bottom-1 -right-1 w-6 h-6 rounded-lg bg-[#050505] border border-white/20 text-[#B7FF00] flex items-center justify-center">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                </div>
            </div>

            <div>
                <h1 class="text-lg sm:text-xl font-black text-white tracking-tight">{{ $user->name }}</h1>
                <p class="text-xs text-[#929292] font-mono">{{ $user->email }}</p>
                <div class="flex items-center justify-center gap-1.5 mt-1.5">
                    <span class="px-2.5 py-0.5 rounded-full bg-white/10 text-[10px] font-mono text-[#B7FF00] uppercase font-bold border border-white/5">
                        {{ str_replace('_', ' ', $user->role) }}
                    </span>
                    <span class="text-[11px] text-[#737373] font-mono">&bull; Shift Session Locked</span>
                </div>
            </div>
        </div>

        <p class="text-xs text-[#929292] leading-relaxed">
            Enter your password to unlock the workspace and resume billing operations.
        </p>

        <!-- Error Alert -->
        @if($errors->any())
            <div class="p-3 bg-[#FEF2F2]/10 border border-[#FEF2F2]/20 text-[#FCA5A5] rounded-xl text-xs font-semibold text-left flex items-center gap-2">
                <svg class="w-4 h-4 text-[#FCA5A5] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <!-- Unlock Form -->
        <form action="{{ route('unlock') }}" method="POST" class="space-y-4">
            @csrf

            <div class="relative">
                <input type="password" name="password" id="unlock_password" required autofocus
                    placeholder="Enter account password"
                    class="w-full px-4 py-3 pr-11 bg-[#050505] border border-white/15 rounded-xl text-xs sm:text-sm font-medium text-white placeholder-[#666666] focus:bg-[#050505] focus:outline-none focus:ring-2 focus:ring-[#B7FF00] text-center transition min-h-[46px]">
                <x-password-toggle-btn target="unlock_password" color="dark" />
            </div>

            <button type="submit" class="w-full py-3 bg-[#B7FF00] hover:bg-[#a6ec00] text-[#101010] rounded-xl text-xs sm:text-sm font-black transition shadow-xs whitespace-nowrap active:scale-[0.98] min-h-[46px] flex items-center justify-center gap-2 border border-[#a6ea00]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"></path></svg>
                <span>Unlock Terminal</span>
            </button>
        </form>

        <!-- Utility Navigation: Forgot Password & Switch Account -->
        <div class="pt-2 border-t border-white/10 flex flex-col items-center gap-2 text-xs">
            <a href="{{ route('password.request') }}" class="text-[#929292] hover:text-white font-mono hover:underline transition">
                Forgot your password?
            </a>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="text-xs text-[#929292] hover:text-[#B7FF00] font-mono hover:underline inline-flex items-center gap-1.5 transition">
                    <span>Sign in as a different staff member</span>
                </button>
            </form>
        </div>
    </main>

    <!-- Bottom Security Footnote -->
    <footer class="w-full max-w-4xl text-center text-[11px] text-[#666666] font-mono relative z-10">
        <span>Hardware POS Session Guard &bull; Shift Transactions Preserved &bull; 256-bit Encrypted</span>
    </footer>

    <script>
        function updateClock() {
            const now = new Date();
            const timeStr = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' });
            const clockEl = document.getElementById('liveClock');
            if (clockEl) clockEl.textContent = timeStr;
        }
        setInterval(updateClock, 1000);
        updateClock();

        function togglePasswordVisibility(inputId, btn) {
            const input = document.getElementById(inputId);
            if (!input) return;
            if (input.type === 'password') {
                input.type = 'text';
                btn.classList.add('text-[#B7FF00]');
                btn.classList.remove('text-[#929292]');
            } else {
                input.type = 'password';
                btn.classList.remove('text-[#B7FF00]');
                btn.classList.add('text-[#929292]');
            }
        }
    </script>
</body>
</html>
