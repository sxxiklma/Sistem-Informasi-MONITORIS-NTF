<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[.22em] text-blue-600">Lapangan & Operasional</p>
                <h2 class="mt-1 text-2xl font-semibold text-slate-900">Tugas Kunjungan Sales</h2>
            </div>
            @can('create', App\Models\VisitTask::class)
            <button type="button" id="btnOpenCreateTask"
                class="inline-flex items-center gap-2 rounded-lg bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800">
                <svg class="h-4 w-4 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Buat Tugas Kunjungan</span>
            </button>
            @else
                @if(auth()->user()->hasAnyRole(['Admin','Supervisor Sales']))
                <button type="button" id="btnOpenCreateTask"
                    class="inline-flex items-center gap-2 rounded-lg bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800">
                    <svg class="h-4 w-4 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Buat Tugas Kunjungan</span>
                </button>
                @endif
            @endcan
        </div>
    </x-slot>

    <div class="min-h-[calc(100vh-65px)] bg-slate-100 py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">

            @if(session('status'))
            <div class="flex items-center justify-between rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800 shadow-sm">
                <div class="flex items-center gap-2">
                    <svg class="h-4 w-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>{{ session('status') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900">&times;</button>
            </div>
            @endif

            @if($errors->any())
            <div class="flex items-center justify-between rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-800 shadow-sm">
                <div class="flex items-center gap-2">
                    <svg class="h-4 w-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ $errors->first() }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-rose-600 hover:text-rose-900">&times;</button>
            </div>
            @endif

            {{-- FILTER BAR --}}
            <form method="GET" action="{{ route('visit-tasks.index') }}"
                class="flex flex-wrap items-end gap-3 rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex-1 min-w-[180px]">
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Cari sales / outlet / kode</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari..."
                        class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div class="min-w-[140px]">
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Status</label>
                    <select name="status" class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Semua Status</option>
                        @foreach($statusList as $s)
                        <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>
                            {{ str_replace('_', ' ', $s) }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="min-w-[120px]">
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Prioritas</label>
                    <select name="priority" class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Semua</option>
                        <option value="mendesak" {{ request('priority') === 'mendesak' ? 'selected' : '' }}>Mendesak</option>
                        <option value="tinggi" {{ request('priority') === 'tinggi' ? 'selected' : '' }}>Tinggi</option>
                        <option value="normal" {{ request('priority') === 'normal' ? 'selected' : '' }}>Normal</option>
                        <option value="rendah" {{ request('priority') === 'rendah' ? 'selected' : '' }}>Rendah</option>
                    </select>
                </div>
                <div class="min-w-[140px]">
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Dari Tanggal</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                        class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div class="min-w-[140px]">
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Sampai Tanggal</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                        class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-slate-950 px-4 py-2 text-xs font-semibold text-white hover:bg-slate-800 transition">
                        <svg class="h-3.5 w-3.5 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
                        Filter
                    </button>
                    <a href="{{ route('visit-tasks.index') }}" class="inline-flex items-center rounded-lg border border-slate-300 px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition">
                        Reset
                    </a>
                </div>
            </form>

            {{-- TASK TABLE --}}
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="hidden md:table min-w-full text-left text-sm">
                        <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500 border-b border-slate-100">
                            <tr>
                                <th class="px-4 py-4">Kode / ID</th>
                                <th class="px-4 py-4">Sales Motoris</th>
                                <th class="px-4 py-4">Tim</th>
                                <th class="px-4 py-4">Outlet Tujuan</th>
                                <th class="px-4 py-4">Jadwal</th>
                                <th class="px-4 py-4">Prioritas</th>
                                <th class="px-4 py-4">Status Tugas</th>
                                <th class="px-4 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($tasks as $task)
                            @php
                                $statusColors = App\Models\VisitTask::STATUS_COLORS;
                                $color = $statusColors[$task->status] ?? 'slate';
                                $prioColors = ['mendesak' => 'rose', 'tinggi' => 'amber', 'normal' => 'blue', 'rendah' => 'slate'];
                                $prioColor = $prioColors[$task->priority] ?? 'slate';
                                $isSales = auth()->user()->hasRole('Sales Motoris');
                                $isMine = $task->sales_id === auth()->id();
                            @endphp
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="px-4 py-4">
                                    <div class="font-mono text-xs font-bold text-slate-900">{{ $task->task_code }}</div>
                                    <div class="text-[11px] text-slate-400 mt-0.5">ID #{{ $task->id }}</div>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex items-center gap-2.5">
                                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-amber-50 text-amber-800 font-bold text-xs border border-amber-200">
                                            {{ strtoupper(substr($task->sales->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <div class="font-semibold text-slate-900 text-xs">{{ $task->sales->name }}</div>
                                            <div class="text-[11px] text-slate-400">{{ $task->sales->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="text-xs text-slate-600">{{ $task->sales->team?->name ?? 'Belum ditentukan' }}</span>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="font-semibold text-slate-900 text-xs">{{ $task->customer->name }}</div>
                                    <div class="text-[11px] text-slate-400 flex items-center gap-1 mt-0.5">
                                        <svg class="h-3 w-3 shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                        <span class="truncate max-w-[180px]">{{ $task->customer->address ?: 'Alamat belum dicatat' }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="text-xs font-semibold text-slate-900">{{ $task->scheduled_at->format('d M Y') }}</div>
                                    <div class="text-[11px] text-slate-400">{{ $task->scheduled_at->format('H:i') }} WIB</div>
                                    @if($task->due_at)
                                    <div class="text-[11px] text-rose-500 mt-0.5">Batas: {{ $task->due_at->format('d M H:i') }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-[11px] font-semibold border
                                        {{ $prioColor === 'rose' ? 'bg-rose-50 text-rose-700 border-rose-200' : '' }}
                                        {{ $prioColor === 'amber' ? 'bg-amber-50 text-amber-800 border-amber-200' : '' }}
                                        {{ $prioColor === 'blue' ? 'bg-blue-50 text-blue-700 border-blue-200' : '' }}
                                        {{ $prioColor === 'slate' ? 'bg-slate-50 text-slate-600 border-slate-200' : '' }}
                                    ">
                                        <span class="h-1.5 w-1.5 rounded-full
                                            {{ $prioColor === 'rose' ? 'bg-rose-500' : '' }}
                                            {{ $prioColor === 'amber' ? 'bg-amber-500' : '' }}
                                            {{ $prioColor === 'blue' ? 'bg-blue-500' : '' }}
                                            {{ $prioColor === 'slate' ? 'bg-slate-400' : '' }}
                                        "></span>
                                        {{ ucfirst($task->priority) }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[11px] font-semibold border
                                        {{ $color === 'amber' ? 'bg-amber-50 text-amber-800 border-amber-200' : '' }}
                                        {{ $color === 'blue' ? 'bg-blue-50 text-blue-700 border-blue-200' : '' }}
                                        {{ $color === 'rose' ? 'bg-rose-50 text-rose-700 border-rose-200' : '' }}
                                        {{ $color === 'violet' ? 'bg-violet-50 text-violet-700 border-violet-200' : '' }}
                                        {{ $color === 'cyan' ? 'bg-cyan-50 text-cyan-700 border-cyan-200' : '' }}
                                        {{ $color === 'indigo' ? 'bg-indigo-50 text-indigo-700 border-indigo-200' : '' }}
                                        {{ $color === 'orange' ? 'bg-orange-50 text-orange-700 border-orange-200' : '' }}
                                        {{ $color === 'emerald' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : '' }}
                                        {{ $color === 'slate' ? 'bg-slate-50 text-slate-600 border-slate-200' : '' }}
                                    ">
                                        <span class="h-1.5 w-1.5 rounded-full
                                            {{ $color === 'amber' ? 'bg-amber-500' : '' }}
                                            {{ $color === 'blue' ? 'bg-blue-500' : '' }}
                                            {{ $color === 'rose' ? 'bg-rose-500' : '' }}
                                            {{ $color === 'violet' ? 'bg-violet-500' : '' }}
                                            {{ $color === 'cyan' ? 'bg-cyan-500' : '' }}
                                            {{ $color === 'indigo' ? 'bg-indigo-500' : '' }}
                                            {{ $color === 'orange' ? 'bg-orange-500' : '' }}
                                            {{ $color === 'emerald' ? 'bg-emerald-500' : '' }}
                                            {{ $color === 'slate' ? 'bg-slate-400' : '' }}
                                        "></span>
                                        {{ str_replace('_', ' ', $task->status) }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-4 py-4 text-center">
                                    <div class="flex items-center justify-center gap-1">
                                        {{-- View Detail --}}
                                        <a href="{{ route('visit-tasks.show', $task) }}"
                                            title="Lihat Detail"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-blue-200 bg-blue-50 text-blue-600 hover:bg-blue-100 transition">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </a>

                                        {{-- Sales: Accept / Reject (only for own tasks in MENUNGGU_RESPONS) --}}
                                        @if($isSales && $isMine && $task->status === 'MENUNGGU_RESPONS')
                                        <button type="button" onclick="openAcceptModal({{ $task->id }}, '{{ $task->task_code }}')"
                                            title="Terima Tugas"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-emerald-200 bg-emerald-50 text-emerald-600 hover:bg-emerald-100 transition">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        </button>
                                        <button type="button" onclick="openRejectModal({{ $task->id }}, '{{ $task->task_code }}')"
                                            title="Tolak Tugas"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-rose-200 bg-rose-50 text-rose-600 hover:bg-rose-100 transition">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                        @endif

                                        {{-- Admin/Supervisor: Reschedule button for rejected tasks --}}
                                        @if(auth()->user()->hasAnyRole(['Admin','Supervisor Sales']) && $task->status === 'DITOLAK')
                                        <button type="button" onclick="openRescheduleModal({{ $task->id }}, '{{ $task->task_code }}')"
                                            title="Jadwalkan Ulang"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-violet-200 bg-violet-50 text-violet-600 hover:bg-violet-100 transition">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        </button>
                                        @endif

                                        {{-- Admin/Supervisor: Cancel button --}}
                                        @if(auth()->user()->hasAnyRole(['Admin','Supervisor Sales']) && in_array($task->status, ['MENUNGGU_RESPONS','DITERIMA','DITOLAK','DIJADWALKAN_ULANG']))
                                        <button type="button" onclick="openCancelModal({{ $task->id }}, '{{ $task->task_code }}')"
                                            title="Batalkan Tugas"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 bg-slate-50 text-slate-500 hover:bg-slate-100 transition">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="px-6 py-14 text-center text-slate-500">
                                    <div class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                                        <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                    </div>
                                    <p class="text-sm font-medium">Belum ada tugas kunjungan.</p>
                                    <p class="text-xs text-slate-400 mt-1">Buat tugas baru untuk menugaskan sales motoris ke outlet.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- MOBILE TASK CARDS (md:hidden) --}}
                <div class="block md:hidden divide-y divide-slate-100">
                    @forelse($tasks as $task)
                    @php
                        $statusColors = App\Models\VisitTask::STATUS_COLORS;
                        $color = $statusColors[$task->status] ?? 'slate';
                        $prioColors = ['mendesak' => 'rose', 'tinggi' => 'amber', 'normal' => 'blue', 'rendah' => 'slate'];
                        $prioColor = $prioColors[$task->priority] ?? 'slate';
                        $isSales = auth()->user()->hasRole('Sales Motoris');
                        $isMine = $task->sales_id === auth()->id();
                    @endphp
                    <div class="p-4 space-y-3 hover:bg-slate-50/70 transition">
                        {{-- Header: Code, Priority, Status --}}
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="font-mono font-bold text-xs text-slate-900">{{ $task->task_code }}</span>
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold border
                                        {{ $prioColor === 'rose' ? 'bg-rose-50 text-rose-700 border-rose-200' : '' }}
                                        {{ $prioColor === 'amber' ? 'bg-amber-50 text-amber-800 border-amber-200' : '' }}
                                        {{ $prioColor === 'blue' ? 'bg-blue-50 text-blue-700 border-blue-200' : '' }}
                                        {{ $prioColor === 'slate' ? 'bg-slate-50 text-slate-600 border-slate-200' : '' }}
                                    ">
                                        {{ ucfirst($task->priority) }}
                                    </span>
                                </div>
                                <div class="text-[11px] text-slate-500 mt-0.5">
                                    {{ $task->sales->name }} &bull; <span class="text-slate-400">{{ $task->sales->team?->name ?? 'Belum ditentukan' }}</span>
                                </div>
                            </div>
                            <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-[10px] font-bold border shrink-0
                                {{ $color === 'amber' ? 'bg-amber-50 text-amber-800 border-amber-200' : '' }}
                                {{ $color === 'blue' ? 'bg-blue-50 text-blue-700 border-blue-200' : '' }}
                                {{ $color === 'rose' ? 'bg-rose-50 text-rose-700 border-rose-200' : '' }}
                                {{ $color === 'violet' ? 'bg-violet-50 text-violet-700 border-violet-200' : '' }}
                                {{ $color === 'cyan' ? 'bg-cyan-50 text-cyan-700 border-cyan-200' : '' }}
                                {{ $color === 'indigo' ? 'bg-indigo-50 text-indigo-700 border-indigo-200' : '' }}
                                {{ $color === 'orange' ? 'bg-orange-50 text-orange-700 border-orange-200' : '' }}
                                {{ $color === 'emerald' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : '' }}
                                {{ $color === 'slate' ? 'bg-slate-50 text-slate-600 border-slate-200' : '' }}
                            ">
                                <span class="h-1.5 w-1.5 rounded-full
                                    {{ $color === 'amber' ? 'bg-amber-500' : '' }}
                                    {{ $color === 'blue' ? 'bg-blue-500' : '' }}
                                    {{ $color === 'rose' ? 'bg-rose-500' : '' }}
                                    {{ $color === 'violet' ? 'bg-violet-500' : '' }}
                                    {{ $color === 'cyan' ? 'bg-cyan-500' : '' }}
                                    {{ $color === 'indigo' ? 'bg-indigo-500' : '' }}
                                    {{ $color === 'orange' ? 'bg-orange-500' : '' }}
                                    {{ $color === 'emerald' ? 'bg-emerald-500' : '' }}
                                    {{ $color === 'slate' ? 'bg-slate-400' : '' }}
                                "></span>
                                <span>{{ str_replace('_', ' ', $task->status) }}</span>
                            </span>
                        </div>

                        {{-- Outlet Info --}}
                        <div class="rounded-xl bg-slate-50 p-2.5 border border-slate-100 text-xs">
                            <div class="font-bold text-slate-900 flex items-center gap-1.5">
                                <svg class="h-3.5 w-3.5 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                <span>{{ $task->customer->name }}</span>
                            </div>
                            <div class="text-[11px] text-slate-500 mt-1 pl-5">
                                {{ $task->customer->address ?: 'Alamat belum dicatat' }}
                            </div>
                            <div class="flex flex-wrap items-center gap-3 mt-2 pl-5 text-[11px] text-slate-500 border-t border-slate-200/60 pt-1.5">
                                <span>📅 {{ $task->scheduled_at->format('d M Y, H:i') }} WIB</span>
                                @if($task->due_at)
                                <span class="text-rose-500 font-medium">Batas: {{ $task->due_at->format('H:i') }}</span>
                                @endif
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex items-center justify-end gap-2 pt-1">
                            <a href="{{ route('visit-tasks.show', $task) }}"
                                class="inline-flex items-center gap-1.5 rounded-lg border border-blue-200 bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700 shadow-2xs hover:bg-blue-100 active:scale-95 transition">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <span>Detail</span>
                            </a>

                            @if($isSales && $isMine && $task->status === 'MENUNGGU_RESPONS')
                            <button type="button" onclick="openAcceptModal({{ $task->id }}, '{{ $task->task_code }}')"
                                class="inline-flex items-center gap-1.5 rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700 shadow-2xs hover:bg-emerald-100 active:scale-95 transition">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Terima</span>
                            </button>
                            <button type="button" onclick="openRejectModal({{ $task->id }}, '{{ $task->task_code }}')"
                                class="inline-flex items-center gap-1.5 rounded-lg border border-rose-200 bg-rose-50 px-3 py-1.5 text-xs font-semibold text-rose-700 shadow-2xs hover:bg-rose-100 active:scale-95 transition">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                <span>Tolak</span>
                            </button>
                            @endif

                            @if(auth()->user()->hasAnyRole(['Admin','Supervisor Sales']) && $task->status === 'DITOLAK')
                            <button type="button" onclick="openRescheduleModal({{ $task->id }}, '{{ $task->task_code }}')"
                                class="inline-flex items-center gap-1.5 rounded-lg border border-violet-200 bg-violet-50 px-3 py-1.5 text-xs font-semibold text-violet-700 shadow-2xs hover:bg-violet-100 active:scale-95 transition">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <span>Jadwalkan</span>
                            </button>
                            @endif

                            @if(auth()->user()->hasAnyRole(['Admin','Supervisor Sales']) && in_array($task->status, ['MENUNGGU_RESPONS','DITERIMA','DITOLAK','DIJADWALKAN_ULANG']))
                            <button type="button" onclick="openCancelModal({{ $task->id }}, '{{ $task->task_code }}')"
                                class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-slate-50 px-3 py-1.5 text-xs font-semibold text-slate-600 shadow-2xs hover:bg-slate-100 active:scale-95 transition">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                <span>Batal</span>
                            </button>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="p-8 text-center text-slate-500">
                        <p class="text-xs">Belum ada tugas kunjungan.</p>
                    </div>
                    @endforelse
                </div>
                <div class="border-t border-slate-100 px-6 py-4">
                    {{ $tasks->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- MODAL: CREATE TASK (Admin / Supervisor) --}}
    {{-- ============================================================ --}}
    <div id="modalCreateTask" class="fixed inset-0 z-[1000] hidden items-center justify-center bg-slate-950/70 p-4 backdrop-blur-xs">
        <div class="max-h-[92vh] w-full max-w-2xl overflow-y-auto rounded-2xl bg-white shadow-2xl">
            <div class="sticky top-0 z-10 flex items-center justify-between border-b border-slate-100 bg-white px-6 py-4">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[.2em] text-blue-600">Penugasan Lapangan</p>
                    <h3 class="text-xl font-bold text-slate-900">Buat Tugas Kunjungan</h3>
                </div>
                <button type="button" onclick="closeModal('modalCreateTask')" class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-700 text-2xl leading-none">&times;</button>
            </div>
            <form method="POST" action="{{ route('visit-tasks.store') }}" class="p-6 space-y-5">
                @csrf
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Sales Motoris <span class="text-red-500">*</span></label>
                        <select name="sales_id" required class="mt-1.5 w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-blue-500 focus:ring-blue-500">
                            <option value="">-- Pilih Sales Motoris --</option>
                            @foreach($salesUsers as $salesUser)
                            <option value="{{ $salesUser->id }}" data-team="{{ $salesUser->team?->name ?? '-' }}">
                                {{ $salesUser->name }} ({{ $salesUser->team?->name ?? 'Tanpa Tim' }})
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Outlet / Tujuan Kunjungan <span class="text-red-500">*</span></label>
                        <select name="customer_id" required class="mt-1.5 w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-blue-500 focus:ring-blue-500">
                            <option value="">-- Pilih Outlet --</option>
                            @foreach($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }} — {{ $customer->address ?: 'Alamat belum dicatat' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Tanggal & Jam Kunjungan <span class="text-red-500">*</span></label>
                        <input type="datetime-local" name="scheduled_at" required
                            min="{{ now()->addMinutes(5)->format('Y-m-d\TH:i') }}"
                            class="mt-1.5 w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Batas Waktu (Opsional)</label>
                        <input type="datetime-local" name="due_at"
                            class="mt-1.5 w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Tingkat Prioritas <span class="text-red-500">*</span></label>
                        <select name="priority" required class="mt-1.5 w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-blue-500 focus:ring-blue-500">
                            <option value="normal" selected>Normal</option>
                            <option value="rendah">Rendah</option>
                            <option value="tinggi">Tinggi</option>
                            <option value="mendesak">Mendesak</option>
                        </select>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Tujuan Kunjungan</label>
                        <textarea name="purpose" rows="2" placeholder="Jelaskan tujuan kunjungan ke outlet ini..."
                            class="mt-1.5 w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-blue-500 focus:ring-blue-500"></textarea>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Target / Hasil yang Diharapkan</label>
                        <textarea name="target_result" rows="2" placeholder="Contoh: Dapatkan order minimal 5 dus, tawaran produk baru..."
                            class="mt-1.5 w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-blue-500 focus:ring-blue-500"></textarea>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Instruksi / Catatan untuk Sales</label>
                        <textarea name="instructions" rows="2" placeholder="Instruksi khusus, rute yang harus dilewati, kontak PIC toko..."
                            class="mt-1.5 w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-blue-500 focus:ring-blue-500"></textarea>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3 pt-2 border-t border-slate-100">
                    <button type="button" onclick="closeModal('modalCreateTask')" class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">Batal</button>
                    <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-slate-950 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-slate-800 transition">
                        <svg class="h-4 w-4 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                        <span>Kirim Tugas</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- MODAL: ACCEPT TASK --}}
    {{-- ============================================================ --}}
    <div id="modalAccept" class="fixed inset-0 z-[1000] hidden items-center justify-center bg-slate-950/70 p-4 backdrop-blur-xs">
        <div class="w-full max-w-md rounded-2xl bg-white shadow-2xl">
            <div class="border-b border-slate-100 px-6 py-4">
                <h3 class="flex items-center gap-2 text-lg font-bold text-slate-900">
                    <svg class="h-5 w-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Terima Tugas Kunjungan?</span>
                </h3>
                <p class="text-sm text-slate-500 mt-0.5">Kode tugas: <span id="acceptTaskCode" class="font-mono font-bold text-slate-900"></span></p>
            </div>
            <form id="formAccept" method="POST" class="p-6 space-y-4">
                @csrf
                <p class="text-sm text-slate-700">Dengan menerima tugas ini, Anda berkomitmen untuk melaksanakan kunjungan sesuai jadwal yang telah ditentukan. Supervisor Anda akan mendapat notifikasi.</p>
                <div class="flex items-center justify-end gap-3">
                    <button type="button" onclick="closeModal('modalAccept')" class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Batal</button>
                    <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700 transition">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Ya, Terima Tugas</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- MODAL: REJECT TASK --}}
    {{-- ============================================================ --}}
    <div id="modalReject" class="fixed inset-0 z-[1000] hidden items-center justify-center bg-slate-950/70 p-4 backdrop-blur-xs">
        <div class="w-full max-w-lg rounded-2xl bg-white shadow-2xl">
            <div class="border-b border-slate-100 px-6 py-4">
                <h3 class="flex items-center gap-2 text-lg font-bold text-slate-900">
                    <svg class="h-5 w-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Tolak Tugas Kunjungan</span>
                </h3>
                <p class="text-sm text-slate-500 mt-0.5">Kode tugas: <span id="rejectTaskCode" class="font-mono font-bold text-slate-900"></span></p>
            </div>
            <form id="formReject" method="POST" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Kategori Alasan Penolakan <span class="text-red-500">*</span></label>
                    <select name="rejection_category" required class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-blue-500 focus:ring-blue-500">
                        <option value="">-- Pilih Alasan --</option>
                        <option value="Jadwal Bentrok">Jadwal Bentrok dengan Tugas Lain</option>
                        <option value="Outlet Tutup">Outlet / Toko Tutup</option>
                        <option value="Kendala Kendaraan">Kendala Kendaraan / Transportasi</option>
                        <option value="Cuaca Buruk">Cuaca Buruk / Force Majeure</option>
                        <option value="Sakit">Sakit / Kondisi Kesehatan</option>
                        <option value="Tugas Tidak Memungkinkan">Tugas Tidak Memungkinkan Dilaksanakan</option>
                        <option value="Outlet di Luar Wilayah">Outlet di Luar Wilayah Saya</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Alasan Lengkap <span class="text-red-500">*</span></label>
                    <textarea name="reason" required minlength="10" rows="3"
                        placeholder="Jelaskan alasan penolakan secara singkat dan jelas (min. 10 karakter)..."
                        class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-blue-500 focus:ring-blue-500"></textarea>
                </div>
                <div class="flex items-center justify-end gap-3">
                    <button type="button" onclick="closeModal('modalReject')" class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Batal</button>
                    <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-rose-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-rose-700 transition">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        <span>Tolak Tugas</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- MODAL: RESCHEDULE TASK --}}
    {{-- ============================================================ --}}
    <div id="modalReschedule" class="fixed inset-0 z-[1000] hidden items-center justify-center bg-slate-950/70 p-4 backdrop-blur-xs">
        <div class="w-full max-w-lg rounded-2xl bg-white shadow-2xl">
            <div class="border-b border-slate-100 px-6 py-4">
                <h3 class="flex items-center gap-2 text-lg font-bold text-slate-900">
                    <svg class="h-5 w-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span>Jadwalkan Ulang Tugas</span>
                </h3>
                <p class="text-sm text-slate-500 mt-0.5">Kode tugas: <span id="rescheduleTaskCode" class="font-mono font-bold text-slate-900"></span></p>
            </div>
            <form id="formReschedule" method="POST" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Jadwal Baru <span class="text-red-500">*</span></label>
                    <input type="datetime-local" name="scheduled_at" required
                        min="{{ now()->addMinutes(5)->format('Y-m-d\TH:i') }}"
                        class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Instruksi Baru (Opsional)</label>
                    <textarea name="instructions" rows="2" placeholder="Perbarui instruksi jika ada perubahan..."
                        class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-blue-500 focus:ring-blue-500"></textarea>
                </div>
                @if(auth()->user()->hasRole('Admin'))
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Tugaskan ke Sales Lain (Opsional)</label>
                    <select name="sales_id" class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-blue-500 focus:ring-blue-500">
                        <option value="">-- Tetap sales yang sama --</option>
                        @foreach($salesUsers as $salesUser)
                        <option value="{{ $salesUser->id }}">{{ $salesUser->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
                <div class="flex items-center justify-end gap-3">
                    <button type="button" onclick="closeModal('modalReschedule')" class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Batal</button>
                    <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-violet-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-violet-700 transition">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span>Jadwalkan Ulang</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- MODAL: CANCEL TASK --}}
    {{-- ============================================================ --}}
    <div id="modalCancel" class="fixed inset-0 z-[1000] hidden items-center justify-center bg-slate-950/70 p-4 backdrop-blur-xs">
        <div class="w-full max-w-md rounded-2xl bg-white shadow-2xl">
            <div class="border-b border-slate-100 px-6 py-4">
                <h3 class="flex items-center gap-2 text-lg font-bold text-slate-900">
                    <svg class="h-5 w-5 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                    <span>Batalkan Tugas</span>
                </h3>
                <p class="text-sm text-slate-500 mt-0.5">Kode tugas: <span id="cancelTaskCode" class="font-mono font-bold text-slate-900"></span></p>
            </div>
            <form id="formCancel" method="POST" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Alasan Pembatalan <span class="text-red-500">*</span></label>
                    <textarea name="reason" required minlength="5" rows="3"
                        placeholder="Jelaskan alasan pembatalan tugas..."
                        class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-blue-500 focus:ring-blue-500"></textarea>
                </div>
                <div class="flex items-center justify-end gap-3">
                    <button type="button" onclick="closeModal('modalCancel')" class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Batal</button>
                    <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-slate-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-slate-800 transition">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        <span>Batalkan Tugas</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    function closeModal(id) {
        document.getElementById(id).classList.replace('flex', 'hidden');
    }

    document.addEventListener('DOMContentLoaded', function () {
        // Create Task modal
        const btnOpen = document.getElementById('btnOpenCreateTask');
        if (btnOpen) {
            btnOpen.addEventListener('click', () => {
                document.getElementById('modalCreateTask').classList.replace('hidden', 'flex');
            });
        }

        // Close modals on backdrop click
        ['modalCreateTask', 'modalAccept', 'modalReject', 'modalReschedule', 'modalCancel'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.addEventListener('click', function (e) {
                if (e.target === el) closeModal(id);
            });
        });
    });

    function openAcceptModal(taskId, taskCode) {
        document.getElementById('acceptTaskCode').textContent = taskCode;
        document.getElementById('formAccept').action = `/visit-tasks/${taskId}/accept`;
        document.getElementById('modalAccept').classList.replace('hidden', 'flex');
    }

    function openRejectModal(taskId, taskCode) {
        document.getElementById('rejectTaskCode').textContent = taskCode;
        document.getElementById('formReject').action = `/visit-tasks/${taskId}/reject`;
        document.getElementById('modalReject').classList.replace('hidden', 'flex');
    }

    function openRescheduleModal(taskId, taskCode) {
        document.getElementById('rescheduleTaskCode').textContent = taskCode;
        document.getElementById('formReschedule').action = `/visit-tasks/${taskId}/reschedule`;
        document.getElementById('modalReschedule').classList.replace('hidden', 'flex');
    }

    function openCancelModal(taskId, taskCode) {
        document.getElementById('cancelTaskCode').textContent = taskCode;
        document.getElementById('formCancel').action = `/visit-tasks/${taskId}/cancel`;
        document.getElementById('modalCancel').classList.replace('hidden', 'flex');
    }
    </script>
</x-app-layout>

