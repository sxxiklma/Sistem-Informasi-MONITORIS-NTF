{{-- PWA Status Toast & Floating Install Prompt --}}
<div id="pwaOfflineToast" class="fixed top-4 left-1/2 -translate-x-1/2 z-50 hidden max-w-md w-[92%] sm:w-auto flex items-center gap-3 rounded-2xl border border-rose-300/60 bg-rose-950/90 p-3 text-white shadow-2xl backdrop-blur-md transition-all duration-300">
    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-rose-500/20 text-rose-400">
        <svg class="h-5 w-5 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636a9 9 0 010 12.728m0 0l-2.829-2.829m2.829 2.829L21 21M15.536 8.464a5 5 0 010 7.072m0 0l-2.829-2.829m-4.243 4.243a9 9 0 01-12.728 0m0 0l2.829-2.829m-2.829 2.829L3 21M8.464 8.464a5 5 0 000 7.072m0 0l2.829-2.829"/></svg>
    </div>
    <div class="flex-1 text-xs">
        <p class="font-bold text-white">Mode Offline Aktif</p>
        <p class="text-[11px] text-rose-200">Sinyal terputus. Data tersimpan di cache lokal.</p>
    </div>
    <button type="button" onclick="window.location.reload()" class="rounded-lg bg-rose-600 px-2.5 py-1 text-[11px] font-bold text-white shadow-xs hover:bg-rose-500">
        Cek Sinyal
    </button>
</div>

<div id="pwaOnlineToast" class="fixed top-4 left-1/2 -translate-x-1/2 z-50 hidden max-w-md w-[92%] sm:w-auto flex items-center gap-3 rounded-2xl border border-emerald-300/60 bg-emerald-950/90 p-3 text-white shadow-2xl backdrop-blur-md transition-all duration-300">
    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-emerald-500/20 text-emerald-400">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
    </div>
    <div class="flex-1 text-xs">
        <p class="font-bold text-white">Kembali Online</p>
        <p class="text-[11px] text-emerald-200">Koneksi internet terhubung kembali.</p>
    </div>
</div>

{{-- Floating Install Bar (Always visible on mobile & desktop browser when not installed) --}}
<div id="pwaFloatingInstallBanner" class="fixed bottom-4 sm:bottom-6 right-4 sm:right-6 z-50 hidden max-w-xs sm:max-w-sm rounded-2xl border border-sky-400/40 bg-slate-950/95 p-3.5 text-white shadow-2xl backdrop-blur-md transition-all duration-300 animate-in fade-in slide-in-from-bottom-4">
    <div class="flex items-center gap-3">
        <img src="{{ asset('images/icons/icon-96x96.png') }}" alt="Netafarm" class="h-11 w-11 rounded-xl object-contain border border-sky-400/40 p-1 bg-slate-900 shrink-0">
        <div class="flex-1 min-w-0 text-xs">
            <h4 class="font-bold text-white truncate flex items-center gap-1.5">
                <span>Pasang Netafarm</span>
                <span class="inline-block px-1.5 py-0.5 text-[9px] font-bold bg-sky-500/20 text-sky-400 rounded-md">PWA</span>
            </h4>
            <p class="text-[11px] text-slate-300 leading-snug">Pasang ke layar utama HP tanpa buka browser</p>
        </div>
        <button type="button" onclick="dismissPwaBanner()" class="text-slate-400 hover:text-white p-1" title="Tutup">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>
    <div class="mt-2.5 flex items-center justify-end gap-2">
        <button type="button" onclick="dismissPwaBanner()" class="px-2.5 py-1 text-[11px] font-semibold text-slate-400 hover:text-white">
            Nanti Saja
        </button>
        <button type="button" onclick="triggerPwaInstall()" class="inline-flex items-center gap-1.5 rounded-lg bg-sky-500 hover:bg-sky-400 px-3.5 py-1.5 text-xs font-bold text-white shadow-md shadow-sky-500/30 active:scale-95 transition">
            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            <span>Install Sekarang</span>
        </button>
    </div>
