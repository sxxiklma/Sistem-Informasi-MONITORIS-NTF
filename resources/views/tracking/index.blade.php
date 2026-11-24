<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[.22em] text-blue-600">Monitoring & Lapangan</p>
                <h2 class="mt-1 text-2xl font-bold text-slate-900">Live Tracking GPS Sales Motoris</h2>
            </div>
            <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
                <span id="liveStatusBadge" class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 border border-emerald-200">
                    <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Live Monitoring Aktif</span>
                </span>
                <button type="button" id="btnRefreshTracking" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 shadow-2xs hover:bg-slate-50 transition">
                    <svg class="h-3.5 w-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    <span>Segarkan</span>
                </button>
            </div>
        </div>
    </x-slot>

    <div class="min-h-[calc(100vh-65px)] bg-slate-100 py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">

            {{-- SALES MOTORIS GPS CONTROLLER BAR --}}
            @if(auth()->user()->hasRole('Sales Motoris'))
            <div class="rounded-2xl border border-blue-200 bg-white p-5 shadow-sm">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div id="salesGpsIcon" class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-600 border border-slate-200">
                            <svg class="h-6 w-6 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-sm font-bold text-slate-900">Sesi Tracking GPS Pribadi</h3>
                                <span id="salesSessionStatus" class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-semibold text-slate-600">
                                    Memeriksa sesi...
                                </span>
                            </div>
                            <p class="text-xs text-slate-500 mt-0.5" id="salesSessionDesc">
                                Mengirim koordinat posisi Anda ke pimpinan & supervisor selama jam operasional.
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" id="btnToggleTracking" class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2.5 text-xs font-bold text-white shadow-sm hover:bg-emerald-700 transition">
                            <svg class="h-3.5 w-3.5 fill-current" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                            <span>Mulai Sesi Tracking</span>
                        </button>
                    </div>
                </div>

                <div class="mt-3 rounded-lg bg-amber-50/70 p-3 text-[11px] text-amber-800 border border-blue-200/60 flex items-start gap-2">
                    <svg class="h-4 w-4 text-blue-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span><strong>Catatan Browser:</strong> Pelacakan lokasi memerlukan browser tetap terbuka (foreground). Layar mati atau browser yang ditutup dapat membatasi aktivitas GPS oleh sistem operasi.</span>
                </div>
            </div>
            @endif

            {{-- MAP CARD --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-5 shadow-sm space-y-4">
                {{-- MAP CONTROLS & FILTER --}}
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 pb-3">
                    <div class="flex flex-wrap items-center gap-2 text-xs">
                        <span class="font-bold text-slate-700">Filter Tim:</span>
                        <select id="filterTeam" class="rounded-lg border-slate-300 text-xs py-1.5 focus:border-amber-500 focus:ring-amber-500">
                            <option value="">Semua Tim</option>
                            @foreach($teams as $team)
                            <option value="{{ $team->id }}">{{ $team->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    {{-- LEGEND --}}
                    <div class="flex flex-wrap items-center gap-3 text-xs text-slate-600">
                        <span class="flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-emerald-500"></span> Aktif (&le;5 mnt)</span>
                        <span class="flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-blue-500"></span> Berjalan (&le;15 mnt)</span>
                        <span class="flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-amber-500"></span> Lambat (&le;30 mnt)</span>
                        <span class="flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-slate-400"></span> Kedaluwarsa (&gt;30 mnt)</span>
                    </div>
                </div>

                {{-- LEAFLET CANVAS --}}
                <div id="liveTrackingMap" class="h-[360px] sm:h-[460px] lg:h-[540px] w-full rounded-2xl border border-slate-300 bg-slate-100 shadow-inner z-0"></div>

                <div class="flex flex-wrap items-center justify-between gap-2 text-xs text-slate-500 px-1">
                    <span id="activeCountText">Memuat sales aktif...</span>
                    <span id="lastUpdatedText">Terakhir diperbarui: -</span>
                </div>
            </div>

            {{-- SALES MOTORIS LIST TABLE --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm space-y-4">
                <h3 class="flex items-center gap-2 text-base font-bold text-slate-900 border-b border-slate-100 pb-3">
                    <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span>Status Seluruh Sales Motoris</span>
                </h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-sm">
                        <thead class="bg-slate-50 text-xs uppercase text-slate-500 border-b border-slate-100">
                            <tr>
                                <th class="px-4 py-3">Nama Sales</th>
                                <th class="px-4 py-3">Tim Penjualan</th>
                                <th class="px-4 py-3">Status Sesi</th>
                                <th class="px-4 py-3">Pembaruan Lokasi</th>
                                <th class="px-4 py-3">Akurasi GPS</th>
                                <th class="px-4 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="salesTableBody" class="divide-y divide-slate-100 text-xs">
                            <tr>
                                <td colspan="6" class="px-4 py-6 text-center text-slate-400">Memuat data posisi sales...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL RIWAYAT PERJALANAN (JOURNEY PATH) --}}
    <div id="modalJourney" class="fixed inset-0 z-[1000] hidden items-center justify-center bg-slate-950/70 p-4 backdrop-blur-xs">
        <div class="max-h-[92vh] w-full max-w-4xl overflow-y-auto rounded-2xl bg-white shadow-2xl flex flex-col">
            <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
                <div>
                    <h3 class="text-lg font-bold text-slate-900" id="journeyModalTitle">Riwayat Perjalanan GPS</h3>
                    <p class="text-xs text-slate-500 mt-0.5" id="journeyModalSubtitle">Rute titik lokasi yang terekam pada sesi ini.</p>
                </div>
                <button type="button" onclick="document.getElementById('modalJourney').classList.replace('flex', 'hidden')" class="rounded-lg p-2 text-slate-400 hover:bg-slate-100 text-xl font-bold">&times;</button>
            </div>
            <div class="p-6 space-y-4">
                <div id="journeyMap" class="h-80 w-full rounded-xl border border-slate-300 shadow-inner"></div>
                <div class="grid grid-cols-3 gap-3 text-center text-xs">
                    <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-200">
                        <span class="text-slate-500 block">Total Titik:</span>
                        <span id="journeyPointsCount" class="font-bold text-slate-900 text-sm">-</span>
                    </div>
                    <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-200">
                        <span class="text-slate-500 block">Waktu Mulai:</span>
                        <span id="journeyStartedAt" class="font-bold text-slate-900 text-sm">-</span>
                    </div>
                    <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-200">
                        <span class="text-slate-500 block">Status:</span>
                        <span id="journeyStatus" class="font-bold text-slate-900 text-sm">-</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    let map = null;
    let markers = {};
    let journeyMap = null;
    let journeyPolyline = null;
    let activeSessionId = null;
    let watchId = null;
    let locationInterval = null;

    document.addEventListener('DOMContentLoaded', function () {
        // Init Main Leaflet Map
        if (window.L) {
            map = window.L.map('liveTrackingMap').setView([-7.2575, 112.7521], 12);
            window.L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap'
            }).addTo(map);
        }

        // Fetch Live positions
        fetchLivePositions();
        setInterval(fetchLivePositions, 20000);

        // Filter Team change
        document.getElementById('filterTeam').addEventListener('change', fetchLivePositions);
        document.getElementById('btnRefreshTracking').addEventListener('click', fetchLivePositions);

        // Sales tracking controller check
        @if(auth()->user()->hasRole('Sales Motoris'))
        checkSalesSession();
        document.getElementById('btnToggleTracking').addEventListener('click', toggleTrackingSession);
        @endif
    });

    async function fetchLivePositions() {
        const teamId = document.getElementById('filterTeam').value;
        const url = `/api/tracking/live${teamId ? '?team_id=' + teamId : ''}`;

        try {
            const res = await fetch(url);
            const data = await res.json();
            renderPositions(data);
            document.getElementById('lastUpdatedText').textContent = 'Terakhir diperbarui: ' + new Date().toLocaleTimeString('id-ID');
        } catch (e) {
            console.error('Failed to fetch tracking data', e);
        }
    }

    function createColorMarkerIcon(color, name) {
        const colorHex = {
            green: '#10b981',
            blue: '#3b82f6',
            orange: '#f59e0b',
            gray: '#94a3b8'
        }[color] || '#3b82f6';

        return window.L.divIcon({
            className: 'custom-pin-marker',
            html: `
                <div class="flex flex-col items-center">
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold text-white shadow-md border border-white whitespace-nowrap" style="background-color: ${colorHex}">
                        <svg style="width:12px;height:12px;display:inline-block;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                        <span>${name}</span>
                    </span>
                    <div class="w-0 h-0 border-l-[5px] border-l-transparent border-r-[5px] border-r-transparent border-t-[6px]" style="border-top-color: ${colorHex}"></div>
                </div>
            `,
            iconSize: [120, 30],
            iconAnchor: [60, 30]
        });
    }

    function renderPositions(salesList) {
        const bounds = [];
        const tableBody = document.getElementById('salesTableBody');

        // Clear markers not in list
        const activeIds = salesList.map(s => s.sales_id);
        Object.keys(markers).forEach(id => {
            if (!activeIds.includes(parseInt(id))) {
                markers[id].remove();
                delete markers[id];
            }
        });

        if (salesList.length === 0) {
            tableBody.innerHTML = `<tr><td colspan="6" class="px-4 py-6 text-center text-slate-400">Tidak ada sales motoris yang sedang aktif tracking saat ini.</td></tr>`;
            document.getElementById('activeCountText').textContent = '0 sales aktif tracking';
            return;
        }

        document.getElementById('activeCountText').textContent = `${salesList.length} sales sedang aktif tracking`;
        tableBody.innerHTML = '';

        salesList.forEach(sales => {
            const pos = [sales.latitude, sales.longitude];
            bounds.push(pos);

            // Update or create marker
            if (markers[sales.sales_id]) {
                markers[sales.sales_id].setLatLng(pos);
                markers[sales.sales_id].setIcon(createColorMarkerIcon(sales.color, sales.name));
            } else {
                const marker = window.L.marker(pos, {
                    icon: createColorMarkerIcon(sales.color, sales.name)
                }).addTo(map);
                marker.bindPopup(`
                    <div class="text-xs space-y-1">
                        <strong class="text-slate-900">${sales.name}</strong><br>
                        <span>Tim: ${sales.team}</span><br>
                        <span>Diperbarui: ${sales.minutes_ago} menit lalu</span><br>
                        <span>Akurasi: ${sales.accuracy ? sales.accuracy + ' m' : '-'}</span><br>
                        <button onclick="viewJourney(${sales.session_id}, '${sales.name}')" class="mt-1 text-blue-600 underline font-bold">Lihat Riwayat Perjalanan</button>
                    </div>
                `);
                markers[sales.sales_id] = marker;
            }

            // Table row
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-slate-50 transition';
            tr.innerHTML = `
                <td class="px-4 py-3 font-semibold text-slate-900">${sales.name}</td>
                <td class="px-4 py-3 text-slate-600">${sales.team}</td>
                <td class="px-4 py-3">
                    <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        Aktif Tracking
                    </span>
                </td>
                <td class="px-4 py-3 text-slate-600">${sales.minutes_ago === 0 ? 'Baru saja' : sales.minutes_ago + ' mnt lalu'}</td>
                <td class="px-4 py-3 text-slate-600">${sales.accuracy ? Math.round(sales.accuracy) + ' m' : '-'}</td>
                <td class="px-4 py-3 text-center">
                    <button type="button" onclick="viewJourney(${sales.session_id}, '${sales.name}')" class="inline-flex items-center gap-1.5 rounded-lg bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-800 border border-blue-200 hover:bg-amber-100 transition shadow-2xs">
                        <svg class="h-3.5 w-3.5 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                        <span>Riwayat</span>
                    </button>
                </td>
            `;
            tableBody.appendChild(tr);
        });

        if (bounds.length > 0 && map) {
            map.fitBounds(bounds, { padding: [50, 50], maxZoom: 14 });
        }
    }

    // JOURNEY MODAL
    async function viewJourney(sessionId, salesName) {
        document.getElementById('journeyModalTitle').textContent = `Riwayat Perjalanan: ${salesName}`;
        document.getElementById('modalJourney').classList.replace('hidden', 'flex');

        try {
            const res = await fetch(`/api/tracking/journey/${sessionId}`);
            const data = await res.json();

            document.getElementById('journeyPointsCount').textContent = data.path.length;
            document.getElementById('journeyStartedAt').textContent = data.session.started_at ? new Date(data.session.started_at).toLocaleTimeString('id-ID') : '-';
            document.getElementById('journeyStatus').textContent = data.session.status.toUpperCase();

            setTimeout(() => {
                if (!journeyMap) {
                    journeyMap = window.L.map('journeyMap').setView([-7.2575, 112.7521], 13);
                    window.L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(journeyMap);
                }
                journeyMap.invalidateSize();

                if (journeyPolyline) journeyPolyline.remove();

                const latLngs = data.path.map(p => [p.lat, p.lng]);
                if (latLngs.length > 0) {
                    journeyPolyline = window.L.polyline(latLngs, { color: '#f59e0b', weight: 5, opacity: 0.85 }).addTo(journeyMap);
                    journeyMap.fitBounds(journeyPolyline.getBounds(), { padding: [30, 30] });

                    // Start & End markers
                    window.L.marker(latLngs[0]).addTo(journeyMap).bindPopup('Mulai Sesi');
                    window.L.marker(latLngs[latLngs.length - 1]).addTo(journeyMap).bindPopup('Titik Terakhir');
                }
            }, 250);
        } catch (e) {
            alert('Gagal mengambil data riwayat perjalanan.');
        }
    }

    // SALES MOTORIS TRACKING
    @if(auth()->user()->hasRole('Sales Motoris'))
    async function checkSalesSession() {
        try {
            const res = await fetch('/api/tracking/status');
            const data = await res.json();
            if (data.active) {
                activeSessionId = data.session_id;
                setTrackingUIActive(true);
                startSendingLocations();
            } else {
                setTrackingUIActive(false);
            }
        } catch(e) {}
    }

    function setTrackingUIActive(isActive) {
        const badge = document.getElementById('salesSessionStatus');
        const btn = document.getElementById('btnToggleTracking');
        const icon = document.getElementById('salesGpsIcon');

        if (isActive) {
            badge.className = 'inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-0.5 text-[11px] font-semibold text-emerald-700 border border-emerald-200';
            badge.innerHTML = '<span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Sesi Berjalan';
            btn.className = 'inline-flex items-center gap-2 rounded-lg bg-rose-600 px-4 py-2.5 text-xs font-bold text-white shadow-sm hover:bg-rose-700 transition';
            btn.innerHTML = '<svg class="h-3.5 w-3.5 fill-current" viewBox="0 0 24 24"><rect x="6" y="6" width="12" height="12" rx="1"/></svg><span>Akhiri Sesi Tracking</span>';
            icon.className = 'flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-200';
            icon.innerHTML = '<svg class="h-6 w-6 text-emerald-600 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>';
        } else {
            badge.className = 'inline-flex items-center gap-1 rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-semibold text-slate-600';
            badge.textContent = 'Tidak Aktif';
            btn.className = 'inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2.5 text-xs font-bold text-white shadow-sm hover:bg-emerald-700 transition';
            btn.innerHTML = '<svg class="h-3.5 w-3.5 fill-current" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg><span>Mulai Sesi Tracking</span>';
            icon.className = 'flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-600 border border-slate-200';
            icon.innerHTML = '<svg class="h-6 w-6 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>';
        }
    }

    async function toggleTrackingSession() {
        if (activeSessionId) {
            // End
            const res = await fetch('/api/tracking/end', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ session_id: activeSessionId })
            });
            const data = await res.json();
            activeSessionId = null;
            clearInterval(locationInterval);
            setTrackingUIActive(false);
            alert(data.message || 'Sesi tracking diakhiri.');
            fetchLivePositions();
        } else {
            // Start
            if (!navigator.geolocation) {
                alert('Geolokasi tidak didukung oleh browser Anda.');
                return;
            }
            navigator.geolocation.getCurrentPosition(
                async pos => {
                    const res = await fetch('/api/tracking/start', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({})
                    });
                    const data = await res.json();
                    activeSessionId = data.session_id;
                    setTrackingUIActive(true);
                    sendSingleLocation(pos);
                    startSendingLocations();
                    fetchLivePositions();
                },
                err => {
                    alert('Izin lokasi ditolak atau GPS tidak aktif: ' + err.message);
                },
                { enableHighAccuracy: true }
            );
        }
    }

    function startSendingLocations() {
        clearInterval(locationInterval);
        locationInterval = setInterval(() => {
            if (!activeSessionId) return;
            navigator.geolocation.getCurrentPosition(
                pos => sendSingleLocation(pos),
                () => {},
                { enableHighAccuracy: true, timeout: 8000 }
            );
        }, 20000); // send every 20 seconds
    }

    async function sendSingleLocation(pos) {
        if (!activeSessionId) return;
        try {
            await fetch('/api/tracking/location', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({
                    session_id: activeSessionId,
                    latitude: pos.coords.latitude,
                    longitude: pos.coords.longitude,
                    accuracy: pos.coords.accuracy,
                    speed: pos.coords.speed,
                    heading: pos.coords.heading,
                    recorded_at: new Date(pos.timestamp).toISOString()
                })
            });
        } catch(e) {}
    }
    @endif
    </script>
</x-app-layout>

