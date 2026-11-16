@php
    $modules = \App\Services\RbacPermissionManager::getModules();
    $currentPermissions = $permissions ?? \App\Services\RbacPermissionManager::getDefaultPermissions();
    
    $totalCapabilities = 0;
    $enabledCapabilities = 0;
    foreach ($modules as $mKey => $m) {
        foreach ($m['actions'] as $aKey => $aLabel) {
            $totalCapabilities++;
            if (!empty($currentPermissions[$mKey][$aKey])) {
                $enabledCapabilities++;
            }
        }
    }
@endphp

<div class="space-y-4">
    <!-- Controls Toolbar: Status, Enable All, Disable All & Category Filter -->
    <div class="card-surface p-4 sm:p-5 shadow-2xs space-y-3.5 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <div class="flex items-center gap-2">
                    <h3 class="text-xs sm:text-sm font-bold text-[#111111]">Granular Role & Feature Permissions</h3>
                    <span id="perm_summary_badge" class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-[#111111] text-[#B7FF00]">
                        <span id="perm_enabled_count">{{ $enabledCapabilities }}</span> / {{ $totalCapabilities }} CAPABILITIES ACTIVE
                    </span>
                </div>
                <p class="text-[11px] text-[#666666] mt-0.5">Control access to individual routes, billing operations, hardware inventory, and the communications suite</p>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                <button type="button" onclick="setAllPermissions(true)" class="btn-secondary text-xs min-h-[34px] px-3 py-1 font-bold">
                    <svg class="w-3.5 h-3.5 text-[#079455]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                    <span>Enable All</span>
                </button>
                <button type="button" onclick="setAllPermissions(false)" class="btn-secondary text-xs min-h-[34px] px-3 py-1 font-bold text-[#DC2626] hover:text-[#B91C1C]">
                    <svg class="w-3.5 h-3.5 text-[#DC2626]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    <span>Disable All</span>
                </button>
            </div>
        </div>

        <!-- Filter Chips & Search Bar -->
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 pt-1 border-t border-[#EDEDEC]">
            <!-- Quick Category Filter Chips -->
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 sm:pb-0 scrollbar-none flex-shrink-0">
                <button type="button" onclick="filterByCategory('all')" id="cat_btn_all" class="perm-cat-btn px-2.5 py-1 rounded-md text-[11px] font-bold transition bg-[#111111] text-[#B7FF00]">
                    All ({{ count($modules) }})
                </button>
                <button type="button" onclick="filterByCategory('core')" id="cat_btn_core" class="perm-cat-btn px-2.5 py-1 rounded-md text-[11px] font-bold transition bg-white text-[#666666] border border-[#E6E6E6] hover:text-[#111111]">
                    Core Billing
                </button>
                <button type="button" onclick="filterByCategory('inventory')" id="cat_btn_inventory" class="perm-cat-btn px-2.5 py-1 rounded-md text-[11px] font-bold transition bg-white text-[#666666] border border-[#E6E6E6] hover:text-[#111111]">
                    Inventory & Stock
                </button>
                <button type="button" onclick="filterByCategory('repair')" id="cat_btn_repair" class="perm-cat-btn px-2.5 py-1 rounded-md text-[11px] font-bold transition bg-white text-[#666666] border border-[#E6E6E6] hover:text-[#111111] flex items-center gap-1">
                    <span>Repair &amp; Service</span>
                    <span class="w-1.5 h-1.5 rounded-full bg-[#D97706]"></span>
                </button>
                <button type="button" onclick="filterByCategory('ledger')" id="cat_btn_ledger" class="perm-cat-btn px-2.5 py-1 rounded-md text-[11px] font-bold transition bg-white text-[#666666] border border-[#E6E6E6] hover:text-[#111111]">
                    Khata
                </button>
                <button type="button" onclick="filterByCategory('communications')" id="cat_btn_communications" class="perm-cat-btn px-2.5 py-1 rounded-md text-[11px] font-bold transition bg-white text-[#666666] border border-[#E6E6E6] hover:text-[#111111] flex items-center gap-1">
                    <span>Mailing</span>
                    <span class="w-1.5 h-1.5 rounded-full bg-[#10B981]"></span>
                </button>
                <button type="button" onclick="filterByCategory('admin')" id="cat_btn_admin" class="perm-cat-btn px-2.5 py-1 rounded-md text-[11px] font-bold transition bg-white text-[#666666] border border-[#E6E6E6] hover:text-[#111111]">
                    Store Admin
                </button>
            </div>

            <!-- Live Search Bar -->
            <div class="relative flex-1 min-w-[200px]">
                <input type="text"
                       id="filter_permissions_input"
                       oninput="filterPermissionsCards(this.value)"
                       placeholder="Search permissions (e.g. mail, imei, stock, discount, payments)..."
                       class="w-full pl-3 pr-8 py-1.5 bg-white border border-[#E6E6E6] rounded-lg text-xs focus:outline-none focus:ring-1 focus:ring-black">
                <svg class="w-3.5 h-3.5 text-[#929292] absolute right-2.5 top-2.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
        </div>
    </div>

    <!-- Modules Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4" id="permissions_module_grid">
        @foreach($modules as $modKey => $mod)
            @php
                $isNew = $mod['is_new'] ?? ($modKey === 'mail');
                $category = $mod['category'] ?? 'core';
                $modActionsCount = count($mod['actions']);
                $modEnabledCount = 0;
                foreach ($mod['actions'] as $actKey => $actLabel) {
                    if (!empty($currentPermissions[$modKey][$actKey])) {
                        $modEnabledCount++;
                    }
                }
            @endphp
            <div class="card-surface p-4 sm:p-5 shadow-2xs space-y-3.5 border {{ $isNew ? 'border-[#B7FF00] bg-white ring-1 ring-[#B7FF00]/40' : 'border-[#E6E6E6] bg-white' }} rounded-xl transition hover:shadow-xs" 
                 id="mod_card_{{ $modKey }}"
                 data-category="{{ $category }}">
                
                <!-- Module Header -->
                <div class="flex items-start justify-between border-b border-[#E6E6E6] pb-3 gap-2">
                    <div class="flex items-start gap-2.5 min-w-0">
                        <div class="w-9 h-9 rounded-xl {{ $isNew ? 'bg-[#050505] text-[#B7FF00] ring-2 ring-[#B7FF00]/50' : 'bg-[#050505] text-[#B7FF00]' }} flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $mod['icon'] }}"></path></svg>
                        </div>
                        <div class="min-w-0 space-y-0.5">
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <h4 class="text-xs sm:text-sm font-black text-[#111111] tracking-tight">{{ $mod['name'] }}</h4>
                                @if($isNew)
                                    <span class="px-1.5 py-0.2 rounded text-[9px] font-mono font-bold bg-[#111111] text-[#B7FF00] border border-[#333]">
                                        NEW MODULE
                                    </span>
                                @endif
                            </div>
                            <p class="text-[11px] text-[#666666] leading-tight line-clamp-2">{{ $mod['description'] }}</p>
                        </div>
                    </div>

                    <div class="flex flex-col items-end gap-1 flex-shrink-0">
                        <span id="mod_count_{{ $modKey }}" class="text-[10px] font-mono font-bold text-[#079455] bg-[#ECFDF3] px-2 py-0.5 rounded border border-[#A6F4C5]">
                            <span class="active-val">{{ $modEnabledCount }}</span>/{{ $modActionsCount }} Active
                        </span>
                        <button type="button" onclick="toggleModuleAll('{{ $modKey }}')" class="text-[10px] font-bold text-[#111111] hover:text-[#079455] bg-[#F7F7F6] px-2 py-0.5 rounded border border-[#E6E6E6] transition whitespace-nowrap">
                            Toggle All
                        </button>
                    </div>
                </div>

                <!-- Granular Actions List -->
                <div class="space-y-2 pt-1">
                    @foreach($mod['actions'] as $actKey => $actLabel)
                        @php
                            $isEnabled = (bool) ($currentPermissions[$modKey][$actKey] ?? true);
                            $isActionNew = ($modKey === 'invoices' && $actKey === 'email') || ($modKey === 'items' && $actKey === 'adjust_stock') || ($modKey === 'mail');
                        @endphp
                        <div class="p-2.5 rounded-lg {{ $isEnabled ? 'bg-[#F7F7F6]' : 'bg-[#FAFAFA] opacity-75' }} border border-[#EDEDEC] hover:border-[#D5D5D5] transition action-row-item">
                            <div class="flex items-center justify-between gap-2">
                                <div class="flex-1 min-w-0 pr-2">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <span class="text-xs font-bold text-[#111111]">{{ $actLabel }}</span>
                                        @if($modKey === 'invoices' && $actKey === 'email')
                                            <span class="px-1.5 py-0.2 rounded text-[9px] font-mono font-bold bg-[#ECFDF3] text-[#027A48] border border-[#A6F4C5]">
                                                DISPATCH
                                            </span>
                                        @elseif($modKey === 'items' && $actKey === 'adjust_stock')
                                            <span class="px-1.5 py-0.2 rounded text-[9px] font-mono font-bold bg-[#EFF8FF] text-[#175CD3] border border-[#B2DDFF]">
                                                AUDIT
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-[10px] font-mono text-[#666666] mt-0.5">Key: {{ $modKey }}.{{ $actKey }}</p>
                                </div>
                                <div class="flex-shrink-0">
                                    <x-toggle-switch
                                        name="permissions[{{ $modKey }}][{{ $actKey }}]"
                                        id="perm_{{ $modKey }}_{{ $actKey }}"
                                        value="1"
                                        :checked="$isEnabled"
                                        size="sm"
                                    />
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>

<script>
    function updateCounters() {
        const allCheckboxes = document.querySelectorAll('input[name^="permissions["]');
        let enabled = 0;
        allCheckboxes.forEach(cb => {
            if (cb.checked) enabled++;
        });

        const countEl = document.getElementById('perm_enabled_count');
        if (countEl) countEl.textContent = enabled;

        // Update module individual counters
        @foreach($modules as $modKey => $mod)
            (function() {
                const modBoxes = document.querySelectorAll('input[name^="permissions[{{ $modKey }}]"]');
                let modActive = 0;
                modBoxes.forEach(cb => {
                    if (cb.checked) modActive++;
                });
                const modCountEl = document.querySelector('#mod_count_{{ $modKey }} .active-val');
                if (modCountEl) modCountEl.textContent = modActive;
            })();
        @endforeach
    }

    // Attach listener to all checkboxes
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('input[name^="permissions["]').forEach(cb => {
            cb.addEventListener('change', updateCounters);
        });
        updateCounters();
    });

    function setAllPermissions(enable) {
        document.querySelectorAll('input[name^="permissions["]').forEach(cb => {
            cb.checked = enable;
        });
        updateCounters();
    }

    function toggleModuleAll(moduleKey) {
        const checkboxes = document.querySelectorAll(`input[name^="permissions[${moduleKey}]"]`);
        if (checkboxes.length === 0) return;
        const allChecked = Array.from(checkboxes).every(cb => cb.checked);
        checkboxes.forEach(cb => {
            cb.checked = !allChecked;
        });
        updateCounters();
    }

    let currentCategory = 'all';

    function filterByCategory(category) {
        currentCategory = category;
        document.querySelectorAll('.perm-cat-btn').forEach(btn => {
            btn.classList.remove('bg-[#111111]', 'text-[#B7FF00]');
            btn.classList.add('bg-white', 'text-[#666666]', 'border', 'border-[#E6E6E6]');
        });

        const activeBtn = document.getElementById('cat_btn_' + category);
        if (activeBtn) {
            activeBtn.classList.remove('bg-white', 'text-[#666666]', 'border', 'border-[#E6E6E6]');
            activeBtn.classList.add('bg-[#111111]', 'text-[#B7FF00]');
        }

        const query = (document.getElementById('filter_permissions_input')?.value || '').toLowerCase().trim();
        applyFilters(query, category);
    }

    function filterPermissionsCards(query) {
        applyFilters(query, currentCategory);
    }

    function applyFilters(query, category) {
        const q = (query || '').toLowerCase().trim();
        const cards = document.querySelectorAll('[id^="mod_card_"]');
        
        cards.forEach(card => {
            const cardCat = card.getAttribute('data-category');
            const matchesCat = (category === 'all' || cardCat === category);
            
            if (!matchesCat) {
                card.style.display = 'none';
                return;
            }

            if (!q) {
                card.style.display = '';
                return;
            }

            const text = card.textContent.toLowerCase();
            card.style.display = text.includes(q) ? '' : 'none';
        });
    }
</script>
