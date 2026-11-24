<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-blue-600">Inventori & Katalog</p>
                <h2 class="mt-1 text-2xl font-semibold text-slate-900">Produk & Stok Barang</h2>
            </div>
            @can('kelola master data')
            <a href="{{ route('products.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md shadow-blue-600/30 transition hover:bg-blue-500 active:scale-95">
                <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Tambah Produk</span>
            </a>
            @endcan
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
                                <th class="w-16 px-4 py-4 text-center">Foto</th>
                                <th class="w-28 px-4 py-4">SKU / Kode</th>
                                <th class="px-6 py-4">Nama Produk</th>
                                <th class="px-6 py-4">Harga Jual</th>
                                <th class="px-6 py-4">Stok Gudang</th>
                                <th class="px-6 py-4">Dibawa Sales</th>
                                <th class="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($products as $product)
                                <tr class="hover:bg-slate-50/70 transition">
                                    {{-- 1. FOTO PRODUK (Paling Kiri) --}}
                                    <td class="w-16 px-4 py-3 text-center">
                                        <div class="relative h-12 w-12 mx-auto shrink-0 overflow-hidden rounded-xl border border-slate-200 bg-slate-50 flex items-center justify-center shadow-2xs group">
                                            @if($product->image_url)
                                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" 
                                                     onclick="showProductImageModal('{{ $product->image_url }}', '{{ addslashes($product->name) }}', '{{ $product->sku }}', 'Rp {{ number_format($product->price, 0, ',', '.') }}')"
                                                     class="h-full w-full object-cover cursor-pointer hover:scale-110 transition-transform duration-200"
                                                     title="Klik untuk melihat foto produk">
                                            @else
                                                <div class="flex flex-col items-center justify-center text-slate-300">
                                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                </div>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- 2. SKU / KODE --}}
                                    <td class="w-28 px-4 py-4 font-mono text-xs font-semibold text-slate-600 whitespace-nowrap">
                                        {{ $product->sku }}
                                    </td>

                                    {{-- 3. NAMA PRODUK --}}
                                    <td class="px-6 py-3.5 font-medium text-slate-900">
                                        <div class="font-bold text-slate-900">{{ $product->name }}</div>
                                        <div class="mt-0.5 text-xs text-slate-500 flex items-center gap-1.5 flex-wrap">
                                            <span class="inline-block rounded bg-sky-50 border border-sky-200/80 px-2 py-0.5 text-[10px] font-semibold text-sky-700">{{ $product->category ?: 'Tanpa kategori' }}</span>
                                            <span class="text-slate-300">·</span>
                                            <span class="text-slate-500 font-medium">Satuan: {{ $product->unit }}</span>
                                        </div>
                                    </td>

                                    {{-- 4. HARGA JUAL --}}
                                    <td class="px-6 py-4 font-semibold text-slate-900 whitespace-nowrap">
                                        Rp {{ number_format($product->price, 0, ',', '.') }}
                                    </td>

                                    {{-- 5. STOK GUDANG --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center gap-1 font-semibold {{ $product->stock_on_hand <= 10 ? 'text-amber-700 bg-amber-50 px-2 py-0.5 rounded border border-amber-200' : 'text-slate-700' }}">
                                            <svg class="h-3.5 w-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                            {{ number_format($product->stock_on_hand) }}
                                        </span>
                                    </td>

                                    {{-- 6. DIBAWA SALES --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center gap-1 text-slate-700">
                                            <svg class="h-3.5 w-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                            {{ number_format($product->stock_carried) }}
                                        </span>
                                    </td>

                                    {{-- 7. AKSI --}}
                                    <td class="whitespace-nowrap px-6 py-4 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <!-- Button View / Detail -->
                                            <button type="button" 
                                                    onclick='viewProductDetail(@json($product))'
                                                    title="Lihat Detail Produk" 
                                                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-blue-200 bg-blue-50 text-blue-600 shadow-2xs hover:bg-blue-100 hover:border-blue-300 transition">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </button>

                                            @can('kelola master data')
                                            <!-- Button Edit -->
                                            <a href="{{ route('products.edit', $product) }}" 
                                               title="Edit Data Produk" 
                                               class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-amber-300 bg-amber-50 text-amber-700 shadow-2xs hover:bg-amber-100 hover:border-amber-400 transition">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </a>

                                            <!-- Button Delete / Nonaktifkan -->
                                            <form class="inline" method="POST" action="{{ route('products.destroy', $product) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        title="Hapus / Nonaktifkan Produk" 
                                                        class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-rose-200 bg-rose-50 text-rose-600 shadow-2xs hover:bg-rose-100 hover:border-rose-300 transition" 
                                                        onclick="return confirm('Hapus / nonaktifkan produk {{ $product->name }}?')">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-slate-500">
                                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400 mb-3">
                                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                        </div>
                                        Belum ada data produk di katalog inventori.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- MOBILE PRODUCT CARDS (md:hidden) --}}
                <div class="block md:hidden divide-y divide-slate-100">
                    @forelse($products as $product)
                        <div class="p-4 space-y-3 hover:bg-slate-50/70 transition">
                            <div class="flex items-start gap-3">
                                {{-- Thumbnail Mobile --}}
                                <div class="relative h-14 w-14 shrink-0 overflow-hidden rounded-xl border border-slate-200 bg-slate-50 flex items-center justify-center shadow-2xs">
                                    @if($product->image_url)
                                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" 
                                             onclick="showProductImageModal('{{ $product->image_url }}', '{{ addslashes($product->name) }}', '{{ $product->sku }}', 'Rp {{ number_format($product->price, 0, ',', '.') }}')"
                                             class="h-full w-full object-cover">
                                    @else
                                        <div class="flex flex-col items-center justify-center text-slate-300">
                                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        </div>
                                    @endif
                                </div>

                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-1.5">
                                        <span class="font-mono font-bold text-xs text-slate-500">{{ $product->sku }}</span>
                                        <span class="rounded bg-sky-50 border border-sky-200/60 px-1.5 py-0.5 text-[10px] font-semibold text-sky-700">{{ $product->category ?: 'Umum' }}</span>
                                    </div>
                                    <h4 class="font-bold text-slate-900 text-sm mt-0.5 truncate">{{ $product->name }}</h4>
                                    <div class="text-[11px] text-slate-400 mt-0.5">Satuan: {{ $product->unit }}</div>
                                </div>
                                <div class="text-right shrink-0">
                                    <span class="font-bold text-blue-600 text-sm">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-2 text-xs bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                                <div>
                                    <span class="text-[10px] text-slate-400 block uppercase font-semibold">Stok Gudang</span>
                                    <span class="font-bold {{ $product->stock_on_hand <= 10 ? 'text-amber-700' : 'text-slate-800' }}">
                                        {{ number_format($product->stock_on_hand) }} {{ $product->unit }}
                                    </span>
                                </div>
                                <div>
                                    <span class="text-[10px] text-slate-400 block uppercase font-semibold">Dibawa Sales</span>
                                    <span class="font-bold text-slate-800">
                                        {{ number_format($product->stock_carried) }} {{ $product->unit }}
                                    </span>
                                </div>
                            </div>

                            <div class="flex items-center justify-end gap-1.5 pt-1">
                                <button type="button" 
                                        onclick='viewProductDetail(@json($product))'
                                        class="inline-flex items-center gap-1 rounded-lg border border-blue-200 bg-blue-50 px-2.5 py-1.5 text-xs font-semibold text-blue-700 hover:bg-blue-100 active:scale-95 transition">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <span>Detail</span>
                                </button>

                                @can('kelola master data')
                                <a href="{{ route('products.edit', $product) }}"
                                   class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-amber-300 bg-amber-50 text-amber-700 hover:bg-amber-100 active:scale-95 transition" title="Edit">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                <form method="POST" action="{{ route('products.destroy', $product) }}" class="inline" onsubmit="return confirm('Hapus produk {{ $product->name }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-rose-200 bg-rose-50 text-rose-600 hover:bg-rose-100 active:scale-95 transition" title="Hapus">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-slate-500">
                            <p class="text-xs">Belum ada data produk barang.</p>
                        </div>
                    @endforelse
                </div>
                <div class="border-t border-slate-100 px-6 py-4">
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </div>

    <script>
        function viewProductDetail(p) {
            if (!window.Swal) return;
            const imgHtml = p.image_url ? `
                <div class="mb-4 overflow-hidden rounded-xl border border-slate-200 bg-white max-h-56 flex items-center justify-center p-2 cursor-pointer" onclick="showProductImageModal('${p.image_url}', '${p.name.replace(/'/g, "\\'")}', '${p.sku}', 'Rp ${(p.price || 0).toLocaleString('id-ID')}')">
                    <img src="${p.image_url}" alt="${p.name}" class="max-h-52 w-auto object-contain rounded-lg hover:scale-105 transition-transform duration-200" title="Klik untuk memperbesar">
                </div>
            ` : `
                <div class="mb-4 flex flex-col items-center justify-center py-6 bg-slate-50 rounded-xl border border-dashed border-slate-200 text-slate-400">
                    <svg class="h-10 w-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span class="text-xs mt-1 text-slate-400">Belum ada foto produk</span>
                </div>
            `;

            window.Swal.fire({
                title: `<span class="text-base font-bold text-slate-900">${p.name}</span>`,
                html: `
                    ${imgHtml}
                    <div class="text-left text-xs space-y-2 p-3.5 border border-slate-200 rounded-xl bg-slate-50 text-slate-700">
                        <div class="flex justify-between items-center py-1 border-b border-slate-200/60">
                            <span class="font-bold text-slate-500 uppercase tracking-wider text-[10px]">SKU / Kode</span>
                            <span class="font-mono font-bold text-slate-900">${p.sku}</span>
                        </div>
                        <div class="flex justify-between items-center py-1 border-b border-slate-200/60">
                            <span class="font-bold text-slate-500 uppercase tracking-wider text-[10px]">Kategori</span>
                            <span class="font-semibold text-sky-700 bg-sky-50 border border-sky-200/60 px-2 py-0.5 rounded text-[11px]">${p.category || '-'}</span>
                        </div>
                        <div class="flex justify-between items-center py-1 border-b border-slate-200/60">
                            <span class="font-bold text-slate-500 uppercase tracking-wider text-[10px]">Satuan</span>
                            <span class="font-semibold text-slate-800">${p.unit || 'pcs'}</span>
                        </div>
                        <div class="flex justify-between items-center py-1 border-b border-slate-200/60">
                            <span class="font-bold text-slate-500 uppercase tracking-wider text-[10px]">Harga Jual</span>
                            <span class="font-bold text-blue-600 text-sm">Rp ${(p.price || 0).toLocaleString('id-ID')}</span>
                        </div>
                        <div class="flex justify-between items-center py-1 border-b border-slate-200/60">
                            <span class="font-bold text-slate-500 uppercase tracking-wider text-[10px]">Stok Gudang</span>
                            <span class="font-bold text-slate-800">${(p.stock_on_hand || 0).toLocaleString('id-ID')} ${p.unit || 'pcs'}</span>
                        </div>
                        <div class="flex justify-between items-center py-1 border-b border-slate-200/60">
                            <span class="font-bold text-slate-500 uppercase tracking-wider text-[10px]">Dibawa Sales</span>
                            <span class="font-bold text-slate-800">${(p.stock_carried || 0).toLocaleString('id-ID')} ${p.unit || 'pcs'}</span>
                        </div>
                        <div class="flex justify-between items-center py-1">
                            <span class="font-bold text-slate-500 uppercase tracking-wider text-[10px]">Barang Retur</span>
                            <span class="font-bold text-slate-800">${(p.stock_returned || 0).toLocaleString('id-ID')} ${p.unit || 'pcs'}</span>
                        </div>
                    </div>
                `,
                confirmButtonText: 'Tutup',
                confirmButtonColor: '#2563eb'
            });
        }

        function showProductImageModal(url, name, sku, price) {
            if (!window.Swal) return;
            window.Swal.fire({
                title: `<span class="text-base font-bold text-slate-900">${name}</span>`,
                html: `
                    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-slate-950 p-2 mb-3 shadow-inner">
                        <img src="${url}" alt="${name}" class="w-full max-h-[420px] object-contain rounded-xl mx-auto">
                    </div>
                    <div class="flex items-center justify-between text-xs px-2 py-1 bg-slate-50 rounded-lg border border-slate-200 text-slate-700">
                        <span class="font-mono font-bold text-slate-500">${sku}</span>
                        <span class="font-bold text-blue-600 text-sm">${price}</span>
                    </div>
                `,
                showCloseButton: true,
                confirmButtonText: 'Tutup',
                confirmButtonColor: '#2563eb'
            });
        }
    </script>
</x-app-layout>
