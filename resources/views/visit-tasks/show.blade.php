<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('visit-tasks.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 transition">
                        &larr; Kembali ke Daftar Tugas
                    </a>
                    <span class="text-xs text-slate-300">/</span>
                    <span class="text-xs font-semibold text-blue-600 uppercase tracking-wider">{{ $visitTask->task_code }}</span>
                </div>
                <h2 class="mt-1 text-2xl font-bold text-slate-900">Detail Tugas: {{ $visitTask->customer->name }}</h2>
            </div>
            <div class="flex items-center gap-2">
                @php
                    $statusColors = App\Models\VisitTask::STATUS_COLORS;
                    $color = $statusColors[$visitTask->status] ?? 'slate';
                @endphp
                <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-bold border
                    {{ $color === 'amber' ? 'bg-amber-50 text-blue-800 border-blue-200' : '' }}
                    {{ $color === 'blue' ? 'bg-blue-50 text-blue-700 border-blue-200' : '' }}
                    {{ $color === 'rose' ? 'bg-rose-50 text-rose-700 border-rose-200' : '' }}
                    {{ $color === 'violet' ? 'bg-violet-50 text-violet-700 border-violet-200' : '' }}
                    {{ $color === 'cyan' ? 'bg-cyan-50 text-cyan-700 border-cyan-200' : '' }}
                    {{ $color === 'indigo' ? 'bg-indigo-50 text-indigo-700 border-indigo-200' : '' }}
                    {{ $color === 'orange' ? 'bg-orange-50 text-orange-700 border-orange-200' : '' }}
                    {{ $color === 'emerald' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : '' }}
                    {{ $color === 'slate' ? 'bg-slate-50 text-slate-600 border-slate-200' : '' }}
                ">
                    <span class="h-2 w-2 rounded-full
                        {{ $color === 'amber' ? 'bg-blue-600' : '' }}
                        {{ $color === 'blue' ? 'bg-blue-500' : '' }}
                        {{ $color === 'rose' ? 'bg-rose-500' : '' }}
                        {{ $color === 'violet' ? 'bg-violet-500' : '' }}
                        {{ $color === 'cyan' ? 'bg-cyan-500' : '' }}
                        {{ $color === 'indigo' ? 'bg-indigo-500' : '' }}
                        {{ $color === 'orange' ? 'bg-orange-500' : '' }}
                        {{ $color === 'emerald' ? 'bg-emerald-500' : '' }}
                        {{ $color === 'slate' ? 'bg-slate-400' : '' }}
                    "></span>
                    {{ str_replace('_', ' ', $visitTask->status) }}
                </span>
            </div>
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

            @if(session('error'))
            <div class="flex items-center justify-between rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-800 shadow-sm">
                <div class="flex items-center gap-2">
                    <svg class="h-4 w-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span>{{ session('error') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-rose-600 hover:text-rose-900">&times;</button>
            </div>
            @endif

            {{-- SALES MOTORIS ACTION CONTROL BAR --}}
            @if(auth()->user()->hasRole('Sales Motoris') && $visitTask->sales_id === auth()->id())
            <div class="rounded-2xl border border-blue-200 bg-gradient-to-r from-amber-50 via-white to-amber-50 p-5 shadow-sm">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <h3 class="text-base font-bold text-slate-900 inline-flex items-center gap-2">
                            <svg class="h-5 w-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                            <span>Alur Pelaksanaan Kunjungan Lapangan</span>
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">Perbarui status aktivitas Anda saat menuju outlet dan melakukan kunjungan.</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        @if($visitTask->status === 'MENUNGGU_RESPONS')
                            <form method="POST" action="{{ route('visit-tasks.accept', $visitTask) }}">
                                @csrf
                                <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-emerald-700 transition">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span>Terima Tugas</span>
                                </button>
                            </form>
                            <button type="button" onclick="document.getElementById('modalRejectDetail').classList.replace('hidden', 'flex')" class="inline-flex items-center gap-1.5 rounded-lg bg-rose-600 px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-rose-700 transition">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                <span>Tolak Tugas</span>
                            </button>
                        @elseif($visitTask->status === 'DITERIMA')
                            <form method="POST" action="{{ route('visit-tasks.status', $visitTask) }}" id="formStartTrip">
                                @csrf
                                <input type="hidden" name="status" value="DALAM_PERJALANAN">
                                <button type="button" id="btnStartTrip" onclick="handleStartTripWithGps()" class="inline-flex items-center gap-2 rounded-lg bg-cyan-600 px-4 py-2.5 text-xs font-bold text-white shadow-sm hover:bg-cyan-700 transition">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                    <span>Mulai Perjalanan & Aktifkan GPS (OTW)</span>
                                </button>
                            </form>
                        @elseif($visitTask->status === 'DALAM_PERJALANAN')
                            <div class="flex items-center gap-2">
                                <button type="button" onclick="handleCheckIn()" id="btnCheckIn" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-xs font-bold text-white shadow-sm hover:bg-indigo-700 transition">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <span>Tiba di Lokasi & Check-in GPS</span>
                                </button>
                                <a href="{{ route('tracking.index') }}" target="_blank" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition shadow-2xs">
                                    <svg class="h-3.5 w-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                                    <span>Lihat di Live Map</span>
                                </a>
                            </div>
                        @elseif($visitTask->status === 'TIBA_DI_LOKASI')
                            <form method="POST" action="{{ route('visit-tasks.status', $visitTask) }}">
                                @csrf
                                <input type="hidden" name="status" value="SEDANG_KUNJUNGAN">
                                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-orange-600 px-4 py-2.5 text-xs font-bold text-white shadow-sm hover:bg-orange-700 transition">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                    <span>Mulai Aktivitas Kunjungan</span>
                                </button>
                            </form>
                        @elseif($visitTask->status === 'SEDANG_KUNJUNGAN')
                            <button type="button" onclick="document.getElementById('modalCheckOut').classList.replace('hidden', 'flex')" class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2.5 text-xs font-bold text-white shadow-sm hover:bg-emerald-700 transition">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>Selesai Kunjungan & Check-out</span>
                            </button>
                        @elseif($visitTask->status === 'SELESAI')
                            <span class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-700 bg-emerald-100 px-3 py-1.5 rounded-lg">
                                <svg class="h-4 w-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>Kunjungan ini telah selesai</span>
                            </span>
                            @if($visitTask->order || $visitTask->orders->count() > 0)
                                @php
                                    $topOrder = $visitTask->order ?? $visitTask->orders->first();
                                @endphp
                                <a href="#sectionTakingOrderPreview" class="inline-flex items-center gap-1.5 rounded-lg bg-blue-100 hover:bg-amber-200 px-3 py-1.5 text-xs font-bold text-amber-900 border border-blue-300 shadow-blue-50/50 transition shadow-2xs">
                                    <svg class="h-4 w-4 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                    <span>Pemesanan Produk: {{ $topOrder->order_number }} (Rp {{ number_format($topOrder->total_amount, 0, ',', '.') }} - {{ strtoupper($topOrder->payment_method) }}) &darr;</span>
                                </a>
                            @endif
                        @endif
                        @if(false)
                            </span>
                        @endif
                    </div>
                </div>

                @if(in_array($visitTask->status, ['DALAM_PERJALANAN', 'TIBA_DI_LOKASI', 'SEDANG_KUNJUNGAN']))
                    <div class="mt-4 rounded-xl bg-blue-50 border border-blue-200 p-3 flex flex-wrap items-center justify-between gap-3 text-xs text-blue-900">
                        <div class="flex items-center gap-2">
                            <span class="relative flex h-3 w-3">
                              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                              <span class="relative inline-flex rounded-full h-3 w-3 bg-blue-600"></span>
                            </span>
                            <span><strong>GPS Live Tracking Aktif:</strong> Koordinat Anda saat ini disinkronkan secara berkala ke server untuk pemantauan real-time oleh Kepala Divisi & Supervisor.</span>
                        </div>
                        <span id="liveGpsLastSync" class="text-[11px] font-mono text-blue-700 bg-white/70 px-2 py-0.5 rounded border border-blue-200 shrink-0">Sinkronisasi otomatis berjalan...</span>
                    </div>
                @endif
            </div>
            @endif

            <div class="grid gap-6 lg:grid-cols-12">
                {{-- LEFT COLUMN: INFO OUTLET & TUGAS --}}
                <div class="space-y-6 lg:col-span-7">
                    {{-- CARD INFORMASI TUGAS --}}
                    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm space-y-4">
                        <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3 flex items-center justify-between">
                            <span class="inline-flex items-center gap-2">
                                <svg class="h-4 w-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                <span>Informasi Penugasan</span>
                            </span>
                            <span class="font-mono text-xs text-blue-600">{{ $visitTask->task_code }}</span>
                        </h3>
                        <div class="grid gap-4 sm:grid-cols-2 text-xs">
                            <div>
                                <span class="font-bold text-slate-500 block">Sales Motoris:</span>
                                <span class="text-slate-900 font-semibold text-sm">{{ $visitTask->sales->name }}</span>
                                <span class="text-slate-500 block">{{ $visitTask->sales->team?->name ?? 'Tanpa Tim' }}</span>
                            </div>
                            <div>
                                <span class="font-bold text-slate-500 block">Ditugaskan Oleh:</span>
                                <span class="text-slate-900 font-semibold">{{ $visitTask->creator->name }}</span>
                                <span class="text-slate-400 block">{{ $visitTask->created_at->format('d M Y H:i') }}</span>
                            </div>
                            <div>
                                <span class="font-bold text-slate-500 block">Jadwal Rencana:</span>
                                <span class="text-slate-900 font-semibold text-sm">{{ $visitTask->scheduled_at->format('d M Y, H:i') }} WIB</span>
                            </div>
                            <div>
                                <span class="font-bold text-slate-500 block">Batas Waktu (Due):</span>
                                <span class="text-slate-900 font-semibold">{{ $visitTask->due_at ? $visitTask->due_at->format('d M Y, H:i') . ' WIB' : 'Tidak ditentukan' }}</span>
                            </div>
                            <div class="sm:col-span-2">
                                <span class="font-bold text-slate-500 block">Tujuan Kunjungan:</span>
                                <p class="mt-1 text-slate-800 bg-slate-50 p-2.5 rounded-lg border border-slate-200">{{ $visitTask->purpose ?: 'Tidak ada tujuan khusus dicatat.' }}</p>
                            </div>
                            @if($visitTask->target_result)
                            <div class="sm:col-span-2">
                                <span class="font-bold text-slate-500 block">Target / Hasil yang Diharapkan:</span>
                                <p class="mt-1 text-slate-800 bg-amber-50/60 p-2.5 rounded-lg border border-blue-200">{{ $visitTask->target_result }}</p>
                            </div>
                            @endif
                            @if($visitTask->instructions)
                            <div class="sm:col-span-2">
                                <span class="font-bold text-slate-500 block">Instruksi Lapangan:</span>
                                <p class="mt-1 text-slate-800 bg-blue-50/60 p-2.5 rounded-lg border border-blue-200">{{ $visitTask->instructions }}</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- CARD OUTLET & PETA --}}
                    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <h3 class="text-base font-bold text-slate-900 inline-flex items-center gap-2">
                                <svg class="h-4 w-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                <span>Outlet Tujuan: {{ $visitTask->customer->name }}</span>
                            </h3>
                            @if($visitTask->customer->latitude && $visitTask->customer->longitude)
                            <a href="https://www.google.com/maps/dir/?api=1&destination={{ $visitTask->customer->latitude }},{{ $visitTask->customer->longitude }}&travelmode=driving"
                                target="_blank"
                                class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-700 border border-emerald-200 hover:bg-emerald-100 transition">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                <span>Buka Google Maps</span>
                            </a>
                            @endif
                        </div>
                        <div class="text-xs space-y-2 text-slate-700">
                            <p><span class="font-bold text-slate-900">Alamat:</span> {{ $visitTask->customer->address ?: 'Alamat belum dicatat' }}</p>
                            <p><span class="font-bold text-slate-900">Kontak:</span> {{ $visitTask->customer->phone ?: '-' }}</p>
                            @if($visitTask->customer->latitude && $visitTask->customer->longitude)
                            <p><span class="font-bold text-slate-900">Koordinat Outlet:</span> <span class="font-mono text-slate-600">{{ $visitTask->customer->latitude }}, {{ $visitTask->customer->longitude }}</span></p>
                            <div id="outletMap" class="h-48 w-full rounded-xl border border-slate-200 shadow-inner"></div>
                            @endif
                        </div>
                    </div>

                    {{-- HASIL KUNJUNGAN JIKA SELESAI --}}
                    @if($visitTask->status === 'SELESAI' || $visitTask->visit_result)
                    <div class="rounded-2xl border border-emerald-200 bg-emerald-50/50 p-6 shadow-sm space-y-3">
                        <h3 class="text-base font-bold text-emerald-950 flex items-center gap-2">
                            <svg class="h-5 w-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Hasil & Laporan Kunjungan</span>
                        </h3>
                        <div class="text-xs space-y-2 text-slate-800">
                            <div>
                                <span class="font-bold block text-slate-900">Hasil Kunjungan:</span>
                                <p class="mt-1 bg-white p-3 rounded-xl border border-emerald-200 text-slate-800">{{ $visitTask->visit_result ?: '-' }}</p>
                            </div>
                            @if($visitTask->visit_notes)
                            <div>
                                <span class="font-bold block text-slate-900">Catatan Lapangan Tambahan:</span>
                                <p class="mt-1 bg-white p-3 rounded-xl border border-slate-200 text-slate-700">{{ $visitTask->visit_notes }}</p>
                            </div>
                            @endif

                            {{-- FOTO DOKUMENTASI LAPANGAN (DAPAT DIBUKA SEMUA ROLE) --}}
                            @if($visitTask->documentation_image)
                            <div class="pt-1">
                                <span class="font-bold block text-slate-900 mb-1.5 flex items-center gap-1.5">
                                    <svg class="h-4 w-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <span>Foto Dokumentasi Kunjungan Lapangan:</span>
                                </span>
                                <div class="rounded-xl border border-emerald-300 bg-white p-3.5 shadow-xs overflow-hidden max-w-full">
                                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 max-w-full">
                                        <div class="relative group cursor-pointer overflow-hidden rounded-xl border border-slate-200 shrink-0 bg-slate-100 shadow-2xs" 
                                             style="width: 140px; height: 105px; min-width: 140px; max-width: 140px;"
                                             onclick="openImageLightbox('{{ asset('storage/' . $visitTask->documentation_image) }}', 'Dokumentasi Tugas {{ $visitTask->task_code }} - {{ $visitTask->customer->name }}')">
                                            <img src="{{ asset('storage/' . $visitTask->documentation_image) }}" 
                                                 alt="Dokumentasi Kunjungan" 
                                                 style="width: 100%; height: 100%; max-width: 140px; max-height: 105px; object-fit: cover; display: block;"
                                                 class="rounded-xl group-hover:scale-105 transition duration-200">
                                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition rounded-xl">
                                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/></svg>
                                            </div>
                                        </div>
                                        <div class="space-y-1.5 flex-1 min-w-0 max-w-full">
                                            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-800 bg-emerald-100 px-2.5 py-0.5 rounded-full border border-emerald-200">
                                                <svg class="h-3 w-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                Dokumentasi Laporan Terverifikasi
                                            </span>
                                            <p class="text-xs text-slate-600">Foto bukti fisik kunjungan berhasil dilampirkan oleh sales motoris saat check-out dan dapat dibuka oleh Admin, Pimpinan, dan Kepala Divisi.</p>
                                            <div class="flex flex-wrap items-center gap-2 pt-1">
                                                <button type="button" 
                                                        onclick="openImageLightbox('{{ asset('storage/' . $visitTask->documentation_image) }}', 'Dokumentasi Tugas {{ $visitTask->task_code }} - {{ $visitTask->customer->name }}')" 
                                                        class="inline-flex items-center gap-1 text-xs font-bold text-amber-900 bg-amber-400 hover:bg-amber-300 px-3 py-1.5 rounded-lg shadow-2xs transition">
                                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                    <span>Buka Foto Ukuran Penuh</span>
                                                </button>
                                                <a href="{{ asset('storage/' . $visitTask->documentation_image) }}" 
                                                   download="Dokumentasi_{{ $visitTask->task_code }}.jpg" 
                                                   class="inline-flex items-center gap-1 text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 px-3 py-1.5 rounded-lg transition border border-slate-200">
                                                    <svg class="h-3.5 w-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                    <span>Unduh Gambar</span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                            <div class="grid grid-cols-2 gap-3 pt-2 text-slate-600">
                                <div>
                                    <span class="font-bold block text-slate-900">Check-in:</span>
                                    <span>{{ $visitTask->check_in_at ? $visitTask->check_in_at->format('d M Y H:i:s') : '-' }}</span>
                                    @if($visitTask->check_in_distance_meters)
                                    <span class="block text-[11px] text-slate-500">Jarak saat check-in: {{ round($visitTask->check_in_distance_meters) }} m</span>
                                    @endif
                                </div>
                                <div>
                                    <span class="font-bold block text-slate-900">Check-out:</span>
                                    <span>{{ $visitTask->check_out_at ? $visitTask->check_out_at->format('d M Y H:i:s') : '-' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- MULTILINE TEST SUCCESS --}}
                    @if($visitTask->order || $visitTask->orders->count() > 0)
                    @php
                        $linkedOrder = $visitTask->order ?? $visitTask->orders->first();
                    @endphp
                    <div id="sectionTakingOrderPreview" class="rounded-2xl border-2 border-blue-300 shadow-blue-50/50 bg-white p-6 shadow-sm space-y-5 scroll-mt-6">
                        {{-- CARD HEADER --}}
                        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 pb-4">
                            <div class="flex items-center gap-3">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-100 text-blue-700">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                </span>
                                <div>
                                    <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                                        <span>Preview Produk Pesanan & Keterangan Pembayaran</span>
                                    </h3>
                                    <p class="text-xs text-slate-500 mt-0.5">
                                        Nomor Pesanan: <span class="font-mono font-bold text-blue-700">{{ $linkedOrder->order_number }}</span>
                                        <span class="mx-1.5 text-slate-300">&bull;</span>
                                        <span>{{ $linkedOrder->created_at->format('d M Y, H:i') }} WIB</span>
                                    </p>
                                </div>
                            </div>
                            <div class="flex flex-wrap items-center gap-2">
                                @if($linkedOrder->payment_method === 'tempo')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800 border border-blue-200">
                                        Tempo / Kredit ({{ $linkedOrder->tenor_days }} Hari)
                                    </span>
                                @elseif($linkedOrder->payment_method === 'dp')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-indigo-100 text-indigo-800 border border-indigo-200">
                                        DP + Tenor ({{ $linkedOrder->tenor_days }} Hari)
                                    </span>
                                @elseif($linkedOrder->payment_method === 'transfer')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800 border border-blue-200">
                                        Transfer (Lunas)
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        Tunai (Lunas)
                                    </span>
                                @endif

                                @if($linkedOrder->payment_status === 'lunas')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-600 text-white shadow-2xs">
                                        LUNAS
                                    </span>
                                @elseif($linkedOrder->payment_status === 'sebagian')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-indigo-600 text-white shadow-2xs">
                                        SEBAGIAN (DP)
                                    </span>
                                @elseif($linkedOrder->is_overdue)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-rose-600 text-white shadow-2xs animate-pulse">
                                        JATUH TEMPO
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-blue-600 text-white shadow-2xs">
                                        BELUM LUNAS (PIUTANG)
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- 1. DAFTAR PRODUK YANG SUDAH DIORDER --}}
                        <div class="space-y-2.5">
                            <div class="flex items-center justify-between">
                                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 flex items-center gap-1.5">
                                    <svg class="h-4 w-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                    <span>Produk yang Diorder</span>
                                </h4>
                                <span class="text-[11px] font-bold text-blue-900 bg-blue-50 border border-blue-200 px-2.5 py-0.5 rounded-full">
                                    {{ $linkedOrder->items->count() }} Jenis Produk &bull; {{ $linkedOrder->items->sum('quantity') }} Pcs
                                </span>
                            </div>
                            <div class="overflow-x-auto rounded-xl border border-slate-200 shadow-2xs">
                                <table class="w-full text-left text-xs">
                                    <thead class="bg-slate-50 text-[11px] uppercase font-bold text-slate-600 border-b border-slate-200">
                                        <tr>
                                            <th class="p-3">Nama Produk</th>
                                            <th class="p-3 text-center">Jumlah</th>
                                            <th class="p-3 text-right">Harga Satuan</th>
                                            <th class="p-3 text-right">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 bg-white">
                                        @foreach($linkedOrder->items as $item)
                                        <tr class="hover:bg-slate-50/70 transition">
                                            <td class="p-3">
                                                <div class="flex items-center gap-2.5">
                                                    <div class="h-10 w-10 shrink-0 overflow-hidden rounded-lg border border-slate-200 bg-slate-50 flex items-center justify-center">
                                                        @if($item->product?->image_url)
                                                            <img src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}" class="h-full w-full object-cover">
                                                        @else
                                                            <svg class="h-5 w-5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                        @endif
                                                    </div>
                                                    <div class="min-w-0">
                                                        <div class="font-bold text-slate-900">{{ $item->product?->name ?? 'Produk' }}</div>
                                                        <div class="text-[11px] text-slate-400 font-mono">{{ $item->product?->sku }} @if($item->product?->category) &bull; <span class="text-slate-500">{{ $item->product->category }}</span>@endif</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="p-3 text-center font-bold text-slate-800">
                                                <span class="inline-block bg-slate-100 px-2 py-0.5 rounded text-xs font-bold">{{ $item->quantity }} {{ $item->product?->unit ?? 'pcs' }}</span>
                                            </td>
                                            <td class="p-3 text-right text-slate-600 font-medium">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                            <td class="p-3 text-right font-bold text-slate-900">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="bg-blue-50/40 border-t border-blue-200 font-bold text-xs">
                                        <tr>
                                            <td colspan="3" class="p-3 text-slate-700 text-right uppercase tracking-wider text-[11px]">Total Nilai Pesanan:</td>
                                            <td class="p-3 text-right text-base text-blue-700 font-extrabold">Rp {{ number_format($linkedOrder->total_amount, 0, ',', '.') }}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        {{-- 2. KETERANGAN PEMBAYARAN & STATUS KEUANGAN / PIUTANG --}}
                        <div class="rounded-xl bg-gradient-to-br from-slate-50 via-white to-amber-50/40 p-4 border border-slate-200 shadow-2xs space-y-3.5">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 flex items-center justify-between pb-2.5 border-b border-slate-200">
                                <span class="flex items-center gap-1.5">
                                    <svg class="h-4 w-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span>Keterangan Pembayaran & Status Piutang Outlet</span>
                                </span>
                            </h4>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                                <div class="bg-white p-3 rounded-lg border border-slate-200">
                                    <span class="text-slate-500 block text-[11px]">Total Nilai Pesanan</span>
                                    <span class="text-base font-extrabold text-slate-900">Rp {{ number_format($linkedOrder->total_amount, 0, ',', '.') }}</span>
                                </div>
                                <div class="bg-white p-3 rounded-lg border border-slate-200">
                                    <span class="text-slate-500 block text-[11px]">Uang Muka (DP) Terbayar</span>
                                    <span class="text-base font-extrabold text-emerald-600">Rp {{ number_format($linkedOrder->down_payment, 0, ',', '.') }}</span>
                                </div>
                                @if($linkedOrder->remaining_amount > 0)
                                <div class="bg-rose-50/80 p-3 rounded-lg border border-rose-200">
                                    <span class="text-rose-700 font-bold block text-[11px]">Sisa Piutang Kios ({{ $visitTask->customer->name }})</span>
                                    <span class="text-base font-black text-rose-700">Rp {{ number_format($linkedOrder->remaining_amount, 0, ',', '.') }}</span>
                                </div>
                                <div class="bg-white p-3 rounded-lg border border-slate-200">
                                    <span class="text-slate-500 block text-[11px]">Jatuh Tempo (Tenor {{ $linkedOrder->tenor_days ?? 0 }} Hari)</span>
                                    <span class="text-sm font-bold {{ $linkedOrder->is_overdue ? 'text-rose-600' : 'text-slate-900' }}">
                                        {{ $linkedOrder->due_date ? $linkedOrder->due_date->format('d M Y') : '-' }}
                                        @if($linkedOrder->is_overdue)
                                            <span class="text-[10px] text-rose-700 bg-rose-100 px-1.5 py-0.5 rounded font-bold ml-1">Lewat {{ $linkedOrder->days_overdue }} hari</span>
                                        @endif
                                    </span>
                                </div>
                                @else
                                <div class="sm:col-span-2 bg-emerald-50/80 p-3 rounded-lg border border-emerald-200 text-emerald-800 font-bold flex items-center gap-2">
                                    <svg class="h-4 w-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span>Pesanan ini telah LUNAS dibayar penuh. Tidak ada tagihan atau piutang tertunggak.</span>
                                </div>
                                @endif
                            </div>

                            <div class="pt-3 border-t border-slate-200 flex flex-wrap gap-2 items-center justify-between">
                                <span class="text-[11px] text-slate-500 font-medium">Tersambung otomatis ke sistem:</span>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('orders.index', ['search' => $linkedOrder->order_number]) }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-blue-800 hover:text-amber-900 bg-blue-100 hover:bg-amber-200 px-3 py-1.5 rounded-lg border border-blue-300 shadow-blue-50/50 transition shadow-2xs">
                                        <svg class="h-3.5 w-3.5 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                        <span>Modul Penjualan & Pesanan &rarr;</span>
                                    </a>
                                    <a href="{{ route('finance.index', ['search' => $linkedOrder->order_number]) }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-800 hover:text-emerald-900 bg-emerald-100 hover:bg-emerald-200 px-3 py-1.5 rounded-lg border border-emerald-300 transition shadow-2xs">
                                        <svg class="h-3.5 w-3.5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <span>Laporan Keuangan & Piutang &rarr;</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                {{-- RIGHT COLUMN: RIWAYAT STATUS & RESPON TUGAS --}}
                <div class="space-y-6 lg:col-span-5">
                    {{-- RESPON PENOLAKAN/PENERIMAAN JIKA ADA --}}
                    @if($visitTask->latestResponse)
                    <div class="rounded-2xl border {{ $visitTask->latestResponse->response === 'DITERIMA' ? 'border-emerald-200 bg-emerald-50/70' : 'border-rose-200 bg-rose-50/70' }} p-5 shadow-sm space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-wider {{ $visitTask->latestResponse->response === 'DITERIMA' ? 'text-emerald-800' : 'text-rose-800' }}">
                                Respon Sales: {{ $visitTask->latestResponse->response }}
                            </span>
                            <span class="text-[11px] text-slate-500">{{ $visitTask->latestResponse->responded_at->format('d M H:i') }}</span>
                        </div>
                        @if($visitTask->latestResponse->rejection_category)
                        <p class="text-xs font-semibold text-rose-900">Kategori: {{ $visitTask->latestResponse->rejection_category }}</p>
                        @endif
                        @if($visitTask->latestResponse->reason)
                        <p class="text-xs text-rose-800 bg-white/80 p-2.5 rounded-lg border border-rose-200">
                            "{{ $visitTask->latestResponse->reason }}"
                        </p>
                        @endif
                    </div>
                    @endif

                    {{-- TIMELINE RIWAYAT STATUS --}}
                    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm space-y-4">
                        <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3 flex items-center justify-between">
                            <span class="inline-flex items-center gap-2">
                                <svg class="h-4 w-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>Riwayat Perjalanan Tugas</span>
                            </span>
                            <span class="text-xs text-slate-400 font-normal">{{ $visitTask->histories->count() }} pembaruan</span>
                        </h3>
                        <div class="relative pl-6 space-y-6 before:absolute before:left-2 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-200">
                            @forelse($visitTask->histories as $h)
                            <div class="relative">
                                <div class="absolute -left-[27px] top-0.5 h-3.5 w-3.5 rounded-full border-2 border-white bg-blue-600 shadow-xs"></div>
                                <div class="text-xs">
                                    <div class="flex items-center justify-between">
                                        <span class="font-bold text-slate-900">{{ str_replace('_', ' ', $h->to_status) }}</span>
                                        <span class="text-[11px] text-slate-400">{{ $h->changed_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                    <div class="text-[11px] text-slate-500 mt-0.5">Oleh: {{ $h->changedBy->name }}</div>
                                    @if($h->notes)
                                    <p class="mt-1 text-slate-700 bg-slate-50 p-2 rounded border border-slate-200">{{ $h->notes }}</p>
                                    @endif
                                </div>
                            </div>
                            @empty
                            <p class="text-xs text-slate-400 italic">Belum ada catatan riwayat status.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL CHECK IN (HIDDEN FORM) --}}
    <form id="formCheckIn" method="POST" action="{{ route('visit-tasks.status', $visitTask) }}" class="hidden">
        @csrf
        <input type="hidden" name="status" value="TIBA_DI_LOKASI">
        <input type="hidden" name="latitude" id="checkInLat">
        <input type="hidden" name="longitude" id="checkInLng">
    </form>

    {{-- MODAL CHECK OUT RESULT --}}
    <div id="modalCheckOut" class="fixed inset-0 z-[1000] hidden items-center justify-center bg-slate-950/70 p-3 sm:p-4 backdrop-blur-xs overflow-y-auto">
        <div class="w-full max-w-2xl rounded-2xl bg-white shadow-2xl flex flex-col max-h-[92vh] my-auto">
            <div class="border-b border-slate-100 px-6 py-4">
                <h3 class="text-lg font-bold text-slate-900 inline-flex items-center gap-2">
                    <svg class="h-5 w-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Selesai Kunjungan & Check-out</span>
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Isi hasil kunjungan dan catatan sebelum menyelesaikan tugas.</p>
            </div>
            <form id="formCheckOut" method="POST" action="{{ route('visit-tasks.status', $visitTask) }}" enctype="multipart/form-data" class="p-5 sm:p-6 space-y-4 overflow-y-auto max-h-[calc(92vh-100px)]">
                @csrf
                <input type="hidden" name="status" value="SELESAI">
                <input type="hidden" name="latitude" id="checkOutLat">
                <input type="hidden" name="longitude" id="checkOutLng">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Hasil Kunjungan <span class="text-red-500">*</span></label>
                    <textarea name="visit_result" required rows="3" placeholder="Contoh: Toko memesan 10 karton kopi, setuju pasang spanduk..."
                        class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-blue-500 focus:ring-blue-500"></textarea>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Catatan Tindak Lanjut (Opsional)</label>
                    <textarea name="visit_notes" rows="2" placeholder="Catatan tambahan, tanggal penagihan berikutnya..."
                        class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-blue-500 focus:ring-blue-500"></textarea>
                </div>

                {{-- UPLOAD FOTO DOKUMENTASI DENGAN MINIMAL MB --}}
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">Foto Dokumentasi Lapangan (Opsional)</label>
                        <span class="inline-flex items-center gap-1 text-[10px] font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                            <svg class="h-3 w-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            Kompresi Otomatis (Minimal MB)
                        </span>
                    </div>

                    <div id="photoDropArea" class="relative rounded-xl border-2 border-dashed border-slate-300 hover:border-blue-500 bg-slate-50/70 p-4 transition text-center cursor-pointer">
                        <input type="file" id="inputDocPhoto" name="documentation_image" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" onchange="handleDocPhotoSelected(event)">
                        
                        <div id="photoPlaceholder" class="space-y-1.5 pointer-events-none">
                            <div class="mx-auto flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 text-blue-600">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <div class="text-xs font-bold text-slate-700">Ambil Foto Kamera atau Pilih Gambar</div>
                            <p class="text-[11px] text-slate-500">Foto toko, nota/invoice, atau produk di outlet.</p>
                            <p class="text-[10px] text-blue-800 font-medium">Foto otomatis dioptimalkan ukurannya (hemat kuota & minimal MB, maks 5 MB).</p>
                        </div>

                        {{-- Preview Box when photo selected --}}
                        <div id="photoPreviewBox" class="hidden flex items-center gap-3 text-left">
                            <img id="photoPreviewImg" src="" alt="Preview" style="width: 80px; height: 60px; min-width: 80px; max-width: 80px; object-fit: cover; display: block;" class="rounded-lg border border-slate-300 shadow-sm shrink-0">
                            <div class="flex-1 min-w-0">
                                <div class="text-xs font-bold text-slate-900 truncate" id="photoFileName">foto-lapangan.jpg</div>
                                <div class="text-[11px] text-emerald-700 font-semibold mt-0.5" id="photoFileSize">Ukuran: -</div>
                                <div class="pt-1">
                                    <button type="button" onclick="resetDocPhoto(event)" class="text-[11px] font-bold text-rose-600 hover:text-rose-800 underline relative z-20">Hapus / Ganti Foto</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SECTION PEMESANAN PRODUK / TAKING ORDER (OPSIONAL) --}}
                <div class="rounded-xl border border-blue-200 bg-amber-50/40 p-4 transition">
                    <div class="flex items-center justify-between">
                        <label for="toggleHasOrder" class="flex items-center gap-2.5 cursor-pointer select-none">
                            <input type="checkbox" id="toggleHasOrder" name="has_order" value="1" onchange="toggleOrderSection(this.checked)" class="h-4 w-4 rounded border-slate-300 text-amber-500 focus:ring-amber-400">
                            <div>
                                <span class="text-xs font-bold uppercase tracking-wider text-slate-900 inline-flex items-center gap-1.5">
                                    <svg class="h-4 w-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                    Catat Pemesanan Produk / Taking Order
                                </span>
                                <span class="ml-1 inline-flex items-center text-[10px] font-bold text-blue-700 bg-blue-100 px-2 py-0.5 rounded-full">Opsional</span>
                            </div>
                        </label>
                    </div>
                    <p class="text-[11px] text-slate-500 mt-1 pl-6">Centang jika kios/pelanggan memesan produk saat kunjungan berlangsung.</p>

                    {{-- ORDER FORM CONTAINER (COLLAPSIBLE) --}}
                    <div id="orderFormContainer" class="hidden mt-4 pt-4 border-t border-blue-200/80 space-y-4">
                        {{-- ITEM REPEATER --}}
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-bold text-slate-800">Daftar Produk yang Dipesan</span>
                                <button type="button" onclick="addOrderItemRow()" class="inline-flex items-center gap-1 text-xs font-bold text-blue-700 hover:text-blue-800 bg-blue-100/90 hover:bg-amber-200 px-2.5 py-1 rounded-lg transition shadow-2xs">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                    Tambah Produk
                                </button>
                            </div>

                            <div id="orderItemsList" class="space-y-2.5">
                                {{-- Dynamically populated via JavaScript --}}
                            </div>
                        </div>

                        {{-- RINGKASAN TOTAL HARGA --}}
                        <div class="rounded-xl bg-white p-3.5 border border-blue-200 flex items-center justify-between shadow-xs">
                            <div>
                                <div class="text-[11px] text-slate-500 font-medium">Total Nilai Pesanan (<span id="totalItemsCount">0</span> item)</div>
                                <div class="text-base font-extrabold text-slate-900" id="totalAmountDisplay">Rp 0</div>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-200">
                                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                    Otomatis Tersambung
                                </span>
                            </div>
                        </div>

                        {{-- METODE PEMBAYARAN --}}
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Metode Pembayaran <span class="text-red-500">*</span></label>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                <label class="payment-method-pill relative flex flex-col items-center justify-center p-2.5 rounded-xl border border-amber-500 bg-amber-50/70 ring-2 ring-amber-400/30 cursor-pointer text-center transition">
                                    <input type="radio" name="order_payment_method" value="tunai" checked onchange="handlePaymentMethodChange(this.value)" class="sr-only">
                                    <span class="text-xs font-bold text-slate-800">Tunai / Cash</span>
                                    <span class="text-[10px] text-emerald-600 font-semibold">Lunas</span>
                                </label>
                                <label class="payment-method-pill relative flex flex-col items-center justify-center p-2.5 rounded-xl border border-slate-200 bg-white hover:border-amber-400 cursor-pointer text-center transition">
                                    <input type="radio" name="order_payment_method" value="transfer" onchange="handlePaymentMethodChange(this.value)" class="sr-only">
                                    <span class="text-xs font-bold text-slate-800">Transfer</span>
                                    <span class="text-[10px] text-emerald-600 font-semibold">Lunas</span>
                                </label>
                                <label class="payment-method-pill relative flex flex-col items-center justify-center p-2.5 rounded-xl border border-slate-200 bg-white hover:border-amber-400 cursor-pointer text-center transition">
                                    <input type="radio" name="order_payment_method" value="tempo" onchange="handlePaymentMethodChange(this.value)" class="sr-only">
                                    <span class="text-xs font-bold text-slate-800">Tempo / Tenor</span>
                                    <span class="text-[10px] text-blue-600 font-semibold">Kredit Piutang</span>
                                </label>
                                <label class="payment-method-pill relative flex flex-col items-center justify-center p-2.5 rounded-xl border border-slate-200 bg-white hover:border-amber-400 cursor-pointer text-center transition">
                                    <input type="radio" name="order_payment_method" value="dp" onchange="handlePaymentMethodChange(this.value)" class="sr-only">
                                    <span class="text-xs font-bold text-slate-800">Uang Muka (DP)</span>
                                    <span class="text-[10px] text-indigo-600 font-semibold">DP + Tenor</span>
                                </label>
                            </div>
                        </div>

                        {{-- OPSI TENOR / JATUH TEMPO (TAMPIL JIKA METODE TEMPO ATAU DP) --}}
                        <div id="tempoOptionsContainer" class="hidden rounded-xl bg-amber-50/80 border border-blue-200 p-3.5 space-y-3">
                            {{-- INPUT DP JIKA METODE DP DIPILIH --}}
                            <div id="dpInputWrapper" class="hidden">
                                <label class="block text-xs font-bold text-slate-700 mb-1">Nominal Uang Muka (DP) yang Dibayar Sekarang (Rp) <span class="text-red-500">*</span></label>
                                <input type="number" name="order_down_payment" id="inputOrderDp" min="0" step="1000" placeholder="Contoh: 100000" oninput="calculateOrderTotals()" class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-blue-500 focus:ring-blue-500">
                                <div class="flex items-center gap-2 mt-1.5">
                                    <span class="text-[11px] text-slate-500">Pilihan cepat:</span>
                                    <button type="button" onclick="setQuickDpPercentage(0.2)" class="text-[11px] font-semibold text-slate-600 bg-white hover:bg-slate-100 border border-slate-200 px-2 py-0.5 rounded">20%</button>
                                    <button type="button" onclick="setQuickDpPercentage(0.3)" class="text-[11px] font-semibold text-slate-600 bg-white hover:bg-slate-100 border border-slate-200 px-2 py-0.5 rounded">30%</button>
                                    <button type="button" onclick="setQuickDpPercentage(0.5)" class="text-[11px] font-semibold text-slate-600 bg-white hover:bg-slate-100 border border-slate-200 px-2 py-0.5 rounded">50%</button>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Jangka Waktu Tenor / Jatuh Tempo Pelunasan</label>
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 mb-2">
                                    <button type="button" onclick="setTempoDays(7, this)" class="tempo-quick-btn text-xs font-semibold py-1.5 px-2 rounded-lg border border-slate-200 bg-white hover:border-amber-400 text-slate-700 text-center transition">7 Hari</button>
                                    <button type="button" onclick="setTempoDays(14, this)" class="tempo-quick-btn active-tempo text-xs font-semibold py-1.5 px-2 rounded-lg border border-amber-500 bg-blue-600 text-white text-center transition">14 Hari</button>
                                    <button type="button" onclick="setTempoDays(30, this)" class="tempo-quick-btn text-xs font-semibold py-1.5 px-2 rounded-lg border border-slate-200 bg-white hover:border-amber-400 text-slate-700 text-center transition">30 Hari</button>
                                    <button type="button" onclick="setTempoDays(45, this)" class="tempo-quick-btn text-xs font-semibold py-1.5 px-2 rounded-lg border border-slate-200 bg-white hover:border-amber-400 text-slate-700 text-center transition">45 Hari</button>
                                </div>
                                <input type="hidden" name="order_tempo_days" id="inputOrderTempoDays" value="14">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs text-slate-500 shrink-0">Atau pilih tanggal jatuh tempo:</span>
                                    <input type="date" name="order_due_date" id="inputOrderDueDate" min="{{ date('Y-m-d') }}" onchange="handleCustomDueDateSelected(this.value)" class="w-full rounded-lg border-slate-300 text-xs shadow-xs focus:border-blue-500 focus:ring-blue-500">
                                </div>
                            </div>

                            {{-- LIVE CALCULATION BREAKDOWN --}}
                            <div class="rounded-lg bg-white/95 p-3 border border-blue-200/80 text-xs space-y-1.5 shadow-2xs">
                                <div class="flex justify-between text-slate-600">
                                    <span>Sisa Piutang Kios:</span>
                                    <span class="font-bold text-rose-600 text-sm" id="remainingReceivableDisplay">Rp 0</span>
                                </div>
                                <div class="flex justify-between text-slate-600">
                                    <span>Tanggal Jatuh Tempo:</span>
                                    <span class="font-bold text-slate-800" id="dueDatePreviewDisplay">-</span>
                                </div>
                                <div class="text-[11px] text-blue-800 pt-1.5 border-t border-slate-100 flex items-center gap-1.5">
                                    <svg class="h-3.5 w-3.5 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span>Piutang ini otomatis masuk ke modul <strong>Laporan Keuangan & Piutang Kios</strong>.</span>
                                </div>
                            </div>
                        </div>

                        {{-- CATATAN PESANAN (OPSIONAL) --}}
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Catatan Pemesanan (Opsional)</label>
                            <input type="text" name="order_notes" placeholder="Contoh: Titip ke kasir, kirim besok pagi..." class="w-full rounded-lg border-slate-300 text-xs shadow-xs focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                    <button type="button" onclick="document.getElementById('modalCheckOut').classList.replace('flex', 'hidden')" class="rounded-lg border border-slate-300 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">Batal</button>
                    <button type="button" id="btnSubmitCheckOut" onclick="handleCheckOutSubmit()" class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-5 py-2 text-xs font-bold text-white hover:bg-emerald-700 transition">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Simpan & Selesaikan Kunjungan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- LIGHTBOX MODAL UNTUK MELIHAT FOTO DOKUMENTASI (ADMIN / PIMPINAN / KADIV / SALES) --}}
    <div id="modalPhotoLightbox" class="fixed inset-0 z-[1200] hidden items-center justify-center bg-slate-950/85 p-4 backdrop-blur-sm animate-fadeIn">
        <div class="relative max-w-4xl w-full bg-slate-900 rounded-2xl overflow-hidden shadow-2xl border border-slate-800">
            <div class="flex items-center justify-between px-5 py-3 border-b border-slate-800 bg-slate-950">
                <div class="flex items-center gap-2 text-white text-sm font-bold truncate pr-4">
                    <svg class="h-4 w-4 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span id="lightboxTitle" class="truncate">Foto Dokumentasi Laporan Kunjungan</span>
                </div>
                <div class="flex items-center gap-3 shrink-0">
                    <a id="lightboxDownloadBtn" href="#" download class="inline-flex items-center gap-1 text-xs text-amber-400 hover:text-amber-300 font-semibold px-2 py-1 rounded bg-slate-800/80">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        <span>Unduh</span>
                    </a>
                    <button type="button" onclick="closeImageLightbox()" class="text-slate-400 hover:text-white text-2xl font-bold leading-none p-1">&times;</button>
                </div>
            </div>
            <div class="p-2 sm:p-4 flex items-center justify-center bg-black/80 max-h-[75vh] overflow-auto">
                <img id="lightboxImg" src="" alt="Dokumentasi Kunjungan" class="max-h-[70vh] w-auto max-w-full rounded-lg object-contain shadow-2xl">
            </div>
        </div>
    </div>

    {{-- MODAL REJECT ON DETAIL --}}
    <div id="modalRejectDetail" class="fixed inset-0 z-[1000] hidden items-center justify-center bg-slate-950/70 p-4 backdrop-blur-xs">
        <div class="w-full max-w-2xl rounded-2xl bg-white shadow-2xl flex flex-col max-h-[92vh] my-auto">
            <div class="border-b border-slate-100 px-6 py-4">
                <h3 class="text-lg font-bold text-slate-900 inline-flex items-center gap-2">
                    <svg class="h-5 w-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Tolak Tugas Kunjungan</span>
                </h3>
            </div>
            <form method="POST" action="{{ route('visit-tasks.reject', $visitTask) }}" class="p-6 space-y-4">
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
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Alasan Lengkap <span class="text-red-500">*</span></label>
                    <textarea name="reason" required minlength="10" rows="3" placeholder="Jelaskan alasan penolakan..."
                        class="w-full rounded-lg border-slate-300 text-sm shadow-xs focus:border-blue-500 focus:ring-blue-500"></textarea>
                </div>
                <div class="flex items-center justify-end gap-3">
                    <button type="button" onclick="document.getElementById('modalRejectDetail').classList.replace('flex', 'hidden')" class="rounded-lg border border-slate-300 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">Batal</button>
                    <button type="submit" class="rounded-lg bg-rose-600 px-5 py-2 text-xs font-bold text-white hover:bg-rose-700 transition">Tolak Tugas</button>
                </div>
            </form>
        </div>
    </div>

    {{-- LIGHTBOX MODAL UNTUK MELIHAT FOTO DOKUMENTASI (ADMIN / PIMPINAN / KADIV / SALES) --}}
    <div id="modalPhotoLightbox" class="fixed inset-0 z-[1200] hidden items-center justify-center bg-slate-950/85 p-4 backdrop-blur-sm animate-fadeIn">
        <div class="relative max-w-4xl w-full bg-slate-900 rounded-2xl overflow-hidden shadow-2xl border border-slate-800">
            <div class="flex items-center justify-between px-5 py-3 border-b border-slate-800 bg-slate-950">
                <div class="flex items-center gap-2 text-white text-sm font-bold truncate pr-4">
                    <svg class="h-4 w-4 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span id="lightboxTitle" class="truncate">Foto Dokumentasi Laporan Kunjungan</span>
                </div>
                <div class="flex items-center gap-3 shrink-0">
                    <a id="lightboxDownloadBtn" href="#" download class="inline-flex items-center gap-1 text-xs text-amber-400 hover:text-amber-300 font-semibold px-2 py-1 rounded bg-slate-800/80">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        <span>Unduh</span>
                    </a>
                    <button type="button" onclick="closeImageLightbox()" class="text-slate-400 hover:text-white text-2xl font-bold leading-none p-1">&times;</button>
                </div>
            </div>
            <div class="p-2 sm:p-4 flex items-center justify-center bg-black/80 max-h-[75vh] overflow-auto">
                <img id="lightboxImg" src="" alt="Dokumentasi Kunjungan" class="max-h-[70vh] w-auto max-w-full rounded-lg object-contain shadow-2xl">
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        @if($visitTask->customer->latitude && $visitTask->customer->longitude)
        const lat = {{ $visitTask->customer->latitude }};
        const lng = {{ $visitTask->customer->longitude }};
        if (window.L) {
            const map = window.L.map('outletMap').setView([lat, lng], 15);
        window.addEventListener('resize', () => { if (map) map.invalidateSize(); });
        window.addEventListener('orientationchange', () => { setTimeout(() => { if (map) map.invalidateSize(); }, 250); });
            window.L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap'
            }).addTo(map);
            window.L.marker([lat, lng]).addTo(map)
                .bindPopup('<strong>{{ addslashes($visitTask->customer->name) }}</strong>')
                .openPopup();
        }
        @endif
    });

    let isSubmittingTrip = false;
    function handleStartTripWithGps() {
        if (isSubmittingTrip) return;
        isSubmittingTrip = true;

        const btn = document.getElementById('btnStartTrip');
        if (btn) {
            btn.innerHTML = '<span class="inline-flex items-center gap-2"><svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg><span>Mengaktifkan GPS & memulai perjalanan...</span></span>';
            btn.disabled = true;
            btn.classList.add('opacity-75', 'cursor-not-allowed');
        }

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                async pos => {
                    try {
                        // Start tracking session in background
                        await fetch('/api/tracking/start', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ task_id: {{ $visitTask->id }} })
                        });
                        // Send initial location
                        await fetch('/api/tracking/location', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                latitude: pos.coords.latitude,
                                longitude: pos.coords.longitude,
                                accuracy: pos.coords.accuracy,
                                speed: pos.coords.speed
                            })
                        });
                    } catch (e) {
                        console.error('Tracking sync error', e);
                    }
                    document.getElementById('formStartTrip').submit();
                },
                err => {
                    // Fallback submit even if geolocation prompt dismissed or error
                    document.getElementById('formStartTrip').submit();
                },
                { enableHighAccuracy: true, timeout: 5000 }
            );
        } else {
            document.getElementById('formStartTrip').submit();
        }
    }

    // Auto location sender during journey
    @if(auth()->user()->hasRole('Sales Motoris') && in_array($visitTask->status, ['DALAM_PERJALANAN', 'TIBA_DI_LOKASI', 'SEDANG_KUNJUNGAN']))
        if (navigator.geolocation) {
            function syncCurrentGps() {
                navigator.geolocation.getCurrentPosition(async pos => {
                    try {
                        await fetch('/api/tracking/location', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                latitude: pos.coords.latitude,
                                longitude: pos.coords.longitude,
                                accuracy: pos.coords.accuracy,
                                speed: pos.coords.speed
                            })
                        });
                        const el = document.getElementById('liveGpsLastSync');
                        if (el) el.textContent = 'Terkirim: ' + new Date().toLocaleTimeString('id-ID');
                    } catch (e) {}
                }, null, { enableHighAccuracy: true, timeout: 5000 });
            }
            syncCurrentGps();
            setInterval(syncCurrentGps, 20000);
        }
    @endif

    let isSubmittingCheckIn = false;
    function handleCheckIn() {
        if (isSubmittingCheckIn) return;

        if (!navigator.geolocation) {
            alert('Geolokasi tidak didukung oleh browser Anda.');
            return;
        }

        isSubmittingCheckIn = true;
        const btn = document.getElementById('btnCheckIn');
        if (btn) {
            btn.innerHTML = '<span class="inline-flex items-center gap-2"><svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg><span>Mengambil lokasi GPS...</span></span>';
            btn.disabled = true;
            btn.classList.add('opacity-75', 'cursor-not-allowed');
        }

        navigator.geolocation.getCurrentPosition(
            pos => {
                document.getElementById('checkInLat').value = pos.coords.latitude;
                document.getElementById('checkInLng').value = pos.coords.longitude;
                document.getElementById('formCheckIn').submit();
            },
            err => {
                const isInsecureOrigin = err.message && err.message.toLowerCase().includes('secure origins');
                const titleText = isInsecureOrigin ? 'GPS Dibatasi Browser (HTTP IP Lokal)' : 'Gagal Mengambil Lokasi GPS';
                const htmlText = isInsecureOrigin 
                    ? '<p class="text-xs text-slate-600 mb-2">Browser Chrome/Edge memblokir sensor GPS pada alamat IP HTTP non-HTTPS (<strong>' + window.location.host + '</strong>).</p><p class="text-xs text-slate-500">Anda dapat tetap melanjutkan Check-in sekarang tanpa titik koordinat GPS, atau aktifkan izin IP di pengaturan <em>chrome://flags</em>.</p>'
                    : '<p class="text-xs text-slate-600 mb-2">' + err.message + '</p><p class="text-xs text-slate-500">Apakah Anda ingin tetap melanjutkan Check-in kedatangan di outlet?</p>';

                const doFallbackSubmit = () => {
                    document.getElementById('formCheckIn').submit();
                };

                const resetBtn = () => {
                    if (btn) {
                        btn.innerHTML = '<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg><span>Tiba di Lokasi & Check-in GPS</span>';
                        btn.disabled = false;
                        btn.classList.remove('opacity-75', 'cursor-not-allowed');
                    }
                    isSubmittingCheckIn = false;
                };

                if (window.Swal) {
                    window.Swal.fire({
                        icon: 'warning',
                        title: titleText,
                        html: htmlText,
                        showCancelButton: true,
                        confirmButtonText: 'Tetap Check-in Tanpa GPS',
                        cancelButtonText: 'Batal',
                        confirmButtonColor: '#0f172a',
                        cancelButtonColor: '#64748b'
                    }).then(res => {
                        if (res.isConfirmed) {
                            doFallbackSubmit();
                        } else {
                            resetBtn();
                        }
                    });
                    return;
                } else {
                    if (confirm('Gagal mengambil titik GPS (' + err.message + '). Tetap lakukan Check-in di outlet?')) {
                        doFallbackSubmit();
                        return;
                    }
                }
                if (btn) {
                    btn.innerHTML = '<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg><span>Tiba di Lokasi & Check-in GPS</span>';
                    btn.disabled = false;
                    btn.classList.remove('opacity-75', 'cursor-not-allowed');
                }
                isSubmittingCheckIn = false;
            },
            { enableHighAccuracy: true, timeout: 10000 }
        );
    }

    let isSubmittingCheckOut = false;
    function handleCheckOutSubmit() {
        if (isSubmittingCheckOut) return;

        const form = document.getElementById('formCheckOut');
        const visitResult = form.querySelector('textarea[name="visit_result"]');
        if (visitResult && !visitResult.value.trim()) {
            alert('Hasil kunjungan wajib diisi sebelum menyelesaikan tugas.');
            visitResult.focus();
            return;
        }

        // Validasi Pemesanan Produk / Taking Order jika dicentang
        const toggleHasOrder = document.getElementById('toggleHasOrder');
        if (toggleHasOrder && toggleHasOrder.checked) {
            let validItemsCount = 0;
            const rows = document.querySelectorAll('.order-item-row');
            rows.forEach(r => {
                const sel = r.querySelector('.select-product');
                const qty = r.querySelector('input[type="number"]');
                if (sel && sel.value && qty && parseInt(qty.value) > 0) {
                    validItemsCount++;
                }
            });

            if (validItemsCount === 0) {
                alert('Silakan pilih minimal 1 produk pesanan, atau hilangkan centang pemesanan jika tidak ada produk yang dipesan.');
                return;
            }

            const paymentMethod = document.querySelector('input[name="order_payment_method"]:checked')?.value || 'tunai';
            if (paymentMethod === 'dp') {
                const dpInput = document.getElementById('inputOrderDp');
                const dpVal = parseFloat(dpInput?.value || 0);
                if (dpVal <= 0) {
                    alert('Harap masukkan nominal Uang Muka (DP) yang dibayar.');
                    dpInput?.focus();
                    return;
                }
            }
        }

        if (false) {
            visitResult.focus();
            return;
        }

        isSubmittingCheckOut = true;
        const btn = document.getElementById('btnSubmitCheckOut');
        if (btn) {
            btn.disabled = true;
            btn.classList.add('opacity-75', 'cursor-not-allowed');
            btn.innerHTML = '<span class="inline-flex items-center gap-2"><svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg><span>Menyimpan & menyelesaikan...</span></span>';
        }

        const doSubmit = () => {
            form.submit();
        };

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                pos => {
                    document.getElementById('checkOutLat').value = pos.coords.latitude;
                    document.getElementById('checkOutLng').value = pos.coords.longitude;
                    doSubmit();
                },
                () => {
                    // Submit anyway without GPS if failed
                    doSubmit();
                },
                { enableHighAccuracy: true, timeout: 5000 }
            );
        } else {
            doSubmit();
        }
    }

    // Photo Upload Optimization with Minimal MB (Client-side HTML5 Canvas Auto Compression)
    function handleDocPhotoSelected(event) {
        const file = event.target.files[0];
        if (!file) return;

        if (!file.type.match('image.*')) {
            alert('File yang dipilih harus berupa file foto atau gambar (JPG/PNG/WEBP).');
            resetDocPhoto();
            return;
        }

        const originalSizeMB = (file.size / (1024 * 1024)).toFixed(2);
        const reader = new FileReader();

        reader.onload = function(e) {
            const img = new Image();
            img.onload = function() {
                const canvas = document.createElement('canvas');
                let width = img.width;
                let height = img.height;
                const maxDim = 1600; // Optimal sharp document resolution with minimal MB

                if (width > height && width > maxDim) {
                    height = Math.round((height * maxDim) / width);
                    width = maxDim;
                } else if (height > maxDim) {
                    width = Math.round((width * maxDim) / height);
                    height = maxDim;
                }

                canvas.width = width;
                canvas.height = height;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0, width, height);

                // Compress to lightweight JPEG with 0.78 quality (~300KB-800KB)
                canvas.toBlob(function(blob) {
                    let previewUrl = e.target.result;
                    let sizeLabel = originalSizeMB + ' MB';

                    if (blob) {
                        const sizeKB = Math.round(blob.size / 1024);
                        const compressedSizeText = sizeKB >= 1024 ? (sizeKB / 1024).toFixed(2) + ' MB' : sizeKB + ' KB';
                        sizeLabel = `${compressedSizeText} (Dioptimasi dari ${originalSizeMB} MB)`;
                        previewUrl = URL.createObjectURL(blob);

                        try {
                            const compressedFile = new File([blob], file.name.replace(/\.[^/.]+$/, "") + ".jpg", {
                                type: 'image/jpeg',
                                lastModified: Date.now()
                            });
                            const dt = new DataTransfer();
                            dt.items.add(compressedFile);
                            document.getElementById('inputDocPhoto').files = dt.files;
                        } catch (err) {
                            console.warn('DataTransfer File replacement not supported, using standard upload', err);
                        }
                    }

                    document.getElementById('photoPlaceholder').classList.add('hidden');
                    document.getElementById('photoPreviewBox').classList.remove('hidden');
                    document.getElementById('photoPreviewImg').src = previewUrl;
                    document.getElementById('photoFileName').textContent = file.name;
                    document.getElementById('photoFileSize').textContent = `Ukuran: ${sizeLabel}`;
                }, 'image/jpeg', 0.78);
            };
            img.src = e.target.result;
        };
        reader.readAsDataURL(file);
    }

    function resetDocPhoto(e) {
        if (e) e.stopPropagation();
        const input = document.getElementById('inputDocPhoto');
        if (input) input.value = '';
        document.getElementById('photoPlaceholder').classList.remove('hidden');
        document.getElementById('photoPreviewBox').classList.add('hidden');
        document.getElementById('photoPreviewImg').src = '';
    }

    // Lightbox Modal Helpers for All Roles
    function openImageLightbox(src, title) {
        if (!src) return;
        const modal = document.getElementById('modalPhotoLightbox');
        const img = document.getElementById('lightboxImg');
        const titleEl = document.getElementById('lightboxTitle');
        const dlBtn = document.getElementById('lightboxDownloadBtn');

        if (img) img.src = src;
        if (titleEl && title) titleEl.textContent = title;
        if (dlBtn) dlBtn.href = src;
        if (modal) modal.classList.replace('hidden', 'flex');
    }

    // ==========================================
    // MODUL PEMESANAN PRODUK / TAKING ORDER DI CHECK-OUT
    // ==========================================
    const availableProducts = @json($products ?? []);
    let orderRowIndex = 0;

    function formatRupiah(num) {
        return new Intl.NumberFormat('id-ID', { maximumFractionDigits: 0 }).format(Math.round(num || 0));
    }

    function toggleOrderSection(isChecked) {
        const container = document.getElementById('orderFormContainer');
        if (!container) return;
        if (isChecked) {
            container.classList.remove('hidden');
            const list = document.getElementById('orderItemsList');
            if (list && list.children.length === 0) {
                addOrderItemRow();
            }
            updatePaymentMethodStyles();
            calculateOrderTotals();
        } else {
            container.classList.add('hidden');
        }
    }

    function addOrderItemRow(selectedProductId = '', qty = 1) {
        const list = document.getElementById('orderItemsList');
        if (!list) return;
        const rowId = orderRowIndex++;

        let productOptions = '<option value="">-- Pilih Produk --</option>';
        availableProducts.forEach(p => {
            const isSel = selectedProductId && selectedProductId == p.id ? 'selected' : '';
            const stockInfo = (p.stock_carried && p.stock_carried > 0) ? `${p.stock_carried} ${p.unit || 'pcs'} di motor` : `${p.stock_on_hand || 0} ${p.unit || 'pcs'} di gudang`;
            const cleanName = (p.name || '').replace(/"/g, '&quot;');
            productOptions += `<option value="${p.id}" data-price="${p.price}" data-unit="${p.unit || 'pcs'}" data-sku="${p.sku || ''}" data-name="${cleanName}" ${isSel}>${p.name} - Rp ${formatRupiah(p.price)} (${stockInfo})</option>`;
        });

        const rowHtml = `
            <div id="orderRow_${rowId}" class="order-item-row rounded-xl border border-slate-200 bg-white p-3 shadow-xs space-y-2 transition hover:border-blue-300 shadow-blue-50/50">
                <div class="flex items-start justify-between gap-2">
                    <div class="flex-1 min-w-0">
                        <select name="order_items[${rowId}][product_id]" onchange="handleProductSelected(${rowId})" class="select-product w-full rounded-lg border-slate-300 text-xs shadow-xs focus:border-blue-500 focus:ring-blue-500">
                            ${productOptions}
                        </select>
                    </div>
                    <button type="button" onclick="removeOrderItemRow(${rowId})" title="Hapus Produk" class="text-slate-400 hover:text-rose-600 transition p-1.5 rounded-lg hover:bg-rose-50 shrink-0">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </div>

                <div class="flex items-center justify-between gap-3 pt-1 border-t border-slate-100 text-xs">
                    <div class="flex items-center gap-1.5">
                        <span class="text-slate-500 text-[11px]">Jumlah:</span>
                        <div class="inline-flex items-center rounded-lg border border-slate-200 shadow-2xs overflow-hidden">
                            <button type="button" onclick="adjustItemQty(${rowId}, -1)" class="px-2 py-1 bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold border-r border-slate-200 select-none">-</button>
                            <input type="number" name="order_items[${rowId}][quantity]" id="qty_${rowId}" value="${qty}" min="1" oninput="calculateOrderTotals()" onchange="calculateOrderTotals()" class="w-14 text-center border-0 p-1 text-xs font-bold text-slate-800 focus:ring-0">
                            <button type="button" onclick="adjustItemQty(${rowId}, 1)" class="px-2 py-1 bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold border-l border-slate-200 select-none">+</button>
                        </div>
                        <span class="text-slate-400 text-[11px]" id="unitLabel_${rowId}">pcs</span>
                    </div>

                    <div class="text-right">
                        <span class="text-[10px] text-slate-400 block" id="unitPriceLabel_${rowId}">@ Rp 0</span>
                        <span class="text-xs font-bold text-slate-900" id="subtotalDisplay_${rowId}">Rp 0</span>
                    </div>
                </div>
            </div>
        `;

        list.insertAdjacentHTML('beforeend', rowHtml);
        handleProductSelected(rowId);
    }

    function removeOrderItemRow(rowId) {
        const row = document.getElementById(`orderRow_${rowId}`);
        if (row) {
            row.remove();
            calculateOrderTotals();
        }
    }

    function adjustItemQty(rowId, delta) {
        const input = document.getElementById(`qty_${rowId}`);
        if (input) {
            let current = parseInt(input.value) || 1;
            let nextVal = Math.max(1, current + delta);
            input.value = nextVal;
            calculateOrderTotals();
        }
    }

    function handleProductSelected(rowId) {
        const row = document.getElementById(`orderRow_${rowId}`);
        if (!row) return;

        const select = row.querySelector('.select-product');
        const opt = select.options[select.selectedIndex];
        const price = opt ? parseFloat(opt.getAttribute('data-price') || 0) : 0;
        const unit = opt ? (opt.getAttribute('data-unit') || 'pcs') : 'pcs';

        const unitLabel = document.getElementById(`unitLabel_${rowId}`);
        const unitPriceLabel = document.getElementById(`unitPriceLabel_${rowId}`);
        if (unitLabel) unitLabel.textContent = unit;
        if (unitPriceLabel) unitPriceLabel.textContent = `@ Rp ${formatRupiah(price)}`;

        calculateOrderTotals();
    }

    function calculateOrderTotals() {
        const rows = document.querySelectorAll('.order-item-row');
        let totalAmount = 0;
        let totalItems = 0;

        rows.forEach(row => {
            const select = row.querySelector('.select-product');
            const qtyInput = row.querySelector('input[type="number"]');
            const rowId = row.id.replace('orderRow_', '');
            const subtotalEl = document.getElementById(`subtotalDisplay_${rowId}`);

            if (select && select.value && qtyInput) {
                const opt = select.options[select.selectedIndex];
                const price = opt ? parseFloat(opt.getAttribute('data-price') || 0) : 0;
                const qty = parseInt(qtyInput.value) || 0;
                const subtotal = price * qty;
                totalAmount += subtotal;
                totalItems += qty;

                if (subtotalEl) {
                    subtotalEl.textContent = `Rp ${formatRupiah(subtotal)}`;
                }
            } else if (subtotalEl) {
                subtotalEl.textContent = 'Rp 0';
            }
        });

        const totalCountEl = document.getElementById('totalItemsCount');
        const totalDisplayEl = document.getElementById('totalAmountDisplay');
        if (totalCountEl) totalCountEl.textContent = totalItems;
        if (totalDisplayEl) totalDisplayEl.textContent = `Rp ${formatRupiah(totalAmount)}`;

        // Calculate DP and Piutang
        const selectedMethod = document.querySelector('input[name="order_payment_method"]:checked')?.value || 'tunai';
        const dpInput = document.getElementById('inputOrderDp');
        let dpAmount = 0;
        if (selectedMethod === 'dp' && dpInput) {
            dpAmount = parseFloat(dpInput.value) || 0;
            if (dpAmount > totalAmount) {
                dpAmount = totalAmount;
                dpInput.value = dpAmount;
            }
        }

        let remainingReceivable = 0;
        if (selectedMethod === 'tempo') {
            remainingReceivable = totalAmount;
        } else if (selectedMethod === 'dp') {
            remainingReceivable = Math.max(0, totalAmount - dpAmount);
        } else {
            remainingReceivable = 0;
        }

        const remainingEl = document.getElementById('remainingReceivableDisplay');
        if (remainingEl) {
            remainingEl.textContent = `Rp ${formatRupiah(remainingReceivable)}`;
        }

        updateDueDatePreview();
    }

    function handlePaymentMethodChange(method) {
        updatePaymentMethodStyles();

        const tempoContainer = document.getElementById('tempoOptionsContainer');
        const dpWrapper = document.getElementById('dpInputWrapper');
        const dpInput = document.getElementById('inputOrderDp');

        if (method === 'tempo') {
            tempoContainer.classList.remove('hidden');
            dpWrapper.classList.add('hidden');
            if (dpInput) dpInput.value = '';
        } else if (method === 'dp') {
            tempoContainer.classList.remove('hidden');
            dpWrapper.classList.remove('hidden');
            if (dpInput && !dpInput.value) {
                const totalText = document.getElementById('totalAmountDisplay')?.textContent || '0';
                const total = parseInt(totalText.replace(/[^0-9]/g, '')) || 0;
                if (total > 0) {
                    dpInput.value = Math.round((total * 0.3) / 1000) * 1000;
                }
            }
        } else {
            tempoContainer.classList.add('hidden');
            dpWrapper.classList.add('hidden');
            if (dpInput) dpInput.value = '';
        }

        calculateOrderTotals();
    }

    function updatePaymentMethodStyles() {
        const radios = document.querySelectorAll('input[name="order_payment_method"]');
        radios.forEach(r => {
            const label = r.closest('label');
            if (label) {
                if (r.checked) {
                    label.classList.add('border-amber-500', 'bg-amber-50/70', 'ring-2', 'ring-amber-400/30');
                    label.classList.remove('border-slate-200', 'bg-white');
                } else {
                    label.classList.remove('border-amber-500', 'bg-amber-50/70', 'ring-2', 'ring-amber-400/30');
                    label.classList.add('border-slate-200', 'bg-white');
                }
            }
        });
    }

    function setTempoDays(days, btn) {
        const inputDays = document.getElementById('inputOrderTempoDays');
        const inputDate = document.getElementById('inputOrderDueDate');
        if (inputDays) inputDays.value = days;
        if (inputDate) inputDate.value = '';

        document.querySelectorAll('.tempo-quick-btn').forEach(b => {
            b.classList.remove('border-amber-500', 'bg-blue-600', 'text-white', 'active-tempo');
            b.classList.add('border-slate-200', 'bg-white', 'text-slate-700');
        });
        if (btn) {
            btn.classList.add('border-amber-500', 'bg-blue-600', 'text-white', 'active-tempo');
            btn.classList.remove('border-slate-200', 'bg-white', 'text-slate-700');
        }

        updateDueDatePreview();
    }

    function handleCustomDueDateSelected(val) {
        document.querySelectorAll('.tempo-quick-btn').forEach(b => {
            b.classList.remove('border-amber-500', 'bg-blue-600', 'text-white', 'active-tempo');
            b.classList.add('border-slate-200', 'bg-white', 'text-slate-700');
        });
        updateDueDatePreview();
    }

    function updateDueDatePreview() {
        const previewEl = document.getElementById('dueDatePreviewDisplay');
        if (!previewEl) return;

        const inputDate = document.getElementById('inputOrderDueDate');
        const inputDays = document.getElementById('inputOrderTempoDays');

        let targetDate = new Date();
        if (inputDate && inputDate.value) {
            targetDate = new Date(inputDate.value);
        } else {
            const days = parseInt(inputDays?.value || '14');
            targetDate.setDate(targetDate.getDate() + days);
        }

        previewEl.textContent = targetDate.toLocaleDateString('id-ID', {
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        });
    }

    function setQuickDpPercentage(pct) {
        const totalText = document.getElementById('totalAmountDisplay')?.textContent || '0';
        const total = parseInt(totalText.replace(/[^0-9]/g, '')) || 0;
        const dpInput = document.getElementById('inputOrderDp');
        if (dpInput && total > 0) {
            dpInput.value = Math.round((total * pct) / 1000) * 1000;
            calculateOrderTotals();
        }
    }

    function closeImageLightbox() {
        const modal = document.getElementById('modalPhotoLightbox');
        if (modal) modal.classList.replace('flex', 'hidden');
    }
    </script>
</x-app-layout>

