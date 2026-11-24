<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('images/netafarm-logo.png') }}">
    <title>{{ config('app.name', 'Netafarm') }} - Sistem Monitoring & Navigasi Sales Motoris</title>
    @include('partials.pwa-head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Minimalist Map & Marker Animations */
        @keyframes pulsePing {
            0% { transform: scale(0.8); opacity: 0.9; }
            100% { transform: scale(2.6); opacity: 0; }
        }
        @keyframes floatHUD {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-4px); }
        }
        .anim-pulse-ping {
            animation: pulsePing 2.4s cubic-bezier(0.2, 0.8, 0.4, 1) infinite;
        }
        .anim-float-hud {
            animation: floatHUD 4s ease-in-out infinite;
        }
        /* Leaflet Dark Customizations */
        .leaflet-container {
            background: #020617 !important;
            font-family: inherit;
        }
        .leaflet-popup-content-wrapper {
            background: rgba(15, 23, 42, 0.95) !important;
            color: #f8fafc !important;
            border: 1px solid rgba(56, 189, 248, 0.3) !important;
            border-radius: 1rem !important;
            backdrop-filter: blur(8px) !important;
            box-shadow: 0 20px 25px -5px rgba(2, 6, 23, 0.7) !important;
        }
        .leaflet-popup-tip {
            background: rgba(15, 23, 42, 0.95) !important;
        }
        .leaflet-popup-close-button {
            color: #94a3b8 !important;
            padding: 6px !important;
        }
        .custom-map-marker {
            background: transparent;
            border: none;
        }
        /* Cyber map background grid */
        .bg-cyber-grid {
            background-image: 
                radial-gradient(circle at 1px 1px, rgba(56, 189, 248, 0.1) 1px, transparent 0),
                linear-gradient(to right, rgba(255, 255, 255, 0.02) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(255, 255, 255, 0.02) 1px, transparent 1px);
            background-size: 28px 28px, 56px 56px, 56px 56px;
        }
    </style>
