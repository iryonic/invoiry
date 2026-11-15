@extends('layouts.app')

@section('title', 'My Profile & Security · ' . config('app.name', 'INVOIRY'))
@section('page-title', 'My Profile')
@section('page-subtitle', 'Manage personal identity, credentials, active sessions, and security history')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    <!-- Profile Header Card -->
    <div class="card-surface p-3 shadow-2xs flex flex-col sm:flex-row sm:items-center justify-between gap-6">
        <div class="flex items-center gap-4">
            <div class="relative group">
                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-16 h-16 rounded-2xl object-cover border-2 border-black/10 bg-[#050505]">
                <label for="avatar_upload_input" class="absolute inset-0 bg-black/50 text-white rounded-2xl opacity-0 group-hover:opacity-100 flex items-center justify-center cursor-pointer transition text-xs font-bold">
                    Change
                </label>
                <form id="avatar_upload_form" action="{{ route('profile.photo') }}" method="POST" enctype="multipart/form-data" class="hidden">
                    @csrf
                    <input type="file" name="avatar" id="avatar_upload_input" accept="image/png,image/jpeg,image/webp" onchange="document.getElementById('avatar_upload_form').submit()">
                </form>
            </div>

            <div class="space-y-1">
                <div class="flex items-center gap-2 flex-wrap">
                    <h2 class="text-lg font-black text-[#111111] tracking-tight">{{ $user->name }}</h2>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-[#050505] text-[#B7FF00] text-[10px] font-mono font-bold uppercase tracking-wide">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#B7FF00]"></span>
                        <span>{{ str_replace('_', ' ', $user->role) }}</span>
                    </span>
                    @if($user->email_verified_at)
                        <span class="badge-paid text-[10px] px-2 py-0.5 font-bold">Verified Email</span>
                    @else
                        <span class="badge-due text-[10px] px-2 py-0.5 font-bold">Unverified</span>
                    @endif
                </div>

                <p class="text-xs text-[#666666]">
                    {{ $user->tenant?->name ?? 'Platform Administrator' }} &bull; Member since {{ $user->created_at->format('M Y') }}
                </p>

                @if($user->avatar)
                    <form action="{{ route('profile.photo.destroy') }}" method="POST" class="pt-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-[11px] font-semibold text-[#DC2626] hover:underline inline-flex items-center gap-1">
                            <span>Remove Photo</span>
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('lock') }}" class="btn-dark text-xs min-h-[40px]">
                <svg class="w-4 h-4 text-[#B7FF00]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                <span>Lock Screen</span>
            </a>
        </div>
    </div>

    <!-- Section 1: Personal Contact Information -->
    <div class="card-surface p-5 sm:p-6 shadow-2xs space-y-4">
        <div class="border-b border-[#E6E6E6] pb-3 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-[#B7FF00]"></span>
                <h3 class="text-xs font-bold text-[#111111] uppercase tracking-wider">Personal Information</h3>
            </div>
            @if(!$user->email_verified_at)
                <form action="{{ route('profile.resend-verification') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-xs text-[#079455] hover:underline font-bold">
                        Confirm & Verify Email
                    </button>
                </form>
            @endif
        </div>

        <form action="{{ route('profile.update') }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-[#344054] mb-1">Full Name *</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                        class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm text-[#111111] focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#344054] mb-1">Email Address *</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                        class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm text-[#111111] focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#344054] mb-1">Phone Number</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="+91 9876543210"
                        class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm font-mono text-[#111111] focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#344054] mb-1">Assigned Business / Store</label>
                    <input type="text" value="{{ $user->tenant?->name ?? 'Global Platform Console' }}" disabled
                        class="w-full px-3 py-2 bg-[#ECECEC] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm text-[#666666] cursor-not-allowed">
                </div>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" class="btn-lime text-xs min-h-[42px] px-5 font-bold">
                    Save Profile Changes
                </button>
            </div>
        </form>
    </div>

    <!-- Section 2: Security & Password Update -->
    <div class="card-surface p-5 sm:p-6 shadow-2xs space-y-4">
        <div class="border-b border-[#E6E6E6] pb-3 flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-[#111111]"></span>
            <h3 class="text-xs font-bold text-[#111111] uppercase tracking-wider">Change Password</h3>
        </div>

        <form action="{{ route('profile.password') }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-[#344054] mb-1">Current Password *</label>
                    <div class="relative">
                        <input type="password" name="current_password" required placeholder="••••••••"
                            class="w-full pl-3 pr-10 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm text-[#111111] focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                        <x-password-toggle-btn />
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#344054] mb-1">New Password (Min 8 chars, letters & digits) *</label>
                    <div class="relative">
                        <input type="password" name="password" required placeholder="••••••••"
                            class="w-full pl-3 pr-10 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm text-[#111111] focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                        <x-password-toggle-btn />
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#344054] mb-1">Confirm New Password *</label>
                    <div class="relative">
                        <input type="password" name="password_confirmation" required placeholder="••••••••"
                            class="w-full pl-3 pr-10 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm text-[#111111] focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
                        <x-password-toggle-btn />
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" class="btn-dark text-xs min-h-[42px] px-5 font-bold">
                    Update Password
                </button>
            </div>
        </form>
    </div>

    <!-- Section 3: Active Device Sessions -->
    <div class="card-surface p-5 sm:p-6 shadow-2xs space-y-4">
        <div class="border-b border-[#E6E6E6] pb-3 flex flex-wrap items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-[#079455]"></span>
                <h3 class="text-xs font-bold text-[#111111] uppercase tracking-wider">Active Device Sessions</h3>
            </div>
            @if($activeSessions->where('is_current', false)->isNotEmpty())
                <form action="{{ route('profile.sessions.logout-others') }}" method="POST" data-confirm="Are you sure you want to sign out all other devices?">
                    @csrf
                    <input type="hidden" name="current_password" id="logout_others_pwd_hidden">
                    <button type="button" onclick="promptLogoutOthers()" class="text-xs text-[#DC2626] hover:underline font-bold">
                        Sign Out Other Devices
                    </button>
                </form>
            @endif
        </div>

        <div class="space-y-2.5">
            @forelse($activeSessions as $session)
                <div class="p-3.5 rounded-xl border border-[#E6E6E6] bg-[#F7F7F6] flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-white border border-[#E6E6E6] flex items-center justify-center text-[#111111] shrink-0">
                            @if(str_contains($session->device, 'Phone') || str_contains($session->device, 'Android') || str_contains($session->device, 'iPhone'))
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                            @else
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            @endif
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-[#111111]">{{ $session->device }}</span>
                                @if($session->is_current)
                                    <span class="badge-paid text-[10px] px-2 py-0.2 font-mono font-bold">This Device</span>
                                @endif
                            </div>
                            <span class="text-[11px] text-[#666666] font-mono">
                                IP: {{ $session->ip_address }} &bull; Active: {{ $session->last_active }} ({{ $session->last_active_at }})
                            </span>
                        </div>
                    </div>

                    @if(!$session->is_current)
                        <form action="{{ route('profile.sessions.destroy', $session->id) }}" method="POST" data-confirm="Terminate this remote session immediately?">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs font-semibold text-[#DC2626] hover:underline">
                                Revoke Session
                            </button>
                        </form>
                    @endif
                </div>
            @empty
                <p class="text-xs text-[#929292]">No active database session records found.</p>
            @endforelse
        </div>
    </div>

    <!-- Section 4: Security & Login Activity History -->
    <div class="card-surface p-5 sm:p-6 shadow-2xs space-y-4">
        <div class="border-b border-[#E6E6E6] pb-3 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-[#111111]"></span>
                <h3 class="text-xs font-bold text-[#111111] uppercase tracking-wider">Recent Security & Login History</h3>
            </div>
            <span class="text-[11px] text-[#666666] font-mono">Last 10 Account Events</span>
        </div>

        <div class="space-y-2">
            @forelse($securityLogs as $log)
                <div class="p-3 bg-[#F7F7F6] rounded-lg border border-[#E6E6E6] flex items-center justify-between text-xs">
                    <div class="space-y-0.5">
                        <span class="font-bold text-[#111111] font-mono block">{{ $log->event }}</span>
                        <span class="text-[11px] text-[#666666]">{{ $log->description }}</span>
                    </div>
                    <div class="text-right text-[11px] font-mono text-[#929292]">
                        <span>{{ $log->created_at->diffForHumans() }}</span>
                        @if($log->ip_address)
                            <span class="block text-[10px]">{{ $log->ip_address }}</span>
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-xs text-[#929292]">No recent security logs recorded for this account.</p>
            @endforelse
        </div>
    </div>
</div>

<script>
async function promptLogoutOthers() {
    const pwd = prompt('Enter your current password to sign out all other devices:');
    if (!pwd) return;
    document.getElementById('logout_others_pwd_hidden').value = pwd;
    document.getElementById('logout_others_pwd_hidden').form.submit();
}
</script>
@endsection

