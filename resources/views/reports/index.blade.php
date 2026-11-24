<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 rounded-md bg-blue-50 border border-blue-200/80 px-2.5 py-0.5 text-[11px] font-bold uppercase tracking-wider text-blue-700">
                        <span class="h-1.5 w-1.5 rounded-full bg-blue-600"></span>
                        Laporan Operasional & Analitik
                    </span>
                    <span class="text-xs text-slate-300 font-medium hidden sm:inline">&bull;</span>
                    <span class="text-xs text-slate-500 font-medium hidden sm:inline">Zona Waktu: Asia/Jakarta (WIB)</span>
                </div>
                <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900">
                    Pusat Laporan & Rekapitulasi Data
                </h2>
            </div>
            <div class="flex flex-wrap items-center gap-2.5">
                {{-- Tombol Export Excel --}}
                <a href="{{ route('reports.export.excel', request()->query()) }}" 
                   class="group inline-flex items-center gap-2 rounded-xl border border-emerald-300 bg-emerald-50 px-4 py-2.5 text-xs font-bold text-emerald-800 shadow-2xs hover:bg-emerald-600 hover:text-white transition-all active:scale-95"
                   title="Unduh data dalam format file spreadsheet Excel">
                    <svg class="h-4 w-4 text-emerald-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span>Export Excel (.xlsx)</span>
                </a>

                {{-- Tombol Export PDF --}}
                <a href="{{ route('reports.export.pdf', request()->query()) }}" 
                   class="group inline-flex items-center gap-2 rounded-xl border border-rose-300 bg-rose-50 px-4 py-2.5 text-xs font-bold text-rose-800 shadow-2xs hover:bg-rose-600 hover:text-white transition-all active:scale-95"
                   title="Unduh laporan resmi format PDF lengkap dengan kop, judul, dan waktu cetak">
                    <svg class="h-4 w-4 text-rose-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    <span>Export PDF (.pdf)</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="min-h-[calc(100vh-65px)] bg-slate-100 py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">

            {{-- 1. TAB SWITCHER (PENJUALAN VS KUNJUNGAN & DURASI) --}}
            <div class="flex items-center gap-2 border-b border-slate-200 pb-2 overflow-x-auto">
                <a href="{{ route('modules.reports', array_merge(request()->except(['page']), ['type' => 'sales'])) }}" 
                   class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-xs font-bold transition-all {{ $type === 'sales' ? 'bg-slate-950 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-200/70 border border-slate-200' }}">
                    <svg class="h-4 w-4 {{ $type === 'sales' ? 'text-sky-400' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Laporan Penjualan & Pesanan</span>
                </a>

                <a href="{{ route('modules.reports', array_merge(request()->except(['page']), ['type' => 'visits'])) }}" 
                   class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-xs font-bold transition-all {{ $type === 'visits' ? 'bg-slate-950 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-200/70 border border-slate-200' }}">
                    <svg class="h-4 w-4 {{ $type === 'visits' ? 'text-sky-400' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Laporan Kunjungan & Durasi Waktu</span>
                </a>
            </div>

            {{-- 2. STATS KPI RINGKASAN TERFILTER --}}
            @if($type === 'visits')
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                {{-- Total Kunjungan --}}
                <div class="rounded-2xl border border-slate-200/90 bg-white p-4 sm:p-5 shadow-xs">
                    <div class="flex items-center justify-between">
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Total Kunjungan</p>
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 text-blue-600 border border-blue-100">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        </div>
                    </div>
                    <p class="mt-2 text-2xl sm:text-3xl font-black text-slate-900">{{ $stats['total_tasks'] }}</p>
                    <p class="mt-1 text-xs text-slate-400">Jadwal tugas sesuai filter</p>
                </div>

                {{-- Kunjungan Selesai --}}
                <div class="rounded-2xl border border-slate-200/90 bg-white p-4 sm:p-5 shadow-xs">
                    <div class="flex items-center justify-between">
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Kunjungan Selesai</p>
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-100">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>
                    <p class="mt-2 text-2xl sm:text-3xl font-black text-emerald-600">{{ $stats['completed_tasks'] }}</p>
                    <p class="mt-1 text-xs text-emerald-600 font-medium">{{ $stats['completion_rate'] }}% tingkat penyelesaian</p>
                </div>

                {{-- Total Durasi Kunjungan --}}
                <div class="rounded-2xl border border-slate-200/90 bg-white p-4 sm:p-5 shadow-xs">
                    <div class="flex items-center justify-between">
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Total Durasi Waktu</p>
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-50 text-blue-600 border border-amber-100">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>
                    <p class="mt-2 text-2xl sm:text-3xl font-black text-slate-900">{{ $stats['total_duration'] }}</p>
                    <p class="mt-1 text-xs text-slate-400">Akumulasi waktu di outlet</p>
                </div>

                {{-- Rata-Rata Waktu per Kunjungan --}}
                <div class="rounded-2xl border border-slate-200/90 bg-white p-4 sm:p-5 shadow-xs">
                    <div class="flex items-center justify-between">
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Rata-Rata Durasi</p>
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 border border-indigo-100">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                    </div>
                    <p class="mt-2 text-2xl sm:text-3xl font-black text-indigo-600">{{ $stats['avg_minutes'] }} Menit</p>
                    <p class="mt-1 text-xs text-slate-400">Per outlet terselesaikan</p>
                </div>
            </div>
            @else
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                {{-- Total Pesanan --}}
                <div class="rounded-2xl border border-slate-200/90 bg-white p-4 sm:p-5 shadow-xs">
                    <div class="flex items-center justify-between">
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Total Transaksi</p>
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 text-blue-600 border border-blue-100">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        </div>
                    </div>
                    <p class="mt-2 text-2xl sm:text-3xl font-black text-slate-900">{{ $stats['total_orders'] }}</p>
                    <p class="mt-1 text-xs text-slate-400">Pesanan terfilter</p>
                </div>

                {{-- Total Omzet --}}
                <div class="rounded-2xl border border-slate-200/90 bg-white p-4 sm:p-5 shadow-xs">
                    <div class="flex items-center justify-between">
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Total Omzet Penjualan</p>
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-50 text-blue-600 border border-amber-100">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>
                    <p class="mt-2 text-xl sm:text-2xl font-black text-blue-600 truncate">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</p>
                    <p class="mt-1 text-xs text-slate-400">Total nilai penjualan bruto</p>
                </div>

                {{-- Pesanan Selesai --}}
                <div class="rounded-2xl border border-slate-200/90 bg-white p-4 sm:p-5 shadow-xs">
                    <div class="flex items-center justify-between">
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Pesanan Tuntas</p>
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-100">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>
                    <p class="mt-2 text-2xl sm:text-3xl font-black text-emerald-600">{{ $stats['completed_count'] }}</p>
                    <p class="mt-1 text-xs text-emerald-600 font-medium">Status selesai (completed)</p>
                </div>

                {{-- Rata-Rata Nilai Pesanan --}}
                <div class="rounded-2xl border border-slate-200/90 bg-white p-4 sm:p-5 shadow-xs">
                    <div class="flex items-center justify-between">
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Rata-Rata Order</p>
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 border border-indigo-100">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                        </div>
                    </div>
                    <p class="mt-2 text-xl sm:text-2xl font-black text-slate-900 truncate">Rp {{ number_format($stats['avg_order'], 0, ',', '.') }}</p>
                    <p class="mt-1 text-xs text-slate-400">Rata-rata omzet per nota</p>
                </div>
            </div>
            @endif

            {{-- 3. FORM FILTERING & SEARCHING LENGKAP --}}
            <div class="rounded-2xl border border-slate-200/90 bg-white p-5 shadow-xs">
                <form method="GET" action="{{ route('modules.reports') }}" class="space-y-4">
                    <input type="hidden" name="type" value="{{ $type }}">

                    {{-- Baris 1: Searching Bar Utama --}}
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">
                            Pencarian Kata Kunci
                        </label>
                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
                                <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <input type="text" name="search" value="{{ request('search') }}" 
                                   placeholder="Cari kode order, kode tugas, nama sales motoris, nama toko/outlet..." 
                                   class="w-full rounded-xl border border-slate-300 bg-slate-50/50 py-2.5 pl-10 pr-4 text-xs font-medium text-slate-900 placeholder:text-slate-400 focus:border-amber-500 focus:bg-white focus:outline-hidden focus:ring-1 focus:ring-amber-500 transition">
                        </div>
                    </div>

                    {{-- Baris 2: Dropdown Tim, Sales, Status, dan Periode Cepat --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                        {{-- Filter Tim Sales --}}
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Tim Sales</label>
                            <select name="team_id" class="w-full rounded-xl border border-slate-300 bg-white py-2 px-3 text-xs font-medium text-slate-800 focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                                <option value="">Semua Tim Sales</option>
                                @foreach($teams as $team)
                                    <option value="{{ $team->id }}" {{ request('team_id') == $team->id ? 'selected' : '' }}>
                                        {{ $team->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Filter Sales Motoris --}}
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Sales Motoris</label>
                            <select name="sales_id" class="w-full rounded-xl border border-slate-300 bg-white py-2 px-3 text-xs font-medium text-slate-800 focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                                <option value="">Semua Sales Motoris</option>
                                @foreach($salesList as $s)
                                    <option value="{{ $s->id }}" {{ request('sales_id') == $s->id ? 'selected' : '' }}>
                                        {{ $s->name }} ({{ $s->team?->name ?? 'Tanpa Tim' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Filter Status --}}
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Status {{ $type === 'visits' ? 'Kunjungan' : 'Pesanan' }}</label>
                            <select name="status" class="w-full rounded-xl border border-slate-300 bg-white py-2 px-3 text-xs font-medium text-slate-800 focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                                <option value="">Semua Status</option>
                                @if($type === 'visits')
                                    <option value="SELESAI" {{ request('status') === 'SELESAI' ? 'selected' : '' }}>Selesai</option>
                                    <option value="SEDANG_KUNJUNGAN" {{ request('status') === 'SEDANG_KUNJUNGAN' ? 'selected' : '' }}>Sedang Kunjungan</option>
                                    <option value="TIBA_DI_LOKASI" {{ request('status') === 'TIBA_DI_LOKASI' ? 'selected' : '' }}>Tiba di Lokasi</option>
                                    <option value="DALAM_PERJALANAN" {{ request('status') === 'DALAM_PERJALANAN' ? 'selected' : '' }}>Dalam Perjalanan</option>
                                    <option value="DITERIMA" {{ request('status') === 'DITERIMA' ? 'selected' : '' }}>Diterima</option>
                                    <option value="MENUNGGU_RESPONS" {{ request('status') === 'MENUNGGU_RESPONS' ? 'selected' : '' }}>Menunggu Respons</option>
                                    <option value="DITOLAK" {{ request('status') === 'DITOLAK' ? 'selected' : '' }}>Ditolak</option>
                                @else
                                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai (Completed)</option>
                                    <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Dikonfirmasi (Confirmed)</option>
                                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Dibatalkan (Cancelled)</option>
                                @endif
                            </select>
                        </div>

                        {{-- Filter Shortcut Periode --}}
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Durasi / Periode Waktu</label>
                            <select name="period" class="w-full rounded-xl border border-slate-300 bg-white py-2 px-3 text-xs font-medium text-slate-800 focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                                <option value="" {{ !request('period') && !request('date_from') ? 'selected' : '' }}>Semua Waktu</option>
                                <option value="today" {{ request('period') === 'today' ? 'selected' : '' }}>Hari Ini</option>
                                <option value="this_week" {{ request('period') === 'this_week' ? 'selected' : '' }}>Minggu Ini</option>
                                <option value="this_month" {{ request('period') === 'this_month' ? 'selected' : '' }}>Bulan Ini</option>
                            </select>
                        </div>
                    </div>

                    {{-- Baris 3: Rentang Tanggal Kustom & Tombol Aksi --}}
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-2 border-t border-slate-100">
                        <div class="flex flex-wrap items-center gap-2 text-xs">
                            <span class="font-bold text-slate-600">Rentang Tanggal:</span>
                            <input type="date" name="date_from" value="{{ request('date_from') }}" 
                                   class="rounded-xl border border-slate-300 bg-white py-1.5 px-3 text-xs text-slate-700 focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                            <span class="text-slate-400">s/d</span>
                            <input type="date" name="date_to" value="{{ request('date_to') }}" 
                                   class="rounded-xl border border-slate-300 bg-white py-1.5 px-3 text-xs text-slate-700 focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
                        </div>

                        <div class="flex items-center gap-2">
                            <button type="submit" 
                                    class="inline-flex items-center gap-1.5 rounded-xl bg-slate-950 px-4 py-2 text-xs font-bold text-white shadow-xs hover:bg-slate-800 transition active:scale-95">
                                <svg class="h-4 w-4 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                                <span>Terapkan Filter</span>
                            </button>
                            <a href="{{ route('modules.reports', ['type' => $type]) }}" 
                               class="rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition active:scale-95">
                                Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            {{-- 4. TABEL DATA LAPORAN (RESPONSIF DUAL-VIEW) --}}
            <div class="rounded-2xl border border-slate-200/90 bg-white shadow-xs overflow-hidden">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-slate-100 px-6 py-4 bg-slate-50/60 gap-2">
                    <div>
                        <h3 class="font-bold text-slate-900 text-sm sm:text-base">
                            {{ $type === 'visits' ? 'Data Kunjungan & Durasi Waktu Lapangan' : 'Data Rekapitulasi Pesanan & Penjualan' }}
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Menampilkan hasil terfilter yang siap dicetak ke Excel maupun PDF.
                        </p>
                    </div>
                    <span class="text-xs font-bold text-slate-500 bg-slate-100 rounded-lg px-2.5 py-1 shrink-0">
                        {{ $type === 'visits' ? $tasks->total() : $orders->total() }} Data Ditemukan
                    </span>
                </div>

                {{-- KONTEN LAPORAN KUNJUNGAN & DURASI --}}
                @if($type === 'visits')
                    {{-- A. Desktop Table --}}
                    <div class="hidden md:block overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-100 text-left text-xs">
                            <thead class="bg-slate-50/80 text-slate-500 uppercase font-bold tracking-wider">
                                <tr>
                                    <th class="px-5 py-3.5">Kode Tugas</th>
                                    <th class="px-5 py-3.5">Jadwal</th>
                                    <th class="px-5 py-3.5">Outlet Tujuan</th>
                                    <th class="px-5 py-3.5">Sales Motoris</th>
                                    <th class="px-5 py-3.5">Tim Sales</th>
                                    <th class="px-5 py-3.5 text-center">Waktu Masuk</th>
                                    <th class="px-5 py-3.5 text-center">Waktu Keluar</th>
                                    <th class="px-5 py-3.5 text-center">Durasi Waktu</th>
                                    <th class="px-5 py-3.5 text-center">Status</th>
                                    <th class="px-5 py-3.5 text-center">Foto</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($tasks as $task)
                                    @php
                                        $durationStr = '-';
                                        if ($task->check_in_at && $task->check_out_at) {
                                            $mins = $task->check_out_at->diffInMinutes($task->check_in_at);
                                            $h = floor($mins / 60);
                                            $m = $mins % 60;
                                            $durationStr = $h > 0 ? "{$h}j {$m}m" : "{$mins} Menit";
                                        } elseif ($task->check_in_at) {
                                            $mins = now()->diffInMinutes($task->check_in_at);
                                            $durationStr = "{$mins}m (Aktif)";
                                        }

                                        $badgeClass = match($task->status) {
                                            'SELESAI' => 'bg-emerald-50 text-emerald-700 border-emerald-200/80',
                                            'DALAM_PERJALANAN', 'TIBA_DI_LOKASI', 'SEDANG_KUNJUNGAN', 'DITERIMA' => 'bg-sky-50 text-sky-700 border-sky-200/80',
                                            'MENUNGGU_RESPONS' => 'bg-amber-50 text-amber-700 border-amber-200/80',
                                            'DITOLAK' => 'bg-rose-50 text-rose-700 border-rose-200/80',
                                            default => 'bg-slate-100 text-slate-700 border-slate-200',
                                        };
                                    @endphp
                                    <tr class="hover:bg-slate-50/80 transition">
                                        <td class="px-5 py-4 font-bold text-slate-900">
                                            <a href="{{ route('visit-tasks.show', $task) }}" class="hover:text-blue-600 transition">
                                                {{ $task->task_code }}
                                            </a>
                                        </td>
                                        <td class="px-5 py-4 text-slate-600">
                                            {{ $task->scheduled_at ? $task->scheduled_at->format('d M Y, H:i') : '-' }}
                                        </td>
                                        <td class="px-5 py-4">
                                            <span class="font-bold text-slate-900 block">{{ $task->customer->name ?? '-' }}</span>
                                            <span class="text-[11px] text-slate-400 block truncate max-w-xs">{{ $task->customer->address ?? '' }}</span>
                                        </td>
                                        <td class="px-5 py-4 text-slate-700 font-medium">
                                            {{ $task->sales->name ?? '-' }}
                                        </td>
                                        <td class="px-5 py-4 text-slate-600">
                                            <span class="rounded bg-slate-100 px-2 py-0.5 text-[11px] font-semibold text-slate-700">
                                                {{ $task->sales->team->name ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="px-5 py-4 text-center text-slate-600">
                                            {{ $task->check_in_at ? $task->check_in_at->format('H:i') : '-' }}
                                        </td>
                                        <td class="px-5 py-4 text-center text-slate-600">
                                            {{ $task->check_out_at ? $task->check_out_at->format('H:i') : '-' }}
                                        </td>
                                        <td class="px-5 py-4 text-center">
                                            <span class="font-bold text-slate-900 px-2.5 py-1 rounded-lg {{ $task->check_out_at ? 'bg-emerald-50 text-emerald-700 border border-emerald-200/60' : 'bg-slate-100 text-slate-700' }}">
                                                {{ $durationStr }}
                                            </span>
                                        </td>
                                        <td class="px-5 py-4 text-center">
                                            <span class="rounded-full px-2.5 py-1 text-[10px] font-bold border {{ $badgeClass }}">
                                                {{ str_replace('_', ' ', $task->status) }}
                                            </span>
                                        </td>
                                        <td class="px-5 py-4 text-center">
                                            @if($task->documentation_image)
                                                <a href="{{ asset('storage/' . $task->documentation_image) }}" target="_blank" class="text-blue-600 hover:text-amber-700 font-bold" title="Buka foto dokumentasi">
                                                    Lihat Foto
                                                </a>
                                            @else
                                                <span class="text-slate-300">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="px-6 py-10 text-center text-slate-400">
                                            Tidak ditemukan data kunjungan yang sesuai dengan filter.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- B. Mobile Cards --}}
                    <div class="block md:hidden divide-y divide-slate-100">
                        @forelse($tasks as $task)
                            @php
                                $durationStr = '-';
                                if ($task->check_in_at && $task->check_out_at) {
                                    $mins = $task->check_out_at->diffInMinutes($task->check_in_at);
                                    $h = floor($mins / 60);
                                    $m = $mins % 60;
                                    $durationStr = $h > 0 ? "{$h}j {$m}m" : "{$mins} Menit";
                                } elseif ($task->check_in_at) {
                                    $mins = now()->diffInMinutes($task->check_in_at);
                                    $durationStr = "{$mins}m (Aktif)";
                                }
                            @endphp
                            <div class="p-4 space-y-2.5 hover:bg-slate-50 transition">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-slate-900 text-xs">{{ $task->task_code }}</span>
                                    <span class="rounded-full px-2 py-0.5 text-[10px] font-bold bg-slate-100 text-slate-700">
                                        {{ str_replace('_', ' ', $task->status) }}
                                    </span>
                                </div>
                                <div>
                                    <p class="font-bold text-slate-900 text-sm">{{ $task->customer->name ?? '-' }}</p>
                                    <p class="text-xs text-slate-400">{{ $task->sales->name ?? '-' }} &bull; {{ $task->sales->team->name ?? '-' }}</p>
                                </div>
                                <div class="flex items-center justify-between text-xs pt-1 border-t border-slate-100">
                                    <span class="text-slate-500">Durasi: <strong class="text-slate-900">{{ $durationStr }}</strong></span>
                                    <a href="{{ route('visit-tasks.show', $task) }}" class="font-bold text-blue-600">Detail &rarr;</a>
                                </div>
                            </div>
                        @empty
                            <div class="p-8 text-center text-xs text-slate-400">Tidak ada data kunjungan.</div>
                        @endforelse
                    </div>

                {{-- KONTEN LAPORAN PENJUALAN --}}
                @else
                    {{-- A. Desktop Table --}}
                    <div class="hidden md:block overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-100 text-left text-xs">
                            <thead class="bg-slate-50/80 text-slate-500 uppercase font-bold tracking-wider">
                                <tr>
                                    <th class="px-5 py-3.5">Kode Order</th>
                                    <th class="px-5 py-3.5">Tanggal</th>
                                    <th class="px-5 py-3.5">Outlet / Pelanggan</th>
                                    <th class="px-5 py-3.5">Sales Motoris</th>
                                    <th class="px-5 py-3.5">Tim Sales</th>
                                    <th class="px-5 py-3.5 text-center">Total Item</th>
                                    <th class="px-5 py-3.5 text-center">Status</th>
                                    <th class="px-5 py-3.5 text-right">Total Nilai Omzet</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($orders as $order)
                                    @php
                                        $badgeClass = match(strtolower($order->status)) {
                                            'completed' => 'bg-emerald-50 text-emerald-700 border-emerald-200/80',
                                            'confirmed' => 'bg-sky-50 text-sky-700 border-sky-200/80',
                                            'cancelled' => 'bg-rose-50 text-rose-700 border-rose-200/80',
                                            default => 'bg-slate-100 text-slate-700 border-slate-200',
                                        };
                                    @endphp
                                    <tr class="hover:bg-slate-50/80 transition">
                                        <td class="px-5 py-4 font-bold text-slate-900">
                                            {{ $order->order_number }}
                                        </td>
                                        <td class="px-5 py-4 text-slate-600">
                                            {{ $order->order_date ? \Carbon\Carbon::parse($order->order_date)->format('d M Y') : '-' }}
                                        </td>
                                        <td class="px-5 py-4 font-bold text-slate-900">
                                            {{ $order->customer->name ?? '-' }}
                                        </td>
                                        <td class="px-5 py-4 text-slate-700 font-medium">
                                            {{ $order->user->name ?? '-' }}
                                        </td>
                                        <td class="px-5 py-4 text-slate-600">
                                            <span class="rounded bg-slate-100 px-2 py-0.5 text-[11px] font-semibold text-slate-700">
                                                {{ $order->user->team->name ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="px-5 py-4 text-center text-slate-600 font-semibold">
                                            {{ $order->items_count ?? $order->items->sum('quantity') }}
                                        </td>
                                        <td class="px-5 py-4 text-center">
                                            <span class="rounded-full px-2.5 py-1 text-[10px] font-bold border {{ $badgeClass }}">
                                                {{ strtoupper($order->status) }}
                                            </span>
                                        </td>
                                        <td class="px-5 py-4 text-right font-black text-slate-900">
                                            Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-6 py-10 text-center text-slate-400">
                                            Tidak ditemukan data pesanan penjualan yang sesuai dengan filter.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- B. Mobile Cards --}}
                    <div class="block md:hidden divide-y divide-slate-100">
                        @forelse($orders as $order)
                            <div class="p-4 space-y-2.5 hover:bg-slate-50 transition">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-slate-900 text-xs">{{ $order->order_number }}</span>
                                    <span class="font-black text-blue-600 text-xs">
                                        Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                    </span>
                                </div>
                                <div>
                                    <p class="font-bold text-slate-900 text-sm">{{ $order->customer->name ?? '-' }}</p>
                                    <p class="text-xs text-slate-400">{{ $order->user->name ?? '-' }} &bull; {{ $order->user->team->name ?? '-' }}</p>
                                </div>
                                <div class="flex items-center justify-between text-xs pt-1 border-t border-slate-100">
                                    <span class="text-slate-500">Tanggal: {{ $order->order_date ? \Carbon\Carbon::parse($order->order_date)->format('d/m/Y') : '-' }}</span>
                                    <span class="rounded-full px-2 py-0.5 text-[10px] font-bold bg-slate-100 text-slate-700">
                                        {{ strtoupper($order->status) }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="p-8 text-center text-xs text-slate-400">Tidak ada data penjualan.</div>
                        @endforelse
                    </div>
                @endif

                {{-- Pagination Links --}}
                <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                    {{ ($type === 'visits' ? $tasks : $orders)->links() }}
                </div>
            </div>

            {{-- 5. KETERANGAN RESMI DOWNLOAD --}}
            <div class="rounded-xl border border-slate-200/80 bg-slate-50 p-4 text-xs text-slate-500 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2">
                <div class="flex items-center gap-2">
                    <svg class="h-4 w-4 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>File unduhan <strong>Excel (.xlsx)</strong> dan <strong>PDF (.pdf)</strong> secara otomatis menyertakan <strong>Judul Laporan</strong>, <strong>Tanggal & Jam Waktu Download</strong>, serta nama pengguna yang mencetak laporan.</span>
                </div>
                <span class="font-medium text-slate-400 shrink-0">MotorisTrack Report Engine</span>
            </div>

        </div>
    </div>
</x-app-layout>

