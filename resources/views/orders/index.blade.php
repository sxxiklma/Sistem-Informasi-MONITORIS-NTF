<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-blue-600">Penjualan Lapangan</p>
                <h2 class="mt-1 text-2xl font-semibold text-slate-900">Pesanan Penjualan (Sales Orders)</h2>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('finance.index') }}" class="inline-flex items-center gap-1.5 rounded-lg border border-amber-300 bg-amber-50 px-3.5 py-2 text-xs font-semibold text-amber-800 shadow-2xs hover:bg-amber-100 transition">
                    <svg class="h-4 w-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Keuangan & Piutang</span>
                </a>
                <a href="{{ route('reports.sales-orders') }}" class="inline-flex items-center gap-1.5 rounded-lg border border-emerald-300 bg-emerald-50 px-3.5 py-2 text-xs font-semibold text-emerald-800 shadow-2xs hover:bg-emerald-100 transition">
                    <svg class="h-4 w-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Ekspor Excel</span>
                </a>
                <a href="{{ route('orders.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-slate-950 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800">
                    <svg class="h-4 w-4 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Input Pesanan Baru</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="min-h-[calc(100vh-65px)] bg-slate-100 py-8">
        <div class="mx-auto max-w-7xl space-y-5 px-4 sm:px-6 lg:px-8">
            @if(session('status'))
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800 shadow-sm flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <svg class="h-4 w-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>{{ session('status') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900">&times;</button>
                </div>
            @endif

            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="hidden md:table min-w-full text-left text-sm">
                        <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500 border-b border-slate-100">
                            <tr>
                                <th class="px-6 py-4">Nomor Pesanan</th>
                                <th class="px-6 py-4">Pelanggan / Outlet</th>
                                <th class="px-6 py-4">Tanggal Order</th>
                                <th class="px-6 py-4">Sales Motoris</th>
                                <th class="px-6 py-4">Total Transaksi</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($orders as $order)
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="px-6 py-4 font-mono text-xs font-bold text-slate-700">
                                        {{ $order->order_number }}
                                    </td>
                                    <td class="px-6 py-4 font-medium text-slate-900">
                                        <div class="font-semibold text-slate-900">{{ $order->customer->name }}</div>
                                        <div class="text-xs text-slate-400">{{ $order->customer->address ?: 'Alamat belum ada' }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-slate-600">
                                        {{ $order->order_date->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4 text-slate-600">
                                        <div class="flex items-center gap-1.5">
                                            <svg class="h-3.5 w-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                            <span>{{ $order->user->name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 font-bold text-slate-900">
                                        Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 border border-emerald-200">
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                            {{ ucfirst($order->status) }}
                                        </span>
                                        @if($order->remaining_amount <= 0)
                                            <div class="mt-1 text-[10px] font-bold text-emerald-600">● Lunas</div>
                                        @elseif($order->is_overdue)
                                            <div class="mt-1 text-[10px] font-bold text-rose-600">● Jatuh Tempo (Sisa: Rp {{ number_format($order->remaining_amount, 0, ',', '.') }})</div>
                                        @elseif($order->paid_amount > 0)
                                            <div class="mt-1 text-[10px] font-bold text-sky-600">● DP Dibayar (Sisa: Rp {{ number_format($order->remaining_amount, 0, ',', '.') }})</div>
                                        @else
                                            <div class="mt-1 text-[10px] font-bold text-blue-600">● Tempo (Sisa: Rp {{ number_format($order->remaining_amount, 0, ',', '.') }})</div>
                                        @endif
                                        <span class="hidden">
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <!-- Button View / Detail (Hanya Icon SVG) -->
                                            <button type="button" 
                                                    onclick='viewOrderDetail(@json($order))'
                                                    title="Lihat Detail Pesanan" 
                                                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-blue-200 bg-blue-50 text-blue-600 shadow-2xs hover:bg-blue-100 hover:border-blue-300 transition">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </button>

                                            <!-- Button Download / Ekspor Excel (Hanya Icon SVG) -->
                                            <a href="{{ route('reports.sales-orders') }}" 
                                               title="Unduh Rekap Excel" 
                                               class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-emerald-300 bg-emerald-50 text-emerald-700 shadow-2xs hover:bg-emerald-100 hover:border-emerald-400 transition">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-slate-500">
                                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400 mb-3">
                                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                        </div>
                                        Belum ada transaksi pesanan penjualan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- MOBILE ORDER CARDS (md:hidden) --}}
                <div class="block md:hidden divide-y divide-slate-100">
                    @forelse($orders as $order)
                        <div class="p-4 space-y-2.5 hover:bg-slate-50/70 transition">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <span class="font-mono font-bold text-xs text-slate-700">{{ $order->order_number }}</span>
                                    <h4 class="font-bold text-slate-900 text-sm mt-0.5">{{ $order->customer->name }}</h4>
                                    <p class="text-[11px] text-slate-400">{{ $order->customer->address ?: 'Alamat belum ada' }}</p>
                                </div>
                                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-0.5 text-[10px] font-bold text-emerald-700 border border-emerald-200 shrink-0">
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                    {{ ucfirst($order->status) }}
                                </span>
                            </div>

                            <div class="flex flex-wrap items-center justify-between gap-2 text-xs bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                                <div class="text-slate-600">
                                    <span class="text-[10px] text-slate-400 block uppercase font-semibold">Sales & Tanggal</span>
                                    <span>{{ $order->user->name }} &bull; {{ $order->order_date->format('d/m/Y') }}</span>
                                </div>
                                <div class="text-right">
                                    <span class="text-[10px] text-slate-400 block uppercase font-semibold">Total Nilai</span>
                                    <span class="font-bold text-blue-600 text-sm">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                                </div>
                            </div>

                            <div class="flex items-center justify-end gap-1.5 pt-1">
                                <button type="button" 
                                        onclick='viewOrderDetail(@json($order))'
                                        class="inline-flex items-center gap-1 rounded-lg border border-blue-200 bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700 hover:bg-blue-100 active:scale-95 transition">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <span>Detail Pesanan</span>
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-slate-500">
                            <p class="text-xs">Belum ada transaksi pesanan penjualan.</p>
                        </div>
                    @endforelse
                </div>
                <div class="border-t border-slate-100 px-6 py-4">
                    {{ $orders->links() }}
                </div>
            </div>
        </div>
    </div>

    <script>
        function viewOrderDetail(o) {
            if (!window.Swal) return;
            window.Swal.fire({
                title: 'Pesanan #' + o.order_number,
                html: `
                    <div class="text-left text-xs space-y-2 p-3 border border-slate-200 rounded-xl bg-slate-50 text-slate-700">
                        <p><span class="font-bold text-slate-900">Nomor:</span> ${o.order_number}</p>
                        <p><span class="font-bold text-slate-900">Pelanggan:</span> ${o.customer ? o.customer.name : '-'}</p>
                        <p><span class="font-bold text-slate-900">Sales:</span> ${o.user ? o.user.name : '-'}</p>
                        <p><span class="font-bold text-slate-900">Tanggal:</span> ${o.order_date || '-'}</p>
                        <p><span class="font-bold text-slate-900">Total Transaksi:</span> Rp ${(o.total_amount || 0).toLocaleString('id-ID')}</p>
                        <p><span class="font-bold text-slate-900">Status:</span> ${o.status}</p>
                    </div>
                `,
                confirmButtonText: 'Tutup',
                confirmButtonColor: '#0f172a'
            });
        }
    </script>
</x-app-layout>
