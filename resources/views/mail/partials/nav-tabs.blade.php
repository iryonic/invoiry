<div class="flex items-center gap-1.5 overflow-x-auto pb-1 border-b border-[#E6E6E6] text-xs font-semibold select-none custom-scrollbar">
    <a href="{{ route('mail.dashboard') }}" 
       class="px-3.5 py-2 rounded-lg transition whitespace-nowrap flex items-center gap-2 {{ request()->routeIs('mail.dashboard') ? 'bg-[#111111] text-[#B7FF00] font-bold shadow-2xs' : 'text-[#666666] hover:text-[#111111] hover:bg-white' }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
        <span>Overview</span>
    </a>

    <a href="{{ route('mail.compose') }}" 
       class="px-3.5 py-2 rounded-lg transition whitespace-nowrap flex items-center gap-2 {{ request()->routeIs('mail.compose') ? 'bg-[#111111] text-[#B7FF00] font-bold shadow-2xs' : 'text-[#666666] hover:text-[#111111] hover:bg-white' }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        <span>Compose Email</span>
    </a>

    <a href="{{ route('mail.templates.index') }}" 
       class="px-3.5 py-2 rounded-lg transition whitespace-nowrap flex items-center gap-2 {{ request()->routeIs('mail.templates.*') ? 'bg-[#111111] text-[#B7FF00] font-bold shadow-2xs' : 'text-[#666666] hover:text-[#111111] hover:bg-white' }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"></path></svg>
        <span>Email Templates</span>
    </a>

    <a href="{{ route('mail.logs.index') }}" 
       class="px-3.5 py-2 rounded-lg transition whitespace-nowrap flex items-center gap-2 {{ request()->routeIs('mail.logs.*') ? 'bg-[#111111] text-[#B7FF00] font-bold shadow-2xs' : 'text-[#666666] hover:text-[#111111] hover:bg-white' }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
        <span>Delivery Logs</span>
    </a>

    <a href="{{ route('mail.scheduled.index') }}" 
       class="px-3.5 py-2 rounded-lg transition whitespace-nowrap flex items-center gap-2 {{ request()->routeIs('mail.scheduled.*') ? 'bg-[#111111] text-[#B7FF00] font-bold shadow-2xs' : 'text-[#666666] hover:text-[#111111] hover:bg-white' }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <span>Scheduled</span>
    </a>

    <a href="{{ route('mail.settings.index') }}" 
       class="px-3.5 py-2 rounded-lg transition whitespace-nowrap flex items-center gap-2 {{ request()->routeIs('mail.settings.*') ? 'bg-[#111111] text-[#B7FF00] font-bold shadow-2xs' : 'text-[#666666] hover:text-[#111111] hover:bg-white' }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
        <span>SMTP Settings</span>
    </a>

    <a href="{{ route('mail.automations.index') }}" 
       class="px-3.5 py-2 rounded-lg transition whitespace-nowrap flex items-center gap-2 {{ request()->routeIs('mail.automations.*') ? 'bg-[#111111] text-[#B7FF00] font-bold shadow-2xs' : 'text-[#666666] hover:text-[#111111] hover:bg-white' }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
        <span>Email Automation</span>
    </a>
</div>

