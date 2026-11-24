<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="{{ asset('images/netafarm-logo.png') }}">
    <title>CV. Netafarm Indolestari | Distributor Pertanian, Hidroponik & Greenhouse</title>
    @include('partials.pwa-head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-950 text-white antialiased selection:bg-blue-600 selection:text-white">
    <main class="relative isolate min-h-screen overflow-hidden">
        <div class="absolute inset-0 -z-10 bg-[radial-gradient(circle_at_80%_15%,rgba(245,158,11,.18),transparent_32%),linear-gradient(135deg,#020617_0%,#0f172a_55%,#172554_100%)]"></div>
        <div class="mx-auto flex min-h-screen max-w-7xl flex-col px-4 sm:px-6 py-6 sm:py-7 lg:px-10">
            <header class="flex items-center justify-between">
                <a href="{{ url('/') }}" class="flex items-center gap-2.5 text-lg font-bold tracking-tight hover:opacity-90 transition">
                    <img src="{{ asset('images/netafarm-logo.png') }}" alt="Netafarm" class="h-9 w-9 shrink-0 object-contain">
                    <span>Neta<span class="text-sky-400">farm</span></span>
                </a>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="triggerPwaInstall()" class="inline-flex items-center gap-1.5 rounded-xl border border-sky-400/50 bg-sky-500/15 hover:bg-sky-500/25 px-3.5 py-2 text-xs font-bold text-sky-300 transition shadow-sm active:scale-95">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        <span>Install Aplikasi</span>
                    </button>
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-700 bg-slate-900/80 px-4 py-2 text-xs font-bold text-white hover:bg-slate-800 transition active:scale-95">
                        <span>Login</span>
                        <svg class="h-3.5 w-3.5 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>
            </header>

            <section class="flex flex-1 items-center py-12 sm:py-16 lg:py-20">
                <div class="grid w-full items-center gap-10 lg:gap-16 lg:grid-cols-[1.1fr_.9fr]">
                    <div>
                        <div class="inline-flex items-center gap-2 rounded-full border border-sky-400/30 bg-blue-500/10 px-3.5 py-1 text-xs font-semibold text-sky-400 mb-5">
                            <span class="h-2 w-2 rounded-full bg-blue-600 animate-pulse"></span>
                            <span>Distributor Pertanian, Hidroponik, Perikanan, Peternakan & Greenhouse</span>
                        </div>
                        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold leading-[1.1] tracking-tight text-white">
                            Distribusi Sarana Pertanian & Hidroponik Modern.
                        </h1>
                        <p class="mt-5 max-w-xl text-base sm:text-lg leading-relaxed text-slate-300">
                            Kelola rute kunjungan, outlet, pesanan penjualan, pemantauan GPS, dan performa tim dalam satu ruang kerja operasional terpadu.
                        </p>
                        <div class="mt-8 flex flex-wrap items-center gap-3">
                            <a href="{{ route('login') }}" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-7 py-3.5 text-sm font-bold text-white transition hover:bg-blue-500 shadow-lg shadow-blue-600/30 active:scale-95">
                                <span>Login ke Sistem</span>
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </a>
                            <button type="button" onclick="triggerPwaInstall()" class="inline-flex items-center gap-2 rounded-xl border border-sky-400/40 bg-sky-500/10 hover:bg-sky-500/20 px-6 py-3.5 text-sm font-bold text-sky-300 transition shadow-lg shadow-sky-500/10 active:scale-95">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                <span>Install Aplikasi PWA</span>
                            </button>
                        </div>
                    </div>
                    <div id="kapabilitas" class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                        @foreach ([
                            ['01', 'Kunjungan GPS', 'Check-in, check-out, navigasi rute jalan raya dan histori lokasi lapangan.'],
                            ['02', 'Outlet Terkelola', 'Peta pelanggan, sales penanggung jawab, dan catatan transaksi.'],
                            ['03', 'Penjualan Terukur', 'Pencatatan pesanan, stok barang bawaan sales, dan laporan omzet.'],
                            ['04', 'Target & Tim Sales', 'Ringkasan KPI dan produktivitas harian sales motoris.']
                        ] as $item)
                            <div class="group rounded-2xl border border-white/10 bg-white/[.05] p-5 backdrop-blur-sm hover:bg-white/[.10] hover:border-amber-400/40 hover:-translate-y-1 transition duration-200">
                                <span class="text-xs font-bold font-mono text-sky-400">{{ $item[0] }}</span>
                                <h2 class="mt-4 font-bold text-white text-base group-hover:text-amber-300 transition">{{ $item[1] }}</h2>
                                <p class="mt-2 text-xs leading-relaxed text-slate-400">{{ $item[2] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
            <footer class="border-t border-white/10 pt-5 text-xs text-slate-500 flex flex-wrap items-center justify-between gap-2">
                <span>&copy; {{ date('Y') }} MotorisTrack &bull; Operasional distribusi yang lebih terukur</span>
                <span class="text-slate-600">Surabaya Operational Hub</span>
            </footer>
        </div>
    </main>
    @include('partials.pwa-script')
</body>
</html>