</head>
<body class="min-h-screen bg-slate-950 font-sans text-slate-100 antialiased selection:bg-blue-600 selection:text-white overflow-x-hidden">
    
    <div class="grid min-h-screen lg:grid-cols-[1.2fr_0.8fr] xl:grid-cols-[1.3fr_0.7fr]">
        
        {{-- SISI KIRI: FULL-BLEED REAL INTERACTIVE LEAFLET DARK MAP --}}
        <aside class="relative min-h-[380px] lg:min-h-screen overflow-hidden bg-slate-950 flex flex-col justify-between border-b lg:border-b-0 lg:border-r border-slate-800/80">
            
            {{-- Map Container (Interactive Full-Height) --}}
            <div id="leafletLoginMap" class="absolute inset-0 z-0 h-full w-full"></div>

            {{-- Top Vignette Overlay for Crisp Readability --}}
            <div class="pointer-events-none absolute inset-x-0 top-0 h-32 bg-gradient-to-b from-slate-950/90 via-slate-950/40 to-transparent z-10"></div>
            {{-- Bottom Vignette Overlay --}}
            <div class="pointer-events-none absolute inset-x-0 bottom-0 h-28 bg-gradient-to-t from-slate-950/90 via-slate-950/40 to-transparent z-10"></div>

            {{-- TOP BAR OVER MAP: Logo & Dispatch Status --}}
            <div class="relative z-20 p-5 sm:p-7 flex items-center justify-between">
                <a href="{{ url('/') }}" class="inline-flex items-center gap-2.5 group">
                    <img src="{{ asset('images/netafarm-logo.png') }}" alt="Netafarm" class="h-9 w-9 shrink-0 object-contain drop-shadow-[0_0_12px_rgba(56,189,248,0.5)] transition-transform duration-300 group-hover:scale-105">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-xl font-black tracking-tight text-white">Neta<span class="text-sky-400">farm</span></span>
                            <span class="px-2 py-0.5 text-[9px] font-mono font-bold tracking-widest uppercase rounded-md bg-sky-500/20 border border-sky-400/40 text-sky-300">
                                GPS LIVE
                            </span>
                        </div>
                        <p class="text-[10px] text-slate-400">Distributor Pertanian, Hidroponik & Greenhouse</p>
                    </div>
                </a>

                {{-- Live Telemetry Pill --}}
                <div class="hidden sm:inline-flex items-center gap-2 rounded-full border border-emerald-400/30 bg-slate-950/80 px-3.5 py-1.5 text-xs backdrop-blur-md shadow-lg">
                    <span class="relative flex h-2 w-2">
                        <span class="anim-pulse-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    <span class="font-mono text-[11px] font-bold text-emerald-400">DISPATCH MONITORING ACTIVE</span>
                </div>
            </div>

            {{-- BOTTOM OVER MAP: Floating HUD Card with Route & Sales Stats --}}
            <div class="relative z-20 p-4 sm:p-6 flex flex-col sm:flex-row items-stretch sm:items-end justify-between gap-3 pointer-events-none">
                
                {{-- Floating Sales Telemetry Card --}}
                <div class="pointer-events-auto anim-float-hud rounded-2xl border border-sky-400/30 bg-slate-950/90 p-3.5 text-white shadow-2xl shadow-sky-950/70 backdrop-blur-md max-w-sm w-full">
                    <div class="flex items-center justify-between pb-2.5 border-b border-slate-800/80">
                        <div class="flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-sky-400 anim-pulse-ping"></span>
                            <p class="text-[11px] font-bold uppercase tracking-wider text-sky-300 font-mono">Sales Motoris 01 (Sofan)</p>
                        </div>
                        <span class="px-2 py-0.5 text-[9px] font-mono font-bold rounded-md bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                            ON ROUTE
                        </span>
                    </div>

                    <div class="mt-2.5 grid grid-cols-3 gap-2 text-center text-xs">
                        <div class="rounded-xl bg-slate-900/80 p-2 border border-slate-800/60">
                            <p class="text-[10px] text-slate-400">Kecepatan</p>
                            <p class="font-mono font-bold text-sky-400 text-xs mt-0.5" id="liveSpeedText">38 km/h</p>
                        </div>
                        <div class="rounded-xl bg-slate-900/80 p-2 border border-slate-800/60">
                            <p class="text-[10px] text-slate-400">Kunjungan</p>
                            <p class="font-mono font-bold text-emerald-400 text-xs mt-0.5">4 / 6 Outlet</p>
                        </div>
                        <div class="rounded-xl bg-slate-900/80 p-2 border border-slate-800/60">
                            <p class="text-[10px] text-slate-400">Order Hari Ini</p>
                            <p class="font-mono font-bold text-amber-400 text-xs mt-0.5">Rp 1.48 Jt</p>
                        </div>
                    </div>

                    <div class="mt-2.5 flex items-center justify-between text-[10px] text-slate-400 pt-1">
                        <span class="truncate">📍 Menuju: <b>Kios Barokah Mart</b></span>
                        <span class="shrink-0 text-sky-300 font-mono">ETA: 8 mnt</span>
                    </div>
                </div>

                {{-- Interactive Map Zoom Helper Hint --}}
                <div class="pointer-events-auto hidden md:flex items-center gap-2 rounded-xl border border-slate-800 bg-slate-950/80 px-3 py-1.5 text-[10px] text-slate-400 backdrop-blur-md">
                    <svg class="h-3.5 w-3.5 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"/></svg>
                    <span>Peta Interaktif: Gulir & Klik Titik Outlet</span>
                </div>
            </div>
        </aside>

        {{-- SISI KANAN: REFINED SLEEK LOGIN PANEL --}}
        <main class="relative flex min-h-screen items-center justify-center bg-slate-950 p-6 sm:p-10 lg:p-12 overflow-hidden">
            
            {{-- Ambient Cyber Grid Texture --}}
            <div class="absolute inset-0 bg-cyber-grid opacity-30 pointer-events-none"></div>
            <div class="absolute top-1/4 -right-20 h-72 w-72 rounded-full bg-blue-600/10 blur-3xl pointer-events-none"></div>
            <div class="absolute bottom-1/4 -left-20 h-72 w-72 rounded-full bg-sky-500/10 blur-3xl pointer-events-none"></div>

            <div class="relative z-10 w-full max-w-md">
                
                {{-- Back to Home link --}}
                <div class="mb-6 flex items-center justify-between">
                    <a href="{{ url('/') }}" class="inline-flex items-center gap-1.5 text-xs text-slate-400 hover:text-sky-300 transition">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        <span>Kembali ke Beranda</span>
                    </a>
                    <span class="inline-flex items-center gap-1.5 text-[10px] font-mono text-slate-500">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                        Server Siap
                    </span>
                </div>

                {{-- Card Wrapper containing the slot --}}
                <div class="rounded-3xl border border-slate-800/90 bg-slate-900/80 p-6 sm:p-8 shadow-2xl shadow-slate-950/90 backdrop-blur-xl">
                    {{ $slot }}
                </div>

                {{-- Footer Info --}}
                <div class="mt-6 text-center text-xs text-slate-500 space-y-1">
                    <p>&copy; {{ date('Y') }} CV. Netafarm Indolestari &bull; Sistem Operasional Motoris</p>
                    <p class="text-[11px] text-slate-600">Jl. Raya Sawunggaling No.150 A, Jemundo, Kec. Taman, Sidoarjo</p>
                </div>

            </div>
        </main>

    </div>

    {{-- LEAFLET INTERACTIVE MAP SCRIPT --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof L === 'undefined') return;

            const mapEl = document.getElementById('leafletLoginMap');
            if (!mapEl) return;

            // Center between Netafarm HQ (Jemundo) and Surabaya Outlets
            const map = L.map('leafletLoginMap', {
                center: [-7.3550, 112.7200],
                zoom: 12,
                zoomControl: false,
                attributionControl: false
            });

            // Dark Matter tile layer by CartoDB
            L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
                maxZoom: 19,
                subdomains: 'abcd',
            }).addTo(map);

            // Re-invalidate size to avoid tile clipping
            setTimeout(() => { map.invalidateSize(); }, 300);

            // 1. Custom Glowing HTML Marker for Netafarm HQ
            const hqIcon = L.divIcon({
                className: 'custom-map-marker',
                html: `
                    <div class="relative flex items-center justify-center">
                        <span class="absolute h-10 w-10 rounded-full bg-sky-500/25 animate-ping"></span>
                        <span class="absolute h-6 w-6 rounded-full bg-sky-500/35"></span>
                        <span class="relative h-4 w-4 rounded-full bg-sky-400 border-2 border-white shadow-lg shadow-sky-500/60"></span>
                    </div>
                `,
                iconSize: [24, 24],
                iconAnchor: [12, 12]
            });

            // 2. Custom Glowing HTML Marker for Kios Outlets
            const outletIcon = L.divIcon({
                className: 'custom-map-marker',
                html: `
                    <div class="relative flex items-center justify-center">
                        <span class="absolute h-6 w-6 rounded-full bg-emerald-500/30 animate-pulse"></span>
                        <span class="relative h-3.5 w-3.5 rounded-full bg-emerald-400 border-2 border-white shadow-md shadow-emerald-500/40"></span>
                    </div>
                `,
                iconSize: [20, 20],
                iconAnchor: [10, 10]
            });

            // 3. Custom Sales Motoris Beacon Icon
            const motorisIcon = L.divIcon({
                className: 'custom-map-marker',
                html: `
                    <div class="relative flex items-center justify-center">
                        <span class="absolute h-12 w-12 rounded-full bg-amber-400/25 animate-ping"></span>
                        <span class="absolute h-7 w-7 rounded-full bg-amber-400/35"></span>
                        <div class="relative h-6 w-6 rounded-full bg-amber-500 border-2 border-white shadow-xl flex items-center justify-center text-[10px] text-slate-950 font-black">
                            🛵
                        </div>
                    </div>
                `,
                iconSize: [24, 24],
                iconAnchor: [12, 12]
            });

            // Realistic road route coordinates from Jemundo, Taman to Surabaya Outlets
            const routeCoords = [
                [-7.3735, 112.7051], // Netafarm HQ (Jemundo, Taman)
                [-7.3690, 112.7110], // Sepanjang
                [-7.3620, 112.7180],
                [-7.3550, 112.7240], // Kebraon
                [-7.3450, 112.7300], // Kios Barokah Mart
                [-7.3380, 112.7310], // Jambangan
                [-7.3290, 112.7280], // Pagesangan
                [-7.3180, 112.7320], // Greenhouse Mitra Wonokromo
                [-7.3100, 112.7250]  // Sub-Distributor Surabaya Selatan
            ];

            // Outer Glow Path
            L.polyline(routeCoords, {
                color: '#0284c7',
                weight: 12,
                opacity: 0.25,
                lineCap: 'round',
                lineJoin: 'round'
            }).addTo(map);

            // Active Glowing Navigation Path
            L.polyline(routeCoords, {
                color: '#38bdf8',
                weight: 4.5,
                opacity: 0.95,
                lineCap: 'round',
                lineJoin: 'round',
                dashArray: '10, 8'
            }).addTo(map);

            // Add HQ Marker
            L.marker([-7.3735, 112.7051], { icon: hqIcon })
                .addTo(map)
                .bindPopup(`
                    <div class="p-2.5 text-xs text-slate-100 min-w-[190px]">
                        <div class="flex items-center gap-1.5 font-bold text-sky-400 text-sm">
                            <span>🏢 Netafarm Central HQ</span>
                        </div>
                        <p class="text-[11px] text-slate-300 mt-1">Jl. Raya Sawunggaling No.150 A, Jemundo, Taman, Sidoarjo</p>
                        <div class="mt-2 pt-2 border-t border-slate-700/60 flex items-center justify-between text-[10px]">
                            <span class="text-slate-400">Depot Distribusi</span>
                            <span class="font-mono text-emerald-400 font-bold">PUSAT OPERASIONAL</span>
                        </div>
                    </div>
                `);

            // Add Outlets
            L.marker([-7.3450, 112.7300], { icon: outletIcon })
                .addTo(map)
                .bindPopup(`
                    <div class="p-2.5 text-xs text-slate-100 min-w-[190px]">
                        <p class="font-bold text-white text-sm">🏪 Kios Tani Barokah Mart</p>
                        <p class="text-[11px] text-slate-300 mt-1">Status: Tiba di Lokasi &bull; Jadwal 09:00 WIB</p>
                        <div class="mt-2 pt-2 border-t border-slate-700/60 flex items-center justify-between text-[10px]">
                            <span class="text-slate-400">Pesanan Masuk</span>
                            <span class="font-mono text-emerald-400 font-bold">Rp 855.000</span>
                        </div>
                    </div>
                `);

            L.marker([-7.3180, 112.7320], { icon: outletIcon })
                .addTo(map)
                .bindPopup(`
                    <div class="p-2.5 text-xs text-slate-100 min-w-[190px]">
                        <p class="font-bold text-white text-sm">🌿 Greenhouse Mitra Sejahtera</p>
                        <p class="text-[11px] text-slate-300 mt-1">Status: Check-in Terjadwal</p>
                        <div class="mt-2 pt-2 border-t border-slate-700/60 flex items-center justify-between text-[10px]">
                            <span class="text-slate-400">Katalog Produk</span>
                            <span class="font-mono text-sky-400 font-bold">Nutrisi AB Mix & Rockwool</span>
                        </div>
                    </div>
                `);

            L.marker([-7.3100, 112.7250], { icon: outletIcon })
                .addTo(map)
                .bindPopup(`
                    <div class="p-2.5 text-xs text-slate-100 min-w-[190px]">
                        <p class="font-bold text-white text-sm">🌾 Sub-Distributor Surabaya</p>
                        <p class="text-[11px] text-slate-300 mt-1">Status: Kunjungan Rutin Mingguan</p>
                    </div>
                `);

            // Animated Moving Motoris Marker
            const movingMarker = L.marker(routeCoords[0], { icon: motorisIcon }).addTo(map);
            let stepIndex = 0;
            setInterval(() => {
                stepIndex = (stepIndex + 1) % routeCoords.length;
                movingMarker.setLatLng(routeCoords[stepIndex]);
                
                // Random speed variance for realism
                const speedEl = document.getElementById('liveSpeedText');
                if (speedEl) {
                    speedEl.innerText = Math.floor(34 + Math.random() * 8) + ' km/h';
                }
            }, 2500);
        });
    </script>

    @include('partials.pwa-script')
</body>
</html>
