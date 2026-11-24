<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[.22em] text-blue-600">Monitoring & Navigasi Lapangan</p>
                <h2 class="mt-1 text-2xl font-bold text-slate-900">Rute & Live Tracking Kunjungan</h2>
                <p class="text-xs text-slate-500 mt-0.5">Pemantauan real-time posisi Sales Motoris, navigasi rute ke outlet, dan status seluruh kunjungan lapangan.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <span id="liveStatusBadge" class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 border border-emerald-200 shrink-0">
                    <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span class="whitespace-nowrap">Live Tracking Aktif</span>
                </span>
                <button type="button" id="btnRefreshTracking" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 shadow-2xs hover:bg-slate-50 transition shrink-0" title="Segarkan posisi sales saat ini">
                    <svg class="h-3.5 w-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    <span class="whitespace-nowrap">Segarkan</span>
                </button>
                <button type="button" onclick="openPlanRouteModal()" class="inline-flex items-center gap-1.5 rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-bold text-white hover:bg-blue-700 shadow-xs transition shrink-0" title="Rencanakan rute navigasi perjalanan ke outlet">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                    <span class="whitespace-nowrap">Rencanakan Rute</span>
                </button>
                @can('kelola kunjungan')
                <button type="button" onclick="openCreateModal()" class="inline-flex items-center gap-1.5 rounded-lg bg-slate-950 px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-slate-800 transition shrink-0">
                    <svg class="h-4 w-4 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span class="whitespace-nowrap">Tambah Kunjungan</span>
                </button>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="min-h-[calc(100vh-65px)] bg-slate-100 py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">

            {{-- ALERT SESSION --}}
            @if(session('status'))
            <div class="flex items-center justify-between rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800 shadow-sm">
                <div class="flex items-center gap-2">
                    <svg class="h-4 w-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>{{ session('status') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900">&times;</button>
            </div>
            @endif

            {{-- 1. SALES MOTORIS GPS CONTROLLER BAR --}}
            @if(auth()->user()->hasRole('Sales Motoris'))
            <div class="rounded-2xl border border-blue-200 bg-white p-5 shadow-sm">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div id="salesGpsIcon" class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-600 border border-slate-200">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/></svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-sm font-bold text-slate-900">Sesi Tracking GPS Pribadi</h3>
                                <span id="salesSessionStatus" class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-0.5 text-[11px] font-semibold text-slate-600">
                                    Memeriksa sesi...
                                </span>
                            </div>
                            <p class="text-xs text-slate-500 mt-0.5" id="salesSessionDesc">
                                Mengirim koordinat posisi Anda ke Kepala Divisi & Supervisor selama jam operasional.
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" id="btnToggleTracking" class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-xs font-bold text-white shadow-sm hover:bg-emerald-700 transition">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                            <span>Mulai Sesi Tracking</span>
                        </button>
                    </div>
                </div>

                <div class="mt-3 rounded-lg bg-blue-50/70 p-3 text-[11px] text-amber-800 border border-blue-200/60 flex items-start gap-2">
                    <svg class="h-4 w-4 text-blue-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span><strong>Catatan Browser:</strong> Pelacakan lokasi memerlukan peramban tetap aktif di latar depan. Anda juga dapat langsung mengaktifkan GPS saat memulai perjalanan pada tugas kunjungan Anda.</span>
                </div>
            </div>
            @endif

            {{-- 2. FULL WIDTH MAP CARD (DI ATAS) --}}
            <div id="mapContainerCard" class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-5 shadow-sm space-y-4">
                {{-- MAP HEADER CONTROLS --}}
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 pb-3">
                    <div class="flex flex-wrap items-center gap-3">
                        <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                            <svg class="h-5 w-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                            <span>Peta Sebaran Kunjungan & Live Tracking Sales</span>
                        </h3>
                        <div class="flex items-center gap-2 text-xs">
                            <span class="font-bold text-slate-600">Tim:</span>
                            <select id="filterTeam" class="rounded-lg border-slate-300 text-xs py-1 focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Semua Tim</option>
                                @foreach($teams as $team)
                                    <option value="{{ $team->id }}">{{ $team->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- LEGEND --}}
                    <div class="flex flex-wrap items-center gap-3 text-xs text-slate-600">
                        <span class="flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-emerald-500"></span> Sedang Kunjungan</span>
                        <span class="flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-blue-500"></span> Perjalanan (OTW)</span>
                        <span class="flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-blue-600"></span> Titik Outlet</span>
                        <span class="flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-slate-400"></span> Offline</span>
                        <button type="button" id="btnResetMapView" class="inline-flex items-center gap-1 rounded-lg border border-slate-300 bg-white px-2.5 py-1 text-[11px] font-semibold text-slate-700 shadow-2xs hover:bg-slate-50 transition">
                            <svg class="h-3 w-3 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                            <span>Pusatkan</span>
                        </button>
                    </div>
                </div>

                {{-- FLOATING ACTIVE ROUTE & TRACKING INFO CARD --}}
                <div id="floatingRouteCard" class="hidden rounded-2xl bg-slate-950 text-white p-4 shadow-2xl border border-slate-800 space-y-3 animate-fadeIn">
                    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-800/80 pb-2.5">
                        <div class="flex items-center gap-3">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-400/20 text-sky-400">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                            </span>
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <h4 class="font-bold text-white text-sm" id="routeCardTitle">Rute Navigasi & Tracking</h4>
                                    <span id="routeCardStatusBadge" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-400/20 text-sky-400 border border-amber-400/30">
                                        Status
                                    </span>
                                </div>
                                <div class="text-xs text-slate-400 mt-0.5" id="routeCardSubtitle">Memuat data rute perjalanan...</div>
                            </div>
                        </div>
                        <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
                            <a id="btnOpenGoogleMaps" href="#" target="_blank" class="inline-flex flex-1 sm:flex-initial items-center justify-center gap-1.5 rounded-lg bg-amber-400 px-3 py-1.5 text-xs font-bold text-slate-950 hover:bg-amber-300 transition shadow-xs whitespace-nowrap">
                                <svg class="h-3.5 w-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                <span>Navigasi Google Maps</span>
                            </a>
                            <a id="btnOpenTaskDetail" href="#" target="_blank" class="inline-flex flex-1 sm:flex-initial items-center justify-center gap-1.5 rounded-lg border border-slate-700 bg-slate-900 px-3 py-1.5 text-xs font-semibold text-slate-300 hover:bg-slate-800 hover:text-white transition whitespace-nowrap">
                                <svg class="h-3.5 w-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                <span>Detail Tugas</span>
                            </a>
                            <button type="button" onclick="clearActiveRoute()" class="rounded-lg bg-slate-800 p-1.5 text-slate-400 hover:text-white transition shrink-0 ml-auto sm:ml-0" title="Tutup Rute">&times;</button>
                        </div>
                    </div>

                    {{-- METRICS ROW: DURASI ("BERAPA LAMA"), JARAK, OPERASIONAL, CHECK-IN --}}
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-xs pt-0.5">
                        {{-- Metric 1: Durasi Tracking ("berapa lama") --}}
                        <div class="rounded-xl bg-slate-900/90 p-2.5 border border-slate-800">
                            <div class="text-[10px] uppercase font-bold text-slate-400 flex items-center gap-1.5">
                                <svg class="h-3.5 w-3.5 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>Durasi Tracking</span>
                            </div>
                            <div class="mt-1 font-mono font-bold text-sm text-sky-400" id="routeDurationBadge">
                                -
                            </div>
                            <div class="text-[10px] text-slate-400 mt-0.5 truncate" id="routeDurationSub">
                                Berangkat &rarr; Selesai
                            </div>
                        </div>

                        {{-- Metric 2: Jarak Tempuh --}}
                        <div class="rounded-xl bg-slate-900/90 p-2.5 border border-slate-800">
                            <div class="text-[10px] uppercase font-bold text-slate-400 flex items-center gap-1.5">
                                <svg class="h-3.5 w-3.5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                                <span>Jarak Tempuh</span>
                            </div>
                            <div class="mt-1 font-mono font-bold text-sm text-cyan-400" id="routeDistanceBadge">
                                -
                            </div>
                            <div class="text-[10px] text-slate-400 mt-0.5 truncate" id="routeDistanceSub">
                                Estimasi jalan raya
                            </div>
                        </div>

                        {{-- Metric 3: Waktu Operasional --}}
                        <div class="rounded-xl bg-slate-900/90 p-2.5 border border-slate-800">
                            <div class="text-[10px] uppercase font-bold text-slate-400 flex items-center gap-1.5">
                                <svg class="h-3.5 w-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>Waktu Operasional</span>
                            </div>
                            <div class="mt-1 font-bold text-xs text-white" id="routeTimeBadge">
                                -
                            </div>
                            <div class="text-[10px] text-slate-400 mt-0.5 truncate" id="routeTimeSub">
                                Jam mulai &rarr; selesai
                            </div>
                        </div>

                        {{-- Metric 4: Check-in / Akurasi --}}
                        <div class="rounded-xl bg-slate-900/90 p-2.5 border border-slate-800">
                            <div class="text-[10px] uppercase font-bold text-slate-400 flex items-center gap-1.5">
                                <svg class="h-3.5 w-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span>Status Check-in</span>
                            </div>
                            <div class="mt-1 font-bold text-xs text-emerald-400" id="routeCheckInBadge">
                                -
                            </div>
                            <div class="text-[10px] text-slate-400 mt-0.5 truncate" id="routeCheckInSub">
                                Di outlet tujuan
                            </div>
                        </div>
                    </div>

                    {{-- Catatan Hasil Kunjungan & Foto Dokumentasi Lapangan (Jika Selesai) --}}
                    <div id="routeResultBox" class="hidden rounded-xl bg-emerald-950/50 border border-emerald-500/40 p-3 text-xs text-emerald-200">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div class="space-y-1">
                                <div><strong class="text-emerald-300">Hasil Kunjungan:</strong> <span id="routeResultText">-</span></div>
                                <div id="routeNotesContainer" class="text-[11px] text-emerald-400 hidden">
                                    <strong>Catatan Tambahan:</strong> <span id="routeNotesText">-</span>
                                </div>
                            </div>

                            {{-- Foto Dokumentasi Laporan Thumbnail (Admin / Pimpinan / Kadiv / Sales) --}}
                            <div id="routePhotoContainer" class="hidden shrink-0">
                                <div class="flex items-center gap-2.5 bg-slate-900/90 p-1.5 pr-3 rounded-xl border border-emerald-400/40 shadow-xs">
                                    <img id="routePhotoImg" src="" alt="Dokumentasi Kunjungan" 
                                         style="width: 64px; height: 48px; min-width: 64px; max-width: 64px; object-fit: cover; display: block;"
                                         onclick="openImageLightbox(this.src, document.getElementById('routeCardTitle').textContent)"
                                         class="rounded-lg border border-slate-700 cursor-pointer hover:opacity-90 hover:scale-105 transition">
                                    <div>
                                        <span class="block text-[11px] font-bold text-white flex items-center gap-1">
                                            <svg class="h-3 w-3 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                            Foto Dokumentasi
                                        </span>
                                        <button type="button" 
                                                onclick="openImageLightbox(document.getElementById('routePhotoImg').src, document.getElementById('routeCardTitle').textContent)"
                                                class="text-[10px] font-bold text-sky-400 hover:text-amber-300 underline inline-block mt-0.5">
                                            Buka Foto &nearr;
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- LEAFLET CANVAS --}}
                <div id="visitLiveMap" class="h-[360px] sm:h-[460px] lg:h-[540px] w-full rounded-2xl border border-slate-300 bg-slate-100 shadow-inner z-0"></div>

                <div class="flex flex-wrap items-center justify-between gap-2 text-xs text-slate-500 px-1">
                    <span id="mapLiveStatusText" class="inline-flex items-center gap-1.5"><svg class="h-3.5 w-3.5 text-slate-400 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg><span>Memuat data live tracking & rute outlet...</span></span>
                    <span id="mapLastUpdatedText">Terakhir diperbarui: -</span>
                </div>
            </div>

            {{-- 3. DAFTAR DATA KUNJUNGAN DI BAWAH PETA (OTOMATIS MASUK) --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm space-y-4">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 pb-3">
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-base font-bold text-slate-900 inline-flex items-center gap-2">
                                <svg class="h-4 w-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                <span>Data Kunjungan Lapangan & Status Tracking</span>
                            </h3>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800">
                                Otomatis Masuk
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5">Semua tugas kunjungan yang dibuat otomatis tampil di sini dan dapat dipantau navigasi rutenya.</p>
                    </div>

                    {{-- FILTER & SEARCH FORM --}}
                    <form method="GET" action="{{ route('visits.index') }}" class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="Cari kode, outlet, sales..." 
                               class="w-full sm:w-48 rounded-lg border-slate-300 text-xs py-1.5 focus:border-blue-500 focus:ring-blue-500">
                        <select name="status" class="w-full sm:w-auto rounded-lg border-slate-300 text-xs py-1.5 focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Semua Status</option>
                            <option value="MENUNGGU_RESPONS" {{ request('status') === 'MENUNGGU_RESPONS' ? 'selected' : '' }}>Menunggu Respons</option>
                            <option value="DITERIMA" {{ request('status') === 'DITERIMA' ? 'selected' : '' }}>Diterima</option>
                            <option value="DALAM_PERJALANAN" {{ request('status') === 'DALAM_PERJALANAN' ? 'selected' : '' }}>Dalam Perjalanan (OTW)</option>
                            <option value="TIBA_DI_LOKASI" {{ request('status') === 'TIBA_DI_LOKASI' ? 'selected' : '' }}>Tiba di Lokasi</option>
                            <option value="SEDANG_KUNJUNGAN" {{ request('status') === 'SEDANG_KUNJUNGAN' ? 'selected' : '' }}>Sedang Kunjungan</option>
                            <option value="SELESAI" {{ request('status') === 'SELESAI' ? 'selected' : '' }}>Selesai</option>
                            <option value="DITOLAK" {{ request('status') === 'DITOLAK' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                        <button type="submit" class="rounded-lg bg-slate-900 px-3 py-1.5 text-xs font-bold text-white hover:bg-slate-800 transition">
                            Filter
                        </button>
                        @if(request()->hasAny(['search', 'status', 'team_id', 'sales_id']))
                        <a href="{{ route('visits.index') }}" class="rounded-lg border border-slate-300 bg-white px-2.5 py-1.5 text-xs font-semibold text-slate-600 hover:bg-slate-50">
                            Reset
                        </a>
                        @endif
                    </form>
                </div>

                {{-- TABLE VISITING DATA --}}
                <div class="overflow-x-auto">
                    <table class="hidden md:table min-w-full text-left text-sm">
                        <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500 border-b border-slate-100">
                            <tr>
                                <th class="px-5 py-4">ID & Prioritas</th>
                                <th class="px-5 py-4">Sales Motoris & Tim</th>
                                <th class="px-5 py-4">Outlet Tujuan</th>
                                <th class="px-5 py-4">Jadwal & Tujuan</th>
                                <th class="px-5 py-4">Status Kunjungan</th>
                                <th class="px-5 py-4">Status Tracking & GPS</th>
                                <th class="px-5 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($visitTasks as $task)
                                @php
                                    $jPayload = $task->getJourneyPayload();
                                    $hasOriginLocation = !empty($jPayload['origin']['latitude']) && !empty($jPayload['origin']['longitude']);
                                    $salesLat = $jPayload['origin']['latitude'] ?? null;
                                    $salesLng = $jPayload['origin']['longitude'] ?? null;
                                    $outletLat = $task->customer->latitude;
                                    $outletLng = $task->customer->longitude;
                                    $isFinished = $task->status === 'SELESAI';
                                @endphp
                                <tr class="hover:bg-slate-50/70 transition">
                                    {{-- 1. ID & PRIORITAS --}}
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <div class="font-mono font-bold text-xs text-slate-900">
                                            {{ $task->task_code }}
                                        </div>
                                        @php
                                            $pColors = [
                                                'rendah' => 'bg-slate-100 text-slate-700 border-slate-200',
                                                'normal' => 'bg-blue-50 text-blue-700 border-blue-200',
                                                'tinggi' => 'bg-blue-50 text-amber-700 border-blue-200',
                                                'mendesak' => 'bg-rose-50 text-rose-700 border-rose-200',
                                            ];
                                        @endphp
                                        <span class="inline-flex items-center px-2 py-0.5 mt-1 rounded text-[10px] font-bold border {{ $pColors[$task->priority] ?? $pColors['normal'] }}">
                                            {{ strtoupper($task->priority) }}
                                        </span>
                                    </td>

                                    {{-- 2. SALES MOTORIS & TIM --}}
                                    <td class="px-5 py-4">
                                        <div class="font-semibold text-slate-900 text-xs">{{ $task->sales->name ?? 'Belum Ditugaskan' }}</div>
                                        <div class="text-[11px] text-slate-500 mt-0.5">
                                            {{ $task->sales->team->name ?? 'Tanpa Tim' }}
                                        </div>
                                    </td>

                                    {{-- 3. OUTLET TUJUAN --}}
                                    <td class="px-5 py-4">
                                        <div class="font-bold text-slate-900 text-xs">{{ $task->customer->name }}</div>
                                        <div class="text-[11px] text-slate-500 truncate max-w-xs mt-0.5">
                                            {{ $task->customer->address ?: 'Alamat belum diisi' }}
                                        </div>
                                        @if($outletLat && $outletLng)
                                            <span class="inline-flex items-center gap-1 text-[10px] font-mono text-emerald-700 mt-0.5">
                                                <svg class="h-3 w-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                <span>{{ number_format($outletLat, 4) }}, {{ number_format($outletLng, 4) }}</span>
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 text-[10px] text-slate-400">
                                                <span class="h-1.5 w-1.5 rounded-full bg-slate-300"></span>
                                                <span>Koordinat belum diisi</span>
                                            </span>
                                        @endif
                                    </td>

                                    {{-- 4. JADWAL & TUJUAN + DURASI TRACKING --}}
                                    <td class="px-5 py-4">
                                        <div class="text-xs font-semibold text-slate-800">
                                            {{ $task->scheduled_at ? $task->scheduled_at->format('d/m/Y H:i') : '-' }}
                                        </div>
                                        <div class="text-[11px] text-slate-500 truncate max-w-xs mt-0.5">
                                            {{ $task->purpose ?: 'Kunjungan rutin outlet' }}
                                        </div>
                                        @if(!empty($jPayload['duration']['total_text']))
                                            <div class="inline-flex items-center gap-1.5 text-[10px] font-semibold text-amber-800 bg-blue-50 border border-blue-200 rounded px-2 py-0.5 mt-1.5">
                                                <span class="inline-flex items-center gap-1">
                                                    <svg class="h-3 w-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    <span>{{ $jPayload['duration']['total_text'] }}</span>
                                                </span>
                                                @if(!empty($jPayload['distance_km']))
                                                    <span class="text-slate-300">|</span>
                                                    <span class="inline-flex items-center gap-1">
                                                        <svg class="h-3 w-3 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                                                        <span>{{ $jPayload['distance_km'] }} KM</span>
                                                    </span>
                                                @endif
                                            </div>
                                        @endif
                                    </td>

                                    {{-- 5. STATUS KUNJUNGAN --}}
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        @php
                                            $sColors = [
                                                'MENUNGGU_RESPONS' => 'bg-blue-50 text-amber-800 border-blue-200',
                                                'DITERIMA'         => 'bg-blue-50 text-blue-700 border-blue-200',
                                                'DALAM_PERJALANAN' => 'bg-cyan-50 text-cyan-700 border-cyan-200',
                                                'TIBA_DI_LOKASI'   => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                                'SEDANG_KUNJUNGAN' => 'bg-orange-50 text-orange-700 border-orange-200',
                                                'SELESAI'          => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                                'DITOLAK'          => 'bg-rose-50 text-rose-700 border-rose-200',
                                                'DIBATALKAN'       => 'bg-slate-100 text-slate-600 border-slate-200',
                                            ];
                                        @endphp
                                        <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[11px] font-bold border {{ $sColors[$task->status] ?? 'bg-slate-100 text-slate-600 border-slate-200' }}">
                                            <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                            {{ str_replace('_', ' ', $task->status) }}
                                        </span>
                                    </td>

                                    {{-- 6. STATUS TRACKING & GPS --}}
                                    <td class="px-5 py-4">
                                        @if($isFinished)
                                            <div class="flex items-center gap-1.5 text-emerald-700 text-xs font-bold">
                                                <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                                                <span>Selesai Melacak</span>
                                            </div>
                                            <div class="text-[10px] text-slate-600 mt-0.5">
                                                {{ $jPayload['duration']['started_at_text'] ?? '' }} &rarr; {{ $jPayload['duration']['ended_at_text'] ?? '' }}
                                            </div>
                                        @elseif($hasOriginLocation)
                                            <div class="flex items-center gap-1.5 text-blue-600 text-xs font-bold">
                                                <span class="h-2 w-2 rounded-full bg-blue-500 animate-pulse"></span>
                                                <span>GPS Aktif</span>
                                            </div>
                                            <div class="font-mono text-[10px] text-slate-500 mt-0.5">
                                                {{ number_format($salesLat, 4) }}, {{ number_format($salesLng, 4) }}
                                            </div>
                                        @else
                                            <span class="inline-flex items-center gap-1 text-xs text-slate-400"><span class="h-1.5 w-1.5 rounded-full bg-slate-300"></span> Belum tracking</span>
                                        @endif

                                        @if($task->check_in_distance_meters)
                                            <div class="mt-1 text-[10px] font-semibold text-blue-700 bg-blue-50 px-1.5 py-0.5 rounded border border-blue-200 inline-block">
                                                Check-in: {{ round($task->check_in_distance_meters) }}m dari toko
                                            </div>
                                        @endif
                                    </td>

                                    {{-- 7. AKSI (HANYA BUTTON ICON SVG) --}}
                                    <td class="whitespace-nowrap px-5 py-4 text-center">
                                        <div class="flex items-center justify-center gap-1.5">
                                            {{-- BUTTON 1: VIEW PETA & RUTE (ICON ONLY - SEMUA ROLE) --}}
                                            <button type="button" 
                                                    onclick='showTaskJourney(@json($jPayload))' 
                                                    title="Tampilkan di Peta & Navigasi Rute" 
                                                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-blue-200 bg-blue-50 text-blue-600 shadow-2xs hover:bg-blue-100 hover:border-blue-300 transition">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </button>

                                            {{-- BUTTON 2: DETAIL TUGAS LINK --}}
                                            <a href="{{ route('visit-tasks.show', $task) }}" 
                                               title="Buka Halaman Detail Tugas Kunjungan" 
                                               class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 bg-slate-50 text-slate-700 shadow-2xs hover:bg-slate-100 hover:border-slate-300 transition">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                                </svg>
                                            </a>

                                            {{-- BUTTON 2B: LIHAT FOTO DOKUMENTASI (JIKA ADA - ADMIN / PIMPINAN / KADIV / SALES) --}}
                                            @if($task->documentation_image)
                                            <button type="button" 
                                                    onclick="openImageLightbox('{{ asset('storage/' . $task->documentation_image) }}', 'Dokumentasi Tugas {{ $task->task_code }} - {{ $task->customer->name }}')" 
                                                    title="Lihat Foto Bukti Dokumentasi Kunjungan" 
                                                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-emerald-300 bg-emerald-50 text-emerald-700 shadow-2xs hover:bg-emerald-100 hover:border-emerald-400 transition">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                </svg>
                                            </button>
                                            @endif

                                            {{-- BUTTON 3 & 4: EDIT & HAPUS (ADMIN / KADIV / SUPERVISOR ONLY - SALES MOTORIS VIEW ONLY) --}}
                                            @if(!auth()->user()->hasRole('Sales Motoris'))
                                            <button type="button" 
                                                    onclick='openEditModal(@json($task))' 
                                                    title="Edit Rencana Kunjungan" 
                                                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-amber-300 bg-blue-50 text-amber-700 shadow-2xs hover:bg-amber-100 hover:border-amber-400 transition">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </button>

                                            <form method="POST" action="{{ route('visits.destroy', $task->id) }}" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        title="Batalkan / Hapus Kunjungan" 
                                                        class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-rose-200 bg-rose-50 text-rose-600 shadow-2xs hover:bg-rose-100 hover:border-rose-300 transition" 
                                                        onclick="return confirm('Hapus tugas kunjungan {{ $task->task_code }} ke {{ $task->customer->name }}?')">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-slate-500">
                                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400 mb-3">
                                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                                        </div>
                                        Belum ada jadwal kunjungan terdaftar. Buat tugas kunjungan baru untuk memulai.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- MOBILE CARDS VIEW (md:hidden) --}}
                <div class="block md:hidden divide-y divide-slate-100">
                    @forelse($visitTasks as $task)
                        @php
                            $jPayload = $task->getJourneyPayload();
                            $hasOriginLocation = !empty($jPayload['origin']['latitude']) && !empty($jPayload['origin']['longitude']);
                            $salesLat = $jPayload['origin']['latitude'] ?? null;
                            $salesLng = $jPayload['origin']['longitude'] ?? null;
                            $outletLat = $task->customer->latitude;
                            $outletLng = $task->customer->longitude;
                            $isFinished = $task->status === 'SELESAI';
                            $pColors = [
                                'rendah' => 'bg-slate-100 text-slate-700 border-slate-200',
                                'normal' => 'bg-blue-50 text-blue-700 border-blue-200',
                                'tinggi' => 'bg-blue-50 text-amber-700 border-blue-200',
                                'mendesak' => 'bg-rose-50 text-rose-700 border-rose-200',
                            ];
                            $statusColors = [
                                'MENUNGGU_RESPONS' => 'bg-slate-100 text-slate-700 border-slate-200',
                                'DITERIMA' => 'bg-blue-50 text-blue-700 border-blue-200',
                                'DITOLAK' => 'bg-rose-50 text-rose-700 border-rose-200',
                                'DALAM_PERJALANAN' => 'bg-sky-50 text-sky-700 border-sky-200 animate-pulse',
                                'TIBA_DI_LOKASI' => 'bg-blue-50 text-amber-700 border-blue-200',
                                'SEDANG_KUNJUNGAN' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                'SELESAI' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                                'BATAL' => 'bg-rose-50 text-rose-700 border-rose-200',
                            ];
                        @endphp
                        <div class="p-4 space-y-3 hover:bg-slate-50/70 transition">
                            {{-- Header: Code, Priority, Status --}}
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="font-mono font-bold text-xs text-slate-900">{{ $task->task_code }}</span>
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold border {{ $pColors[$task->priority] ?? $pColors['normal'] }}">
                                            {{ strtoupper($task->priority) }}
                                        </span>
                                    </div>
                                    <div class="text-[11px] text-slate-500 mt-0.5">
                                        {{ $task->sales->name ?? 'Belum Ditugaskan' }} &bull; <span class="text-slate-400">{{ $task->sales->team->name ?? 'Tanpa Tim' }}</span>
                                    </div>
                                </div>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold border shrink-0 {{ $statusColors[$task->status] ?? 'bg-slate-100 text-slate-700 border-slate-200' }}">
                                    {{ str_replace('_', ' ', $task->status) }}
                                </span>
                            </div>

                            {{-- Outlet Info --}}
                            <div class="rounded-xl bg-slate-50 p-2.5 border border-slate-100 text-xs">
                                <div class="font-bold text-slate-900 flex items-center gap-1.5">
                                    <svg class="h-3.5 w-3.5 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                    <span>{{ $task->customer->name }}</span>
                                </div>
                                <div class="text-[11px] text-slate-500 mt-1 pl-5">
                                    {{ $task->customer->address ?: 'Alamat belum diisi' }}
                                </div>
                                @if($task->purpose)
                                <div class="text-[11px] text-slate-600 mt-1 pl-5 italic">
                                    "{{ $task->purpose }}"
                                </div>
                                @endif
                                <div class="flex flex-wrap items-center gap-3 mt-2 pl-5 text-[11px] text-slate-500 border-t border-slate-200/60 pt-1.5">
                                    <span>📅 {{ $task->scheduled_at->format('d M, H:i') }} WIB</span>
                                    @if($isFinished && !empty($jPayload['duration']['trip_minutes']))
                                    <span class="text-emerald-700 font-medium">⏱️ {{ $jPayload['duration']['trip_minutes'] }} mnt</span>
                                    @endif
                                    @if($isFinished && !empty($jPayload['distance_km']))
                                    <span class="text-blue-700 font-medium">🛣️ {{ $jPayload['distance_km'] }} km</span>
                                    @endif
                                </div>
                            </div>

                            {{-- Action Buttons --}}
                            <div class="flex items-center justify-end gap-2 pt-1">
                                <button type="button" 
                                        onclick='showTaskJourney(@json($jPayload)); window.scrollTo({top: 0, behavior: "smooth"});' 
                                        class="inline-flex items-center gap-1.5 rounded-lg border border-blue-200 bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700 shadow-2xs hover:bg-blue-100 active:scale-95 transition">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <span>Rute Peta</span>
                                </button>

                                <a href="{{ route('visit-tasks.show', $task) }}" 
                                   class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-slate-50 px-3 py-1.5 text-xs font-semibold text-slate-700 shadow-2xs hover:bg-slate-100 active:scale-95 transition">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                    <span>Detail</span>
                                </a>

                                @if($task->documentation_image)
                                <button type="button" 
                                        onclick="openImageLightbox('{{ asset('storage/' . $task->documentation_image) }}', 'Dokumentasi Tugas {{ $task->task_code }} - {{ $task->customer->name }}')" 
                                        class="inline-flex items-center gap-1.5 rounded-lg border border-emerald-300 bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700 shadow-2xs hover:bg-emerald-100 active:scale-95 transition">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <span>Foto Bukti</span>
                                </button>
                                @endif

                                @if(!auth()->user()->hasRole('Sales Motoris'))
                                <button type="button" onclick='openEditModal(@json($task))' title="Edit" class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-amber-300 bg-blue-50 text-amber-700 active:scale-95 transition">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-slate-500">
                            <p class="text-xs">Belum ada data tugas kunjungan lapangan.</p>
                        </div>
                    @endforelse
                </div>

                <div class="border-t border-slate-100 px-2 py-3">
                    {{ $visitTasks->links() }}
                </div>
            </div>

        </div>
    </div>

    {{-- MODAL TAMBAH KUNJUNGAN --}}
    <div id="modalCreateVisit" class="fixed inset-0 z-[1000] hidden items-center justify-center bg-slate-950/70 p-4 backdrop-blur-2xs">
        <div class="w-full max-w-xl rounded-2xl bg-white p-4 sm:p-6 shadow-2xl max-h-[92vh] overflow-y-auto">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <h3 class="text-lg font-bold text-slate-900">Buat Tugas Kunjungan Baru</h3>
                <button type="button" onclick="closeCreateModal()" class="text-2xl text-slate-400 hover:text-slate-700">&times;</button>
            </div>
            <form method="POST" action="{{ route('visits.store') }}" class="mt-4 space-y-4">
                @csrf
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Outlet / Pelanggan <span class="text-rose-500">*</span></label>
                        <select name="customer_id" required class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">-- Pilih Outlet --</option>
                            @foreach($customers as $c)
                                <option value="{{ $c->id }}">{{ $c->name }} ({{ ucfirst($c->type) }}) - {{ $c->address ?: 'Tanpa alamat' }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Sales Motoris <span class="text-rose-500">*</span></label>
                        <select name="sales_id" required class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">-- Pilih Sales --</option>
                            @foreach($salesUsers as $s)
                                <option value="{{ $s->id }}">{{ $s->name }} {{ $s->team ? '(' . $s->team->name . ')' : '' }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Prioritas <span class="text-rose-500">*</span></label>
                        <select name="priority" required class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="normal">Normal</option>
                            <option value="tinggi">Tinggi</option>
                            <option value="mendesak">Mendesak</option>
                            <option value="rendah">Rendah</option>
                        </select>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Jadwal Kunjungan <span class="text-rose-500">*</span></label>
                        <input type="datetime-local" name="scheduled_at" required value="{{ now()->addHours(2)->format('Y-m-d\TH:i') }}" class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Tujuan / Instruksi</label>
                        <textarea name="purpose" rows="2" placeholder="Tujuan kunjungan (cth: Cek stok, penagihan invoice, promosi produk baru)..." class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                    <button type="button" onclick="closeCreateModal()" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">Batal</button>
                    <button type="submit" class="rounded-xl bg-slate-950 px-5 py-2 text-xs font-bold text-white hover:bg-slate-800 transition">Simpan Kunjungan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL EDIT KUNJUNGAN --}}
    <div id="modalEditVisit" class="fixed inset-0 z-[1000] hidden items-center justify-center bg-slate-950/70 p-4 backdrop-blur-2xs">
        <div class="w-full max-w-xl rounded-2xl bg-white p-4 sm:p-6 shadow-2xl max-h-[92vh] overflow-y-auto">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <h3 class="text-lg font-bold text-slate-900" id="editModalTitle">Edit Rencana Kunjungan</h3>
                <button type="button" onclick="closeEditModal()" class="text-2xl text-slate-400 hover:text-slate-700">&times;</button>
            </div>
            <form id="formEditVisit" method="POST" action="#" class="mt-4 space-y-4">
                @csrf
                @method('PUT')
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Outlet / Pelanggan <span class="text-rose-500">*</span></label>
                        <select id="editCustomerId" name="customer_id" required class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                            @foreach($customers as $c)
                                <option value="{{ $c->id }}">{{ $c->name }} ({{ ucfirst($c->type) }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Sales Motoris</label>
                        <select id="editSalesId" name="sales_id" class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                            @foreach($salesUsers as $s)
                                <option value="{{ $s->id }}">{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Prioritas</label>
                        <select id="editPriority" name="priority" class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="rendah">Rendah</option>
                            <option value="normal">Normal</option>
                            <option value="tinggi">Tinggi</option>
                            <option value="mendesak">Mendesak</option>
                        </select>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Jadwal Kunjungan <span class="text-rose-500">*</span></label>
                        <input type="datetime-local" id="editScheduledAt" name="scheduled_at" required class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Tujuan / Catatan</label>
                        <textarea id="editPurpose" name="purpose" rows="2" class="w-full rounded-xl border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                    <button type="button" onclick="closeEditModal()" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">Batal</button>
                    <button type="submit" class="rounded-xl bg-slate-950 px-5 py-2 text-xs font-bold text-white hover:bg-slate-800 transition">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL RENCANAKAN RUTE (KADIV / SUPERVISOR / ADMIN) --}}
    <div id="modalPlanRoute" class="fixed inset-0 z-[1000] hidden items-center justify-center bg-slate-950/70 p-4 backdrop-blur-2xs">
        <div class="w-full max-w-2xl rounded-2xl bg-white p-4 sm:p-6 shadow-2xl space-y-4 max-h-[92vh] overflow-y-auto">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2.5">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-100 text-amber-700 font-bold"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg></span>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Rencanakan & Buat Rute Kunjungan</h3>
                        <p class="text-xs text-slate-500">Simulasikan jalur navigasi, hitung estimasi jarak & durasi jalan raya, dan buat tugas kunjungan.</p>
                    </div>
                </div>
                <button type="button" onclick="closePlanRouteModal()" class="text-2xl text-slate-400 hover:text-slate-700">&times;</button>
            </div>

            <div class="space-y-4 text-xs">
                {{-- Outlet Tujuan (Pin B) --}}
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        1. Outlet Tujuan (Pin B) <span class="text-rose-500">*</span>
                    </label>
                    <select id="planCustomerId" onchange="onPlanCustomerChange()" class="w-full rounded-xl border-slate-300 text-xs focus:border-blue-500 focus:ring-blue-500">
                        <option value="">-- Pilih Outlet Tujuan --</option>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}" 
                                    data-lat="{{ $c->latitude }}" 
                                    data-lng="{{ $c->longitude }}" 
                                    data-name="{{ $c->name }}" 
                                    data-address="{{ $c->address ?: 'Alamat belum diisi' }}">
                                {{ $c->name }} ({{ ucfirst($c->type) }}) - {{ $c->address ?: 'Tanpa alamat' }}
                                @if($c->latitude && $c->longitude) [GPS Tersedia] @else [Belum Ada GPS] @endif
                            </option>
                        @endforeach
                    </select>
                    <div id="planCustomerInfo" class="mt-1 text-[11px] text-slate-500 hidden">
                        <span class="font-semibold text-slate-600">Koordinat:</span> <span id="planCustomerCoords" class="font-mono font-semibold text-emerald-700">-</span>
                    </div>
                </div>

                {{-- Sales Motoris --}}
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        2. Sales Motoris (Penanggung Jawab Kunjungan)
                    </label>
                    <select id="planSalesId" class="w-full rounded-xl border-slate-300 text-xs focus:border-blue-500 focus:ring-blue-500">
                        <option value="">-- Pilih Sales Motoris (Opsional) --</option>
                        @foreach($salesUsers as $s)
                            @php
                                $sLat = $s->activeTrackingSession?->latestLocation?->latitude;
                                $sLng = $s->activeTrackingSession?->latestLocation?->longitude;
                            @endphp
                            <option value="{{ $s->id }}" 
                                    data-name="{{ $s->name }}" 
                                    data-lat="{{ $sLat }}" 
                                    data-lng="{{ $sLng }}">
                                {{ $s->name }} {{ $s->team ? '(' . $s->team->name . ')' : '' }}
                                @if($sLat && $sLng) [GPS Aktif: {{ number_format($sLat, 3) }}, {{ number_format($sLng, 3) }}] @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Titik Awal Berangkat (Pin A) --}}
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        3. Titik Awal Berangkat (Pin A) <span class="text-rose-500">*</span>
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <label class="flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 cursor-pointer">
                            <input type="radio" name="planOriginType" value="browser_gps" checked onchange="onPlanOriginTypeChange()" class="text-blue-600 focus:ring-amber-500">
                            <div>
                                <div class="font-bold text-slate-800 flex items-center gap-1.5"><svg class="h-3.5 w-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg><span>Lokasi GPS Saya Saat Ini</span></div>
                                <div class="text-[10px] text-slate-500">Menggunakan sensor GPS peramban Anda</div>
                            </div>
                        </label>
                        <label class="flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 cursor-pointer">
                            <input type="radio" name="planOriginType" value="sales_gps" onchange="onPlanOriginTypeChange()" class="text-blue-600 focus:ring-amber-500">
                            <div>
                                <div class="font-bold text-slate-800 flex items-center gap-1.5"><svg class="h-3.5 w-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg><span>Lokasi GPS Sales Terpilih</span></div>
                                <div class="text-[10px] text-slate-500">Dari posisi pelacakan terakhir sales</div>
                            </div>
                        </label>
                        <label class="flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 cursor-pointer">
                            <input type="radio" name="planOriginType" value="depot_sidoarjo" onchange="onPlanOriginTypeChange()" class="text-blue-600 focus:ring-amber-500">
                            <div>
                                <div class="font-bold text-slate-800 flex items-center gap-1.5"><svg class="h-3.5 w-3.5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg><span>Kantor Depo Sidoarjo / Jatim</span></div>
                                <div class="text-[10px] text-slate-500">-7.4478, 112.7183</div>
                            </div>
                        </label>
                        <label class="flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 cursor-pointer">
                            <input type="radio" name="planOriginType" value="depot_surabaya" onchange="onPlanOriginTypeChange()" class="text-blue-600 focus:ring-amber-500">
                            <div>
                                <div class="font-bold text-slate-800 flex items-center gap-1.5"><svg class="h-3.5 w-3.5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg><span>Kantor Pusat Surabaya</span></div>
                                <div class="text-[10px] text-slate-500">-7.2575, 112.7521</div>
                            </div>
                        </label>
                        <label class="flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 cursor-pointer sm:col-span-2">
                            <input type="radio" name="planOriginType" value="custom" onchange="onPlanOriginTypeChange()" class="text-blue-600 focus:ring-amber-500">
                            <div>
                                <div class="font-bold text-slate-800 flex items-center gap-1.5"><svg class="h-3.5 w-3.5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg><span>Koordinat Khusus (Input Manual)</span></div>
                                <div class="text-[10px] text-slate-500">Tentukan latitude dan longitude secara spesifik</div>
                            </div>
                        </label>
                    </div>

                    {{-- Custom Lat/Lng input --}}
                    <div id="planCustomCoordsRow" class="hidden grid-cols-2 gap-2 mt-2">
                        <div>
                            <input type="number" step="any" id="planCustomLat" placeholder="Latitude asal (cth: -7.4478)" class="w-full rounded-xl border-slate-300 text-xs focus:border-amber-500">
                        </div>
                        <div>
                            <input type="number" step="any" id="planCustomLng" placeholder="Longitude asal (cth: 112.7183)" class="w-full rounded-xl border-slate-300 text-xs focus:border-amber-500">
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap items-center justify-between gap-3 pt-3 border-t border-slate-100">
                <button type="button" onclick="closePlanRouteModal()" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                    Batal
                </button>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="executePlanRoute()" class="inline-flex items-center gap-1.5 rounded-xl bg-blue-600 px-4 py-2 text-xs font-bold text-slate-950 hover:bg-amber-400 transition shadow-xs">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg><span>Tampilkan & Gambar Rute</span>
                    </button>
                    <button type="button" onclick="proceedToCreateTaskFromPlan()" class="inline-flex items-center gap-1.5 rounded-xl bg-slate-950 px-4 py-2 text-xs font-bold text-white hover:bg-slate-800 transition">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg><span>Lanjut Buat Tugas</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- SCRIPT: LEAFLET MAP, REAL-TIME TRACKING & OSRM ROUTING --}}
    <script>
    let map = null;
    let salesMarkers = {};
    let outletMarkers = {};
    let activeRoutePolyline = null;
    let activeShadowPolyline = null;
    let pinAMarker = null;
    let pinBMarker = null;
    let pinCheckInMarker = null;
    let userCurrentLat = null;
    let userCurrentLng = null;

    const defaultCenter = [-7.2575, 112.7521]; // Pusat Operasional Surabaya

    document.addEventListener('DOMContentLoaded', function () {
        // Cache user geolocation if available
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                pos => {
                    userCurrentLat = pos.coords.latitude;
                    userCurrentLng = pos.coords.longitude;
                },
                null,
                { timeout: 6000 }
            );
        }

        // 1. Init Leaflet Map
        if (window.L) {
            map = window.L.map('visitLiveMap').setView(defaultCenter, 12);
            window.addEventListener('resize', () => { if (map) map.invalidateSize(); });
            window.addEventListener('orientationchange', () => { setTimeout(() => { if (map) map.invalidateSize(); }, 250); });
            window.L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap kontributor',
                maxZoom: 19
            }).addTo(map);

            // Render Outlet Markers
            renderOutletMarkers();
        }

        // 2. Fetch Live Tracking Positions
        fetchLivePositions();
        setInterval(fetchLivePositions, 15000);

        // 3. Filter Team Change
        const teamFilter = document.getElementById('filterTeam');
        if (teamFilter) teamFilter.addEventListener('change', fetchLivePositions);
        const refreshBtn = document.getElementById('btnRefreshTracking');
        if (refreshBtn) refreshBtn.addEventListener('click', fetchLivePositions);
        const resetViewBtn = document.getElementById('btnResetMapView');
        if (resetViewBtn) resetViewBtn.addEventListener('click', resetMapView);

        // 4. Sales Tracking Session Controller (If Sales Motoris)
        @if(auth()->user()->hasRole('Sales Motoris'))
        checkSalesSession();
        const toggleTrackingBtn = document.getElementById('btnToggleTracking');
        if (toggleTrackingBtn) toggleTrackingBtn.addEventListener('click', toggleTrackingSession);
        @endif
    });

    // Render static outlet markers from PHP dataset
    function renderOutletMarkers() {
        const tasks = @json($visitTasks->items());
        tasks.forEach(t => {
            if (t.customer && t.customer.latitude && t.customer.longitude) {
                const lat = parseFloat(t.customer.latitude);
                const lng = parseFloat(t.customer.longitude);
                if (!isNaN(lat) && !isNaN(lng) && !outletMarkers[t.customer_id]) {
                    const icon = window.L.divIcon({
                        className: 'custom-outlet-pin',
                        html: `<div style="background:#f59e0b; width:26px; height:26px; border-radius:50%; border:2px solid #ffffff; box-shadow:0 2px 6px rgba(0,0,0,0.3); display:flex; align-items:center; justify-content:center; color:#ffffff;"><svg style="width:14px; height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg></div>`,
                        iconSize: [24, 24],
                        iconAnchor: [12, 12]
                    });

                    const m = window.L.marker([lat, lng], { icon: icon }).addTo(map);
                    m.bindPopup(`
                        <div style="font-size:12px; line-height:1.4;">
                            <strong style="color:#0f172a; font-size:13px;">${t.customer.name}</strong><br>
                            <span style="color:#64748b;">${t.customer.address || '-'}</span><br>
                            <span style="display:inline-block; margin-top:4px; font-size:11px; font-weight:bold; color:#d97706;">Tugas: ${t.task_code}</span>
                        </div>
                    `);
                    outletMarkers[t.customer_id] = m;
                }
            }
        });
    }

    // Fetch live positions from API
    async function fetchLivePositions() {
        const teamId = document.getElementById('filterTeam').value;
        const url = `/api/tracking/live${teamId ? '?team_id=' + teamId : ''}`;

        try {
            const res = await fetch(url);
            const data = await res.json();
            renderSalesMarkers(data);
            document.getElementById('mapLastUpdatedText').textContent = 'Terakhir diperbarui: ' + new Date().toLocaleTimeString('id-ID');
        } catch (e) {
            console.error('Failed to fetch tracking data', e);
        }
    }

    function renderSalesMarkers(positions) {
        document.getElementById('mapLiveStatusText').innerHTML = `<span class="inline-flex items-center gap-1.5"><span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span><strong>${positions.length} Sales Motoris</strong> aktif live tracking</span>`;

        const activeSalesIds = new Set();

        positions.forEach(pos => {
            activeSalesIds.add(pos.sales_id);
            const colorHex = {
                green: '#10b981',
                blue: '#3b82f6',
                orange: '#f59e0b',
                gray: '#94a3b8'
            }[pos.color] || '#3b82f6';

            const iconHtml = `
                <div style="position:relative; display:flex; flex-direction:column; align-items:center;">
                    <div style="background-color:${colorHex}; width:32px; height:32px; border-radius:50%; border:3px solid #ffffff; box-shadow:0 3px 8px rgba(0,0,0,0.35); display:flex; align-items:center; justify-content:center; color:#ffffff;">
                        <svg style="width:16px; height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <div style="background:#0f172a; color:#ffffff; font-size:10px; font-weight:bold; padding:1px 6px; border-radius:4px; white-space:nowrap; margin-top:2px; box-shadow:0 1px 4px rgba(0,0,0,0.3);">
                        ${pos.name.split(' ')[0]}
                    </div>
                </div>
            `;

            const icon = window.L.divIcon({
                className: 'custom-sales-pin',
                html: iconHtml,
                iconSize: [36, 48],
                iconAnchor: [18, 24]
            });

            if (salesMarkers[pos.sales_id]) {
                salesMarkers[pos.sales_id].setLatLng([pos.latitude, pos.longitude]);
                salesMarkers[pos.sales_id].setIcon(icon);
            } else {
                const marker = window.L.marker([pos.latitude, pos.longitude], { icon: icon }).addTo(map);
                salesMarkers[pos.sales_id] = marker;
            }

            const popupContent = `
                <div style="font-size:12px; line-height:1.5; min-width:180px;">
                    <strong style="color:#0f172a; font-size:13px;">${pos.name}</strong><br>
                    <span style="color:#64748b;">Tim: ${pos.team}</span><br>
                    <span style="color:#0284c7; font-weight:bold;">Status: ${pos.minutes_ago <= 15 ? 'Sedang Berjalan' : 'Aktif'}</span><br>
                    <span style="color:#94a3b8; font-size:10px;">Update: ${pos.minutes_ago} menit lalu</span>
                </div>
            `;
            salesMarkers[pos.sales_id].bindPopup(popupContent);
        });

        // Remove old inactive markers
        Object.keys(salesMarkers).forEach(salesId => {
            if (!activeSalesIds.has(parseInt(salesId))) {
                map.removeLayer(salesMarkers[salesId]);
                delete salesMarkers[salesId];
            }
        });
    }

    // SHOW FULL TASK JOURNEY ON MAP (PIN A -> CHECK-IN -> PIN B, OSRM ROUTE, DURATIONS)
    async function showTaskJourney(j) {
        if (!j) return;

        // Smooth scroll to map card
        const mapCard = document.getElementById('mapContainerCard');
        if (mapCard) mapCard.scrollIntoView({ behavior: 'smooth', block: 'start' });

        clearActiveRoute();

        const destLat = j.customer && j.customer.latitude ? parseFloat(j.customer.latitude) : null;
        const destLng = j.customer && j.customer.longitude ? parseFloat(j.customer.longitude) : null;

        if (!destLat || !destLng || isNaN(destLat) || isNaN(destLng)) {
            alert(`Outlet "${j.customer ? j.customer.name : 'ini'}" belum memiliki koordinat GPS. Harap isi di data outlet.`);
            return;
        }

        const origLat = j.origin && j.origin.latitude ? parseFloat(j.origin.latitude) : null;
        const origLng = j.origin && j.origin.longitude ? parseFloat(j.origin.longitude) : null;

        // 1. PIN B (Outlet Tujuan)
        const pinBIcon = window.L.divIcon({
            className: 'custom-dest-pin',
            html: `<div style="background:#f59e0b; width:34px; height:34px; border-radius:50%; border:3px solid #ffffff; box-shadow:0 3px 10px rgba(0,0,0,0.35); display:flex; align-items:center; justify-content:center; color:#ffffff; font-weight:bold; font-size:13px;">B</div>`,
            iconSize: [34, 34],
            iconAnchor: [17, 17]
        });

        pinBMarker = window.L.marker([destLat, destLng], { icon: pinBIcon }).addTo(map);
        pinBMarker.bindPopup(`
            <div style="font-size:12px; line-height:1.5; min-width:200px;">
                <div style="font-size:10px; font-weight:bold; color:#d97706; text-transform:uppercase;">Outlet Tujuan (Pin B)</div>
                <strong style="color:#0f172a; font-size:13px;">${j.customer.name}</strong><br>
                <span style="color:#64748b;">${j.customer.address || '-'}</span><br>
                <div style="margin-top:4px; padding-top:4px; border-top:1px solid #f1f5f9; font-size:11px;">
                    <span style="color:#475569;">Kode Tugas: <strong>${j.task_code}</strong></span><br>
                    <span style="color:#475569;">Sales: <strong>${j.sales ? j.sales.name : '-'}</strong></span>
                </div>
            </div>
        `);

        // 2. PIN A (Titik Keberangkatan / Asal)
        if (origLat && origLng && !isNaN(origLat) && !isNaN(origLng)) {
            const pinAIcon = window.L.divIcon({
                className: 'custom-origin-pin',
                html: `<div style="background:#2563eb; width:34px; height:34px; border-radius:50%; border:3px solid #ffffff; box-shadow:0 3px 10px rgba(0,0,0,0.35); display:flex; align-items:center; justify-content:center; color:#ffffff; font-weight:bold; font-size:13px;">A</div>`,
                iconSize: [34, 34],
                iconAnchor: [17, 17]
            });

            pinAMarker = window.L.marker([origLat, origLng], { icon: pinAIcon }).addTo(map);
            pinAMarker.bindPopup(`
                <div style="font-size:12px; line-height:1.5; min-width:200px;">
                    <div style="font-size:10px; font-weight:bold; color:#2563eb; text-transform:uppercase;">Titik Keberangkatan (Pin A)</div>
                    <strong style="color:#0f172a; font-size:13px;">${j.origin.label || 'Titik Awal'}</strong><br>
                    <span style="color:#64748b;">Sales Motoris: <strong>${j.sales ? j.sales.name : '-'}</strong></span><br>
                    ${j.origin.time ? `<span style="color:#94a3b8; font-size:11px;">Waktu Berangkat: ${j.origin.time}</span>` : ''}
                </div>
            `);
        }

        // 3. PIN CHECK-IN (Jika ada koordinat check-in)
        const checkInLat = j.check_in && j.check_in.latitude ? parseFloat(j.check_in.latitude) : null;
        const checkInLng = j.check_in && j.check_in.longitude ? parseFloat(j.check_in.longitude) : null;
        if (checkInLat && checkInLng && !isNaN(checkInLat) && !isNaN(checkInLng)) {
            const checkInIcon = window.L.divIcon({
                className: 'custom-checkin-pin',
                html: `<div style="background:#10b981; width:26px; height:26px; border-radius:50%; border:2px solid #ffffff; box-shadow:0 2px 6px rgba(0,0,0,0.3); display:flex; align-items:center; justify-content:center; color:#ffffff;"><svg style="width:14px; height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg></div>`,
                iconSize: [26, 26],
                iconAnchor: [13, 13]
            });
            pinCheckInMarker = window.L.marker([checkInLat, checkInLng], { icon: checkInIcon }).addTo(map);
            pinCheckInMarker.bindPopup(`
                <div style="font-size:12px; line-height:1.4;">
                    <strong style="color:#059669;">Lokasi Check-in Tiba</strong><br>
                    <span>${j.check_in.at || '-'}</span><br>
                    ${j.check_in.distance_meters ? `<span style="font-size:11px; color:#64748b;">${j.check_in.distance_meters}m dari toko</span>` : ''}
                </div>
            `);
        }

        // 4. DRAW ROUTE (OSRM Jalan Raya atau Fallback Direct Polyline)
        let calculatedKm = j.distance_km;
        let calculatedMinutes = j.duration ? j.duration.trip_minutes : null;

        if (origLat && origLng && !isNaN(origLat) && !isNaN(origLng)) {
            try {
                const osrmUrl = `https://router.project-osrm.org/route/v1/driving/${origLng},${origLat};${destLng},${destLat}?overview=full&geometries=geojson`;
                const rRes = await fetch(osrmUrl);
                const rData = await rRes.json();

                if (rData.code === 'Ok' && rData.routes && rData.routes.length > 0) {
                    const route = rData.routes[0];
                    calculatedKm = calculatedKm || (route.distance / 1000).toFixed(1);
                    calculatedMinutes = calculatedMinutes || Math.round(route.duration / 60);

                    // Underlay Glow
                    activeShadowPolyline = window.L.geoJSON(route.geometry, {
                        style: { color: '#0369a1', weight: 8, opacity: 0.35 }
                    }).addTo(map);

                    // Primary Polyline
                    activeRoutePolyline = window.L.geoJSON(route.geometry, {
                        style: {
                            color: j.status === 'SELESAI' ? '#059669' : '#0284c7',
                            weight: 4,
                            opacity: 0.9,
                            dashArray: j.status === 'SELESAI' ? null : '6, 8'
                        }
                    }).addTo(map);

                    map.fitBounds(activeRoutePolyline.getBounds(), { padding: [50, 50] });
                } else {
                    throw new Error('OSRM route unavailable');
                }
            } catch (err) {
                console.warn('OSRM routing fallback to direct polyline:', err);
                activeRoutePolyline = window.L.polyline([[origLat, origLng], [destLat, destLng]], {
                    color: j.status === 'SELESAI' ? '#059669' : '#0284c7',
                    weight: 4,
                    dashArray: '6, 8',
                    opacity: 0.8
                }).addTo(map);
                map.fitBounds(activeRoutePolyline.getBounds(), { padding: [50, 50] });
            }
        } else {
            // Zoom to outlet directly
            map.setView([destLat, destLng], 15);
        }

        // 5. UPDATE FLOATING ROUTE CARD
        updateFloatingRouteCard(j, origLat, origLng, destLat, destLng, calculatedKm, calculatedMinutes);
    }

    // UPDATE FLOATING METRICS CARD
    function updateFloatingRouteCard(j, origLat, origLng, destLat, destLng, calculatedKm, calculatedMinutes) {
        const card = document.getElementById('floatingRouteCard');
        if (!card) return;

        // Title & Subtitle
        document.getElementById('routeCardTitle').textContent = `${j.task_code} → ${j.customer.name}`;
        document.getElementById('routeCardSubtitle').textContent = `Sales: ${j.sales ? j.sales.name : '-'} (${j.sales ? j.sales.team : '-'}) • Titik Asal: ${j.origin && j.origin.label ? j.origin.label : 'Tercatat'}`;

        // Status Badge
        const statusBadge = document.getElementById('routeCardStatusBadge');
        statusBadge.textContent = (j.status || '').replace(/_/g, ' ');
        if (j.status === 'SELESAI') {
            statusBadge.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30';
        } else if (j.status === 'DALAM_PERJALANAN') {
            statusBadge.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-cyan-500/20 text-cyan-400 border border-cyan-500/30';
        } else {
            statusBadge.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-600/20 text-sky-400 border border-amber-500/30';
        }

        // Metric 1: Durasi Tracking ("berapa lama")
        const durBadge = document.getElementById('routeDurationBadge');
        const durSub = document.getElementById('routeDurationSub');
        if (j.duration && j.duration.total_text) {
            durBadge.textContent = j.duration.total_text;
            if (j.duration.trip_text && j.duration.visit_text) {
                durSub.textContent = `OTW: ${j.duration.trip_text} | Di Toko: ${j.duration.visit_text}`;
            } else {
                durSub.textContent = `${j.duration.started_at_text || ''} → ${j.duration.ended_at_text || ''}`;
            }
        } else if (calculatedMinutes) {
            durBadge.textContent = `~ ${calculatedMinutes} Menit`;
            durSub.textContent = 'Estimasi berkendara jalan raya';
        } else {
            durBadge.textContent = '-';
            durSub.textContent = 'Durasi belum tercatat';
        }

        // Metric 2: Jarak Tempuh
        const distBadge = document.getElementById('routeDistanceBadge');
        distBadge.textContent = calculatedKm ? `${calculatedKm} KM` : '-';

        // Metric 3: Waktu Operasional
        const timeBadge = document.getElementById('routeTimeBadge');
        const timeSub = document.getElementById('routeTimeSub');
        if (j.duration && (j.duration.started_at_text || j.duration.ended_at_text)) {
            timeBadge.textContent = `${j.duration.started_at_text || '-'} s/d ${j.duration.ended_at_text || '-'}`;
            timeSub.textContent = j.status === 'SELESAI' ? 'Kunjungan telah selesai' : 'Sedang berjalan';
        } else {
            timeBadge.textContent = j.scheduled_at ? j.scheduled_at.substring(11, 16) + ' WIB' : '-';
            timeSub.textContent = 'Jadwal operasional';
        }

        // Metric 4: Check-in
        const ciBadge = document.getElementById('routeCheckInBadge');
        const ciSub = document.getElementById('routeCheckInSub');
        if (j.check_in && j.check_in.time) {
            ciBadge.textContent = `Pukul ${j.check_in.time} WIB`;
            ciSub.textContent = j.check_in.distance_meters ? `${j.check_in.distance_meters}m dari outlet` : 'Tervalidasi di lokasi';
        } else if (j.status === 'SELESAI') {
            ciBadge.textContent = 'Selesai Terverifikasi';
            ciSub.textContent = 'Kunjungan ditutup';
        } else {
            ciBadge.textContent = 'Belum Check-in';
            ciSub.textContent = 'Menunggu kedatangan sales';
        }

        // Google Maps & Detail buttons
        const gmapsBtn = document.getElementById('btnOpenGoogleMaps');
        if (origLat && origLng && destLat && destLng) {
            gmapsBtn.href = `https://www.google.com/maps/dir/?api=1&origin=${origLat},${origLng}&destination=${destLat},${destLng}&travelmode=driving`;
        } else if (destLat && destLng) {
            gmapsBtn.href = `https://www.google.com/maps?q=${destLat},${destLng}`;
        }
        
        const detailBtn = document.getElementById('btnOpenTaskDetail');
        if (j.id) {
            detailBtn.href = `/visit-tasks/${j.id}`;
            detailBtn.classList.remove('hidden');
        } else {
            detailBtn.classList.add('hidden');
        }

        // Result box if finished & photo documentation
        const resBox = document.getElementById('routeResultBox');
        const photoCont = document.getElementById('routePhotoContainer');
        const photoImg = document.getElementById('routePhotoImg');

        if (j.check_out && (j.check_out.result || j.check_out.documentation_image)) {
            document.getElementById('routeResultText').textContent = j.check_out.result || 'Kunjungan telah selesai';
            const notesCont = document.getElementById('routeNotesContainer');
            if (j.check_out.notes) {
                document.getElementById('routeNotesText').textContent = j.check_out.notes;
                notesCont.classList.remove('hidden');
            } else {
                notesCont.classList.add('hidden');
            }

            if (j.check_out.documentation_image) {
                photoImg.src = j.check_out.documentation_image;
                photoCont.classList.remove('hidden');
            } else {
                photoCont.classList.add('hidden');
            }

            resBox.classList.remove('hidden');
        } else {
            resBox.classList.add('hidden');
            if (photoCont) photoCont.classList.add('hidden');
        }

        card.classList.remove('hidden');
    }

    function clearActiveRoute() {
        if (activeShadowPolyline) { map.removeLayer(activeShadowPolyline); activeShadowPolyline = null; }
        if (activeRoutePolyline) { map.removeLayer(activeRoutePolyline); activeRoutePolyline = null; }
        if (pinAMarker) { map.removeLayer(pinAMarker); pinAMarker = null; }
        if (pinBMarker) { map.removeLayer(pinBMarker); pinBMarker = null; }
        if (pinCheckInMarker) { map.removeLayer(pinCheckInMarker); pinCheckInMarker = null; }
        const card = document.getElementById('floatingRouteCard');
        if (card) card.classList.add('hidden');
        const photoCont = document.getElementById('routePhotoContainer');
        if (photoCont) photoCont.classList.add('hidden');
    }

    function resetMapView() {
        clearActiveRoute();
        map.setView(defaultCenter, 12);
    }

    // Kadiv / Supervisor Route Planner Functions
    function openPlanRouteModal() {
        document.getElementById('modalPlanRoute').classList.replace('hidden', 'flex');
    }

    function closePlanRouteModal() {
        document.getElementById('modalPlanRoute').classList.replace('flex', 'hidden');
    }

    function onPlanCustomerChange() {
        const sel = document.getElementById('planCustomerId');
        const opt = sel.options[sel.selectedIndex];
        const info = document.getElementById('planCustomerInfo');
        const coordsSpan = document.getElementById('planCustomerCoords');

        if (opt && opt.value) {
            const lat = opt.getAttribute('data-lat');
            const lng = opt.getAttribute('data-lng');
            if (lat && lng) {
                coordsSpan.textContent = `${parseFloat(lat).toFixed(4)}, ${parseFloat(lng).toFixed(4)}`;
                coordsSpan.className = 'font-mono font-semibold text-emerald-700';
                info.classList.remove('hidden');
            } else {
                coordsSpan.textContent = 'Belum memiliki koordinat GPS';
                coordsSpan.className = 'font-semibold text-rose-600';
                info.classList.remove('hidden');
            }
        } else {
            info.classList.add('hidden');
        }
    }

    function onPlanOriginTypeChange() {
        const radios = document.getElementsByName('planOriginType');
        let selVal = 'browser_gps';
        for (const r of radios) {
            if (r.checked) selVal = r.value;
        }
        const customRow = document.getElementById('planCustomCoordsRow');
        if (selVal === 'custom') {
            customRow.classList.replace('hidden', 'grid');
        } else {
            customRow.classList.replace('grid', 'hidden');
        }
    }

    async function executePlanRoute() {
        const custSel = document.getElementById('planCustomerId');
        const custOpt = custSel.options[custSel.selectedIndex];
        if (!custOpt || !custOpt.value) {
            alert('Harap pilih Outlet Tujuan terlebih dahulu.');
            return;
        }

        const destLat = parseFloat(custOpt.getAttribute('data-lat'));
        const destLng = parseFloat(custOpt.getAttribute('data-lng'));
        if (isNaN(destLat) || isNaN(destLng)) {
            alert('Outlet yang dipilih belum memiliki koordinat GPS.');
            return;
        }

        const salesSel = document.getElementById('planSalesId');
        const salesOpt = salesSel.options[salesSel.selectedIndex];
        const salesName = salesOpt && salesOpt.value ? salesOpt.getAttribute('data-name') : 'Sales Motoris';

        const radios = document.getElementsByName('planOriginType');
        let originType = 'browser_gps';
        for (const r of radios) {
            if (r.checked) originType = r.value;
        }

        let origLat = null;
        let origLng = null;
        let origLabel = 'Titik Berangkat';

        if (originType === 'browser_gps') {
            origLabel = 'Lokasi GPS Anda (Browser)';
            if (userCurrentLat && userCurrentLng) {
                origLat = userCurrentLat;
                origLng = userCurrentLng;
            } else if (navigator.geolocation) {
                try {
                    const pos = await new Promise((res, rej) => navigator.geolocation.getCurrentPosition(res, rej, { timeout: 5000 }));
                    origLat = pos.coords.latitude;
                    origLng = pos.coords.longitude;
                    userCurrentLat = origLat;
                    userCurrentLng = origLng;
                } catch (e) {
                    origLat = -7.4478;
                    origLng = 112.7183;
                    origLabel = 'Depo Distribusi (GPS Peramban Tidak Diizinkan)';
                }
            } else {
                origLat = -7.4478;
                origLng = 112.7183;
                origLabel = 'Depo Distribusi Sidoarjo';
            }
        } else if (originType === 'sales_gps') {
            origLabel = `Posisi GPS ${salesName}`;
            if (salesOpt && salesOpt.getAttribute('data-lat')) {
                origLat = parseFloat(salesOpt.getAttribute('data-lat'));
                origLng = parseFloat(salesOpt.getAttribute('data-lng'));
            }
            if (!origLat || isNaN(origLat)) {
                origLat = -7.4478;
                origLng = 112.7183;
                origLabel = 'Depo Distribusi Sidoarjo (Sales Belum Tracking)';
            }
        } else if (originType === 'depot_sidoarjo') {
            origLat = -7.4478;
            origLng = 112.7183;
            origLabel = 'Depo Distribusi Sidoarjo / Jatim';
        } else if (originType === 'depot_surabaya' || originType === 'depot_jakarta') {
            origLat = -7.2575;
            origLng = 112.7521;
            origLabel = 'Kantor Pusat Surabaya';
        } else if (originType === 'custom') {
            origLat = parseFloat(document.getElementById('planCustomLat').value);
            origLng = parseFloat(document.getElementById('planCustomLng').value);
            origLabel = 'Koordinat Khusus Manual';
            if (isNaN(origLat) || isNaN(origLng)) {
                alert('Harap masukkan angka koordinat latitude & longitude yang valid.');
                return;
            }
        }

        const simulatedJourney = {
            id: null,
            task_code: 'RENCANA-RUTE',
            status: 'SIMULASI_RUTE',
            sales: { name: salesName, team: 'Tim Motoris' },
            customer: {
                name: custOpt.getAttribute('data-name'),
                address: custOpt.getAttribute('data-address'),
                latitude: destLat,
                longitude: destLng
            },
            origin: {
                label: origLabel,
                latitude: origLat,
                longitude: origLng,
                time: 'Estimasi Berangkat'
            },
            check_in: {},
            check_out: {},
            duration: {},
            distance_km: null
        };

        closePlanRouteModal();
        await showTaskJourney(simulatedJourney);
    }

    function proceedToCreateTaskFromPlan() {
        const custSel = document.getElementById('planCustomerId');
        const custVal = custSel ? custSel.value : '';
        const salesSel = document.getElementById('planSalesId');
        const salesVal = salesSel ? salesSel.value : '';

        closePlanRouteModal();
        openCreateModal();

        if (custVal) {
            const createCustSel = document.querySelector('#modalCreateVisit select[name="customer_id"]');
            if (createCustSel) createCustSel.value = custVal;
        }
        if (salesVal) {
            const createSalesSel = document.querySelector('#modalCreateVisit select[name="sales_id"]');
            if (createSalesSel) createSalesSel.value = salesVal;
        }
    }

    // Sales Tracking Controller (Sales Motoris Role)
    let activeSessionId = null;
    let watchId = null;
    let locationInterval = null;

    async function checkSalesSession() {
        try {
            const res = await fetch('/api/tracking/status');
            const data = await res.json();
            const btn = document.getElementById('btnToggleTracking');
            const statusBadge = document.getElementById('salesSessionStatus');
            const icon = document.getElementById('salesGpsIcon');

            if (data.active) {
                activeSessionId = data.session.id;
                btn.innerHTML = '<svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M6 6h12v12H6z"/></svg><span>Akhiri Sesi Tracking</span>';
                btn.className = 'inline-flex items-center gap-2 rounded-xl bg-rose-600 px-4 py-2.5 text-xs font-bold text-white shadow-sm hover:bg-rose-700 transition';
                statusBadge.textContent = 'Tracking Aktif';
                statusBadge.className = 'inline-flex items-center gap-1 rounded-full bg-emerald-100 text-emerald-800 px-2.5 py-0.5 text-[11px] font-bold';
                icon.className = 'flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700 text-xl font-bold border border-emerald-300 animate-pulse';
                startLocalGeoWatch();
            } else {
                activeSessionId = null;
                btn.innerHTML = '<svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg><span>Mulai Sesi Tracking</span>';
                btn.className = 'inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-xs font-bold text-white shadow-sm hover:bg-emerald-700 transition';
                statusBadge.textContent = 'Tidak Aktif';
                statusBadge.className = 'inline-flex items-center gap-1 rounded-full bg-slate-100 text-slate-600 px-2.5 py-0.5 text-[11px] font-semibold';
                icon.className = 'flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-600 text-xl font-bold border border-slate-200';
                stopLocalGeoWatch();
            }
        } catch (e) {
            console.error('Check sales session error', e);
        }
    }

    async function toggleTrackingSession() {
        const btn = document.getElementById('btnToggleTracking');
        btn.disabled = true;

        if (activeSessionId) {
            try {
                await fetch('/api/tracking/end', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                });
                activeSessionId = null;
                checkSalesSession();
                fetchLivePositions();
            } catch (e) {
                console.error(e);
            }
        } else {
            if (!navigator.geolocation) {
                alert('Geolokasi tidak didukung oleh browser Anda.');
                btn.disabled = false;
                return;
            }

            navigator.geolocation.getCurrentPosition(
                async pos => {
                    try {
                        const res = await fetch('/api/tracking/start', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                        });
                        const data = await res.json();
                        activeSessionId = data.session.id;

                        await sendCoords(pos);
                        checkSalesSession();
                        fetchLivePositions();
                    } catch (e) {
                        console.error(e);
                    }
                    btn.disabled = false;
                },
                err => {
                    alert('Gagal mengaktifkan GPS: ' + err.message + '. Harap izinkan akses lokasi di browser.');
                    btn.disabled = false;
                },
                { enableHighAccuracy: true, timeout: 8000 }
            );
            return;
        }
        btn.disabled = false;
    }

    function startLocalGeoWatch() {
        if (!navigator.geolocation || locationInterval) return;
        locationInterval = setInterval(() => {
            navigator.geolocation.getCurrentPosition(sendCoords, null, { enableHighAccuracy: true, timeout: 5000 });
        }, 15000);
    }

    function stopLocalGeoWatch() {
        if (locationInterval) { clearInterval(locationInterval); locationInterval = null; }
    }

    async function sendCoords(pos) {
        try {
            await fetch('/api/tracking/location', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({
                    latitude: pos.coords.latitude,
                    longitude: pos.coords.longitude,
                    accuracy: pos.coords.accuracy,
                    speed: pos.coords.speed
                })
            });
        } catch (e) {}
    }

    // Modal Helpers
    function openCreateModal() { document.getElementById('modalCreateVisit').classList.replace('hidden', 'flex'); }
    function closeCreateModal() { document.getElementById('modalCreateVisit').classList.replace('flex', 'hidden'); }

    function openEditModal(task) {
        document.getElementById('editModalTitle').textContent = 'Edit Rencana: ' + task.task_code;
        document.getElementById('formEditVisit').action = '/visits/' + task.id;
        document.getElementById('editCustomerId').value = task.customer_id;
        document.getElementById('editSalesId').value = task.sales_id;
        document.getElementById('editPriority').value = task.priority || 'normal';
        if (task.scheduled_at) {
            document.getElementById('editScheduledAt').value = task.scheduled_at.substring(0, 16);
        }
        document.getElementById('editPurpose').value = task.purpose || '';
        document.getElementById('modalEditVisit').classList.replace('hidden', 'flex');
    }
    function closeEditModal() { document.getElementById('modalEditVisit').classList.replace('flex', 'hidden'); }

    // Lightbox modal helpers for Admin / Pimpinan / Kadiv / Sales
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

    function closeImageLightbox() {
        const modal = document.getElementById('modalPhotoLightbox');
        if (modal) modal.classList.replace('flex', 'hidden');
    }
    </script>
    {{-- LIGHTBOX MODAL UNTUK MELIHAT FOTO DOKUMENTASI LAPANGAN (ADMIN / PIMPINAN / KADIV / SALES) --}}
    <div id="modalPhotoLightbox" class="fixed inset-0 z-[1200] hidden items-center justify-center bg-slate-950/85 p-4 backdrop-blur-sm animate-fadeIn">
        <div class="relative max-w-4xl w-full bg-slate-900 rounded-2xl overflow-hidden shadow-2xl border border-slate-800">
            <div class="flex items-center justify-between px-5 py-3 border-b border-slate-800 bg-slate-950">
                <div class="flex items-center gap-2 text-white text-sm font-bold truncate pr-4">
                    <svg class="h-4 w-4 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span id="lightboxTitle" class="truncate">Foto Dokumentasi Laporan Kunjungan</span>
                </div>
                <div class="flex items-center gap-3 shrink-0">
                    <a id="lightboxDownloadBtn" href="#" download class="inline-flex items-center gap-1 text-xs text-sky-400 hover:text-amber-300 font-semibold px-2.5 py-1 rounded-lg bg-slate-800/80 hover:bg-slate-800 transition">
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
</x-app-layout>
