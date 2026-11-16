@extends('layouts.app')

@section('title', 'Repair Jobs & Workshop · ' . ($tenant->name ?? 'Mobile Repair'))
@section('page-title', 'Repair Jobs & Workshop')
@section('page-subtitle', 'Manage phone service orders, diagnosis, technician assignments, and job sheet status')

@section('content')
<div class="space-y-6 w-full" style="overflow-x: clip;">

    <!-- Top Action & Title Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-[11px] font-bold bg-[#E0F2FE] text-[#0369A1] mb-1 border border-[#BAE6FD]">
                <span class="w-1.5 h-1.5 rounded-full bg-[#0284C7]"></span>
                <span>Workshop &amp; Service Desk</span>
            </div>
            <h2 class="text-xl sm:text-2xl font-bold text-[#111111] tracking-tight">Repair Service Orders</h2>
            <p class="text-xs sm:text-sm text-[#666666] mt-0.5">Track hardware repairs, screen replacements, motherboard fixes, and status updates</p>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('spare-parts.index') }}" class="btn-secondary px-4 py-2.5 rounded-lg text-xs sm:text-sm font-semibold flex items-center gap-2 whitespace-nowrap min-h-[44px]">
                <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                <span>Spare Parts Catalogue</span>
            </a>
            <a href="{{ route('repairs.create') }}" class="btn-lime px-4 py-2.5 rounded-lg text-xs sm:text-sm font-bold flex items-center gap-2 shadow-xs whitespace-nowrap min-h-[44px]">
                <svg class="w-4 h-4 text-[#101010]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                <span>New Repair Request</span>
            </a>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
        <a href="{{ route('repairs.index', ['status' => 'all']) }}" class="card-surface p-4 flex flex-col justify-between shadow-2xs transition {{ $status === 'all' ? 'border-[#111111] ring-1 ring-[#111111]' : '' }}">
            <span class="text-xs font-bold text-[#111111] uppercase tracking-wider">Total Registered</span>
            <div class="text-xl sm:text-2xl font-bold text-[#111111] mt-1">{{ $stats['total_jobs'] }}</div>
            <span class="text-[11px] text-[#666666] font-mono mt-0.5">Lifetime service orders</span>
        </a>

        <a href="{{ route('repairs.index', ['status' => 'in_progress']) }}" class="card-surface p-4 flex flex-col justify-between shadow-2xs transition {{ $status === 'in_progress' ? 'border-[#2563EB] ring-1 ring-[#2563EB]' : '' }}">
            <span class="text-xs font-bold text-[#2563EB] uppercase tracking-wider">Active in Bench</span>
            <div class="text-xl sm:text-2xl font-bold text-[#2563EB] mt-1">{{ $stats['active_jobs'] }}</div>
            <span class="text-[11px] text-[#666666] font-mono mt-0.5">{{ $stats['in_progress'] }} currently in work</span>
        </a>

        <a href="{{ route('repairs.index', ['status' => 'completed']) }}" class="card-surface p-4 flex flex-col justify-between shadow-2xs transition {{ $status === 'completed' ? 'border-[#079455] ring-1 ring-[#079455]' : '' }}">
            <span class="text-xs font-bold text-[#079455] uppercase tracking-wider">Ready for Pickup</span>
            <div class="text-xl sm:text-2xl font-bold text-[#079455] mt-1">{{ $stats['completed'] }}</div>
            <span class="text-[11px] text-[#079455] font-mono mt-0.5">Waiting for delivery</span>
        </a>

        <a href="{{ route('repairs.index', ['status' => 'delivered']) }}" class="card-surface p-4 flex flex-col justify-between shadow-2xs transition {{ $status === 'delivered' ? 'border-[#666666] ring-1 ring-[#666666]' : '' }}">
            <span class="text-xs font-bold text-[#666666] uppercase tracking-wider">Delivered &amp; Closed</span>
            <div class="text-xl sm:text-2xl font-bold text-[#111111] mt-1">{{ $stats['delivered'] }}</div>
            <span class="text-[11px] text-[#666666] font-mono mt-0.5">Successfully handed over</span>
        </a>

        <div class="card-surface p-4 flex flex-col justify-between shadow-2xs">
            <span class="text-xs font-bold text-[#DC2626] uppercase tracking-wider">Pending Due Amount</span>
            <div class="text-xl sm:text-2xl font-bold text-[#DC2626] mt-1">₹{{ number_format($stats['pending_due'], 2) }}</div>
            <span class="text-[11px] text-[#666666] font-mono mt-0.5">Unsettled repair bills</span>
        </div>
    </div>

    <!-- Filter Bar & Search -->
    <div class="card-surface p-4">
        <form method="GET" action="{{ route('repairs.index') }}" class="flex flex-col md:flex-row items-stretch md:items-center gap-3">
            <!-- Search Field -->
            <div class="relative flex-1">
                <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-[#888888]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search Job #, Customer, Phone, Model, IMEI or Serial..."
                    class="w-full pl-10 pr-4 py-2.5 bg-[#FAFAFA] border border-[#E5E5E5] rounded-lg text-sm text-[#111111] focus:bg-white focus:border-[#111111] focus:outline-none transition min-h-[44px]"
                >
            </div>

            <!-- Status Filter -->
            <div class="w-full md:w-52">
                <select name="status" onchange="this.form.submit()" class="w-full py-2.5 px-3 bg-[#FAFAFA] border border-[#E5E5E5] rounded-lg text-sm text-[#111111] focus:bg-white focus:border-[#111111] focus:outline-none transition min-h-[44px]">
                    <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All Statuses</option>
                    @foreach($statuses as $key => $st)
                        <option value="{{ $key }}" {{ $status === $key ? 'selected' : '' }}>
                            {{ $st['label'] }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Technician Filter -->
            <div class="w-full md:w-48">
                <select name="technician_id" onchange="this.form.submit()" class="w-full py-2.5 px-3 bg-[#FAFAFA] border border-[#E5E5E5] rounded-lg text-sm text-[#111111] focus:bg-white focus:border-[#111111] focus:outline-none transition min-h-[44px]">
                    <option value="all">All Technicians</option>
                    @foreach($technicians as $tech)
                        <option value="{{ $tech->id }}" {{ request('technician_id') == $tech->id ? 'selected' : '' }}>
                            {{ $tech->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn-secondary px-4 py-2.5 text-xs sm:text-sm font-semibold rounded-lg flex items-center justify-center gap-2 whitespace-nowrap min-h-[44px]">
                <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                <span>Filter</span>
            </button>

            @if(request()->hasAny(['search', 'status', 'technician_id']) && (request('status') !== 'all' || request('search') || request('technician_id') !== 'all'))
                <a href="{{ route('repairs.index') }}" class="p-2.5 text-xs text-[#DC2626] hover:bg-red-50 rounded-lg flex items-center justify-center whitespace-nowrap min-h-[44px]" title="Reset filters">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Repair Jobs Table -->
    <div class="card-surface overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs sm:text-sm">
                <thead>
                    <tr class="border-b border-[#EBEBEB] bg-[#FAFAFA] text-[#666666] font-semibold">
                        <th class="py-3 px-4">Job # &amp; Date</th>
                        <th class="py-3 px-4">Customer</th>
                        <th class="py-3 px-4">Device &amp; Details</th>
                        <th class="py-3 px-4">Reported Problem</th>
                        <th class="py-3 px-4">Technician</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-right">Cost &amp; Due</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#EBEBEB] text-[#111111]">
                    @forelse($jobs as $job)
                        @php
                            $stInfo = $job->status_info;
                            $due = $job->due_balance;
                        @endphp
                        <tr class="hover:bg-[#F9F9F8] transition-colors group">
                            <!-- Job # & Priority -->
                            <td class="py-3.5 px-4 align-top">
                                <a href="{{ route('repairs.show', $job) }}" class="font-mono font-bold text-[#111111] hover:underline flex items-center gap-1.5">
                                    <span>#{{ $job->job_number }}</span>
                                </a>
                                <div class="text-[11px] text-[#888888] mt-0.5">
                                    {{ $job->created_at->format('d M Y, h:i A') }}
                                </div>
                                <div class="mt-1">
                                    @if($job->priority === 'urgent')
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-red-100 text-red-800">URGENT</span>
                                    @elseif($job->priority === 'high')
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800">HIGH</span>
                                    @endif
                                </div>
                            </td>

                            <!-- Customer Info -->
                            <td class="py-3.5 px-4 align-top">
                                @if($job->customer)
                                    <a href="{{ route('customers.show', $job->customer) }}" class="font-semibold text-[#111111] hover:underline block">
                                        {{ $job->customer->name }}
                                    </a>
                                    <div class="text-[11px] text-[#666666] font-mono mt-0.5">
                                        {{ $job->customer->phone }}
                                    </div>
                                @else
                                    <span class="text-[#888888] italic">Walk-in Customer</span>
                                @endif
                            </td>

                            <!-- Device & Identifiers -->
                            <td class="py-3.5 px-4 align-top">
                                <div class="font-bold text-[#111111]">
                                    {{ $job->device_brand }} {{ $job->device_model }}
                                </div>
                                <div class="text-[11px] text-[#666666] space-y-0.5 mt-0.5">
                                    @if($job->device_color)
                                        <div><span class="text-[#888888]">Color:</span> {{ $job->device_color }}</div>
                                    @endif
                                    @if($job->imei)
                                        <div class="font-mono"><span class="text-[#888888]">IMEI:</span> {{ $job->imei }}</div>
                                    @endif
                                    @if($job->serial_number)
                                        <div class="font-mono"><span class="text-[#888888]">S/N:</span> {{ $job->serial_number }}</div>
                                    @endif
                                </div>
                            </td>

                            <!-- Reported Problem -->
                            <td class="py-3.5 px-4 align-top max-w-[200px]">
                                <p class="text-xs text-[#333333] line-clamp-2 leading-relaxed">
                                    {{ $job->reported_problem }}
                                </p>
                                @if($job->diagnosis_notes)
                                    <p class="text-[11px] text-[#888888] line-clamp-1 italic mt-1">
                                        Diag: {{ $job->diagnosis_notes }}
                                    </p>
                                @endif
                            </td>

                            <!-- Technician -->
                            <td class="py-3.5 px-4 align-top">
                                @if($job->technician)
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-5 h-5 rounded-full bg-[#111111] text-white flex items-center justify-center text-[10px] font-bold">
                                            {{ strtoupper(substr($job->technician->name, 0, 1)) }}
                                        </span>
                                        <span class="font-medium text-xs text-[#111111]">{{ $job->technician->name }}</span>
                                    </div>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] bg-gray-100 text-gray-600 font-medium">
                                        Unassigned
                                    </span>
                                @endif
                                @if($job->expected_completion_date)
                                    <div class="text-[10px] text-[#888888] mt-1">
                                        Due: {{ \Carbon\Carbon::parse($job->expected_completion_date)->format('d M') }}
                                    </div>
                                @endif
                            </td>

                            <!-- Status Badge -->
                            <td class="py-3.5 px-4 align-top">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold {{ $stInfo['color'] ?? 'bg-gray-100 text-gray-800' }}">
                                    <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                    <span>{{ $stInfo['label'] ?? ucfirst($job->status) }}</span>
                                </span>
                            </td>

                            <!-- Cost & Balance Due -->
                            <td class="py-3.5 px-4 align-top text-right font-mono">
                                <div class="font-bold text-[#111111]">
                                    ₹{{ number_format($job->total_amount, 2) }}
                                </div>
                                @if($due > 0)
                                    <div class="text-[11px] text-[#DC2626] font-semibold mt-0.5">
                                        Due: ₹{{ number_format($due, 2) }}
                                    </div>
                                @else
                                    <div class="text-[11px] text-[#079455] font-semibold mt-0.5">
                                        Paid Full
                                    </div>
                                @endif
                                <div class="text-[10px] text-[#888888]">
                                    Paid: ₹{{ number_format($job->amount_paid, 2) }}
                                </div>
                            </td>

                            <!-- Action Buttons -->
                            <td class="py-3.5 px-4 align-top text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('repairs.show', $job) }}" class="p-1.5 rounded-md text-[#111111] hover:bg-neutral-100 transition" title="Open Job Workshop Details">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </a>

                                    <a href="{{ route('repairs.job-sheet', $job) }}" target="_blank" class="p-1.5 rounded-md text-slate-600 hover:bg-neutral-100 transition" title="Print Job Sheet">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                    </a>

                                    <a href="{{ route('repairs.edit', $job) }}" class="p-1.5 rounded-md text-[#666666] hover:bg-neutral-100 transition" title="Edit Repair Info">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 px-4 text-center">
                                <div class="max-w-md mx-auto space-y-3">
                                    <div class="w-12 h-12 rounded-full bg-[#E0F2FE] text-[#0284C7] flex items-center justify-center mx-auto">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    </div>
                                    <h3 class="text-base font-bold text-[#111111]">No repair jobs found</h3>
                                    <p class="text-xs sm:text-sm text-[#666666]">
                                        @if(request()->hasAny(['search', 'status', 'technician_id']))
                                            No repair orders match your filter criteria. Try adjusting or clearing filters.
                                        @else
                                            No repair jobs have been checked in yet. Register customer intake to track repairs, spare parts consumption, and diagnostic charges.
                                        @endif
                                    </p>
                                    <div class="pt-2">
                                        <a href="{{ route('repairs.create') }}" class="btn-lime px-4 py-2.5 rounded-lg text-xs sm:text-sm font-bold inline-flex items-center gap-2">
                                            <svg class="w-4 h-4 text-[#101010]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                                            <span>Check In First Repair Job</span>
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($jobs->hasPages())
            <div class="p-4 border-t border-[#EBEBEB]">
                {{ $jobs->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

