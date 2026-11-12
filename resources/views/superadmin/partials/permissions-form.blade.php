@php
    $modules = \App\Services\RbacPermissionManager::getModules();
    $currentPermissions = $permissions ?? \App\Services\RbacPermissionManager::getDefaultPermissions();
@endphp

<div class="space-y-4">
    <!-- Controls Toolbar: Enable All, Disable All -->
    <div class="card-surface p-3.5 sm:p-4 shadow-2xs flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl">
        <div>
            <h3 class="text-xs sm:text-sm font-bold text-[#111111]">Granular Role & Feature Permissions</h3>
            <p class="text-[11px] text-[#666666]">Enable or restrict specific modules, actions, and page-level routes</p>
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

    <!-- Live Search & Filter Bar -->
    <div class="relative">
        <input type="text"
               id="filter_permissions_input"
               oninput="filterPermissionsCards(this.value)"
               placeholder="Search modules or permissions (e.g. invoices, reports, delete, create)..."
               class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
        <svg class="w-4 h-4 text-[#929292] absolute right-3 top-2.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
    </div>

    <!-- Modules Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @foreach($modules as $modKey => $mod)
            <div class="card-surface p-4 sm:p-5 shadow-2xs space-y-3.5 border border-[#E6E6E6] rounded-xl bg-white" id="mod_card_{{ $modKey }}">
                <!-- Module Header -->
                <div class="flex items-center justify-between border-b border-[#E6E6E6] pb-2.5">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-8 h-8 rounded-lg bg-[#050505] text-[#B7FF00] flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $mod['icon'] }}"></path></svg>
                        </div>
                        <div class="min-w-0">
                            <h4 class="text-xs sm:text-sm font-bold text-[#111111] truncate">{{ $mod['name'] }}</h4>
                            <p class="text-[10px] text-[#666666] truncate">{{ $mod['description'] }}</p>
                        </div>
                    </div>
                    <button type="button" onclick="toggleModuleAll('{{ $modKey }}')" class="text-[11px] font-bold text-[#111111] hover:text-[#079455] bg-[#F7F7F6] px-2 py-1 rounded-md border border-[#E6E6E6] transition whitespace-nowrap">
                        Toggle All
                    </button>
                </div>

                <!-- Granular Actions List -->
                <div class="space-y-2.5 pt-1">
                    @foreach($mod['actions'] as $actKey => $actLabel)
                        @php
                            $isEnabled = (bool) ($currentPermissions[$modKey][$actKey] ?? true);
                        @endphp
                        <div class="p-2.5 rounded-lg bg-[#F7F7F6] border border-[#EDEDEC] hover:border-[#D5D5D5] transition">
                            <x-toggle-switch
                                name="permissions[{{ $modKey }}][{{ $actKey }}]"
                                id="perm_{{ $modKey }}_{{ $actKey }}"
                                value="1"
                                :checked="$isEnabled"
                                :label="$actLabel"
                                :description="'Controls ' . strtolower($actLabel) . ' capability'"
                            />
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>

<script>
    function setAllPermissions(enable) {
        document.querySelectorAll('input[name^="permissions["]').forEach(cb => {
            cb.checked = enable;
        });
    }

    function toggleModuleAll(moduleKey) {
        const checkboxes = document.querySelectorAll(`input[name^="permissions[${moduleKey}]"]`);
        if (checkboxes.length === 0) return;
        const allChecked = Array.from(checkboxes).every(cb => cb.checked);
        checkboxes.forEach(cb => {
            cb.checked = !allChecked;
        });
    }

    function filterPermissionsCards(query) {
        const q = (query || '').toLowerCase().trim();
        const cards = document.querySelectorAll('[id^="mod_card_"]');
        cards.forEach(card => {
            if (!q) {
                card.style.display = '';
                return;
            }
            const text = card.textContent.toLowerCase();
            card.style.display = text.includes(q) ? '' : 'none';
        });
    }
</script>