</div>

{{-- Interactive PWA Guidance Modal (Custom Modal for All Devices) --}}
<div id="pwaGuideModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm animate-in fade-in duration-200">
    <div class="w-full max-w-md rounded-2xl border border-sky-400/30 bg-slate-900 p-5 text-white shadow-2xl">
        <div class="flex items-center justify-between pb-3 border-b border-slate-800">
            <div class="flex items-center gap-2.5">
                <img src="{{ asset('images/icons/icon-96x96.png') }}" alt="Logo" class="h-7 w-7 rounded-lg object-contain">
                <h3 class="font-bold text-sm text-white">Petunjuk Pemasangan Aplikasi</h3>
            </div>
            <button type="button" onclick="closePwaModal()" class="text-slate-400 hover:text-white p-1 rounded-lg">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        
        <div class="mt-4 space-y-3.5 text-xs text-slate-300">
            <div id="pwaHttpWarning" class="hidden p-3 rounded-xl border border-amber-500/40 bg-amber-500/10 text-amber-200 space-y-2">
                <div class="flex items-center gap-2 font-bold text-amber-300">
                    <svg class="h-4 w-4 shrink-0 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span>Perhatian: Anda Mengakses via HTTP</span>
                </div>
                <p class="text-[11px] leading-relaxed">
                    Browser Android memblokir tombol instal otomatis jika belum menggunakan HTTPS. Silakan gunakan link HTTPS resmi di bawah agar tombol instal langsung aktif:
                </p>
                <a href="https://a1b9-182-8-122-243.ngrok-free.app" class="inline-flex items-center justify-center gap-1.5 w-full py-2 px-3 rounded-lg bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs transition">
                    <span>Buka via HTTPS Resmi (Ngrok)</span>
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                </a>
            </div>

            <div class="p-3 rounded-xl border border-slate-800 bg-slate-950/60 space-y-2">
                <div class="flex items-center gap-2 font-bold text-sky-400">
                    <span>📱 Di HP Android (Google Chrome)</span>
                </div>
                <ol class="list-decimal list-inside space-y-1 text-slate-300 text-[11px] leading-relaxed">
                    <li>Ketuk menu <b>titik tiga (⋮)</b> di pojok kanan atas Chrome.</li>
                    <li>Pilih menu <b>"Instal Aplikasi"</b> atau <b>"Tambahkan ke Layar Utama"</b>.</li>
                    <li>Ketuk <b>"Instal"</b> pada dialog yang muncul.</li>
                </ol>
            </div>

            <div class="p-3 rounded-xl border border-slate-800 bg-slate-950/60 space-y-2">
                <div class="flex items-center gap-2 font-bold text-emerald-400">
                    <span>🍎 Di iPhone / iPad (Safari)</span>
                </div>
                <ol class="list-decimal list-inside space-y-1 text-slate-300 text-[11px] leading-relaxed">
                    <li>Ketuk ikon <b>Bagikan (Share)</b> (kotak dengan panah ke atas di bawah).</li>
                    <li>Gulir ke bawah lalu pilih <b>"Tambahkan ke Layar Utama" (Add to Home Screen)</b>.</li>
                    <li>Ketuk <b>Tambah</b> di pojok kanan atas.</li>
                </ol>
            </div>
        </div>

        <div class="mt-4 flex justify-end">
            <button type="button" onclick="closePwaModal()" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs transition">
                Mengerti
            </button>
        </div>
    </div>
</div>

