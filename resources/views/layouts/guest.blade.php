<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('images/netafarm-logo.png') }}">
    <title>{{ config('app.name', 'Netafarm') }} - Sistem Monitoring & Navigasi Motoris</title>
    @include('partials.pwa-head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Minimalist Map & Navigation Animations */
        @keyframes dashOffset {
            from { stroke-dashoffset: 240; }
            to { stroke-dashoffset: 0; }
        }
        @keyframes pulseRadar {
            0% { transform: scale(0.6); opacity: 0.9; }
            100% { transform: scale(2.8); opacity: 0; }
        }
        @keyframes floatCard {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-6px); }
        }
        @keyframes pingFast {
            0% { transform: scale(1); opacity: 1; }
            75%, 100% { transform: scale(2.4); opacity: 0; }
        }
        @keyframes scanline {
            0% { transform: translateY(-100%); }
            100% { transform: translateY(1000%); }
        }
        .anim-route-line {
            stroke-dasharray: 8 6;
            animation: dashOffset 4s linear infinite;
        }
        .anim-route-line-fast {
            stroke-dasharray: 6 4;
            animation: dashOffset 2.5s linear infinite;
        }
        .anim-radar {
            animation: pulseRadar 3s cubic-bezier(0.2, 0.8, 0.4, 1) infinite;
        }
        .anim-radar-delay {
            animation: pulseRadar 3s cubic-bezier(0.2, 0.8, 0.4, 1) infinite;
            animation-delay: 1.5s;
        }
        .anim-float {
            animation: floatCard 4s ease-in-out infinite;
        }
        .anim-ping-fast {
            animation: pingFast 1.8s cubic-bezier(0, 0, 0.2, 1) infinite;
        }
        /* Topographic / Grid map background pattern */
        .bg-map-grid {
            background-image: 
                radial-gradient(circle at 1px 1px, rgba(56, 189, 248, 0.12) 1px, transparent 0),
                linear-gradient(to right, rgba(255, 255, 255, 0.03) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
            background-size: 32px 32px, 64px 64px, 64px 64px;
        }
    </style>
</head>
<body class="min-h-screen bg-slate-950 font-sans text-slate-100 antialiased selection:bg-blue-600 selection:text-white">
    <div class="grid min-h-screen lg:grid-cols-[1.1fr_.9fr]">
        
        {{-- SISI KIRI: ANIMATED MINIMALIST GPS & MAPS VISUALIZATION --}}
        <aside class="relative hidden overflow-hidden bg-slate-950 p-8 lg:p-12 text-white lg:flex lg:flex-col lg:justify-between border-r border-slate-800/80">
            {{-- Deep ambient gradient glow --}}
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_30%_30%,rgba(14,165,233,0.18),transparent_50%),radial-gradient(circle_at_80%_80%,rgba(2,132,199,0.12),transparent_45%),linear-gradient(175deg,#020617_0%,#090d1a_60%,#0c192e_100%)] pointer-events-none"></div>
            
            {{-- Map Grid Texture Overlay --}}
            <div class="absolute inset-0 bg-map-grid opacity-60 pointer-events-none"></div>

            {{-- HEADER: Logo & Identity --}}
            <div class="relative z-10">
                <a href="{{ url('/') }}" class="inline-flex items-center gap-3 text-2xl font-black tracking-tight group">
                    <img src="{{ asset('images/netafarm-logo.png') }}" alt="Netafarm Logo" class="h-10 w-10 shrink-0 object-contain drop-shadow-[0_0_12px_rgba(56,189,248,0.4)] transition-transform duration-300 group-hover:scale-105">
                    <span class="text-white">Neta<span class="text-sky-400">farm</span></span>
                    <span class="inline-block ms-2 px-2 py-0.5 text-[10px] font-mono font-bold tracking-widest uppercase rounded-md bg-sky-500/15 border border-sky-400/30 text-sky-300">
                        GPS Hub
                    </span>
                </a>
                <p class="mt-2 text-xs font-medium text-slate-400">CV. Netafarm Indolestari &bull; Distributor Pertanian & Hidroponik</p>
            </div>

            {{-- CENTER: INTERACTIVE ANIMATED MINIMALIST MAP & ROUTE SIMULATOR --}}
            <div class="relative z-10 my-6 flex-1 flex flex-col justify-center">
                
                {{-- Minimalist Canvas Container with border glow --}}
                <div class="relative w-full max-w-lg rounded-3xl border border-sky-500/30 bg-slate-900/60 p-6 shadow-2xl shadow-sky-950/50 backdrop-blur-md overflow-hidden">
                    
                    {{-- Ambient Radar Sweep Line behind map --}}
                    <div class="absolute inset-0 bg-[linear-gradient(rgba(56,189,248,0.03)_1px,transparent_1px),linear-gradient(90deg,rgba(56,189,248,0.03)_1px,transparent_1px)] bg-[size:24px_24px] pointer-events-none"></div>

                    {{-- Header of Map Simulator --}}
                    <div class="flex items-center justify-between pb-4 border-b border-slate-800/80 text-xs">
                        <div class="flex items-center gap-2">
                            <span class="relative flex h-2.5 w-2.5">
                                <span class="anim-ping-fast absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                            </span>
                            <span class="font-mono font-bold uppercase tracking-wider text-emerald-400 text-[11px]">Live Dispatch Active</span>
                        </div>
                        <div class="font-mono text-[11px] text-slate-400 flex items-center gap-1.5">
                            <svg class="h-3.5 w-3.5 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span>-7.3735° S, 112.7051° E</span>
                        </div>
                    </div>

                    {{-- THE VECTOR MAP SVG --}}
                    <div class="relative h-64 w-full mt-3 flex items-center justify-center">
                        <svg class="w-full h-full overflow-visible" viewBox="0 0 460 250" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <defs>
                                <linearGradient id="routeGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" stop-color="#38bdf8" />
                                    <stop offset="50%" stop-color="#0284c7" />
                                    <stop offset="100%" stop-color="#22d3ee" />
                                </linearGradient>
                                <linearGradient id="subRouteGradient" x1="0%" y1="0%" x2="100%" y2="0%">
                                    <stop offset="0%" stop-color="#38bdf8" stop-opacity="0.3" />
                                    <stop offset="100%" stop-color="#0284c7" stop-opacity="0.7" />
                                </linearGradient>
                                <filter id="glow" x="-20%" y="-20%" width="140%" height="140%">
                                    <feGaussianBlur stdDeviation="4" result="blur" />
                                    <feComposite in="SourceGraphic" in2="blur" operator="over" />
                                </filter>
                            </defs>

                            {{-- Faint Street Grid Roads --}}
                            <path d="M 20 80 Q 140 60 260 85 T 440 70" stroke="rgba(255,255,255,0.06)" stroke-width="2" />
                            <path d="M 40 180 Q 160 170 280 200 T 440 180" stroke="rgba(255,255,255,0.06)" stroke-width="2" />
                            <path d="M 110 30 Q 130 130 120 220" stroke="rgba(255,255,255,0.06)" stroke-width="2" />
                            <path d="M 330 30 Q 320 120 340 220" stroke="rgba(255,255,255,0.06)" stroke-width="2" />

                            {{-- Secondary Dispatch Route (connecting outlets) --}}
                            <path d="M 70 160 Q 130 90 220 130 T 370 70" stroke="url(#subRouteGradient)" stroke-width="2.5" stroke-dasharray="4 4" />

                            {{-- Primary Active Navigation Route (Animated Glow Path) --}}
                            <path id="activeHighwayRoute" d="M 60 70 C 130 30, 160 180, 240 140 S 340 110, 400 170" 
                                  stroke="url(#routeGradient)" 
                                  stroke-width="3.5" 
                                  stroke-linecap="round"
                                  class="anim-route-line"
                                  filter="url(#glow)" />

                            {{-- WAYPOINT 1: Netafarm Central Depot (Jemundo HQ) --}}
                            <g transform="translate(60, 70)">
                                <circle cx="0" cy="0" r="18" fill="rgba(2, 132, 199, 0.2)" class="anim-radar" />
                                <circle cx="0" cy="0" r="18" fill="rgba(56, 189, 248, 0.15)" class="anim-radar-delay" />
                                <circle cx="0" cy="0" r="7" fill="#0284c7" stroke="#ffffff" stroke-width="2" />
                                <text x="14" y="-8" fill="#e0f2fe" font-size="11" font-weight="700" font-family="sans-serif">Netafarm HQ</text>
                                <text x="14" y="6" fill="#94a3b8" font-size="9" font-family="sans-serif">Jemundo, Sidoarjo</text>
                            </g>

                            {{-- WAYPOINT 2: Intermediate Outlet --}}
                            <g transform="translate(240, 140)">
                                <circle cx="0" cy="0" r="5" fill="#38bdf8" stroke="#ffffff" stroke-width="2" />
                                <text x="-8" y="-14" text-anchor="middle" fill="#bae6fd" font-size="10" font-weight="600" font-family="sans-serif">Kios Barokah</text>
                            </g>

                            {{-- WAYPOINT 3: Destination Outlet (Greenhouse / Mitra) --}}
                            <g transform="translate(400, 170)">
                                <circle cx="0" cy="0" r="14" fill="rgba(34, 197, 94, 0.2)" class="anim-radar" />
                                <circle cx="0" cy="0" r="6" fill="#22c55e" stroke="#ffffff" stroke-width="2" />
                                <text x="-12" y="20" text-anchor="end" fill="#bbf7d0" font-size="10" font-weight="700" font-family="sans-serif">Greenhouse Mitra</text>
                                <text x="-12" y="32" text-anchor="end" fill="#86efac" font-size="9" font-family="sans-serif">Check-in Ready</text>
                            </g>

                            {{-- ANIMATED MOTORIS SALES BEACON (Moving Marker on Route) --}}
                            <g transform="translate(180, 138)">
                                <circle cx="0" cy="0" r="12" fill="rgba(245, 158, 11, 0.3)" class="anim-radar" />
                                <circle cx="0" cy="0" r="6" fill="#f59e0b" stroke="#ffffff" stroke-width="2" />
                            </g>
                        </svg>

                        {{-- Floating Glassmorphism Telemetry Card --}}
                        <div class="anim-float absolute bottom-2 left-4 right-4 sm:left-auto sm:right-4 sm:bottom-4 rounded-2xl border border-sky-400/30 bg-slate-950/85 p-3 text-white backdrop-blur-md shadow-xl flex items-center justify-between gap-4 max-w-xs">
                            <div class="flex items-center gap-2.5">
                                <div class="flex h-8 w-8 items-center justify-center rounded-xl bg-sky-500/20 text-sky-400">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                </div>
                                <div class="text-[11px] leading-tight">
                                    <p class="font-bold text-white">Sales Motoris 01</p>
                                    <p class="text-[10px] text-sky-300 font-mono">Kecepatan: 36 km/jam</p>
                                </div>
                            </div>
                            <span class="rounded-lg bg-emerald-500/20 px-2 py-1 text-[10px] font-bold font-mono text-emerald-300 border border-emerald-500/30">
                                ON ROUTE
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Feature Bullet Points below map --}}
                <div class="mt-8 grid grid-cols-2 gap-4 max-w-lg">
                    <div class="flex items-start gap-3 rounded-2xl border border-white/5 bg-slate-900/40 p-3.5 backdrop-blur-sm">
                        <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-sky-500/10 text-sky-400">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                        </div>
                        <div class="text-xs">
                            <p class="font-bold text-slate-200">Presisi Titik Outlet</p>
                            <p class="text-[11px] text-slate-400 mt-0.5">Navigasi jalan raya otomatis ke lokasi kios tani.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 rounded-2xl border border-white/5 bg-slate-900/40 p-3.5 backdrop-blur-sm">
                        <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-sky-500/10 text-sky-400">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div class="text-xs">
                            <p class="font-bold text-slate-200">Check-In Geofence</p>
                            <p class="text-[11px] text-slate-400 mt-0.5">Validasi kehadiran sales langsung di kios mitra.</p>
                        </div>
                    </div>
                </div>

            </div>

            {{-- FOOTER: Copyright --}}
            <div class="relative z-10 flex items-center justify-between text-xs text-slate-500 pt-4 border-t border-slate-900">
                <span class="font-mono text-[11px]">&copy; {{ date('Y') }} MotorisTrack Platform</span>
                <span class="text-[11px] text-slate-500">Surabaya &bull; Sidoarjo Operational Hub</span>
            </div>
        </aside>

        {{-- SISI KANAN: FORM LOGIN DENGAN AMBIENT MAP ACCENT --}}
        <main class="relative flex min-h-screen items-center justify-center bg-slate-950 px-5 py-10 sm:px-8 overflow-hidden">
            {{-- Ambient background map glow for mobile & desktop --}}
            <div class="absolute inset-0 bg-map-grid opacity-35 pointer-events-none"></div>
            <div class="absolute -top-32 -right-32 h-96 w-96 rounded-full bg-blue-600/15 blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-32 -left-32 h-96 w-96 rounded-full bg-sky-500/10 blur-3xl pointer-events-none"></div>

            <div class="relative z-10 w-full max-w-md">
                {{-- Mobile Brand Header --}}
                <div class="mb-6 lg:hidden flex items-center justify-between">
                    <a href="{{ url('/') }}" class="inline-flex items-center gap-2.5 text-xl font-bold tracking-tight text-white">
                        <img src="{{ asset('images/netafarm-logo.png') }}" alt="Netafarm" class="h-9 w-9 shrink-0 object-contain drop-shadow-[0_0_8px_rgba(56,189,248,0.4)]">
                        <span>Neta<span class="text-sky-400">farm</span></span>
                    </a>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-sky-500/15 border border-sky-400/30 px-3 py-1 text-[11px] font-mono text-sky-300">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-400 anim-ping-fast"></span>
                        GPS Aktif
                    </span>
                </div>

                {{-- The Login Card --}}
                <div class="rounded-3xl border border-slate-800 bg-slate-900/90 p-7 shadow-2xl shadow-slate-950/80 backdrop-blur-xl sm:p-9 text-slate-100">
                    {{ $slot }}
                </div>
                
                {{-- Bottom Micro Tagline --}}
                <div class="mt-6 flex items-center justify-center gap-2 text-xs text-slate-500">
                    <svg class="h-3.5 w-3.5 text-sky-400/70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    <span>Koneksi aman terenkripsi &bull; Sistem Operasional Motoris</span>
                </div>
            </div>
        </main>

    </div>

    <script>
        window.addEventListener('load', function () {
            if (!window.Swal) return;
            @if ($errors->any()) 
                window.Swal.fire({ 
                    icon: 'error', 
                    title: 'Login belum berhasil', 
                    html: @json($errors->all('<div class="text-left text-xs">:message</div>')), 
                    confirmButtonColor: '#0284c7',
                    background: '#0f172a',
                    color: '#f8fafc'
                }); 
            @endif
            @if (session('status')) 
                window.Swal.fire({ 
                    icon: 'success', 
                    title: 'Berhasil', 
                    text: @json(session('status')), 
                    timer: 2400, 
                    showConfirmButton: false,
                    background: '#0f172a',
                    color: '#f8fafc'
                }); 
            @endif
        });
    </script>
    @include('partials.pwa-script')
</body>
</html>
