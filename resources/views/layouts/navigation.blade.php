<nav class="text-white">
    {{-- 1. DESKTOP SIDEBAR WITH ACCORDION & AUTO-CLOSING --}}
    <aside 
        @mouseenter="if (sidebarCollapsed) sidebarHover = true"
        @mouseleave="if (sidebarCollapsed) sidebarHover = false"
        class="fixed inset-y-0 left-0 z-40 hidden flex-col border-r border-slate-800 bg-slate-950 transition-all duration-300 ease-in-out lg:flex shadow-2xl select-none"
        :class="(sidebarCollapsed && !sidebarHover) ? 'w-20' : 'w-64'"
        x-data="{
            activeAccordion: '{{ 
                (request()->routeIs('visit-tasks.*') || request()->routeIs('visits.*') || request()->routeIs('tracking.*') || request()->routeIs('customers.*') || request()->routeIs('customer-types.*')) 
                    ? 'operasional' 
                    : ((request()->routeIs('products.*') || request()->routeIs('orders.*') || request()->routeIs('modules.targets')) 
                        ? 'komersial' 
                        : '') 
            }}',
            toggleAccordion(name) {
                if (sidebarCollapsed && !sidebarHover) {
                    sidebarHover = true;
                }
                this.activeAccordion = (this.activeAccordion === name ? '' : name);
            }
        }">
        
        {{-- Brand Logo Header --}}
        <div class="flex h-20 items-center border-b border-slate-800/80 px-4"
             :class="(sidebarCollapsed && !sidebarHover) ? 'justify-center' : 'justify-start'">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 overflow-hidden text-lg font-semibold tracking-tight hover:opacity-95 transition">
                <img src="{{ asset('images/netafarm-logo.png') }}" alt="Netafarm" class="h-10 w-10 shrink-0 object-contain">
                <div x-show="!sidebarCollapsed || sidebarHover" 
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 translate-x-1"
                     x-transition:enter-end="opacity-100 translate-x-0"
                     class="whitespace-nowrap font-bold text-white tracking-tight leading-none">
                    <span class="text-base font-extrabold tracking-tight">Neta<span class="text-sky-400">farm</span></span>
                    <span class="block text-[8px] font-semibold uppercase tracking-widest text-slate-400 mt-1">Distributor Pertanian</span>
                </div>
            </a>
        </div>

        {{-- Nav Links Area --}}
        <div class="flex flex-1 flex-col justify-between overflow-y-auto px-3 py-4 space-y-4">
            <div class="space-y-1.5">
                <p x-show="!sidebarCollapsed || sidebarHover" 
                   class="px-3 pb-1 text-[10px] font-bold uppercase tracking-[.2em] text-slate-400 transition-opacity">
                    Menu Navigasi
                </p>

                {{-- 1. Ringkasan (Dashboard - Top standalone item) --}}
                <a href="{{ route('dashboard') }}" 
                   title="Ringkasan"
                   class="group flex items-center rounded-xl px-3 py-2.5 text-xs font-semibold transition-all duration-150 active:scale-[0.98] {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white shadow-md shadow-blue-900/30 font-bold' : 'text-slate-300 hover:bg-slate-900 hover:text-white' }}"
                   :class="(sidebarCollapsed && !sidebarHover) ? 'justify-center' : 'justify-start gap-3'">
                    <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-slate-400 group-hover:text-sky-400' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <span x-show="!sidebarCollapsed || sidebarHover" class="truncate text-xs">Ringkasan</span>
                </a>

                {{-- ======================================================== --}}
                {{-- ACCORDION 1: OPERASIONAL LAPANGAN (Kunjungan & Rute)     --}}
                {{-- ======================================================== --}}
                <div class="pt-1">
                    <button type="button" 
                            @click="toggleAccordion('operasional')" 
                            class="group flex w-full items-center justify-between rounded-xl px-3 py-2.5 text-xs font-medium transition-all duration-150 active:scale-[0.99]"
                            :class="activeAccordion === 'operasional' 
                                ? 'bg-slate-900/90 text-sky-400 border border-slate-800' 
                                : '{{ (request()->routeIs('visit-tasks.*') || request()->routeIs('visits.*') || request()->routeIs('tracking.*') || request()->routeIs('customers.*') || request()->routeIs('customer-types.*')) ? 'text-sky-400 bg-slate-900/50 font-semibold' : 'text-slate-300 hover:bg-slate-900/60 hover:text-white' }}'"
                            :title="(sidebarCollapsed && !sidebarHover) ? 'Operasional Lapangan' : ''">
                        <div class="flex items-center gap-3 min-w-0" :class="(sidebarCollapsed && !sidebarHover) ? 'mx-auto' : ''">
                            <svg class="h-5 w-5 shrink-0 transition-colors" :class="activeAccordion === 'operasional' || {{ (request()->routeIs('visit-tasks.*') || request()->routeIs('visits.*') || request()->routeIs('tracking.*') || request()->routeIs('customers.*') || request()->routeIs('customer-types.*')) ? 'true' : 'false' }} ? 'text-sky-400' : 'text-slate-400 group-hover:text-sky-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                            </svg>
                            <span x-show="!sidebarCollapsed || sidebarHover" class="truncate text-xs font-medium">
                                Operasional Lapangan
                            </span>
                        </div>
                        <div class="flex items-center gap-1.5" x-show="!sidebarCollapsed || sidebarHover">
                            @if(request()->routeIs('visit-tasks.*') || request()->routeIs('visits.*') || request()->routeIs('tracking.*') || request()->routeIs('customers.*') || request()->routeIs('customer-types.*'))
                                <span class="h-1.5 w-1.5 rounded-full bg-sky-400 animate-pulse"></span>
                            @endif
                            <svg class="h-4 w-4 shrink-0 transition-transform duration-200 text-slate-400 group-hover:text-slate-200" 
                                 :class="activeAccordion === 'operasional' ? 'rotate-180 text-sky-400' : ''" 
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </button>

                    {{-- Accordion 1 Submenu Items (Auto collapses when another opens) --}}
                    <div x-show="activeAccordion === 'operasional'"
                         x-transition:enter="transition-all ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-1 max-h-0"
                         x-transition:enter-end="opacity-100 translate-y-0 max-h-96"
                         x-transition:leave="transition-all ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0 max-h-96"
                         x-transition:leave-end="opacity-0 -translate-y-1 max-h-0"
                         class="overflow-hidden pl-3 pr-1 pt-1 space-y-1 border-l-2 border-slate-800/80 ml-5 my-1"
                         :class="(sidebarCollapsed && !sidebarHover) ? 'hidden' : ''">
                        
                        {{-- Tugas Kunjungan --}}
                        @can('kelola kunjungan')
                        <a href="{{ route('visit-tasks.index') }}" 
                           class="group flex items-center gap-2.5 rounded-lg px-2.5 py-2 text-xs transition-all duration-150 {{ request()->routeIs('visit-tasks.*') ? 'bg-blue-600 text-white font-bold shadow-xs shadow-blue-900/20' : 'text-slate-400 hover:bg-slate-900 hover:text-white' }}">
                            <svg class="h-4 w-4 shrink-0 {{ request()->routeIs('visit-tasks.*') ? 'text-white' : 'text-slate-500 group-hover:text-sky-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                            <span class="truncate">Tugas Kunjungan</span>
                        </a>
                        @endcan

                        {{-- Rute & Navigasi Peta --}}
                        <a href="{{ route('visits.index') }}" 
                           class="group flex items-center gap-2.5 rounded-lg px-2.5 py-2 text-xs transition-all duration-150 {{ (request()->routeIs('visits.*') || request()->routeIs('tracking.*')) ? 'bg-blue-600 text-white font-bold shadow-xs shadow-blue-900/20' : 'text-slate-400 hover:bg-slate-900 hover:text-white' }}">
                            <svg class="h-4 w-4 shrink-0 {{ (request()->routeIs('visits.*') || request()->routeIs('tracking.*')) ? 'text-white' : 'text-slate-500 group-hover:text-sky-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span class="truncate">Rute & Navigasi Peta</span>
                        </a>

                        {{-- Pelanggan / Outlet --}}
                        <a href="{{ route('customers.index') }}" 
                           class="group flex items-center gap-2.5 rounded-lg px-2.5 py-2 text-xs transition-all duration-150 {{ request()->routeIs('customers.*') ? 'bg-blue-600 text-white font-bold shadow-xs shadow-blue-900/20' : 'text-slate-400 hover:bg-slate-900 hover:text-white' }}">
                            <svg class="h-4 w-4 shrink-0 {{ request()->routeIs('customers.*') ? 'text-white' : 'text-slate-500 group-hover:text-sky-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            <span class="truncate">Pelanggan / Outlet</span>
                        </a>

                        {{-- Kategori / Jenis Pelanggan --}}
                        @can('kelola master data')
                        <a href="{{ route('customer-types.index') }}" 
                           class="group flex items-center gap-2.5 rounded-lg px-2.5 py-2 text-xs transition-all duration-150 {{ request()->routeIs('customer-types.*') ? 'bg-blue-600 text-white font-bold shadow-xs shadow-blue-900/20' : 'text-slate-400 hover:bg-slate-900 hover:text-white' }}">
                            <svg class="h-4 w-4 shrink-0 {{ request()->routeIs('customer-types.*') ? 'text-white' : 'text-slate-500 group-hover:text-sky-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                            <span class="truncate">Kategori Pelanggan</span>
                        </a>
                        @endcan
                    </div>
                </div>

                {{-- ======================================================== --}}
                {{-- ACCORDION 2: PENJUALAN & PRODUK                          --}}
                {{-- ======================================================== --}}
                <div class="pt-1">
                    <button type="button" 
                            @click="toggleAccordion('komersial')" 
                            class="group flex w-full items-center justify-between rounded-xl px-3 py-2.5 text-xs font-medium transition-all duration-150 active:scale-[0.99]"
                            :class="activeAccordion === 'komersial' 
                                ? 'bg-slate-900/90 text-sky-400 border border-slate-800' 
                                : '{{ (request()->routeIs('products.*') || request()->routeIs('orders.*') || request()->routeIs('modules.targets')) ? 'text-sky-400 bg-slate-900/50 font-semibold' : 'text-slate-300 hover:bg-slate-900/60 hover:text-white' }}'"
                            :title="(sidebarCollapsed && !sidebarHover) ? 'Penjualan & Produk' : ''">
                        <div class="flex items-center gap-3 min-w-0" :class="(sidebarCollapsed && !sidebarHover) ? 'mx-auto' : ''">
                            <svg class="h-5 w-5 shrink-0 transition-colors" :class="activeAccordion === 'komersial' || {{ (request()->routeIs('products.*') || request()->routeIs('orders.*') || request()->routeIs('modules.targets')) ? 'true' : 'false' }} ? 'text-sky-400' : 'text-slate-400 group-hover:text-sky-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                            <span x-show="!sidebarCollapsed || sidebarHover" class="truncate text-xs font-medium">
                                Penjualan & Produk
                            </span>
                        </div>
                        <div class="flex items-center gap-1.5" x-show="!sidebarCollapsed || sidebarHover">
                            @if(request()->routeIs('products.*') || request()->routeIs('orders.*') || request()->routeIs('modules.targets'))
                                <span class="h-1.5 w-1.5 rounded-full bg-sky-400 animate-pulse"></span>
                            @endif
                            <svg class="h-4 w-4 shrink-0 transition-transform duration-200 text-slate-400 group-hover:text-slate-200" 
                                 :class="activeAccordion === 'komersial' ? 'rotate-180 text-sky-400' : ''" 
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </button>

                    {{-- Accordion 2 Submenu Items (Auto collapses when another opens) --}}
                    <div x-show="activeAccordion === 'komersial'"
                         x-transition:enter="transition-all ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-1 max-h-0"
                         x-transition:enter-end="opacity-100 translate-y-0 max-h-96"
                         x-transition:leave="transition-all ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0 max-h-96"
                         x-transition:leave-end="opacity-0 -translate-y-1 max-h-0"
                         class="overflow-hidden pl-3 pr-1 pt-1 space-y-1 border-l-2 border-slate-800/80 ml-5 my-1"
                         :class="(sidebarCollapsed && !sidebarHover) ? 'hidden' : ''">
                        
                        {{-- Produk & Stok / Katalog --}}
                        <a href="{{ route('products.index') }}" 
                           class="group flex items-center gap-2.5 rounded-lg px-2.5 py-2 text-xs transition-all duration-150 {{ request()->routeIs('products.*') ? 'bg-blue-600 text-white font-bold shadow-xs shadow-blue-900/20' : 'text-slate-400 hover:bg-slate-900 hover:text-white' }}">
                            <svg class="h-4 w-4 shrink-0 {{ request()->routeIs('products.*') ? 'text-white' : 'text-slate-500 group-hover:text-sky-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                            <span class="truncate">{{ auth()->user()->hasRole('Sales Motoris') ? 'Katalog Produk' : 'Produk & Stok' }}</span>
                        </a>

                        {{-- Penjualan & Pesanan --}}
                        @can('kelola pesanan')
                        <a href="{{ route('orders.index') }}" 
                           class="group flex items-center gap-2.5 rounded-lg px-2.5 py-2 text-xs transition-all duration-150 {{ request()->routeIs('orders.*') ? 'bg-blue-600 text-white font-bold shadow-xs shadow-blue-900/20' : 'text-slate-400 hover:bg-slate-900 hover:text-white' }}">
                            <svg class="h-4 w-4 shrink-0 {{ request()->routeIs('orders.*') ? 'text-white' : 'text-slate-500 group-hover:text-sky-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <span class="truncate">Penjualan & Pesanan</span>
                        </a>
                        @endcan

                        {{-- Target & KPI --}}
                        @can('lihat laporan')
                        <a href="{{ route('modules.targets') }}" 
                           class="group flex items-center gap-2.5 rounded-lg px-2.5 py-2 text-xs transition-all duration-150 {{ request()->routeIs('modules.targets') ? 'bg-blue-600 text-white font-bold shadow-xs shadow-blue-900/20' : 'text-slate-400 hover:bg-slate-900 hover:text-white' }}">
                            <svg class="h-4 w-4 shrink-0 {{ request()->routeIs('modules.targets') ? 'text-white' : 'text-slate-500 group-hover:text-sky-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                            </svg>
                            <span class="truncate">Target & KPI</span>
                        </a>
                        @endcan
                    </div>
                </div>

                {{-- ======================================================== --}}
                {{-- 2.5. KEUANGAN & PIUTANG KIOS (STANDALONE)                --}}
                {{-- ======================================================== --}}
                @canany(['lihat laporan', 'kelola pesanan'])
                <div class="pt-1">
                    <a href="{{ route('finance.index') }}" 
                       title="Keuangan & Piutang Kios"
                       class="group flex items-center rounded-xl px-3 py-2.5 text-xs font-semibold transition-all duration-150 active:scale-[0.98] {{ request()->routeIs('finance.*') ? 'bg-blue-600 text-white shadow-md shadow-blue-900/30 font-bold' : 'text-slate-300 hover:bg-slate-900 hover:text-white' }}"
                       :class="(sidebarCollapsed && !sidebarHover) ? 'justify-center' : 'justify-start gap-3'">
                        <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('finance.*') ? 'text-white' : 'text-slate-400 group-hover:text-sky-400' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span x-show="!sidebarCollapsed || sidebarHover" class="truncate text-xs">Keuangan & Piutang</span>
                    </a>
                </div>
                @endcanany

                {{-- ======================================================== --}}
                {{-- 3. HALAMAN LAPORAN (TERPISAH, BUKAN SUB-MENU)           --}}
                {{-- ======================================================== --}}
                @can('lihat laporan')
                <div class="pt-1">
                    <a href="{{ route('modules.reports') }}" 
                       title="Laporan & Monitoring"
                       class="group flex items-center rounded-xl px-3 py-2.5 text-xs font-semibold transition-all duration-150 active:scale-[0.98] {{ request()->routeIs('modules.reports') ? 'bg-blue-600 text-white shadow-md shadow-blue-900/30 font-bold' : 'text-slate-300 hover:bg-slate-900 hover:text-white' }}"
                       :class="(sidebarCollapsed && !sidebarHover) ? 'justify-center' : 'justify-start gap-3'">
                        <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('modules.reports') ? 'text-white' : 'text-slate-400 group-hover:text-sky-400' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span x-show="!sidebarCollapsed || sidebarHover" class="truncate text-xs">Laporan</span>
                    </a>
                </div>
                @endcan

                {{-- ======================================================== --}}
                {{-- 4. MANAJEMEN SISTEM (TERPISAH, BUKAN SUB-MENU LAPORAN)   --}}
                {{-- ======================================================== --}}
                {{-- Pengguna --}}
                @can('kelola pengguna')
                <div class="pt-0.5">
                    <a href="{{ route('modules.users') }}" 
                       title="Manajemen Pengguna"
                       class="group flex items-center rounded-xl px-3 py-2.5 text-xs font-semibold transition-all duration-150 active:scale-[0.98] {{ request()->routeIs('modules.users') ? 'bg-blue-600 text-white shadow-md shadow-blue-900/30 font-bold' : 'text-slate-300 hover:bg-slate-900 hover:text-white' }}"
                       :class="(sidebarCollapsed && !sidebarHover) ? 'justify-center' : 'justify-start gap-3'">
                        <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('modules.users') ? 'text-white' : 'text-slate-400 group-hover:text-sky-400' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <span x-show="!sidebarCollapsed || sidebarHover" class="truncate text-xs">Pengguna</span>
                    </a>
                </div>
                @endcan

                {{-- Pengaturan & Audit Log Sistem --}}
                @can('kelola pengaturan')
                <div class="pt-0.5">
                    <a href="{{ route('modules.settings') }}" 
                       title="Pengaturan & Audit Sistem"
                       class="group flex items-center rounded-xl px-3 py-2.5 text-xs font-semibold transition-all duration-150 active:scale-[0.98] {{ request()->routeIs('modules.settings') ? 'bg-blue-600 text-white shadow-md shadow-blue-900/30 font-bold' : 'text-slate-300 hover:bg-slate-900 hover:text-white' }}"
                       :class="(sidebarCollapsed && !sidebarHover) ? 'justify-center' : 'justify-start gap-3'">
                        <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('modules.settings') ? 'text-white' : 'text-slate-400 group-hover:text-sky-400' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span x-show="!sidebarCollapsed || sidebarHover" class="truncate text-xs">Pengaturan & Audit</span>
                    </a>
                </div>
                @endcan
            </div>

            {{-- Compact / Standard Footer Profile & Logout Actions --}}
            <div class="border-t border-slate-800/80 pt-3 mt-auto">
                <div :class="(sidebarCollapsed && !sidebarHover) ? 'flex flex-col gap-2' : 'grid grid-cols-2 gap-2'">
                    <a href="{{ route('profile.edit') }}" 
                       class="group flex items-center justify-center gap-1.5 rounded-xl border border-slate-800 bg-slate-900/80 px-2.5 py-2.5 text-xs font-semibold text-slate-300 shadow-2xs hover:border-sky-400/50 hover:bg-slate-900 hover:text-sky-400 transition-all duration-150 active:scale-95"
                       title="Profil Akun">
                        <svg class="h-4 w-4 text-slate-400 group-hover:text-sky-400 transition-colors shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span x-show="!sidebarCollapsed || sidebarHover" class="truncate text-[11px]">Profil Akun</span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <button type="submit" 
                                class="group flex w-full items-center justify-center gap-1.5 rounded-xl border border-slate-800 bg-slate-900/80 px-2.5 py-2.5 text-xs font-semibold text-slate-300 shadow-2xs hover:border-rose-900/50 hover:bg-rose-950/30 hover:text-rose-400 transition-all duration-150 active:scale-95"
                                title="Keluar dari sistem">
                            <svg class="h-4 w-4 text-slate-400 group-hover:text-rose-400 transition-colors shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            <span x-show="!sidebarCollapsed || sidebarHover" class="truncate text-[11px]">Keluar</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </aside>

    {{-- 2. UNIFIED MOBILE TOP HEADER --}}
    <div class="sticky top-0 z-30 flex h-16 items-center justify-between border-b border-slate-800 bg-slate-950 px-4 shadow-md lg:hidden">
        <div class="flex items-center gap-2.5">
            <button @click="mobileNavOpen = true" 
                class="flex h-10 w-10 items-center justify-center rounded-xl border border-slate-800 bg-slate-900 text-slate-200 active:scale-95 transition" 
                aria-label="Buka menu navigasi">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 text-base font-bold tracking-tight">
                <img src="{{ asset('images/netafarm-logo.png') }}" alt="Netafarm" class="h-8 w-8 shrink-0 object-contain">
                <span>Neta<span class="text-sky-400">farm</span></span>
            </a>
        </div>

        {{-- Mobile Right Controls: Notification & Avatar --}}
        <div class="flex items-center gap-2" x-data="{ notifOpen: false, unreadCount: 0, notifList: [] }"
            x-init="
                fetch('/notifications/count').then(r=>r.json()).then(d=>unreadCount=d.count).catch(()=>{});
                setInterval(() => {
                    fetch('/notifications/count').then(r=>r.json()).then(d=>unreadCount=d.count).catch(()=>{});
                }, 15000);
            ">
            <div class="relative">
                <button @click="
                    notifOpen = !notifOpen;
                    if (notifOpen) {
                        fetch('/notifications').then(r=>r.json()).then(d=>notifList=d).catch(()=>{});
                    }
                " class="relative flex h-10 w-10 items-center justify-center rounded-xl border border-slate-800 bg-slate-900 text-slate-300 hover:text-white active:scale-95 transition" aria-label="Notifikasi">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <template x-if="unreadCount > 0">
                        <span class="absolute -top-1 -right-1 flex h-4 min-w-[16px] items-center justify-center rounded-full bg-rose-600 px-1 text-[10px] font-bold text-white shadow-xs"
                            x-text="unreadCount > 9 ? '9+' : unreadCount"></span>
                    </template>
                </button>

                {{-- Mobile Notification Dropdown --}}
                <div x-show="notifOpen" @click.outside="notifOpen = false" x-transition
                    class="fixed inset-x-3 top-16 max-w-sm mx-auto rounded-2xl bg-white p-3 shadow-2xl border border-slate-200 z-50 text-xs text-slate-800">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-2 px-1">
                        <span class="font-bold text-slate-900">Notifikasi Lapangan</span>
                        <button @click="
                            fetch('/notifications/read-all', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
                                .then(() => { unreadCount = 0; notifList.forEach(n=>n.is_read=true); });
                        " class="text-[11px] text-blue-600 hover:underline font-semibold">Tandai dibaca</button>
                    </div>
                    <div class="max-h-64 overflow-y-auto divide-y divide-slate-100 py-1">
                        <template x-if="notifList.length === 0">
                            <p class="py-6 text-center text-slate-400">Belum ada notifikasi baru.</p>
                        </template>
                        <template x-for="n in notifList" :key="n.id">
                            <div :class="n.is_read ? 'opacity-70 bg-white' : 'bg-amber-50/50'"
                                class="p-2.5 rounded-lg hover:bg-slate-50 transition">
                                <div class="flex items-start justify-between gap-1">
                                    <strong class="font-semibold text-slate-900" x-text="n.title"></strong>
                                    <span class="text-[10px] text-slate-400 shrink-0" x-text="n.time_ago"></span>
                                </div>
                                <p class="text-slate-600 mt-0.5 line-clamp-2" x-text="n.message"></p>
                                <template x-if="n.action_url">
                                    <a :href="n.action_url" class="mt-1 inline-block text-[11px] font-bold text-blue-600 hover:underline">
                                        Buka Detail &rarr;
                                    </a>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <a href="{{ route('profile.edit') }}" class="flex h-10 w-10 overflow-hidden items-center justify-center rounded-xl bg-blue-600 text-white font-bold text-xs shadow-xs active:scale-95 transition" title="Profil Akun">
                @if(Auth::user()->avatar_url)
                    <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}" class="h-full w-full object-cover">
                @else
                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                @endif
            </a>
        </div>
    </div>

    {{-- 3. INTERACTIVE MOBILE SLIDE-OVER DRAWER --}}
    <div x-show="mobileNavOpen" 
        x-transition:enter="transition-opacity ease-linear duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-linear duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-xs lg:hidden"
        style="display: none;">
        
        <div x-show="mobileNavOpen"
            @click.outside="mobileNavOpen = false"
            x-transition:enter="transition ease-in-out duration-200 transform"
            x-transition:enter-start="-translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in-out duration-200 transform"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="-translate-x-full"
            class="flex h-full w-80 max-w-[85vw] flex-col bg-slate-950 shadow-2xl"
            x-data="{
                mobileAccordion: '{{ 
                    (request()->routeIs('visit-tasks.*') || request()->routeIs('visits.*') || request()->routeIs('tracking.*') || request()->routeIs('customers.*') || request()->routeIs('customer-types.*')) 
                        ? 'operasional' 
                        : ((request()->routeIs('products.*') || request()->routeIs('orders.*') || request()->routeIs('modules.targets')) 
                            ? 'komersial' 
                            : '') 
                }}',
                toggleMobileAccordion(name) {
                    this.mobileAccordion = (this.mobileAccordion === name ? '' : name);
                }
            }">
            
            {{-- Drawer Header --}}
            <div class="flex items-center justify-between border-b border-slate-800 p-4">
                <div class="flex items-center gap-2.5">
                    <img src="{{ asset('images/netafarm-logo.png') }}" alt="Netafarm" class="h-8 w-8 shrink-0 object-contain">
                    <span class="font-bold text-white text-base">Menu <span class="text-sky-400">Netafarm</span></span>
                </div>
                <button @click="mobileNavOpen = false" class="flex h-9 w-9 items-center justify-center rounded-lg bg-slate-900 text-slate-400 hover:text-white transition" aria-label="Tutup menu">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- User Info Card in Drawer --}}
            <div class="border-b border-slate-800/80 bg-slate-900/60 p-4">
                <div class="flex items-center gap-3">
                    <div class="flex h-11 w-11 shrink-0 overflow-hidden items-center justify-center rounded-xl bg-blue-600 text-white font-bold text-sm shadow-xs">
                        @if(Auth::user()->avatar_url)
                            <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}" class="h-full w-full object-cover">
                        @else
                            {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                        @endif
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="truncate font-bold text-white text-sm">{{ Auth::user()->name }}</p>
                        <p class="truncate text-xs text-slate-400">{{ Auth::user()->email }}</p>
                        <div class="mt-1 flex flex-wrap gap-1">
                            <span class="rounded bg-amber-400/20 px-1.5 py-0.5 text-[9px] font-bold uppercase text-amber-300">
                                {{ Auth::user()->roles->first()?->name ?? 'User' }}
                            </span>
                            @if(Auth::user()->team)
                            <span class="rounded bg-slate-800 px-1.5 py-0.5 text-[9px] font-semibold text-slate-300">
                                {{ Auth::user()->team->name }}
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Drawer Navigation Links --}}
            <div class="flex-1 overflow-y-auto p-3 space-y-2">
                {{-- Mobile Ringkasan --}}
                <a href="{{ route('dashboard') }}" 
                   @click="mobileNavOpen = false"
                   class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-xs font-semibold transition {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white font-bold' : 'text-slate-300 hover:bg-slate-900 hover:text-white' }}">
                    <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <span>Ringkasan</span>
                </a>

                {{-- Mobile Accordion 1: Operasional --}}
                <div class="border border-slate-800/80 rounded-xl overflow-hidden bg-slate-900/30">
                    <button type="button" 
                            @click="toggleMobileAccordion('operasional')"
                            class="flex w-full items-center justify-between px-3 py-2.5 text-xs font-semibold text-left transition"
                            :class="mobileAccordion === 'operasional' ? 'bg-slate-900 text-sky-400' : 'text-slate-300 hover:text-white'">
                        <div class="flex items-center gap-2.5">
                            <svg class="h-4 w-4 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                            </svg>
                            <span>Operasional Lapangan</span>
                        </div>
                        <svg class="h-4 w-4 text-slate-400 transition-transform duration-200" 
                             :class="mobileAccordion === 'operasional' ? 'rotate-180 text-sky-400' : ''" 
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="mobileAccordion === 'operasional'" x-transition class="p-2 space-y-1 bg-slate-950/60 border-t border-slate-800/50">
                        @can('kelola kunjungan')
                        <a href="{{ route('visit-tasks.index') }}" @click="mobileNavOpen = false" class="flex items-center gap-2 rounded-lg px-2.5 py-2 text-xs {{ request()->routeIs('visit-tasks.*') ? 'bg-blue-600 text-white font-bold' : 'text-slate-400 hover:text-white' }}">
                            <span>Tugas Kunjungan</span>
                        </a>
                        @endcan
                        <a href="{{ route('visits.index') }}" @click="mobileNavOpen = false" class="flex items-center gap-2 rounded-lg px-2.5 py-2 text-xs {{ (request()->routeIs('visits.*') || request()->routeIs('tracking.*')) ? 'bg-blue-600 text-white font-bold' : 'text-slate-400 hover:text-white' }}">
                            <span>Rute & Navigasi Peta</span>
                        </a>
                        <a href="{{ route('customers.index') }}" @click="mobileNavOpen = false" class="flex items-center gap-2 rounded-lg px-2.5 py-2 text-xs {{ request()->routeIs('customers.*') ? 'bg-blue-600 text-white font-bold' : 'text-slate-400 hover:text-white' }}">
                            <span>Pelanggan / Outlet</span>
                        </a>
                        @can('kelola master data')
                        <a href="{{ route('customer-types.index') }}" @click="mobileNavOpen = false" class="flex items-center gap-2 rounded-lg px-2.5 py-2 text-xs {{ request()->routeIs('customer-types.*') ? 'bg-blue-600 text-white font-bold' : 'text-slate-400 hover:text-white' }}">
                            <span>Kategori Pelanggan</span>
                        </a>
                        @endcan
                    </div>
                </div>

                {{-- Mobile Accordion 2: Penjualan & Produk --}}
                <div class="border border-slate-800/80 rounded-xl overflow-hidden bg-slate-900/30">
                    <button type="button" 
                            @click="toggleMobileAccordion('komersial')"
                            class="flex w-full items-center justify-between px-3 py-2.5 text-xs font-semibold text-left transition"
                            :class="mobileAccordion === 'komersial' ? 'bg-slate-900 text-sky-400' : 'text-slate-300 hover:text-white'">
                        <div class="flex items-center gap-2.5">
                            <svg class="h-4 w-4 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                            <span>Penjualan & Produk</span>
                        </div>
                        <svg class="h-4 w-4 text-slate-400 transition-transform duration-200" 
                             :class="mobileAccordion === 'komersial' ? 'rotate-180 text-sky-400' : ''" 
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div x-show="mobileAccordion === 'komersial'" x-transition class="p-2 space-y-1 bg-slate-950/60 border-t border-slate-800/50">
                        <a href="{{ route('products.index') }}" @click="mobileNavOpen = false" class="flex items-center gap-2 rounded-lg px-2.5 py-2 text-xs {{ request()->routeIs('products.*') ? 'bg-blue-600 text-white font-bold' : 'text-slate-400 hover:text-white' }}">
                            <span>{{ auth()->user()->hasRole('Sales Motoris') ? 'Katalog Produk' : 'Produk & Stok' }}</span>
                        </a>
                        @can('kelola pesanan')
                        <a href="{{ route('orders.index') }}" @click="mobileNavOpen = false" class="flex items-center gap-2 rounded-lg px-2.5 py-2 text-xs {{ request()->routeIs('orders.*') ? 'bg-blue-600 text-white font-bold' : 'text-slate-400 hover:text-white' }}">
                            <span>Penjualan & Pesanan</span>
                        </a>
                        @endcan
                        @can('lihat laporan')
                        <a href="{{ route('modules.targets') }}" @click="mobileNavOpen = false" class="flex items-center gap-2 rounded-lg px-2.5 py-2 text-xs {{ request()->routeIs('modules.targets') ? 'bg-blue-600 text-white font-bold' : 'text-slate-400 hover:text-white' }}">
                            <span>Target & KPI</span>
                        </a>
                        @endcan
                    </div>
                </div>

                {{-- Mobile Keuangan & Piutang (Standalone, Direct Link) --}}
                @canany(['lihat laporan', 'kelola pesanan'])
                <a href="{{ route('finance.index') }}" 
                   @click="mobileNavOpen = false"
                   class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-xs font-semibold transition {{ request()->routeIs('finance.*') ? 'bg-blue-600 text-white font-bold' : 'text-slate-300 hover:bg-slate-900 hover:text-white' }}">
                    <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('finance.*') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Keuangan & Piutang</span>
                </a>
                @endcanany

                {{-- Mobile Laporan (Standalone, Direct Link) --}}
                @can('lihat laporan')
                <a href="{{ route('modules.reports') }}" 
                   @click="mobileNavOpen = false"
                   class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-xs font-semibold transition {{ request()->routeIs('modules.reports') ? 'bg-blue-600 text-white font-bold' : 'text-slate-300 hover:bg-slate-900 hover:text-white' }}">
                    <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('modules.reports') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span>Laporan</span>
                </a>
                @endcan

                {{-- Mobile Pengguna (Standalone, Direct Link) --}}
                @can('kelola pengguna')
                <a href="{{ route('modules.users') }}" 
                   @click="mobileNavOpen = false"
                   class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-xs font-semibold transition {{ request()->routeIs('modules.users') ? 'bg-blue-600 text-white font-bold' : 'text-slate-300 hover:bg-slate-900 hover:text-white' }}">
                    <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('modules.users') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span>Pengguna</span>
                </a>
                @endcan

                {{-- Mobile Pengaturan & Audit (Standalone, Direct Link) --}}
                @can('kelola pengaturan')
                <a href="{{ route('modules.settings') }}" 
                   @click="mobileNavOpen = false"
                   class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-xs font-semibold transition {{ request()->routeIs('modules.settings') ? 'bg-blue-600 text-white font-bold' : 'text-slate-300 hover:bg-slate-900 hover:text-white' }}">
                    <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('modules.settings') ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span>Pengaturan & Audit</span>
                </a>
                @endcan
            </div>

            {{-- Drawer Footer Actions --}}
            <div class="border-t border-slate-800 p-4 bg-slate-900/40 space-y-2">
                <a href="{{ route('profile.edit') }}" @click="mobileNavOpen = false" class="flex items-center gap-2.5 rounded-xl border border-slate-700 bg-slate-900 px-3.5 py-2.5 text-xs font-semibold text-white hover:bg-slate-800 transition">
                    <svg class="h-4 w-4 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    <span>Pengaturan Akun & Profil</span>
                </a>
                {{-- Install App PWA Button in Mobile Drawer --}}
                <div id="pwaInstallContainerDrawer" class="hidden mb-3">
                    <button type="button" onclick="triggerPwaInstall()" class="flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-sky-600 to-blue-600 px-3.5 py-2.5 text-xs font-bold text-white shadow-md shadow-sky-600/30 hover:from-sky-500 hover:to-blue-500 transition active:scale-95">
                        <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        <span>Install Aplikasi di HP</span>
                    </button>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-xl bg-rose-600/10 border border-rose-500/20 px-3.5 py-2.5 text-xs font-semibold text-rose-400 hover:bg-rose-600/20 transition">
                        <svg class="h-4 w-4 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        <span>Keluar dari Sistem</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- 4. SLEEK MOBILE BOTTOM NAVIGATION BAR (Thumb-friendly for Motoris in field) --}}
    <div class="fixed bottom-0 inset-x-0 z-40 flex h-16 items-center justify-around border-t border-slate-200 bg-white/95 px-2 backdrop-blur-md shadow-lg lg:hidden">
        {{-- 1. Beranda --}}
        <a href="{{ route('dashboard') }}" 
           class="flex flex-1 flex-col items-center justify-center py-1 text-center transition active:scale-90 {{ request()->routeIs('dashboard') ? 'text-blue-600 font-bold' : 'text-slate-500 hover:text-slate-900' }}">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span class="mt-0.5 text-[10px] tracking-tight">Beranda</span>
            @if(request()->routeIs('dashboard'))
                <span class="mt-0.5 h-1 w-1 rounded-full bg-blue-600"></span>
            @endif
        </a>

        {{-- 2. Peta & Navigasi --}}
        <a href="{{ route('visits.index') }}" 
           class="flex flex-1 flex-col items-center justify-center py-1 text-center transition active:scale-90 {{ (request()->routeIs('visits.*') || request()->routeIs('tracking.*')) ? 'text-blue-600 font-bold' : 'text-slate-500 hover:text-slate-900' }}">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
            </svg>
            <span class="mt-0.5 text-[10px] tracking-tight">Rute Peta</span>
            @if(request()->routeIs('visits.*') || request()->routeIs('tracking.*'))
                <span class="mt-0.5 h-1 w-1 rounded-full bg-blue-600"></span>
            @endif
        </a>

        {{-- 3. Tugas Kunjungan (atau Outlet jika non-kunjungan) --}}
        @can('kelola kunjungan')
        <a href="{{ route('visit-tasks.index') }}" 
           class="flex flex-1 flex-col items-center justify-center py-1 text-center transition active:scale-90 {{ request()->routeIs('visit-tasks.*') ? 'text-blue-600 font-bold' : 'text-slate-500 hover:text-slate-900' }}">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
            </svg>
            <span class="mt-0.5 text-[10px] tracking-tight">Tugas</span>
            @if(request()->routeIs('visit-tasks.*'))
                <span class="mt-0.5 h-1 w-1 rounded-full bg-blue-600"></span>
            @endif
        </a>
        @else
        <a href="{{ route('customers.index') }}" 
           class="flex flex-1 flex-col items-center justify-center py-1 text-center transition active:scale-90 {{ request()->routeIs('customers.*') ? 'text-blue-600 font-bold' : 'text-slate-500 hover:text-slate-900' }}">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            <span class="mt-0.5 text-[10px] tracking-tight">Outlet</span>
            @if(request()->routeIs('customers.*'))
                <span class="mt-0.5 h-1 w-1 rounded-full bg-blue-600"></span>
            @endif
        </a>
        @endcan

        {{-- 4. Produk / Katalog --}}
        <a href="{{ route('products.index') }}" 
           class="flex flex-1 flex-col items-center justify-center py-1 text-center transition active:scale-90 {{ request()->routeIs('products.*') ? 'text-blue-600 font-bold' : 'text-slate-500 hover:text-slate-900' }}">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
            <span class="mt-0.5 text-[10px] tracking-tight">Produk</span>
            @if(request()->routeIs('products.*'))
                <span class="mt-0.5 h-1 w-1 rounded-full bg-blue-600"></span>
            @endif
        </a>

        {{-- 5. Tombol Menu Drawer --}}
        <button type="button" @click="mobileNavOpen = true" 
                class="flex flex-1 flex-col items-center justify-center py-1 text-center text-slate-500 hover:text-slate-900 transition active:scale-90"
                aria-label="Semua Menu">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
            </svg>
            <span class="mt-0.5 text-[10px] tracking-tight">Menu</span>
        </button>
    </div>
</nav>