<script>
    let deferredPwaPrompt = null;

    // Check if app is running in Standalone PWA mode
    const isRunningStandalone = window.matchMedia('(display-mode: standalone)').matches || 
                                window.navigator.standalone === true || 
                                document.referrer.includes('android-app://');

    // 1. Register Service Worker
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/sw.js')
                .then((reg) => {
                    console.log('[PWA] Service Worker aktif:', reg.scope);
                })
                .catch((err) => {
                    console.warn('[PWA] Service Worker gagal didaftarkan:', err);
                });
        });
    }

    // 2. Handle beforeinstallprompt (PWA installability event from Chrome)
    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        deferredPwaPrompt = e;
        console.log('[PWA] beforeinstallprompt event captured!');

        // Show buttons in sidebar & drawer if present
        const sideBtn = document.getElementById('pwaInstallContainerSidebar');
        if (sideBtn) sideBtn.classList.remove('hidden');

        const drawerBtn = document.getElementById('pwaInstallContainerDrawer');
        if (drawerBtn) drawerBtn.classList.remove('hidden');

        // Show floating install banner
        showFloatingBanner();
    });

    // Automatically display floating banner on regular browser after 800ms
    window.addEventListener('DOMContentLoaded', () => {
        if (!isRunningStandalone) {
            setTimeout(showFloatingBanner, 800);
        }
    });

    function showFloatingBanner() {
        if (isRunningStandalone) return;
        const banner = document.getElementById('pwaFloatingInstallBanner');
        if (banner) {
            banner.classList.remove('hidden');
        }
    }

    window.addEventListener('appinstalled', () => {
        deferredPwaPrompt = null;
        console.log('[PWA] Aplikasi Netafarm berhasil diinstal!');
        const banner = document.getElementById('pwaFloatingInstallBanner');
        if (banner) banner.classList.add('hidden');
        const sideBtn = document.getElementById('pwaInstallContainerSidebar');
        if (sideBtn) sideBtn.classList.add('hidden');
        const drawerBtn = document.getElementById('pwaInstallContainerDrawer');
        if (drawerBtn) drawerBtn.classList.add('hidden');
    });

    function triggerPwaInstall() {
        if (deferredPwaPrompt) {
            deferredPwaPrompt.prompt();
            deferredPwaPrompt.userChoice.then((choiceResult) => {
                if (choiceResult.outcome === 'accepted') {
                    console.log('[PWA] User menyetujui instalasi PWA');
                    const banner = document.getElementById('pwaFloatingInstallBanner');
                    if (banner) banner.classList.add('hidden');
                }
                deferredPwaPrompt = null;
            });
        } else {
            // Show guide modal
            const modal = document.getElementById('pwaGuideModal');
            const warning = document.getElementById('pwaHttpWarning');
            if (modal) {
                // If on plain HTTP, show the HTTPS link recommendation
                if (window.location.protocol !== 'https:' && window.location.hostname !== 'localhost' && window.location.hostname !== '127.0.0.1') {
                    if (warning) warning.classList.remove('hidden');
                } else {
                    if (warning) warning.classList.add('hidden');
                }
                modal.classList.remove('hidden');
            }
        }
    }

    function closePwaModal() {
        const modal = document.getElementById('pwaGuideModal');
        if (modal) modal.classList.add('hidden');
    }

    function dismissPwaBanner() {
        const banner = document.getElementById('pwaFloatingInstallBanner');
        if (banner) banner.classList.add('hidden');
    }

    // 3. Online/Offline Network Status Detection
    function handleNetworkChange() {
        const offToast = document.getElementById('pwaOfflineToast');
        const onToast = document.getElementById('pwaOnlineToast');

        if (!navigator.onLine) {
            if (onToast) onToast.classList.add('hidden');
            if (offToast) offToast.classList.remove('hidden');
        } else {
            if (offToast) offToast.classList.add('hidden');
            if (onToast) {
                onToast.classList.remove('hidden');
                setTimeout(() => {
                    onToast.classList.add('hidden');
                }, 3500);
            }
        }
    }

    window.addEventListener('online', handleNetworkChange);
    window.addEventListener('offline', handleNetworkChange);
    if (!navigator.onLine) {
        handleNetworkChange();
    }
</script>
