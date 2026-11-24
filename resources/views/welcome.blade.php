<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('images/netafarm-logo.png') }}">
    <title>CV. Netafarm Indolestari | Sistem Operasional & Navigasi Sales Motoris</title>
    @include('partials.pwa-head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Minimalist Navigation & Map Keyframe Animations */
        @keyframes routeDash {
            from { stroke-dashoffset: 320; }
            to { stroke-dashoffset: 0; }
        }
        @keyframes radarSweep {
            0% { transform: scale(0.6); opacity: 0.9; }
            100% { transform: scale(2.8); opacity: 0; }
        }
        @keyframes floatHUD {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-5px); }
        }
        @keyframes beaconPing {
            0% { transform: scale(1); opacity: 1; }
            75%, 100% { transform: scale(2.5); opacity: 0; }
        }
        .anim-route-flow {
            stroke-dasharray: 8 6;
            animation: routeDash 3.5s linear infinite;
        }
        .anim-radar-ping {
            animation: radarSweep 2.8s cubic-bezier(0.2, 0.8, 0.4, 1) infinite;
        }
        .anim-radar-ping-delay {
            animation: radarSweep 2.8s cubic-bezier(0.2, 0.8, 0.4, 1) infinite;
            animation-delay: 1.4s;
        }
        .anim-beacon {
            animation: beaconPing 1.8s cubic-bezier(0, 0, 0.2, 1) infinite;
        }
        .anim-float-hud {
            animation: floatHUD 4s ease-in-out infinite;
        }
        .bg-cyber-grid {
            background-image: 
                radial-gradient(circle at 1px 1px, rgba(56, 189, 248, 0.12) 1px, transparent 0),
                linear-gradient(to right, rgba(255, 255, 255, 0.03) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
            background-size: 32px 32px, 64px 64px, 64px 64px;
        }
    </style>
</head>
<body class="min-h-screen bg-slate-950 text-white antialiased selection:bg-blue-600 selection:text-white" x-data="interactiveMapApp()">
    <main class="relative isolate min-h-screen overflow-hidden flex flex-col justify-between">
        {{-- Deep background ambient gradients --}}
        <div class="absolute inset-0 -z-10 bg-[radial-gradient(circle_at_20%_20%,rgba(14,165,233,0.15),transparent_40%),radial-gradient(circle_at_80%_80%,rgba(2,132,199,0.12),transparent_45%),linear-gradient(145deg,#020617_0%,#090d1a_50%,#0c192e_100%)]"></div>
        <div class="absolute inset-0 -z-10 bg-cyber-grid opacity-50 pointer-events-none"></div>

        <div class="mx-auto flex w-full max-w-7xl flex-col px-4 sm:px-6 py-6 sm:py-7 lg:px-10 flex-1 justify-between">
            
            {{-- HEADER NAVBAR --}}
            <header class="flex items-center justify-between">
                <a href="{{ url('/') }}" class="flex items-center gap-2.5 text-lg sm:text-xl font-bold tracking-tight hover:opacity-90 transition group">
                    <img src="{{ asset('images/netafarm-logo.png') }}" alt="Netafarm" class="h-9 w-9 shrink-0 object-contain drop-shadow-[0_0_10px_rgba(56,189,248,0.4)] transition-transform duration-300 group-hover:scale-105">
                    <span>Neta<span class="text-sky-400">farm</span></span>
                    <span class="hidden sm:inline-block ms-1.5 px-2 py-0.5 text-[10px] font-mono font-bold tracking-widest uppercase rounded-md bg-sky-500/15 border border-sky-400/30 text-sky-300">
                        MotorisTrack GPS
                    </span>
                </a>
                <div class="flex items-center gap-2 sm:gap-3">
                    <button type="button" onclick="triggerPwaInstall()" class="inline-flex items-center gap-1.5 rounded-xl border border-sky-400/50 bg-sky-500/15 hover:bg-sky-500/25 px-3 sm:px-4 py-2 text-xs font-bold text-sky-300 transition shadow-sm active:scale-95">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        <span class="hidden sm:inline">Install Aplikasi PWA</span>
                        <span class="sm:hidden">Install</span>
                    </button>
                    <button type="button" x-on:click="activeTab = 'login'; scrollToCard()" class="inline-flex items-center gap-2 rounded-xl border border-slate-700 bg-slate-900/90 hover:bg-slate-800 px-4 py-2 text-xs font-bold text-white transition active:scale-95 shadow-md">
                        <span>Masuk</span>
                        <svg class="h-3.5 w-3.5 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                </div>
            </header>

            {{-- MAIN SECTION: HERO LEFT & INTERACTIVE MAP/LOGIN RIGHT --}}
            <section class="flex flex-1 items-center py-8 sm:py-12 lg:py-14">
                <div class="grid w-full items-center gap-10 lg:gap-14 lg:grid-cols-[1fr_1.1fr]">
                    
                    {{-- SISI KIRI: BRANDING & HEADLINE --}}
                    <div>
                        <div class="inline-flex items-center gap-2.5 rounded-full border border-sky-400/30 bg-sky-500/10 px-3.5 py-1 text-xs font-semibold text-sky-400 mb-5">
                            <span class="relative flex h-2 w-2">
                                <span class="anim-beacon absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                            </span>
                            <span>Distributor Pertanian, Hidroponik, Perikanan & Greenhouse</span>
                        </div>
                        
                        <h1 class="text-3xl sm:text-5xl lg:text-5xl xl:text-6xl font-black leading-[1.12] tracking-tight text-white">
                            Navigasi Rute & Distribusi Sales Motoris.
                        </h1>
                        
                        <p class="mt-4 sm:mt-5 max-w-xl text-sm sm:text-base leading-relaxed text-slate-300">
                            Kelola rute kunjungan jalan raya, geofence check-in kios tani, pemesanan produk, dan pelacakan live GPS dalam satu ruang kerja operasional terpadu.
                        </p>

                        {{-- Action Buttons --}}
                        <div class="mt-7 sm:mt-8 flex flex-wrap items-center gap-3">
                            <button type="button" x-on:click="activeTab = 'login'; scrollToCard()" class="inline-flex items-center gap-2.5 rounded-xl bg-gradient-to-r from-sky-500 via-blue-600 to-sky-600 hover:from-sky-400 hover:to-blue-500 px-6 sm:px-7 py-3.5 text-sm font-bold text-white transition shadow-lg shadow-sky-500/25 active:scale-95">
                                <span>Masuk ke Ruang Kerja</span>
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </button>
                            <button type="button" x-on:click="activeTab = 'map'; scrollToCard()" class="inline-flex items-center gap-2 rounded-xl border border-sky-400/40 bg-sky-500/10 hover:bg-sky-500/20 px-5 sm:px-6 py-3.5 text-sm font-bold text-sky-300 transition shadow-md shadow-sky-500/10 active:scale-95">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                                <span>Simulasi Peta Rute</span>
                            </button>
                        </div>

                        {{-- Micro Telemetry Stats --}}
                        <div class="mt-8 pt-6 border-t border-slate-800/80 grid grid-cols-3 gap-3 max-w-lg text-xs">
                            <div>
                                <p class="font-mono text-lg sm:text-xl font-bold text-white">21+</p>
                                <p class="text-[11px] text-slate-400 mt-0.5">Katalog Produk</p>
                            </div>
                            <div>
                                <p class="font-mono text-lg sm:text-xl font-bold text-sky-400">100%</p>
                                <p class="text-[11px] text-slate-400 mt-0.5">GPS Tracking</p>
                            </div>
                            <div>
                                <p class="font-mono text-lg sm:text-xl font-bold text-emerald-400">Offline</p>
                                <p class="text-[11px] text-slate-400 mt-0.5">PWA Support</p>
                            </div>
                        </div>
                    </div>

                    {{-- SISI KANAN: REDESIGNED INTERACTIVE ANIMATED MAP & LOGIN CARD --}}
                    <div id="interactiveCard" class="relative w-full rounded-3xl border border-sky-500/30 bg-slate-900/80 p-5 sm:p-7 shadow-2xl shadow-sky-950/60 backdrop-blur-xl overflow-hidden transition-all duration-300">
                        
                        {{-- CARD HEADER WITH INTERACTIVE TABS --}}
                        <div class="flex items-center justify-between pb-4 border-b border-slate-800/80">
                            <div class="flex items-center gap-2">
                                <span class="relative flex h-2.5 w-2.5">
                                    <span class="anim-beacon absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                                </span>
                                <span class="font-mono text-[11px] font-bold uppercase tracking-wider text-emerald-400" x-text="activeTab === 'map' ? 'PETA NAVIGASI LIVE' : 'AUTENTIKASI AKUN'"></span>
                            </div>

                            {{-- Navigation Switcher Tabs --}}
                            <div class="flex items-center rounded-xl bg-slate-950/80 p-1 border border-slate-800">
                                <button type="button" x-on:click="activeTab = 'map'" :class="activeTab === 'map' ? 'bg-sky-500 text-white shadow-md' : 'text-slate-400 hover:text-white'" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold transition">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                                    <span>Peta GPS</span>
                                </button>
                                <button type="button" x-on:click="activeTab = 'login'" :class="activeTab === 'login' ? 'bg-sky-500 text-white shadow-md' : 'text-slate-400 hover:text-white'" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold transition">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                    <span>Login Sistem</span>
                                </button>
                            </div>
                        </div>

                        {{-- TAB 1: INTERACTIVE ANIMATED MAP NAVIGATION UI --}}
                        <div x-show="activeTab === 'map'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="mt-4 space-y-4">
                            
                            {{-- Route Selector Buttons --}}
                            <div class="flex items-center gap-2 overflow-x-auto pb-1 text-[11px] font-medium no-scrollbar">
                                <span class="text-slate-500 text-[10px] uppercase font-mono tracking-wider shrink-0">Rute:</span>
                                <button type="button" x-on:click="selectRoute(1)" :class="currentRoute === 1 ? 'border-sky-400 bg-sky-500/20 text-sky-200' : 'border-slate-800 bg-slate-950/60 text-slate-400'" class="px-2.5 py-1 rounded-lg border transition shrink-0">
                                    Rute 1: Sidoarjo (Jemundo - Taman)
                                </button>
                                <button type="button" x-on:click="selectRoute(2)" :class="currentRoute === 2 ? 'border-sky-400 bg-sky-500/20 text-sky-200' : 'border-slate-800 bg-slate-950/60 text-slate-400'" class="px-2.5 py-1 rounded-lg border transition shrink-0">
                                    Rute 2: Surabaya Selatan
                                </button>
                            </div>

                            {{-- THE INTERACTIVE SVG MAP CANVAS --}}
                            <div class="relative h-64 sm:h-72 w-full rounded-2xl bg-slate-950/90 border border-slate-800 p-2 overflow-hidden flex items-center justify-center">
                                
                                {{-- Background Radar Grid & Coordinates --}}
                                <div class="absolute inset-0 bg-cyber-grid opacity-40 pointer-events-none"></div>

                                <svg class="w-full h-full overflow-visible" viewBox="0 0 460 260" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <defs>
                                        <linearGradient id="mainRouteGlow" x1="0%" y1="0%" x2="100%" y2="100%">
                                            <stop offset="0%" stop-color="#38bdf8" />
                                            <stop offset="60%" stop-color="#0284c7" />
                                            <stop offset="100%" stop-color="#22d3ee" />
                                        </linearGradient>
                                        <filter id="svgGlowFilter" x="-20%" y="-20%" width="140%" height="140%">
                                            <feGaussianBlur stdDeviation="3.5" result="blur" />
                                            <feComposite in="SourceGraphic" in2="blur" operator="over" />
                                        </filter>
                                    </defs>

                                    {{-- Faint Base Highway Grid Network --}}
                                    <path d="M 30 70 Q 150 40 270 75 T 430 60" stroke="rgba(255,255,255,0.06)" stroke-width="2" />
                                    <path d="M 40 180 Q 180 160 300 200 T 430 170" stroke="rgba(255,255,255,0.06)" stroke-width="2" />
                                    <path d="M 120 20 Q 140 130 110 240" stroke="rgba(255,255,255,0.06)" stroke-width="2" />
                                    <path d="M 340 20 Q 320 120 350 240" stroke="rgba(255,255,255,0.06)" stroke-width="2" />

                                    {{-- Active Route Path 1 --}}
                                    <g x-show="currentRoute === 1">
                                        <path d="M 65 75 C 140 30, 150 185, 235 140 S 340 110, 395 180" 
                                              stroke="url(#mainRouteGlow)" 
                                              stroke-width="3.5" 
                                              stroke-linecap="round"
                                              class="anim-route-flow"
                                              filter="url(#svgGlowFilter)" />
                                    </g>

                                    {{-- Active Route Path 2 --}}
                                    <g x-show="currentRoute === 2">
                                        <path d="M 65 75 C 90 140, 190 60, 280 90 S 370 120, 395 180" 
                                              stroke="url(#mainRouteGlow)" 
                                              stroke-width="3.5" 
                                              stroke-linecap="round"
                                              class="anim-route-flow"
                                              filter="url(#svgGlowFilter)" />
                                    </g>

                                    {{-- WAYPOINT 1: Netafarm HQ (Jemundo, Sidoarjo) --}}
                                    <g transform="translate(65, 75)" class="cursor-pointer" x-on:click="activeOutlet = outlets.hq">
                                        <circle cx="0" cy="0" r="16" fill="rgba(2, 132, 199, 0.25)" class="anim-radar-ping" />
                                        <circle cx="0" cy="0" r="16" fill="rgba(56, 189, 248, 0.2)" class="anim-radar-ping-delay" />
                                        <circle cx="0" cy="0" r="7.5" fill="#0284c7" stroke="#ffffff" stroke-width="2" />
                                        <text x="14" y="-8" fill="#e0f2fe" font-size="11" font-weight="700" font-family="sans-serif">Netafarm HQ</text>
                                        <text x="14" y="6" fill="#94a3b8" font-size="9" font-family="sans-serif">Jemundo, Sidoarjo</text>
                                    </g>

                                    {{-- WAYPOINT 2: Kios Barokah Mart --}}
                                    <g transform="translate(235, 140)" class="cursor-pointer" x-on:click="activeOutlet = outlets.outlet1">
                                        <circle cx="0" cy="0" r="12" fill="rgba(245, 158, 11, 0.25)" class="anim-beacon" />
                                        <circle cx="0" cy="0" r="6" fill="#f59e0b" stroke="#ffffff" stroke-width="2" />
                                        <text x="0" y="-14" text-anchor="middle" fill="#fef3c7" font-size="10" font-weight="700" font-family="sans-serif">Kios Barokah</text>
                                    </g>

                                    {{-- WAYPOINT 3: Greenhouse Mitra Sejahtera --}}
                                    <g transform="translate(395, 180)" class="cursor-pointer" x-on:click="activeOutlet = outlets.outlet2">
                                        <circle cx="0" cy="0" r="14" fill="rgba(34, 197, 94, 0.25)" class="anim-radar-ping" />
                                        <circle cx="0" cy="0" r="6.5" fill="#22c55e" stroke="#ffffff" stroke-width="2" />
                                        <text x="-12" y="18" text-anchor="end" fill="#bbf7d0" font-size="10" font-weight="700" font-family="sans-serif">Greenhouse Mitra</text>
                                        <text x="-12" y="30" text-anchor="end" fill="#86efac" font-size="9" font-family="sans-serif">Rp 855.000 (Order)</text>
                                    </g>

                                    {{-- WAYPOINT 4: Tambak Bioflok Nila --}}
                                    <g transform="translate(280, 50)" class="cursor-pointer" x-on:click="activeOutlet = outlets.outlet3">
                                        <circle cx="0" cy="0" r="5" fill="#38bdf8" stroke="#ffffff" stroke-width="1.5" />
                                        <text x="12" y="4" fill="#bae6fd" font-size="9.5" font-weight="600" font-family="sans-serif">Tambak Sedati</text>
                                    </g>

                                    {{-- MOVING MOTORIS DISPATCH CURSOR --}}
                                    <g :transform="currentRoute === 1 ? 'translate(170, 150)' : 'translate(195, 68)'">
                                        <circle cx="0" cy="0" r="12" fill="rgba(56, 189, 248, 0.3)" class="anim-radar-ping" />
                                        <circle cx="0" cy="0" r="5.5" fill="#38bdf8" stroke="#ffffff" stroke-width="1.5" />
                                    </g>
                                </svg>

                                {{-- Floating Mini Telemetry HUD Badge --}}
                                <div class="anim-float-hud absolute bottom-2.5 right-2.5 rounded-xl border border-sky-400/30 bg-slate-950/90 px-3 py-2 text-white backdrop-blur-md shadow-lg flex items-center gap-3 text-[10px]">
                                    <div class="flex items-center gap-2">
                                        <span class="h-2 w-2 rounded-full bg-emerald-400 anim-beacon"></span>
                                        <span class="font-mono text-slate-300">GPS ±3m</span>
                                    </div>
                                    <div class="font-mono text-sky-400">
                                        <span x-text="telemetrySpeed"></span> km/h
                                    </div>
                                </div>
                            </div>

                            {{-- ACTIVE OUTLET INTERACTIVE INSPECTOR CARD --}}
                            <div class="p-3.5 rounded-2xl border border-sky-400/25 bg-slate-950/70 text-xs flex items-center justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-500/15 text-sky-400 border border-sky-400/20">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <p class="font-bold text-white text-sm" x-text="activeOutlet.name"></p>
                                            <span class="px-2 py-0.5 text-[9px] font-bold uppercase rounded-md" :class="activeOutlet.badgeClass" x-text="activeOutlet.status"></span>
                                        </div>
                                        <p class="text-[11px] text-slate-400 mt-0.5" x-text="activeOutlet.address"></p>
                                    </div>
                                </div>
                                <button type="button" x-on:click="activeTab = 'login'" class="hidden sm:inline-flex items-center gap-1 rounded-xl bg-sky-500 hover:bg-sky-400 text-white font-bold text-xs px-3 py-1.5 shadow-sm transition active:scale-95 shrink-0">
                                    <span>Buka Rute</span>
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </button>
                            </div>
                        </div>

                        {{-- TAB 2: MINIMALIST INTEGRATED LOGIN FORM --}}
                        <div x-show="activeTab === 'login'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="mt-4 space-y-4">
                            
                            {{-- Role Quick Fill Chips (1-Click Test Login) --}}
                            <div class="p-2.5 rounded-xl border border-slate-800 bg-slate-950/60">
                                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1.5 flex items-center justify-between">
                                    <span>Pilih Akun Uji Coba Cepat:</span>
                                    <span class="text-sky-400 font-mono">Password: password</span>
                                </p>
                                <div class="grid grid-cols-3 gap-1.5 text-[11px]">
                                    <button type="button" x-on:click="fillAccount('admin@motoris.test')" class="px-2 py-1.5 rounded-lg border border-slate-700 bg-slate-900 hover:bg-sky-500/20 hover:border-sky-400/50 text-slate-200 font-medium transition text-center truncate">
                                        👑 Admin
                                    </button>
                                    <button type="button" x-on:click="fillAccount('sofan@motoris.test')" class="px-2 py-1.5 rounded-lg border border-slate-700 bg-slate-900 hover:bg-sky-500/20 hover:border-sky-400/50 text-slate-200 font-medium transition text-center truncate">
                                        🛵 Sofan (Sales)
                                    </button>
                                    <button type="button" x-on:click="fillAccount('supervisor@motoris.test')" class="px-2 py-1.5 rounded-lg border border-slate-700 bg-slate-900 hover:bg-sky-500/20 hover:border-sky-400/50 text-slate-200 font-medium transition text-center truncate">
                                        👔 Supervisor
                                    </button>
                                </div>
                            </div>

                            {{-- The Login Form --}}
                            <form method="POST" action="{{ route('login') }}" class="space-y-3.5">
                                @csrf
                                <div>
                                    <label for="welcomeEmail" class="block text-xs font-semibold uppercase tracking-wider text-slate-300">Alamat Email</label>
                                    <div class="relative mt-1.5">
                                        <div class="pointer-events-none absolute inset-y-0 start-0 flex items-center ps-3 text-sky-400">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/></svg>
                                        </div>
                                        <input id="welcomeEmail" x-model="emailInput" class="block w-full rounded-xl border border-slate-700 bg-slate-950/80 ps-9 pe-4 py-2.5 text-xs text-white placeholder-slate-500 focus:border-sky-400 focus:ring-1 focus:ring-sky-400 transition" type="email" name="email" required autocomplete="username" placeholder="Masukkan email akun" />
                                    </div>
                                </div>

                                <div>
                                    <div class="flex items-center justify-between">
                                        <label for="welcomePassword" class="block text-xs font-semibold uppercase tracking-wider text-slate-300">Kata Sandi</label>
                                        @if (Route::has('password.request'))
                                            <a class="text-[11px] font-medium text-sky-400 hover:text-sky-300 transition" href="{{ route('password.request') }}">Lupa sandi?</a>
                                        @endif
                                    </div>
                                    <div class="relative mt-1.5">
                                        <div class="pointer-events-none absolute inset-y-0 start-0 flex items-center ps-3 text-sky-400">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                        </div>
                                        <input id="welcomePassword" x-model="passwordInput" class="block w-full rounded-xl border border-slate-700 bg-slate-950/80 ps-9 pe-10 py-2.5 text-xs text-white placeholder-slate-500 focus:border-sky-400 focus:ring-1 focus:ring-sky-400 transition" :type="showPassword ? 'text' : 'password'" name="password" required autocomplete="current-password" placeholder="Masukkan kata sandi" />
                                        <button type="button" x-on:click="showPassword = !showPassword" class="absolute inset-y-0 end-0 flex w-10 items-center justify-center text-slate-400 hover:text-sky-400 transition">
                                            <svg x-show="!showPassword" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            <svg x-show="showPassword" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                                        </button>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between pt-0.5">
                                    <label class="flex items-center gap-2 text-xs text-slate-300 cursor-pointer">
                                        <input type="checkbox" name="remember" class="rounded border-slate-700 bg-slate-950 text-sky-500 shadow-sm focus:ring-sky-400">
                                        <span>Ingat perangkat ini</span>
                                    </label>
                                </div>

                                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-sky-500 via-blue-600 to-sky-600 hover:from-sky-400 hover:to-blue-500 px-4 py-3 text-xs sm:text-sm font-bold text-white shadow-lg shadow-sky-500/25 transition active:scale-[0.98]">
                                    <span>Masuk ke Dashboard</span>
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
            </section>

            {{-- FOOTER --}}
            <footer class="border-t border-slate-800/80 pt-5 text-xs text-slate-500 flex flex-wrap items-center justify-between gap-2">
                <span>&copy; {{ date('Y') }} MotorisTrack Platform &bull; Operasional distribusi pertanian CV. Netafarm Indolestari</span>
                <span class="text-slate-600">Surabaya &bull; Sidoarjo Operational Hub</span>
            </footer>

        </div>
    </main>

    <script>
        function interactiveMapApp() {
            return {
                activeTab: 'map',
                currentRoute: 1,
                telemetrySpeed: 36,
                showPassword: false,
                emailInput: 'admin@motoris.test',
                passwordInput: 'password',
                outlets: {
                    hq: {
                        name: 'Netafarm Central HQ',
                        address: 'Jl. Raya Sawunggaling No.150 A, Jemundo, Taman, Sidoarjo',
                        status: 'DEPOT PUSAT',
                        badgeClass: 'bg-sky-500/20 text-sky-300 border border-sky-400/30'
                    },
                    outlet1: {
                        name: 'Kios Tani Barokah Mart',
                        address: 'Jl. Basuki Rahmat, Tegalsari - Siap Kunjungan (Jadwal 09:00)',
                        status: 'TIBA DI LOKASI',
                        badgeClass: 'bg-amber-500/20 text-amber-300 border border-amber-400/30'
                    },
                    outlet2: {
                        name: 'Greenhouse Mitra Sejahtera',
                        address: 'Kec. Taman, Sidoarjo - Order: Nutrisi AB Mix & Rockwool',
                        status: 'CHECK-IN SELESAI',
                        badgeClass: 'bg-emerald-500/20 text-emerald-300 border border-emerald-400/30'
                    },
                    outlet3: {
                        name: 'Tambak Bioflok Nila Sedati',
                        address: 'Kec. Sedati, Sidoarjo - Pengiriman Probiotik Bioflok',
                        status: 'RUTE BERIKUTNYA',
                        badgeClass: 'bg-cyan-500/20 text-cyan-300 border border-cyan-400/30'
                    }
                },
                activeOutlet: {
                    name: 'Kios Tani Barokah Mart',
                    address: 'Jl. Basuki Rahmat, Tegalsari - Siap Kunjungan (Jadwal 09:00)',
                    status: 'TIBA DI LOKASI',
                    badgeClass: 'bg-amber-500/20 text-amber-300 border border-amber-400/30'
                },
                init() {
                    // Random speed telemetry ticker
                    setInterval(() => {
                        this.telemetrySpeed = Math.floor(32 + Math.random() * 10);
                    }, 2500);
                },
                selectRoute(num) {
                    this.currentRoute = num;
                    if (num === 1) {
                        this.activeOutlet = this.outlets.outlet1;
                    } else {
                        this.activeOutlet = this.outlets.outlet3;
                    }
                },
                fillAccount(email) {
                    this.emailInput = email;
                    this.passwordInput = 'password';
                },
                scrollToCard() {
                    const card = document.getElementById('interactiveCard');
                    if (card) {
                        card.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }
            }
        }
    </script>

    @include('partials.pwa-script')
</body>
</html>
