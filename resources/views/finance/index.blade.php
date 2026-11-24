<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-blue-600">Finansial & Penagihan</p>
                <h2 class="mt-1 text-2xl font-bold text-slate-900">Laporan Keuangan & Piutang Kios</h2>
                <p class="mt-0.5 text-xs text-slate-500">Monitoring omzet penjualan produk, pelunasan tempo kios, pembayaran DP, dan penagihan piutang pelanggan.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2.5">
                <a href="{{ route('finance.export.pdf', request()->query()) }}" 
                   target="_blank"
                   class="inline-flex items-center gap-2 rounded-xl border border-rose-200 bg-rose-50 px-3.5 py-2 text-xs font-semibold text-rose-700 shadow-2xs hover:bg-rose-100 hover:border-rose-300 transition active:scale-95">
                    <svg class="h-4 w-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span>Cetak PDF / Laporan</span>
                </a>
                @can('kelola pesanan')
                <a href="{{ route('orders.create') }}" 
                   class="inline-flex items-center gap-2 rounded-xl bg-slate-950 px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-slate-800 transition active:scale-95">
                    <svg class="h-4 w-4 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Transaksi Baru</span>
                </a>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="min-h-[calc(100vh-65px)] bg-slate-100 py-6" 
         x-data="{
             payModalOpen: false,
             historyModalOpen: false,
             detailModalOpen: false,
             activeOrder: null,
             paymentList: [],
             loadingHistory: false,
             payForm: {
                 actionUrl: '',
                 amount: '',
                 payment_date: '{{ now()->format('Y-m-d') }}',
                 payment_method: 'tunai',
                 notes: ''
             },
             openPayModal(order) {
                 this.activeOrder = order;
                 this.payForm.actionUrl = '/finance/orders/' + order.id + '/payments';
                 this.payForm.amount = order.remaining_amount;
                 this.payForm.payment_date = '{{ now()->format('Y-m-d') }}';
                 this.payForm.payment_method = 'tunai';
                 this.payForm.notes = '';
                 this.payModalOpen = true;
             },
             setQuickAmount(percent) {
                 if (!this.activeOrder) return;
                 let val = Math.round((this.activeOrder.remaining_amount * percent) / 100);
                 this.payForm.amount = val;
             },
             openHistoryModal(order) {
                 this.activeOrder = order;
                 this.historyModalOpen = true;
                 this.loadingHistory = true;
                 this.paymentList = [];
                 fetch('/finance/orders/' + order.id + '/payments')
                     .then(res => res.json())
                     .then(data => {
                         this.paymentList = data.payments;
                         this.loadingHistory = false;
                     })
                     .catch(err => {
                         this.loadingHistory = false;
                     });
             },
             openDetailModal(order) {
                 this.activeOrder = order;
                 this.detailModalOpen = true;
             },
             formatRupiah(num) {
                 return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(num || 0);
             }
         }">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">

            {{-- Flash Alert Notification --}}
            @if(session('status'))
                <div class="flex items-center justify-between rounded-2xl border border-emerald-200 bg-emerald-50/90 p-4 shadow-xs backdrop-blur-xs">
                    <div class="flex items-center gap-3">
                        <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-500 text-white shadow-2xs">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold uppercase tracking-wider text-emerald-900">Pembayaran Sukses Tercatat</h4>
                            <p class="text-xs text-emerald-800">{{ session('status') }}</p>
                        </div>
                    </div>
                    <button type="button" onclick="this.closest('div').remove()" class="rounded-lg p-1.5 text-emerald-600 hover:bg-emerald-100 transition">&times;</button>
                </div>
            @endif

            @if(isset($errors) && $errors->any())
                <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4 shadow-xs">
                    <div class="flex items-center gap-2 font-bold text-xs text-rose-800">
                        <svg class="h-4 w-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span>Gagal Memproses Pembayaran:</span>
                    </div>
                    <ul class="mt-2 list-inside list-disc text-xs text-rose-700 space-y-1">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- 1. KPI STATS CARDS --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                {{-- Card 1: Total Omzet --}}
                <div class="relative overflow-hidden rounded-2xl border border-slate-200/90 bg-white p-5 shadow-xs">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Nilai Penjualan</p>
                            <h3 class="mt-1.5 text-2xl font-black tracking-tight text-slate-900">
                                Rp {{ number_format($stats['total_omzet'], 0, ',', '.') }}
                            </h3>
                            <p class="mt-1 text-[11px] font-medium text-slate-500">
                                Dari {{ $stats['total_orders'] }} transaksi pesanan
                            </p>
                        </div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-50 text-blue-600 border border-amber-200/60 shadow-2xs">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-3 h-1 w-full rounded-full bg-slate-100">
                        <div class="h-1 rounded-full bg-amber-500" style="width: 100%"></div>
                    </div>
                </div>

                {{-- Card 2: Total Kas Masuk --}}
                <div class="relative overflow-hidden rounded-2xl border border-emerald-200/80 bg-white p-5 shadow-xs">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-wider text-emerald-600">Total Kas Masuk</p>
                            <h3 class="mt-1.5 text-2xl font-black tracking-tight text-emerald-700">
                                Rp {{ number_format($stats['total_kas_masuk'], 0, ',', '.') }}
                            </h3>
                            <p class="mt-1 text-[11px] font-medium text-emerald-600/80">
                                Tunai, DP & pelunasan cicilan
                            </p>
                        </div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600 border border-emerald-200 shadow-2xs">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-3 h-1 w-full rounded-full bg-emerald-100">
                        @php
                            $paidPercent = $stats['total_omzet'] > 0 ? min(100, round(($stats['total_kas_masuk'] / $stats['total_omzet']) * 100)) : 0;
                        @endphp
                        <div class="h-1 rounded-full bg-emerald-500" style="width: {{ $paidPercent }}%"></div>
                    </div>
                </div>

                {{-- Card 3: Total Piutang Berjalan --}}
                <div class="relative overflow-hidden rounded-2xl border border-sky-200/80 bg-white p-5 shadow-xs">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-wider text-sky-600">Sisa Piutang Berjalan</p>
                            <h3 class="mt-1.5 text-2xl font-black tracking-tight text-sky-700">
                                Rp {{ number_format($stats['total_piutang'], 0, ',', '.') }}
                            </h3>
                            <p class="mt-1 text-[11px] font-medium text-sky-600/80">
                                Tersebar di {{ $stats['count_kios_piutang'] }} kios pelanggan
                            </p>
                        </div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-sky-50 text-sky-600 border border-sky-200 shadow-2xs">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-3 h-1 w-full rounded-full bg-sky-100">
                        @php
                            $remainPercent = $stats['total_omzet'] > 0 ? min(100, round(($stats['total_piutang'] / $stats['total_omzet']) * 100)) : 0;
                        @endphp
                        <div class="h-1 rounded-full bg-sky-500" style="width: {{ $remainPercent }}%"></div>
                    </div>
                </div>

                {{-- Card 4: Piutang Jatuh Tempo (Overdue Alert) --}}
                <div class="relative overflow-hidden rounded-2xl border {{ $stats['total_jatuh_tempo'] > 0 ? 'border-rose-300 bg-rose-50/40 ring-2 ring-rose-200' : 'border-slate-200 bg-white' }} p-5 shadow-xs">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="flex items-center gap-1.5">
                                <p class="text-[11px] font-bold uppercase tracking-wider {{ $stats['total_jatuh_tempo'] > 0 ? 'text-rose-600' : 'text-slate-400' }}">Jatuh Tempo (Overdue)</p>
                                @if($stats['count_jatuh_tempo'] > 0)
                                    <span class="flex h-2 w-2 rounded-full bg-rose-500 animate-ping"></span>
                                @endif
                            </div>
                            <h3 class="mt-1.5 text-2xl font-black tracking-tight {{ $stats['total_jatuh_tempo'] > 0 ? 'text-rose-700' : 'text-slate-900' }}">
                                Rp {{ number_format($stats['total_jatuh_tempo'], 0, ',', '.') }}
                            </h3>
                            <p class="mt-1 text-[11px] font-medium {{ $stats['total_jatuh_tempo'] > 0 ? 'text-rose-600' : 'text-slate-500' }}">
                                {{ $stats['count_jatuh_tempo'] }} tagihan melewati tempo
                            </p>
                        </div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl {{ $stats['total_jatuh_tempo'] > 0 ? 'bg-rose-100 text-rose-700 border border-rose-300' : 'bg-slate-100 text-slate-500 border border-slate-200' }} shadow-2xs">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-3 h-1 w-full rounded-full bg-rose-100">
                        <div class="h-1 rounded-full bg-rose-500" style="width: {{ $stats['total_jatuh_tempo'] > 0 ? '100' : '0' }}%"></div>
                    </div>
                </div>
            </div>

            {{-- 2. FILTER & SEARCH SECTION --}}
            <div class="rounded-2xl border border-slate-200/90 bg-white p-5 shadow-xs">
                <form method="GET" action="{{ route('finance.index') }}" class="space-y-4">
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-5">
                        {{-- Search Text --}}
                        <div class="lg:col-span-1">
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Cari Order / Kios</label>
                            <div class="relative">
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="No. Order, Kios, HP..." 
                                       class="w-full rounded-xl border-slate-200 bg-slate-50/70 pl-8 pr-3 py-2 text-xs focus:border-amber-400 focus:bg-white focus:ring-amber-400">
                                <svg class="absolute left-2.5 top-2.5 h-3.5 w-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                        </div>

                        {{-- Filter Kios Pelanggan --}}
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Kios / Outlet</label>
                            <select name="customer_id" class="w-full rounded-xl border-slate-200 bg-slate-50/70 py-2 text-xs focus:border-amber-400 focus:bg-white focus:ring-amber-400">
                                <option value="">Semua Kios Pelanggan</option>
                                @foreach($customers as $c)
                                    <option value="{{ $c->id }}" {{ request('customer_id') == $c->id ? 'selected' : '' }}>
                                        {{ $c->name }} ({{ $c->code ?: 'OUT' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Filter Status Pembayaran --}}
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Status Pembayaran</label>
                            <select name="payment_status" class="w-full rounded-xl border-slate-200 bg-slate-50/70 py-2 text-xs focus:border-amber-400 focus:bg-white focus:ring-amber-400">
                                <option value="">Semua Status</option>
                                <option value="lunas" {{ request('payment_status') == 'lunas' ? 'selected' : '' }}>✅ Lunas (100% Selesai)</option>
                                <option value="sebagian" {{ request('payment_status') == 'sebagian' ? 'selected' : '' }}>⏳ DP / Bayar Sebagian</option>
                                <option value="belum_lunas" {{ request('payment_status') == 'belum_lunas' ? 'selected' : '' }}>💳 Tempo Murni (Belum Bayar)</option>
                                <option value="jatuh_tempo" {{ request('payment_status') == 'jatuh_tempo' ? 'selected' : '' }}>⚠️ Jatuh Tempo (Overdue)</option>
                                <option value="piutang" {{ request('payment_status') == 'piutang' ? 'selected' : '' }}>🔴 Semua Yang Ada Piutang</option>
                            </select>
                        </div>

                        {{-- Filter Metode Pembayaran --}}
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Metode Pembayaran</label>
                            <select name="payment_method" class="w-full rounded-xl border-slate-200 bg-slate-50/70 py-2 text-xs focus:border-amber-400 focus:bg-white focus:ring-amber-400">
                                <option value="">Semua Metode</option>
                                <option value="tunai" {{ request('payment_method') == 'tunai' ? 'selected' : '' }}>💵 Tunai (Cash On Delivery)</option>
                                <option value="dp" {{ request('payment_method') == 'dp' ? 'selected' : '' }}>💸 DP (Uang Muka & Sisa)</option>
                                <option value="tempo" {{ request('payment_method') == 'tempo' ? 'selected' : '' }}>⏱️ Tempo (Termin / Kredit)</option>
                                <option value="transfer" {{ request('payment_method') == 'transfer' ? 'selected' : '' }}>🏦 Transfer Bank</option>
                            </select>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex items-end gap-2">
                            <button type="submit" class="flex-1 inline-flex items-center justify-center gap-1.5 rounded-xl bg-amber-400 px-3.5 py-2 text-xs font-bold text-slate-950 shadow-xs hover:bg-amber-300 transition active:scale-95">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                                <span>Filter</span>
                            </button>
                            <a href="{{ route('finance.index') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 transition active:scale-95" title="Reset filter">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            {{-- 3. FINANCIAL RECEIVABLES TABLE --}}
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xs">
                <div class="border-b border-slate-100 bg-slate-50/60 px-6 py-4 flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-2">
                        <span class="flex h-2.5 w-2.5 rounded-full bg-amber-400"></span>
                        <h3 class="text-sm font-bold text-slate-800">Daftar Tagihan & Piutang Produk Kios Pelanggan</h3>
                        <span class="rounded-full bg-slate-200 px-2.5 py-0.5 text-[11px] font-bold text-slate-700">
                            {{ $orders->total() }} Data
                        </span>
                    </div>
                    <div class="text-xs text-slate-500">
                        Klik <span class="font-bold text-blue-600">Catat Bayar</span> untuk memasukkan setoran / cicilan kios.
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-xs">
                        <thead class="bg-slate-50/80 uppercase tracking-wider text-slate-500 border-b border-slate-200/80 text-[10px]">
                            <tr>
                                <th class="px-5 py-3.5 font-bold">Faktur & Tanggal</th>
                                <th class="px-5 py-3.5 font-bold">Kios Pelanggan</th>
                                <th class="px-5 py-3.5 font-bold">Metode & Status</th>
                                <th class="px-5 py-3.5 font-bold text-right">Total Transaksi</th>
                                <th class="px-5 py-3.5 font-bold text-right">DP / Terbayar</th>
                                <th class="px-5 py-3.5 font-bold text-right">Sisa Piutang</th>
                                <th class="px-5 py-3.5 font-bold text-center">Jatuh Tempo</th>
                                <th class="px-5 py-3.5 font-bold text-center">Aksi Finansial</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($orders as $order)
                                @php
                                    $isOverdue = $order->is_overdue;
                                    $hasRemaining = $order->remaining_amount > 0;
                                @endphp
                                <tr class="hover:bg-slate-50/80 transition {{ $isOverdue ? 'bg-rose-50/30' : '' }}">
                                    {{-- 1. Faktur & Tanggal --}}
                                    <td class="whitespace-nowrap px-5 py-3.5">
                                        <div class="font-mono font-bold text-slate-900 flex items-center gap-1.5">
                                            <span>{{ $order->order_number }}</span>
                                        </div>
                                        <div class="text-[11px] text-slate-400 mt-0.5 flex items-center gap-1">
                                            <svg class="h-3 w-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            <span>{{ $order->order_date->format('d M Y') }}</span>
                                        </div>
                                        <div class="text-[10px] text-slate-400 mt-0.5">
                                            Sales: <span class="font-semibold text-slate-600">{{ $order->user->name }}</span>
                                        </div>
                                    </td>

                                    {{-- 2. Kios Pelanggan with photo thumbnail --}}
                                    <td class="px-5 py-3.5">
                                        <div class="flex items-center gap-3">
                                            <div class="h-9 w-9 shrink-0 overflow-hidden rounded-xl border border-slate-200 bg-slate-100 shadow-2xs">
                                                @if($order->customer?->photo_url)
                                                    <img src="{{ $order->customer->photo_url }}" alt="{{ $order->customer->name }}" class="h-full w-full object-cover">
                                                @else
                                                    <div class="flex h-full w-full items-center justify-center font-bold text-[10px] text-blue-600 bg-amber-50">
                                                        {{ strtoupper(substr($order->customer?->name ?? 'KI', 0, 2)) }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="min-w-0">
                                                <div class="font-bold text-slate-900 truncate">
                                                    {{ $order->customer?->name ?? '-' }}
                                                </div>
                                                <div class="text-[10px] text-slate-400 flex items-center gap-1 mt-0.5">
                                                    <span>{{ $order->customer?->code ?? '-' }}</span>
                                                    @if($order->customer?->phone)
                                                        <span>•</span>
                                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $order->customer->phone) }}" target="_blank" class="text-emerald-600 hover:underline inline-flex items-center gap-0.5 font-medium">
                                                            <span>{{ $order->customer->phone }}</span>
                                                        </a>
                                                    @endif
                                                </div>
                                                @if($order->customer?->address)
                                                    <div class="text-[10px] text-slate-400 truncate max-w-xs mt-0.5" title="{{ $order->customer->address }}">
                                                        {{ $order->customer->address }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>

                                    {{-- 3. Metode & Status Bayar --}}
                                    <td class="whitespace-nowrap px-5 py-3.5">
                                        <div class="flex flex-col gap-1 items-start">
                                            {{-- Payment Method Badge --}}
                                            @if($order->payment_method === 'dp')
                                                <span class="inline-flex items-center gap-1 rounded-md bg-purple-50 px-2 py-0.5 text-[10px] font-bold text-purple-700 border border-purple-200">
                                                    <span>DP + Tempo</span>
                                                </span>
                                            @elseif($order->payment_method === 'tempo')
                                                <span class="inline-flex items-center gap-1 rounded-md bg-amber-50 px-2 py-0.5 text-[10px] font-bold text-amber-700 border border-amber-200">
                                                    <span>Tempo (Termin)</span>
                                                </span>
                                            @elseif($order->payment_method === 'transfer')
                                                <span class="inline-flex items-center gap-1 rounded-md bg-sky-50 px-2 py-0.5 text-[10px] font-bold text-sky-700 border border-sky-200">
                                                    <span>Transfer Bank</span>
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 rounded-md bg-slate-100 px-2 py-0.5 text-[10px] font-bold text-slate-700 border border-slate-200">
                                                    <span>Tunai Langsung</span>
                                                </span>
                                            @endif

                                            {{-- Status Status Badge --}}
                                            @if($isOverdue)
                                                <span class="inline-flex items-center gap-1 rounded-full bg-rose-100 px-2.5 py-0.5 text-[10px] font-bold text-rose-800 border border-rose-300">
                                                    <span class="h-1.5 w-1.5 rounded-full bg-rose-600 animate-pulse"></span>
                                                    <span>Jatuh Tempo (Telat {{ $order->days_overdue }} hari)</span>
                                                </span>
                                            @elseif(!$hasRemaining)
                                                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-0.5 text-[10px] font-bold text-emerald-700 border border-emerald-200">
                                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                                    <span>Lunas</span>
                                                </span>
                                            @elseif($order->paid_amount > 0)
                                                <span class="inline-flex items-center gap-1 rounded-full bg-sky-50 px-2.5 py-0.5 text-[10px] font-bold text-sky-700 border border-sky-200">
                                                    <span class="h-1.5 w-1.5 rounded-full bg-sky-500"></span>
                                                    <span>Sebagian (DP Dibayar)</span>
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2.5 py-0.5 text-[10px] font-bold text-amber-700 border border-amber-300">
                                                    <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                                                    <span>Belum Lunas</span>
                                                </span>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- 4. Total Transaksi --}}
                                    <td class="whitespace-nowrap px-5 py-3.5 text-right font-mono font-bold text-slate-900">
                                        Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                        <div class="text-[10px] font-sans font-normal text-slate-400">
                                            {{ $order->items->count() }} jenis produk
                                        </div>
                                    </td>

                                    {{-- 5. DP / Sudah Dibayar --}}
                                    <td class="whitespace-nowrap px-5 py-3.5 text-right font-mono text-emerald-700 font-bold">
                                        Rp {{ number_format($order->paid_amount, 0, ',', '.') }}
                                        @if($order->down_payment > 0)
                                            <div class="text-[10px] font-sans font-medium text-slate-400">
                                                (DP: Rp {{ number_format($order->down_payment, 0, ',', '.') }})
                                            </div>
                                        @endif
                                    </td>

                                    {{-- 6. Sisa Piutang --}}
                                    <td class="whitespace-nowrap px-5 py-3.5 text-right font-mono font-black text-sm {{ $hasRemaining ? ($isOverdue ? 'text-rose-600' : 'text-blue-600') : 'text-slate-400' }}">
                                        Rp {{ number_format($order->remaining_amount, 0, ',', '.') }}
                                        @if($hasRemaining)
                                            <div class="text-[10px] font-sans font-bold {{ $isOverdue ? 'text-rose-500' : 'text-amber-500' }}">
                                                {{ round(($order->remaining_amount / max(1, $order->total_amount)) * 100) }}% Belum bayar
                                            </div>
                                        @else
                                            <div class="text-[10px] font-sans font-semibold text-emerald-600">
                                                Tuntas
                                            </div>
                                        @endif
                                    </td>

                                    {{-- 7. Jatuh Tempo & Waktu --}}
                                    <td class="whitespace-nowrap px-5 py-3.5 text-center">
                                        @if($order->due_date)
                                            <div class="font-mono text-xs {{ $isOverdue ? 'font-bold text-rose-600' : 'text-slate-700' }}">
                                                {{ $order->due_date->format('d/m/Y') }}
                                            </div>
                                            @if($hasRemaining)
                                                @if($isOverdue)
                                                    <span class="mt-0.5 inline-block rounded bg-rose-100 px-1.5 py-0.5 text-[9px] font-bold text-rose-800">
                                                        Lewat {{ $order->days_overdue }} hari!
                                                    </span>
                                                @else
                                                    <span class="mt-0.5 inline-block rounded bg-slate-100 px-1.5 py-0.5 text-[9px] font-medium text-slate-600">
                                                        Sisa {{ $order->days_remaining }} hari
                                                    </span>
                                                @endif
                                            @endif
                                        @else
                                            <span class="text-[11px] text-slate-400">-</span>
                                        @endif
                                    </td>

                                    {{-- 8. Aksi Finansial --}}
                                    <td class="whitespace-nowrap px-5 py-3.5 text-center">
                                        <div class="flex items-center justify-center gap-1.5">
                                            {{-- Button Catat Bayar (jika masih ada piutang) --}}
                                            @if($hasRemaining)
                                                <button type="button" 
                                                        @click="openPayModal({{ json_encode($order) }})"
                                                        title="Catat Pembayaran / Pelunasan Piutang"
                                                        class="inline-flex items-center gap-1 rounded-lg bg-emerald-600 px-2.5 py-1.5 text-[11px] font-bold text-white shadow-2xs hover:bg-emerald-500 transition active:scale-95">
                                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                                    <span>Bayar</span>
                                                </button>
                                            @endif

                                            {{-- Button Riwayat Cicilan --}}
                                            <button type="button" 
                                                    @click="openHistoryModal({{ json_encode($order) }})"
                                                    title="Lihat Riwayat Pembayaran & Cicilan"
                                                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 bg-slate-50 text-slate-600 shadow-2xs hover:border-slate-300 hover:bg-slate-100 transition active:scale-95">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            </button>

                                            {{-- Button Detail Produk Order --}}
                                            <button type="button" 
                                                    @click="openDetailModal({{ json_encode($order) }})"
                                                    title="Rincian Produk Pesanan"
                                                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-blue-200 bg-blue-50 text-blue-600 shadow-2xs hover:bg-blue-100 transition active:scale-95">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center text-slate-400">
                                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-400 mb-3">
                                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        </div>
                                        <p class="font-semibold text-slate-700">Tidak ada transaksi yang cocok dengan filter</p>
                                        <p class="text-xs text-slate-400 mt-1">Silakan atur kembali kata kunci atau status pencarian Anda.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination Links --}}
                @if($orders->hasPages())
                    <div class="border-t border-slate-100 bg-white px-6 py-4">
                        {{ $orders->links() }}
                    </div>
                @endif
            </div>
        </div>

        {{-- ======================================================== --}}
        {{-- MODAL 1: CATAT PEMBAYARAN / PELUNASAN PIUTANG            --}}
        {{-- ======================================================== --}}
        <div x-show="payModalOpen" 
             style="display: none;"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs">
            <div @click.outside="payModalOpen = false"
                 class="w-full max-w-lg overflow-hidden rounded-3xl bg-white shadow-2xl border border-slate-200">
                {{-- Modal Header --}}
                <div class="flex items-center justify-between border-b border-slate-100 bg-slate-50/70 px-6 py-4">
                    <div class="flex items-center gap-2.5">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-200 shadow-2xs">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-900 text-sm">Catat Pembayaran Tagihan Kios</h3>
                            <p class="text-[11px] text-slate-500 font-mono" x-text="activeOrder?.order_number"></p>
                        </div>
                    </div>
                    <button type="button" @click="payModalOpen = false" class="rounded-xl p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Modal Form --}}
                <form :action="payForm.actionUrl" method="POST" class="p-6 space-y-4">
                    @csrf
                    {{-- Summary Card --}}
                    <div class="rounded-2xl bg-slate-50 p-4 border border-slate-200/80 space-y-2 text-xs">
                        <div class="flex justify-between">
                            <span class="text-slate-500">Kios Pelanggan:</span>
                            <span class="font-bold text-slate-900" x-text="activeOrder?.customer?.name"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Total Transaksi:</span>
                            <span class="font-mono font-bold text-slate-900" x-text="formatRupiah(activeOrder?.total_amount)"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Sudah Terbayar:</span>
                            <span class="font-mono font-bold text-emerald-600" x-text="formatRupiah(activeOrder?.paid_amount)"></span>
                        </div>
                        <div class="border-t border-slate-200 pt-2 flex justify-between font-bold">
                            <span class="text-slate-700">Sisa Piutang Berjalan:</span>
                            <span class="font-mono text-rose-600 font-black text-sm" x-text="formatRupiah(activeOrder?.remaining_amount)"></span>
                        </div>
                    </div>

                    {{-- Form Fields --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Tanggal Pembayaran</label>
                            <input type="date" name="payment_date" x-model="payForm.payment_date" required 
                                   class="w-full rounded-xl border-slate-200 text-xs focus:border-amber-400 focus:ring-amber-400">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Metode Setoran</label>
                            <select name="payment_method" x-model="payForm.payment_method" required 
                                    class="w-full rounded-xl border-slate-200 text-xs focus:border-amber-400 focus:ring-amber-400">
                                <option value="tunai">💵 Tunai / Kas Sales</option>
                                <option value="transfer">🏦 Transfer Rekening</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600">Nominal Pembayaran (Rp)</label>
                            <div class="flex gap-1.5">
                                <button type="button" @click="setQuickAmount(100)" class="rounded-md bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-800 hover:bg-emerald-200 transition">
                                    Lunasi 100%
                                </button>
                                <button type="button" @click="setQuickAmount(50)" class="rounded-md bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-700 hover:bg-slate-200 transition">
                                    50%
                                </button>
                            </div>
                        </div>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 font-bold text-xs text-slate-400">Rp</span>
                            <input type="number" name="amount" x-model="payForm.amount" min="100" :max="activeOrder?.remaining_amount" required 
                                   class="w-full rounded-xl border-slate-200 pl-10 pr-3 py-2 text-sm font-mono font-bold focus:border-emerald-500 focus:ring-emerald-500">
                        </div>
                        <p class="mt-1 text-[10px] text-slate-400">
                            Maksimal input: <span class="font-mono font-semibold text-slate-600" x-text="formatRupiah(activeOrder?.remaining_amount)"></span>
                        </p>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1">Catatan / Keterangan (Opsional)</label>
                        <textarea name="notes" x-model="payForm.notes" rows="2" placeholder="Contoh: Titipan pelunasan via kunjungan rutin, transfer BCA slip..." 
                                  class="w-full rounded-xl border-slate-200 text-xs focus:border-amber-400 focus:ring-amber-400"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2.5 pt-2">
                        <button type="button" @click="payModalOpen = false" class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                            Batal
                        </button>
                        <button type="submit" class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-600 px-5 py-2 text-xs font-bold text-white shadow-sm hover:bg-emerald-500 transition active:scale-95">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Simpan Pembayaran</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ======================================================== --}}
        {{-- MODAL 2: RIWAYAT PEMBAYARAN & CICILAN                    --}}
        {{-- ======================================================== --}}
        <div x-show="historyModalOpen" 
             style="display: none;"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs">
            <div @click.outside="historyModalOpen = false"
                 class="w-full max-w-xl overflow-hidden rounded-3xl bg-white shadow-2xl border border-slate-200">
                <div class="flex items-center justify-between border-b border-slate-100 bg-slate-50/70 px-6 py-4">
                    <div class="flex items-center gap-2.5">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 text-blue-600 border border-amber-200 shadow-2xs">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-900 text-sm">Riwayat Pembayaran & Cicilan Tagihan</h3>
                            <p class="text-[11px] text-slate-500 font-mono" x-text="activeOrder?.order_number + ' - ' + activeOrder?.customer?.name"></p>
                        </div>
                    </div>
                    <button type="button" @click="historyModalOpen = false" class="rounded-xl p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="p-6 space-y-4">
                    {{-- Status Balance Bar --}}
                    <div class="grid grid-cols-3 gap-2 rounded-2xl bg-slate-50 p-3 text-center border border-slate-200/80 text-xs">
                        <div>
                            <span class="block text-[10px] uppercase font-bold text-slate-400">Total Faktur</span>
                            <span class="font-mono font-bold text-slate-800" x-text="formatRupiah(activeOrder?.total_amount)"></span>
                        </div>
                        <div>
                            <span class="block text-[10px] uppercase font-bold text-emerald-600">Terbayar</span>
                            <span class="font-mono font-bold text-emerald-700" x-text="formatRupiah(activeOrder?.paid_amount)"></span>
                        </div>
                        <div>
                            <span class="block text-[10px] uppercase font-bold text-rose-500">Sisa Piutang</span>
                            <span class="font-mono font-bold text-rose-600" x-text="formatRupiah(activeOrder?.remaining_amount)"></span>
                        </div>
                    </div>

                    {{-- DP Info if any --}}
                    <template x-if="activeOrder?.down_payment > 0">
                        <div class="rounded-xl bg-purple-50 p-3 text-xs border border-purple-200/80 flex items-center justify-between">
                            <div>
                                <span class="font-bold text-purple-900">Uang Muka (Down Payment)</span>
                                <p class="text-[11px] text-purple-700">Dibayarkan saat pembuatan pesanan</p>
                            </div>
                            <span class="font-mono font-bold text-purple-900" x-text="formatRupiah(activeOrder?.down_payment)"></span>
                        </div>
                    </template>

                    {{-- Payment List --}}
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Riwayat Setoran / Cicilan</h4>
                        <template x-if="loadingHistory">
                            <div class="py-8 text-center text-xs text-slate-400">
                                <svg class="inline h-5 w-5 animate-spin text-amber-500 mb-1" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                                <p>Memuat riwayat...</p>
                            </div>
                        </template>

                        <template x-if="!loadingHistory && paymentList.length === 0">
                            <div class="rounded-xl border border-dashed border-slate-200 p-6 text-center text-xs text-slate-400">
                                Belum ada riwayat cicilan tambahan yang dicatat untuk tagihan ini.
                            </div>
                        </template>

                        <div class="space-y-2 max-h-60 overflow-y-auto" x-show="!loadingHistory && paymentList.length > 0">
                            <template x-for="(pay, index) in paymentList" :key="pay.id">
                                <div class="flex items-center justify-between rounded-xl border border-slate-200/80 bg-white p-3 text-xs shadow-2xs hover:bg-slate-50 transition">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600 font-bold text-[10px]">
                                            #<span x-text="index + 1"></span>
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <span class="font-mono font-bold text-slate-800" x-text="pay.payment_number"></span>
                                                <span class="rounded bg-slate-100 px-1.5 py-0.5 text-[9px] font-semibold uppercase text-slate-600" x-text="pay.payment_method"></span>
                                            </div>
                                            <div class="text-[10px] text-slate-400 mt-0.5">
                                                <span x-text="pay.payment_date"></span>
                                                <template x-if="pay.received_by?.name">
                                                    <span> • Diterima: <strong x-text="pay.received_by.name"></strong></span>
                                                </template>
                                            </div>
                                            <template x-if="pay.notes">
                                                <p class="text-[11px] text-slate-600 italic mt-0.5" x-text="pay.notes"></p>
                                            </template>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="font-mono font-bold text-emerald-700 text-sm" x-text="formatRupiah(pay.amount)"></span>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="pt-2 text-right">
                        <button type="button" @click="historyModalOpen = false" class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100 transition">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ======================================================== --}}
        {{-- MODAL 3: DETAIL PRODUK PESANAN                           --}}
        {{-- ======================================================== --}}
        <div x-show="detailModalOpen" 
             style="display: none;"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs">
            <div @click.outside="detailModalOpen = false"
                 class="w-full max-w-xl overflow-hidden rounded-3xl bg-white shadow-2xl border border-slate-200">
                <div class="flex items-center justify-between border-b border-slate-100 bg-slate-50/70 px-6 py-4">
                    <div class="flex items-center gap-2.5">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-600 border border-blue-200 shadow-2xs">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-900 text-sm">Rincian Pembelian Produk Kios</h3>
                            <p class="text-[11px] text-slate-500 font-mono" x-text="activeOrder?.order_number"></p>
                        </div>
                    </div>
                    <button type="button" @click="detailModalOpen = false" class="rounded-xl p-1 text-slate-400 hover:bg-slate-100 hover:text-slate-700 transition">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="p-6 space-y-4">
                    <div class="rounded-2xl bg-slate-50 p-4 border border-slate-200/80 grid grid-cols-2 gap-2 text-xs">
                        <div>
                            <span class="text-slate-400 block text-[10px] uppercase font-bold">Kios Pelanggan:</span>
                            <span class="font-bold text-slate-900" x-text="activeOrder?.customer?.name"></span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[10px] uppercase font-bold">Sales Motoris:</span>
                            <span class="font-bold text-slate-900" x-text="activeOrder?.user?.name"></span>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Item Produk</h4>
                        <div class="overflow-x-auto rounded-xl border border-slate-200">
                            <table class="min-w-full text-xs text-left">
                                <thead class="bg-slate-50 text-[10px] font-bold text-slate-500 uppercase border-b border-slate-200">
                                    <tr>
                                        <th class="px-3 py-2.5">Produk</th>
                                        <th class="px-3 py-2.5 text-center">Qty</th>
                                        <th class="px-3 py-2.5 text-right">Harga</th>
                                        <th class="px-3 py-2.5 text-right">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <template x-for="item in activeOrder?.items" :key="item.id">
                                        <tr>
                                            <td class="px-3 py-2.5 font-semibold text-slate-800" x-text="item.product ? item.product.name : 'Produk #' + item.product_id"></td>
                                            <td class="px-3 py-2.5 text-center font-mono" x-text="item.quantity"></td>
                                            <td class="px-3 py-2.5 text-right font-mono" x-text="formatRupiah(item.unit_price)"></td>
                                            <td class="px-3 py-2.5 text-right font-mono font-bold text-slate-900" x-text="formatRupiah(item.subtotal)"></td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="flex items-center justify-between border-t border-slate-200 pt-3 text-xs font-bold">
                        <span class="text-slate-700">Total Transaksi:</span>
                        <span class="font-mono text-base text-slate-900" x-text="formatRupiah(activeOrder?.total_amount)"></span>
                    </div>

                    <div class="pt-2 text-right">
                        <button type="button" @click="detailModalOpen = false" class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100 transition">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
