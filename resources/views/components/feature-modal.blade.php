@props(['id', 'title', 'fields' => [], 'submitLabel' => 'Simpan', 'action' => null, 'method' => 'POST'])

<div id="{{ $id }}" class="fixed inset-0 z-[1000] hidden items-center justify-center bg-slate-950/70 p-4 backdrop-blur-2xs" role="dialog" aria-modal="true" aria-labelledby="{{ $id }}-title">
    <div class="max-h-[92vh] w-full max-w-3xl overflow-y-auto rounded-2xl bg-white p-6 shadow-2xl">
        <div class="flex items-start justify-between gap-5 border-b border-slate-100 pb-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[.2em] text-amber-600">MotorisTrack</p>
                <h3 id="{{ $id }}-title" class="mt-1 text-xl font-bold text-slate-900">{{ $title }}</h3>
            </div>
            <button type="button" data-close-modal="{{ $id }}" class="rounded-lg p-1 text-2xl leading-none text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition" aria-label="Tutup modal">&times;</button>
        </div>

        <form class="feature-modal-form mt-6 space-y-5" data-modal-form="{{ $id }}" data-live-submit="{{ $action ? 'true' : 'false' }}" method="{{ strtoupper($method) === 'GET' ? 'GET' : 'POST' }}" action="{{ $action ?? '#' }}">
            @csrf
            @if (!in_array(strtoupper($method), ['GET', 'POST'])) 
                @method($method) 
            @endif

            <div class="grid gap-4 sm:grid-cols-2">
                @foreach ($fields as $field)
                    @php($type = $field['type'] ?? 'text')
                    @php($val = $field['value'] ?? '')
                    <div class="{{ ($field['wide'] ?? false) ? 'sm:col-span-2' : '' }}">
                        @if ($type === 'checkbox')
                            <label class="flex items-center gap-3 text-sm font-medium text-slate-700">
                                <input type="checkbox" name="{{ $field['name'] }}" value="1" {{ !empty($val) ? 'checked' : '' }} class="rounded border-slate-300 text-amber-500 focus:ring-amber-500">
                                {{ $field['label'] }}
                            </label>
                        @else
                            <label class="text-xs font-bold uppercase tracking-wider text-slate-700" for="{{ $id }}-{{ $field['name'] }}">
                                {{ $field['label'] }}
                                @if ($field['required'] ?? false)<span class="text-rose-500"> *</span>@endif
                            </label>

                            @if ($type === 'select')
                                <select id="{{ $id }}-{{ $field['name'] }}" name="{{ $field['name'] }}" class="mt-1.5 w-full rounded-lg border-slate-300 text-sm focus:border-amber-500 focus:ring-amber-500" @required($field['required'] ?? false)>
                                    <option value="">-- Pilih {{ strtolower($field['label']) }} --</option>
                                    @foreach ($field['options'] ?? [] as $optVal => $optLabel)
                                        @php($optionValue = is_array($optLabel) ? $optLabel['value'] : (is_string($optVal) && !is_numeric($optVal) ? $optVal : $optLabel))
                                        @php($optionText = is_array($optLabel) ? $optLabel['label'] : $optLabel)
                                        <option value="{{ $optionValue }}" {{ (string)$val === (string)$optionValue ? 'selected' : '' }}>
                                            {{ $optionText }}
                                        </option>
                                    @endforeach
                                </select>
                            @elseif ($type === 'textarea')
                                <textarea id="{{ $id }}-{{ $field['name'] }}" name="{{ $field['name'] }}" rows="3" class="mt-1.5 w-full rounded-lg border-slate-300 text-sm focus:border-amber-500 focus:ring-amber-500" @required($field['required'] ?? false)>{{ $val }}</textarea>
                            @else
                                <input id="{{ $id }}-{{ $field['name'] }}" name="{{ $field['name'] }}" type="{{ $type }}" value="{{ $val }}" class="mt-1.5 w-full rounded-lg border-slate-300 text-sm focus:border-amber-500 focus:ring-amber-500" @required($field['required'] ?? false) @if (isset($field['placeholder'])) placeholder="{{ $field['placeholder'] }}" @endif>
                            @endif
                        @endif
                    </div>
                @endforeach
            </div>

            <!-- Action buttons with SVG icons -->
            <div class="flex justify-end gap-3 border-t border-slate-100 pt-5">
                <button type="button" data-close-modal="{{ $id }}" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-2xs hover:bg-slate-50 transition">
                    <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    <span>Batal</span>
                </button>
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-slate-950 px-5 py-2.5 text-sm font-bold text-white shadow-md hover:bg-slate-800 transition">
                    <svg class="h-4 w-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>{{ $submitLabel }}</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    window.featureModalInit = window.featureModalInit || function (id) {
        const modal = document.getElementById(id);
        if (!modal) return;
        const close = () => modal.classList.replace('flex', 'hidden');
        document.querySelectorAll(`[data-open-modal="${id}"]`).forEach((button) => button.addEventListener('click', () => modal.classList.replace('hidden', 'flex')));
        modal.querySelectorAll(`[data-close-modal="${id}"]`).forEach((button) => button.addEventListener('click', close));
        modal.addEventListener('click', (event) => { if (event.target === modal) close(); });
        document.addEventListener('keydown', (event) => { if (event.key === 'Escape' && !modal.classList.contains('hidden')) close(); });
        modal.querySelector('form').addEventListener('submit', (event) => {
            if (!event.target.checkValidity()) { event.target.reportValidity(); return; }
            if (event.target.dataset.liveSubmit === 'true') return;
            event.preventDefault();
            close();
            window.Swal?.fire({ icon: 'success', title: 'Form diproses', text: 'Data telah disimpan di sistem.', timer: 1800, showConfirmButton: false });
        });
    };
    window.featureModalInit('{{ $id }}');
</script>
