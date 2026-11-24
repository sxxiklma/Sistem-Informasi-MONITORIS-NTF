<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[.22em] text-blue-600">Penjualan Lapangan</p>
                <h2 class="mt-1 text-2xl font-semibold text-slate-900">Input Pesanan Penjualan</h2>
            </div>
            <a href="{{ route('orders.index') }}" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-xs font-semibold text-slate-700 shadow-2xs hover:bg-slate-50 transition">
                <svg class="h-4 w-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Kembali ke Daftar</span>
            </a>
        </div>
    </x-slot>

    <div class="min-h-[calc(100vh-65px)] bg-slate-100 py-8">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <section class="rounded-2xl border border-slate-200 bg-white p-8 text-center shadow-sm">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-amber-50 text-blue-600 mb-4 border border-amber-200 shadow-2xs">
                    <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                </div>
                <h3 class="text-lg font-bold text-slate-900">Formulir Transaksi Pesanan Baru</h3>
                <p class="mt-2 text-sm text-slate-500 max-w-md mx-auto">Input pesanan outlet, pilih produk, jumlah kuantitas, harga, diskon, dan metode pembayaran.</p>
                <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
                    <button type="button" data-open-modal="order-create" class="inline-flex items-center gap-2 rounded-lg bg-slate-950 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-slate-800 transition">
                        <svg class="h-4 w-4 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Buka Form Pesanan</span>
                    </button>
                    <a href="{{ route('orders.index') }}" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-2xs hover:bg-slate-50 transition">
                        <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        <span>Batal</span>
                    </a>
                </div>
            </section>
        </div>
    </div>

    <x-feature-modal 
        id="order-create" 
        title="Buat Pesanan Baru" 
        :action="route('orders.store')" 
        :fields="[
            ['label' => 'Tanggal pesanan', 'name' => 'order_date', 'type' => 'date', 'required' => true, 'value' => today()->format('Y-m-d')],
            ['label' => 'Pelanggan / Outlet', 'name' => 'customer_id', 'type' => 'select', 'options' => $customers->pluck('name', 'id')->all(), 'required' => true],
            ['label' => 'Produk / SKU', 'name' => 'product_id', 'type' => 'select', 'options' => $products->pluck('name', 'id')->all(), 'required' => true],
            ['label' => 'Jumlah barang', 'name' => 'quantity', 'type' => 'number', 'required' => true, 'placeholder' => '1'],
            ['label' => 'Harga satuan (Rp)', 'name' => 'unit_price', 'type' => 'number', 'placeholder' => 'Otomatis atau manual'],
            ['label' => 'Diskon (Rp)', 'name' => 'discount_amount', 'type' => 'number', 'placeholder' => '0'],
            ['label' => 'Metode pembayaran', 'name' => 'payment_method', 'type' => 'select', 'options' => ['tunai' => 'Tunai (Cash)', 'transfer' => 'Transfer Bank', 'dp' => 'DP (Uang Muka & Sisa Tempo)', 'tempo' => 'Tempo (Termin / Kredit)']],
            ['label' => 'Nominal DP / Uang Muka (Rp)', 'name' => 'down_payment', 'type' => 'number', 'placeholder' => 'Isi jika metode DP (opsional)'],
            ['label' => 'Tanggal Jatuh Tempo', 'name' => 'due_date', 'type' => 'date', 'value' => today()->addDays(14)->format('Y-m-d')],
            ['label' => 'Catatan pesanan', 'name' => 'notes', 'type' => 'textarea', 'wide' => true]
        ]" 
        submit-label="Simpan Pesanan" 
    />
    <script>
        window.addEventListener('load', () => document.querySelector('[data-open-modal="order-create"]').click());
    </script>
</x-app-layout>
