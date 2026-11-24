<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 rounded-md bg-blue-50 border border-blue-200/80 px-2.5 py-0.5 text-[11px] font-bold uppercase tracking-wider text-blue-700">
                        <span class="h-1.5 w-1.5 rounded-full bg-blue-600"></span>
                        Dashboard {{ $role }}
                    </span>
                    <span class="text-xs text-slate-300 font-medium hidden sm:inline">&bull;</span>
                    <span class="text-xs text-slate-500 font-medium hidden sm:inline">{{ now()->translatedFormat('l, d F Y') }}</span>
                </div>
                <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900">
                    Ringkasan Operasional Lapangan
                </h2>
            </div>
            <div class="flex flex-wrap items-center gap-2.5">
                <a href="{{ route('tracking.index') }}" 
                   class="group inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-bold text-slate-700 shadow-2xs hover:border-emerald-300 hover:bg-emerald-50/50 hover:text-emerald-700 transition-all active:scale-95">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    <span>Live Tracking GPS</span>
                </a>
                <a href="{{ route('visit-tasks.index') }}" 
                   class="group inline-flex items-center gap-2 rounded-xl bg-slate-950 px-4 py-2.5 text-xs font-bold text-white shadow-xs hover:bg-slate-800 transition-all active:scale-95">
                    <svg class="h-4 w-4 text-sky-400 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                    <span>Tugas Kunjungan</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="min-h-[calc(100vh-65px)] bg-slate-100 py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">

            {{-- ======================================================== --}}
            {{-- 1. DASHBOARD KHUSUS ADMIN --}}
            {{-- ======================================================== --}}
            @if($role === 'Admin')
            {{-- Row 1: Primary Operational Metrics --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                {{-- Sales Motoris Aktif --}}
                <div class="group relative rounded-2xl border border-slate-200/90 bg-white p-4 sm:p-5 shadow-xs hover:border-indigo-300 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
                    <div class="flex items-center justify-between">
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Sales Motoris Aktif</p>
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-50 border border-indigo-100/80 text-indigo-600 group-hover:scale-105 transition-transform">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                    </div>
                    <p class="mt-2 text-2xl sm:text-3xl font-black tracking-tight text-slate-900">{{ $activeSalesCount }}</p>
                    <p class="mt-1 text-xs text-slate-400">Total terdaftar & aktif</p>
                </div>

                {{-- Tugas Hari Ini --}}
                <div class="group relative rounded-2xl border border-slate-200/90 bg-white p-4 sm:p-5 shadow-xs hover:border-blue-300 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
                    <div class="flex items-center justify-between">
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Tugas Hari Ini</p>
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 border border-blue-100/80 text-blue-600 group-hover:scale-105 transition-transform">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                    <p class="mt-2 text-2xl sm:text-3xl font-black tracking-tight text-slate-900">{{ $todayTasks }}</p>
                    <p class="mt-1 text-xs text-slate-400">Jadwal kunjungan hari ini</p>
                </div>

                {{-- Menunggu Respons --}}
                <div class="group relative rounded-2xl border border-slate-200/90 bg-white p-4 sm:p-5 shadow-xs hover:border-blue-300 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
                    <div class="flex items-center justify-between">
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Menunggu Respons</p>
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 border border-amber-100/80 text-blue-600 group-hover:scale-105 transition-transform">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <p class="mt-2 text-2xl sm:text-3xl font-black tracking-tight text-blue-600">{{ $pendingTasks }}</p>
                    <p class="mt-1 text-xs text-blue-600 font-medium">Belum direspons sales</p>
                </div>

                {{-- Sales Sedang Tracking --}}
                <div class="group relative rounded-2xl border border-slate-200/90 bg-white p-4 sm:p-5 shadow-xs hover:border-emerald-300 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
                    <div class="flex items-center justify-between">
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Sales Sedang Tracking</p>
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 border border-emerald-100/80 text-emerald-600 group-hover:scale-105 transition-transform">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                            </svg>
                        </div>
                    </div>
                    <p class="mt-2 text-2xl sm:text-3xl font-black tracking-tight text-emerald-600 flex items-center gap-2">
                        <span>{{ $activeTrackingCount }}</span>
                        <span class="relative flex h-2.5 w-2.5">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                        </span>
                    </p>
                    <p class="mt-1 text-xs text-emerald-600 font-medium">Sesi GPS aktif saat ini</p>
                </div>
            </div>

            {{-- Row 2: Status Breakdown & Omzet --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                {{-- Tugas Diterima --}}
                <div class="group relative rounded-2xl border border-slate-200/90 bg-white p-4 sm:p-5 shadow-xs hover:border-sky-300 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
                    <div class="flex items-center justify-between">
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Tugas Diterima</p>
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-sky-50 border border-sky-100/80 text-sky-600 group-hover:scale-105 transition-transform">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <p class="mt-2 text-2xl sm:text-3xl font-black tracking-tight text-slate-900">{{ $acceptedTasks }}</p>
                    <p class="mt-1 text-xs text-slate-400">Siap atau dalam proses</p>
                </div>

                {{-- Tugas Ditolak --}}
                <div class="group relative rounded-2xl border border-slate-200/90 bg-white p-4 sm:p-5 shadow-xs hover:border-rose-300 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
                    <div class="flex items-center justify-between">
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Tugas Ditolak</p>
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-rose-50 border border-rose-100/80 text-rose-600 group-hover:scale-105 transition-transform">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <p class="mt-2 text-2xl sm:text-3xl font-black tracking-tight text-rose-600">{{ $rejectedTasks }}</p>
                    <p class="mt-1 text-xs text-rose-600 font-medium">Perlu evaluasi / jadwal ulang</p>
                </div>

                {{-- Tugas Selesai --}}
                <div class="group relative rounded-2xl border border-slate-200/90 bg-white p-4 sm:p-5 shadow-xs hover:border-emerald-300 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
                    <div class="flex items-center justify-between">
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Tugas Selesai</p>
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 border border-emerald-100/80 text-emerald-600 group-hover:scale-105 transition-transform">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                    </div>
                    <p class="mt-2 text-2xl sm:text-3xl font-black tracking-tight text-emerald-600">{{ $completedTasks }}</p>
                    <p class="mt-1 text-xs text-emerald-600 font-medium">Kunjungan berhasil tuntas</p>
                </div>

                {{-- Omzet Bulan Ini --}}
                <div class="group relative rounded-2xl border border-slate-200/90 bg-white p-4 sm:p-5 shadow-xs hover:border-blue-300 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
                    <div class="flex items-center justify-between">
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Omzet Bulan Ini</p>
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 border border-amber-100/80 text-blue-600 group-hover:scale-105 transition-transform">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <p class="mt-2 text-xl sm:text-2xl font-black tracking-tight text-slate-900 truncate">Rp {{ number_format($monthRevenue, 0, ',', '.') }}</p>
                    <p class="mt-1 text-xs text-slate-400">{{ $monthOrdersCount ?? 0 }} pesanan tervalidasi</p>
                </div>
            </div>

            {{-- ======================================================== --}}
            {{-- 2. DASHBOARD KHUSUS PIMPINAN --}}
            {{-- ======================================================== --}}
            @elseif($role === 'Pimpinan')
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                {{-- Total Kunjungan --}}
                <div class="group relative rounded-2xl border border-slate-200/90 bg-white p-4 sm:p-5 shadow-xs hover:border-indigo-300 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
                    <div class="flex items-center justify-between">
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Total Kunjungan</p>
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-50 border border-indigo-100/80 text-indigo-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        </div>
                    </div>
                    <p class="mt-2 text-2xl sm:text-3xl font-black tracking-tight text-slate-900">{{ $totalTasks }}</p>
                    <p class="mt-1 text-xs text-slate-400">Seluruh tugas operasional</p>
                </div>

                {{-- Tingkat Penyelesaian --}}
                <div class="group relative rounded-2xl border border-slate-200/90 bg-white p-4 sm:p-5 shadow-xs hover:border-emerald-300 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
                    <div class="flex items-center justify-between">
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Tingkat Penyelesaian</p>
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 border border-emerald-100/80 text-emerald-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>
                    <p class="mt-2 text-2xl sm:text-3xl font-black tracking-tight text-emerald-600">{{ $completionRate }}%</p>
                    <p class="mt-1 text-xs text-emerald-600 font-medium">{{ $completedTasks }} tugas terselesaikan</p>
                </div>

                {{-- Omzet Bulan Berjalan --}}
                <div class="group relative rounded-2xl border border-slate-200/90 bg-white p-4 sm:p-5 shadow-xs hover:border-blue-300 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
                    <div class="flex items-center justify-between">
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Omzet Bulan Berjalan</p>
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 border border-amber-100/80 text-blue-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>
                    <p class="mt-2 text-xl sm:text-2xl font-black tracking-tight text-slate-900 truncate">Rp {{ number_format($monthRevenue, 0, ',', '.') }}</p>
                    <p class="mt-1 text-xs text-slate-400">Realisasi pesanan tervalidasi</p>
                </div>

                {{-- Sales Sedang Tracking --}}
                <div class="group relative rounded-2xl border border-slate-200/90 bg-white p-4 sm:p-5 shadow-xs hover:border-blue-300 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
                    <div class="flex items-center justify-between">
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Sales Sedang Tracking</p>
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 border border-blue-100/80 text-blue-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                    </div>
                    <p class="mt-2 text-2xl sm:text-3xl font-black tracking-tight text-blue-600 flex items-center gap-2">
                        <span>{{ $activeTrackingCount }}</span>
                        <span class="relative flex h-2.5 w-2.5">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-blue-500"></span>
                        </span>
                    </p>
                    <p class="mt-1 text-xs text-blue-600 font-medium">Posisi GPS terpantau langsung</p>
                </div>
            </div>

            {{-- Tabel Produktivitas Setiap Tim (Khusus Pimpinan) --}}
            <div class="rounded-2xl border border-slate-200/90 bg-white p-6 shadow-xs space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="flex items-center gap-2 text-base font-bold text-slate-900">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-50 text-blue-600 border border-amber-200/60">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <span>Produktivitas & Kinerja Setiap Tim Sales</span>
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-xs">
                        <thead class="bg-slate-50/80 text-slate-500 uppercase font-semibold">
                            <tr>
                                <th class="px-4 py-3 rounded-l-lg">Nama Tim</th>
                                <th class="px-4 py-3">Jumlah Sales</th>
                                <th class="px-4 py-3">Total Tugas</th>
                                <th class="px-4 py-3">Selesai</th>
                                <th class="px-4 py-3">Persentase (%)</th>
                                <th class="px-4 py-3 text-right rounded-r-lg">Omzet Tim</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($teamProductivity as $tp)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-4 py-3.5 font-bold text-slate-900">{{ $tp['team'] }}</td>
                                <td class="px-4 py-3.5 text-slate-600">{{ $tp['sales'] }} orang</td>
                                <td class="px-4 py-3.5 text-slate-600 font-medium">{{ $tp['tasks'] }}</td>
                                <td class="px-4 py-3.5 text-emerald-600 font-bold">{{ $tp['completed'] }}</td>
                                <td class="px-4 py-3.5">
                                    <div class="flex items-center gap-2">
                                        <div class="w-24 bg-slate-100 rounded-full h-2 overflow-hidden border border-slate-200/50">
                                            <div class="bg-emerald-500 h-2 rounded-full transition-all duration-500" style="width: {{ $tp['rate'] }}%"></div>
                                        </div>
                                        <span class="font-bold text-slate-800">{{ $tp['rate'] }}%</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5 text-right font-bold text-blue-600">Rp {{ number_format($tp['revenue'], 0, ',', '.') }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="px-4 py-6 text-center text-slate-400">Belum ada data tim sales.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ======================================================== --}}
            {{-- 3. DASHBOARD KHUSUS SUPERVISOR SALES --}}
            {{-- ======================================================== --}}
            @elseif($role === 'Supervisor Sales')
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                {{-- Anggota Tim Sales --}}
                <div class="group relative rounded-2xl border border-slate-200/90 bg-white p-4 sm:p-5 shadow-xs hover:border-indigo-300 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
                    <div class="flex items-center justify-between">
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Anggota Tim Sales</p>
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-50 border border-indigo-100/80 text-indigo-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                    </div>
                    <p class="mt-2 text-2xl sm:text-3xl font-black tracking-tight text-slate-900">{{ $teamMembersCount }}</p>
                    <p class="mt-1 text-xs text-slate-400">{{ auth()->user()->team?->name ?? 'Tim Anda' }}</p>
                </div>

                {{-- Tugas Tim Hari Ini --}}
                <div class="group relative rounded-2xl border border-slate-200/90 bg-white p-4 sm:p-5 shadow-xs hover:border-blue-300 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
                    <div class="flex items-center justify-between">
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Tugas Tim Hari Ini</p>
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 border border-blue-100/80 text-blue-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                    </div>
                    <p class="mt-2 text-2xl sm:text-3xl font-black tracking-tight text-slate-900">{{ $teamTodayTasks }}</p>
                    <p class="mt-1 text-xs text-slate-400">Jadwal tugas tim</p>
                </div>

                {{-- Menunggu Respons --}}
                <div class="group relative rounded-2xl border border-slate-200/90 bg-white p-4 sm:p-5 shadow-xs hover:border-blue-300 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
                    <div class="flex items-center justify-between">
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Menunggu Respons</p>
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 border border-amber-100/80 text-blue-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>
                    <p class="mt-2 text-2xl sm:text-3xl font-black tracking-tight text-blue-600">{{ $teamPendingTasks }}</p>
                    <p class="mt-1 text-xs text-blue-600 font-medium">Belum diterima sales</p>
                </div>

                {{-- Tugas Ditolak --}}
                <div class="group relative rounded-2xl border border-slate-200/90 bg-white p-4 sm:p-5 shadow-xs hover:border-rose-300 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
                    <div class="flex items-center justify-between">
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Tugas Ditolak</p>
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-rose-50 border border-rose-100/80 text-rose-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>
                    <p class="mt-2 text-2xl sm:text-3xl font-black tracking-tight text-rose-600">{{ $teamRejectedTasks }}</p>
                    <p class="mt-1 text-xs text-rose-600 font-medium">Perlu evaluasi / jadwal ulang</p>
                </div>
            </div>

            {{-- Status Operasional Tim --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
                {{-- Sales Dalam Perjalanan --}}
                <div class="group rounded-2xl border border-slate-200/90 bg-white p-4 sm:p-5 shadow-xs hover:border-cyan-300 hover:shadow-md transition-all duration-200 flex items-center justify-between">
                    <div>
                        <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            Sales Dalam Perjalanan
                        </span>
                        <p class="mt-2 text-2xl sm:text-3xl font-black text-cyan-600">{{ $teamMovingSales }}</p>
                        <p class="mt-1 text-xs text-slate-400">Menuju lokasi pelanggan</p>
                    </div>
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-cyan-50 border border-cyan-100 text-cyan-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                </div>

                {{-- Sales Tiba / Sedang Kunjungan --}}
                <div class="group rounded-2xl border border-slate-200/90 bg-white p-4 sm:p-5 shadow-xs hover:border-indigo-300 hover:shadow-md transition-all duration-200 flex items-center justify-between">
                    <div>
                        <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            Tiba / Sedang Kunjungan
                        </span>
                        <p class="mt-2 text-2xl sm:text-3xl font-black text-indigo-600">{{ $teamCheckedIn }}</p>
                        <p class="mt-1 text-xs text-slate-400">Aktif berada di outlet</p>
                    </div>
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-indigo-50 border border-indigo-100 text-indigo-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                </div>

                {{-- Kunjungan Selesai --}}
                <div class="group rounded-2xl border border-slate-200/90 bg-white p-4 sm:p-5 shadow-xs hover:border-emerald-300 hover:shadow-md transition-all duration-200 flex items-center justify-between">
                    <div>
                        <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            Kunjungan Selesai
                        </span>
                        <p class="mt-2 text-2xl sm:text-3xl font-black text-emerald-600">{{ $teamCompleted }}</p>
                        <p class="mt-1 text-xs text-emerald-600 font-medium">Laporan terverifikasi</p>
                    </div>
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
            </div>

            {{-- ======================================================== --}}
            {{-- 4. DASHBOARD KHUSUS SALES MOTORIS --}}
            {{-- ======================================================== --}}
            @else
            {{-- Alert if has pending response tasks --}}
            @if($myNewTasks > 0)
            <div class="rounded-2xl border border-amber-300 bg-amber-50/90 p-4 sm:p-5 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-700 border border-amber-300/60 shadow-2xs">
                        <svg class="h-6 w-6 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-amber-900">Perhatian: Ada {{ $myNewTasks }} Tugas Kunjungan Baru Menunggu Respons!</h4>
                        <p class="text-xs text-amber-700 mt-0.5">Segera buka detail tugas dan pilih Terima atau Tolak dengan alasan sebelum memulai perjalanan.</p>
                    </div>
                </div>
                <a href="{{ route('visit-tasks.index', ['status' => 'MENUNGGU_RESPONS']) }}" class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-amber-600 px-4 py-2.5 text-xs font-bold text-white shadow-xs hover:bg-amber-700 transition active:scale-95 shrink-0">
                    <span>Lihat Tugas Sekarang</span>
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>
            @endif

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                {{-- Tugas Baru --}}
                <div class="group relative rounded-2xl border border-slate-200/90 bg-white p-4 sm:p-5 shadow-xs hover:border-blue-300 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
                    <div class="flex items-center justify-between">
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Tugas Baru</p>
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 border border-amber-100 text-blue-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>
                    <p class="mt-2 text-2xl sm:text-3xl font-black tracking-tight text-blue-600">{{ $myNewTasks }}</p>
                    <p class="mt-1 text-xs text-blue-600 font-medium">Menunggu respons Anda</p>
                </div>

                {{-- Jadwal Hari Ini --}}
                <div class="group relative rounded-2xl border border-slate-200/90 bg-white p-4 sm:p-5 shadow-xs hover:border-blue-300 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
                    <div class="flex items-center justify-between">
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Jadwal Hari Ini</p>
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 border border-blue-100 text-blue-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                    </div>
                    <p class="mt-2 text-2xl sm:text-3xl font-black tracking-tight text-slate-900">{{ $myTodayTasks }}</p>
                    <p class="mt-1 text-xs text-slate-400">Target kunjungan hari ini</p>
                </div>

                {{-- Tugas Diterima --}}
                <div class="group relative rounded-2xl border border-slate-200/90 bg-white p-4 sm:p-5 shadow-xs hover:border-cyan-300 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
                    <div class="flex items-center justify-between">
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Tugas Diterima</p>
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-cyan-50 border border-cyan-100 text-cyan-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>
                    <p class="mt-2 text-2xl sm:text-3xl font-black tracking-tight text-cyan-600">{{ $myAcceptedTasks }}</p>
                    <p class="mt-1 text-xs text-slate-400">Siap dijalankan</p>
                </div>

                {{-- Tugas Selesai --}}
                <div class="group relative rounded-2xl border border-slate-200/90 bg-white p-4 sm:p-5 shadow-xs hover:border-emerald-300 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
                    <div class="flex items-center justify-between">
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Tugas Selesai</p>
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </div>
                    </div>
                    <p class="mt-2 text-2xl sm:text-3xl font-black tracking-tight text-emerald-600">{{ $myCompletedTasks }}</p>
                    <p class="mt-1 text-xs text-emerald-600 font-medium">Kunjungan terlaksana</p>
                </div>
            </div>

            {{-- Omzet & Status GPS Sales --}}
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="group rounded-2xl border border-slate-200/90 bg-white p-5 shadow-xs hover:border-blue-300 hover:shadow-md transition-all duration-200">
                    <div class="flex items-center justify-between">
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Omzet Penjualan Saya Bulan Ini</p>
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 border border-amber-100 text-blue-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>
                    <p class="mt-2 text-2xl font-black text-slate-900">Rp {{ number_format($myRevenue, 0, ',', '.') }}</p>
                    <p class="mt-1 text-xs text-slate-400">Total pesanan terverifikasi</p>
                </div>

                <div class="group rounded-2xl border border-slate-200/90 bg-white p-5 shadow-xs hover:shadow-md transition-all duration-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Status GPS Tracking Perangkat</p>
                        <div class="mt-2 flex items-center gap-2 text-sm font-bold {{ $hasActiveTracking ? 'text-emerald-600' : 'text-slate-500' }}">
                            @if($hasActiveTracking)
                                <span class="relative flex h-3 w-3">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                                </span>
                                <span>GPS Tracking Sedang Aktif</span>
                            @else
                                <span class="h-2.5 w-2.5 rounded-full bg-slate-300"></span>
                                <span>GPS Tracking Tidak Aktif</span>
                            @endif
                        </div>
                        <p class="mt-1 text-xs text-slate-400">Kendalikan sesi tracking di menu Tracking GPS</p>
                    </div>
                    <a href="{{ route('tracking.index') }}" class="inline-flex items-center justify-center gap-1.5 rounded-xl bg-slate-950 px-4 py-2.5 text-xs font-bold text-white hover:bg-slate-800 transition active:scale-95 shrink-0">
                        <span>Buka Tracking</span>
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>
            </div>
            @endif

            {{-- ======================================================== --}}
            {{-- GRAFIK TREN & AKTIVITAS TERBARU (SEMUA ROLE) --}}
            {{-- ======================================================== --}}
            <div class="grid gap-6 lg:grid-cols-[1.5fr_1fr]">
                {{-- Chart Section --}}
                <section class="rounded-2xl border border-slate-200/90 bg-white p-6 shadow-xs">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                        <div class="flex items-center gap-2.5">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-50 text-blue-600 border border-amber-200/60">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-900 text-sm sm:text-base">Tren Kunjungan 7 Hari Terakhir</h3>
                                <p class="text-xs text-slate-400">Perbandingan rencana vs realisasi kunjungan tugas</p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 h-64">
                        <canvas id="visitTrend"></canvas>
                    </div>
                </section>

                {{-- Recent Tasks Section --}}
                <section class="rounded-2xl border border-slate-200/90 bg-white shadow-xs overflow-hidden flex flex-col">
                    <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4 bg-slate-50/60">
                        <div class="flex items-center gap-2">
                            <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-slate-100 text-slate-700">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <h3 class="font-bold text-slate-900 text-sm">Aktivitas Tugas Terkini</h3>
                        </div>
                        <a href="{{ route('visit-tasks.index') }}" class="text-xs font-bold text-blue-600 hover:text-amber-700 hover:underline">
                            Lihat Semua &rarr;
                        </a>
                    </div>
                    <div class="divide-y divide-slate-100 max-h-80 overflow-y-auto flex-1">
                        @forelse($recentTasks as $task)
                        <div class="flex items-center justify-between gap-3 px-6 py-3.5 hover:bg-slate-50/80 transition">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-700 font-bold text-xs border border-slate-200/60">
                                    {{ strtoupper(substr($task->customer->name ?? 'T', 0, 2)) }}
                                </div>
                                <div class="min-w-0">
                                    <a href="{{ route('visit-tasks.show', $task) }}" class="truncate font-semibold text-slate-900 text-xs block hover:text-blue-600">
                                        {{ $task->customer->name }}
                                    </a>
                                    <p class="text-[11px] text-slate-400 mt-0.5 truncate">
                                        {{ $task->task_code }} &bull; {{ $task->sales->name ?? 'Sales Belum Ditugaskan' }}
                                    </p>
                                </div>
                            </div>
                            @php
                                $statusClasses = match($task->status) {
                                    'SELESAI' => 'bg-emerald-50 text-emerald-700 border-emerald-200/80',
                                    'MENUNGGU_RESPONS' => 'bg-amber-50 text-amber-700 border-amber-200/80',
                                    'DITERIMA' => 'bg-sky-50 text-sky-700 border-sky-200/80',
                                    'DALAM_PERJALANAN' => 'bg-indigo-50 text-indigo-700 border-indigo-200/80',
                                    'TIBA_DI_LOKASI', 'SEDANG_KUNJUNGAN' => 'bg-purple-50 text-purple-700 border-purple-200/80',
                                    'DITOLAK' => 'bg-rose-50 text-rose-700 border-rose-200/80',
                                    default => 'bg-slate-100 text-slate-700 border-slate-200',
                                };
                            @endphp
                            <span class="shrink-0 rounded-full px-2.5 py-1 text-[10px] font-bold border {{ $statusClasses }}">
                                {{ str_replace('_', ' ', $task->status) }}
                            </span>
                        </div>
                        @empty
                        <div class="p-8 text-center text-xs text-slate-400">Belum ada aktivitas tugas kunjungan.</div>
                        @endforelse
                    </div>
                </section>
            </div>

            {{-- ADMIN AUDIT LOGS (Only for Admin) --}}
            @if($role === 'Admin' && isset($recentAudits))
            <div class="rounded-2xl border border-slate-200/90 bg-white p-6 shadow-xs space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-2">
                        <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-slate-100 text-slate-700">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-900 text-sm sm:text-base">Audit Log Aktivitas Terakhir</h3>
                            <p class="text-xs text-slate-400">Pencatatan histori operasional & sistem</p>
                        </div>
                    </div>
                    <a href="{{ route('modules.settings') }}" class="text-xs font-bold text-blue-600 hover:text-amber-700 hover:underline">
                        Buka Audit Penuh &rarr;
                    </a>
                </div>
                <div class="divide-y divide-slate-100 text-xs">
                    @forelse($recentAudits as $audit)
                    <div class="py-3 flex items-center justify-between gap-4">
                        <div class="min-w-0">
                            <span class="font-semibold text-slate-800 block truncate">{{ $audit->description ?: 'Aktivitas tercatat' }}</span>
                            <span class="text-slate-400 block text-[11px] mt-0.5">
                                {{ $audit->causer?->name ?? 'Sistem' }} &bull; {{ $audit->created_at->diffForHumans() }}
                            </span>
                        </div>
                        <span class="shrink-0 rounded-lg bg-slate-100 px-2.5 py-1 text-[10px] font-mono font-medium text-slate-600 border border-slate-200/60 uppercase">
                            {{ $audit->event ?? 'event' }}
                        </span>
                    </div>
                    @empty
                    <p class="py-4 text-center text-slate-400">Belum ada audit log.</p>
                    @endforelse
                </div>
            </div>
            @endif

        </div>
    </div>

    <!-- Chart.js initialization -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('visitTrend');
        if (!ctx) return;

        const trendData = @json($trend);
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: trendData.map(d => d.label),
                datasets: [
                    {
                        label: 'Direncanakan',
                        data: trendData.map(d => d.planned),
                        backgroundColor: '#e2e8f0',
                        hoverBackgroundColor: '#cbd5e1',
                        borderRadius: 6
                    },
                    {
                        label: 'Selesai',
                        data: trendData.map(d => d.completed),
                        backgroundColor: '#f59e0b',
                        hoverBackgroundColor: '#d97706',
                        borderRadius: 6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { 
                        position: 'top',
                        labels: {
                            boxWidth: 12,
                            font: { size: 11, weight: 'bold' }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false }
                    },
                    y: { 
                        beginAtZero: true, 
                        ticks: { precision: 0 },
                        grid: { color: '#f1f5f9' }
                    }
                }
            }
        });
    });
    </script>
</x-app-layout>
