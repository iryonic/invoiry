@php
    $forms = \App\Services\DynamicFormConfig::getForms();
    $currentFormConfig = $formConfig ?? \App\Services\DynamicFormConfig::getDefaultConfig();
@endphp

<div class="space-y-4">
    <!-- Header & Subtitle -->
    <div class="card-surface p-3.5 sm:p-4 shadow-2xs flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-[#F7F7F6] border border-[#E6E6E6] rounded-xl">
        <div>
            <h3 class="text-xs sm:text-sm font-bold text-[#111111]">Dynamic Form Field Customization</h3>
            <p class="text-[11px] text-[#666666]">Enable or hide specific input fields across transaction forms for this business</p>
        </div>
        <div class="flex items-center gap-1.5 px-2.5 py-1 rounded bg-[#111111] text-[#B7FF00] text-[10px] font-mono font-bold">
            <span>DEFAULT: ALL ENABLED</span>
        </div>
    </div>

    <!-- Live Search & Filter Bar -->
    <div class="relative">
        <input type="text"
               id="filter_form_fields_input"
               oninput="filterFormFields(this.value)"
               placeholder="Search form fields across all forms (e.g. gst, imei, discount, notes)..."
               class="w-full px-3 py-2 bg-[#F7F7F6] border border-[#E6E6E6] rounded-lg text-xs sm:text-sm focus:bg-white focus:outline-none focus:ring-1 focus:ring-black">
        <svg class="w-4 h-4 text-[#929292] absolute right-3 top-2.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
    </div>

    <!-- Forms Tabs Navigation -->
    <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none border-b border-[#E6E6E6]">
        @foreach($forms as $formKey => $formMeta)
            <button type="button"
                    onclick="switchFormTab('{{ $formKey }}')"
                    id="tab_btn_{{ $formKey }}"
                    class="form-tab-btn px-3.5 py-2 rounded-lg text-xs font-bold transition whitespace-nowrap flex-shrink-0 {{ $loop->first ? 'bg-[#111111] text-[#B7FF00]' : 'bg-white text-[#666666] hover:text-[#111111] border border-[#E6E6E6]' }}">
                {{ $formMeta['name'] }}
            </button>
        @endforeach
    </div>

    <!-- Forms Content Sections -->
    <div class="space-y-4">
        @foreach($forms as $formKey => $formMeta)
            <div id="form_section_{{ $formKey }}" class="form-section-pane {{ $loop->first ? '' : 'hidden' }} space-y-3">
                <div class="flex items-center justify-between px-1">
                    <div>
                        <h4 class="text-xs font-bold text-[#111111] uppercase tracking-wider">{{ $formMeta['name'] }}</h4>
                        <p class="text-[11px] text-[#666666]">{{ $formMeta['description'] }}</p>
                    </div>
                </div>

                <div class="card-surface p-4 sm:p-5 shadow-2xs border border-[#E6E6E6] rounded-xl bg-white divide-y divide-[#E6E6E6]">
                    @foreach($formMeta['fields'] as $fieldKey => $f)
                        @php
                            $isSystem = $f['system'];
                            $savedVis = $currentFormConfig[$formKey][$fieldKey]['visible'] ?? $f['default_visible'];
                            $savedReq = $currentFormConfig[$formKey][$fieldKey]['required'] ?? $f['default_required'];
                        @endphp
                        <div class="form-field-row py-3.5 first:pt-0 last:pb-0 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex-1 min-w-0 pr-2">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="text-xs font-bold text-[#111111]">{{ $f['label'] }}</span>
                                    @if($isSystem)
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-mono font-bold bg-[#111111] text-[#B7FF00]">
                                            SYSTEM CRITICAL
                                        </span>
                                    @else
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-mono font-bold bg-[#F2F4F7] text-[#475569]">
                                            CONFIGURABLE
                                        </span>
                                    @endif
                                </div>
                                @if(!empty($f['hint']))
                                    <p class="text-[11px] text-[#666666] mt-0.5">{{ $f['hint'] }}</p>
                                @endif
                            </div>

                            <div class="flex items-center gap-2 sm:gap-3 flex-wrap sm:flex-nowrap w-full sm:w-auto justify-between sm:justify-end mt-2 sm:mt-0 pt-2 sm:pt-0 border-t sm:border-t-0 border-[#F0F0F0]">
                                <!-- Visibility Toggle -->
                                <div class="flex-1 sm:flex-initial sm:w-32 flex items-center justify-between bg-[#F7F7F6] px-2.5 py-1.5 rounded-lg border border-[#E6E6E6]">
                                    <span class="text-[11px] font-bold text-[#344054]">Visible</span>
                                    <x-toggle-switch
                                        name="form_fields[{{ $formKey }}][{{ $fieldKey }}][visible]"
                                        id="ff_vis_{{ $formKey }}_{{ $fieldKey }}"
                                        value="1"
                                        :checked="$isSystem ? true : (bool)$savedVis"
                                        :disabled="$isSystem"
                                        size="sm"
                                    />
                                    @if($isSystem)
                                        <!-- Hidden input to guarantee submit value -->
                                        <input type="hidden" name="form_fields[{{ $formKey }}][{{ $fieldKey }}][visible]" value="1">
                                    @endif
                                </div>

                                <!-- Required Toggle -->
                                <div class="flex-1 sm:flex-initial sm:w-32 flex items-center justify-between bg-[#F7F7F6] px-2.5 py-1.5 rounded-lg border border-[#E6E6E6]">
                                    <span class="text-[11px] font-bold text-[#344054]">Required</span>
                                    <x-toggle-switch
                                        name="form_fields[{{ $formKey }}][{{ $fieldKey }}][required]"
                                        id="ff_req_{{ $formKey }}_{{ $fieldKey }}"
                                        value="1"
                                        :checked="$isSystem ? (bool)$f['default_required'] : (bool)$savedReq"
                                        :disabled="$isSystem && $f['default_required']"
                                        size="sm"
                                    />
                                    @if($isSystem && $f['default_required'])
                                        <input type="hidden" name="form_fields[{{ $formKey }}][{{ $fieldKey }}][required]" value="1">
                                    @endif
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
    function switchFormTab(formKey) {
        document.querySelectorAll('.form-section-pane').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('.form-tab-btn').forEach(btn => {
            btn.classList.remove('bg-[#111111]', 'text-[#B7FF00]');
            btn.classList.add('bg-white', 'text-[#666666]', 'border', 'border-[#E6E6E6]');
        });

        const activePane = document.getElementById('form_section_' + formKey);
        const activeBtn = document.getElementById('tab_btn_' + formKey);
        if (activePane) activePane.classList.remove('hidden');
        if (activeBtn) {
            activeBtn.classList.remove('bg-white', 'text-[#666666]', 'border', 'border-[#E6E6E6]');
            activeBtn.classList.add('bg-[#111111]', 'text-[#B7FF00]');
        }
    }

    function filterFormFields(query) {
        const q = (query || '').toLowerCase().trim();
        const rows = document.querySelectorAll('.form-field-row');
        rows.forEach(r => {
            if (!q) {
                r.style.display = '';
                return;
            }
            const text = r.textContent.toLowerCase();
            r.style.display = text.includes(q) ? '' : 'none';
        });

        // If filtering, also show sections that contain matches
        if (q) {
            document.querySelectorAll('.form-section-pane').forEach(pane => {
                const visibleRows = pane.querySelectorAll('.form-field-row:not([style*="display: none"])');
                if (visibleRows.length > 0) {
                    pane.classList.remove('hidden');
                }
            });
        }
    }
</script>

