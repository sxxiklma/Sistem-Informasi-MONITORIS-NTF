<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\CustomerType;
use App\Models\Product;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\User;
use App\Models\VisitTask;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class NetafarmSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Update / Seed Customer Types for Netafarm
        $types = [
            ['code' => 'kios', 'name' => 'Kios Pertanian & Saprotan', 'description' => 'Toko/kios penyedia pupuk, benih, dan sarana produksi pertanian', 'badge_color' => 'sky', 'sort_order' => 1],
            ['code' => 'hidroponik', 'name' => 'Mitra Hidroponik & Greenhouse', 'description' => 'Petani hidroponik komersial, instalasi greenhouse, dan sayur segar', 'badge_color' => 'emerald', 'sort_order' => 2],
            ['code' => 'perikanan', 'name' => 'Budidaya Perikanan / Tambak', 'description' => 'Pembudidaya ikan lele, nila, patin, bioflok, dan tambak', 'badge_color' => 'indigo', 'sort_order' => 3],
            ['code' => 'peternakan', 'name' => 'Peternakan & Poultry Shop', 'description' => 'Kandang ayam, sapi, kambing, dan toko pakan ternak', 'badge_color' => 'purple', 'sort_order' => 4],
            ['code' => 'grosir', 'name' => 'Sub-Distributor & Grosir Pupuk', 'description' => 'Agen besar distributor wilayah Jawa Timur', 'badge_color' => 'sky', 'sort_order' => 5],
            ['code' => 'gapoktan', 'name' => 'Gapoktan / Kelompok Tani', 'description' => 'Gabungan kelompok tani tanaman pangan dan hortikultura', 'badge_color' => 'emerald', 'sort_order' => 6],
        ];

        foreach ($types as $t) {
            CustomerType::updateOrCreate(['code' => $t['code']], $t);
        }

        // 2. Clear old sembako / dimsum products and replace with Netafarm Agricultural Products
        $productsData = [
            // HIDROPONIK
            ['sku' => 'NF-H01', 'name' => 'Nutrisi AB Mix Hidroponik Sayur Daun 5L', 'category' => 'Hidroponik', 'unit' => 'Set', 'price' => 85000, 'stock_on_hand' => 150, 'stock_carried' => 15],
            ['sku' => 'NF-H02', 'name' => 'Nutrisi AB Mix Hidroponik Buah / Melon 5L', 'category' => 'Hidroponik', 'unit' => 'Set', 'price' => 95000, 'stock_on_hand' => 120, 'stock_carried' => 10],
            ['sku' => 'NF-H03', 'name' => 'Rockwool Cultilene Import Media Tanam (Slab 100x15x7.5cm)', 'category' => 'Hidroponik', 'unit' => 'Slab', 'price' => 75000, 'stock_on_hand' => 85, 'stock_carried' => 8],
            ['sku' => 'NF-H04', 'name' => 'Netpot Hidroponik 5cm Hitam Tebal (Pack isi 100)', 'category' => 'Hidroponik', 'unit' => 'Pack', 'price' => 35000, 'stock_on_hand' => 200, 'stock_carried' => 20],
            ['sku' => 'NF-H05', 'name' => 'Digital TDS & EC Meter Total Dissolved Solids', 'category' => 'Hidroponik', 'unit' => 'Unit', 'price' => 65000, 'stock_on_hand' => 60, 'stock_carried' => 5],
            ['sku' => 'NF-H06', 'name' => 'Benih Selada Grand Rapids Super Import 15gr', 'category' => 'Hidroponik', 'unit' => 'Kaleng', 'price' => 42000, 'stock_on_hand' => 180, 'stock_carried' => 25],
            ['sku' => 'NF-H07', 'name' => 'pH Down Asam Fosfat Food Grade 1 Liter', 'category' => 'Hidroponik', 'unit' => 'Botol', 'price' => 45000, 'stock_on_hand' => 90, 'stock_carried' => 10],

            // GREENHOUSE
            ['sku' => 'NF-G01', 'name' => 'Plastik UV Greenhouse 200 Micron Vatan 14% (3m x 50m)', 'category' => 'Greenhouse', 'unit' => 'Roll', 'price' => 1450000, 'stock_on_hand' => 35, 'stock_carried' => 2],
            ['sku' => 'NF-G02', 'name' => 'Insect Net / Kasa Hijau 50 Mesh Anti Kutu Kebul (2m x 50m)', 'category' => 'Greenhouse', 'unit' => 'Roll', 'price' => 620000, 'stock_on_hand' => 40, 'stock_carried' => 3],
            ['sku' => 'NF-G03', 'name' => 'Spring Clip & Profil Lock Alumunium Greenhouse 2m', 'category' => 'Greenhouse', 'unit' => 'Batang', 'price' => 28000, 'stock_on_hand' => 300, 'stock_carried' => 30],
            ['sku' => 'NF-G04', 'name' => 'Shading Net / Paranet Peneduh 75% High Quality (3m x 50m)', 'category' => 'Greenhouse', 'unit' => 'Roll', 'price' => 780000, 'stock_on_hand' => 50, 'stock_carried' => 4],

            // PERTANIAN & PUPUK
            ['sku' => 'NF-P01', 'name' => 'Pupuk NPK Mutiara 16-16-16 Kemasan 1kg Repack', 'category' => 'Pertanian & Pupuk', 'unit' => 'Pouch', 'price' => 24000, 'stock_on_hand' => 250, 'stock_carried' => 35],
            ['sku' => 'NF-P02', 'name' => 'Pupuk Hayati Cair Pembenah Tanah & Mikroba 1 Liter', 'category' => 'Pertanian & Pupuk', 'unit' => 'Botol', 'price' => 55000, 'stock_on_hand' => 110, 'stock_carried' => 15],
            ['sku' => 'NF-P03', 'name' => 'Insektisida Nabati Ekstrak Neem Oil Organik 500ml', 'category' => 'Pertanian & Pupuk', 'unit' => 'Botol', 'price' => 65000, 'stock_on_hand' => 80, 'stock_carried' => 10],
            ['sku' => 'NF-P04', 'name' => 'Sprayer Elektrik Pertanian 16 Liter 2in1 Baterai & Manual', 'category' => 'Pertanian & Pupuk', 'unit' => 'Unit', 'price' => 385000, 'stock_on_hand' => 45, 'stock_carried' => 3],

            // PERIKANAN
            ['sku' => 'NF-K01', 'name' => 'Probiotik Bioflok Pengurai Air & Bakteri Kolam 1L', 'category' => 'Perikanan', 'unit' => 'Botol', 'price' => 48000, 'stock_on_hand' => 95, 'stock_carried' => 12],
            ['sku' => 'NF-K02', 'name' => 'Pakan Benih Ikan Nila / Lele Starter Protein 38% (Sak 10kg)', 'category' => 'Perikanan', 'unit' => 'Sak', 'price' => 165000, 'stock_on_hand' => 70, 'stock_carried' => 8],
            ['sku' => 'NF-K03', 'name' => 'Aerator Hi-Blow Air Pump Kolam Terpal & Akuaponik 45W', 'category' => 'Perikanan', 'unit' => 'Unit', 'price' => 320000, 'stock_on_hand' => 30, 'stock_carried' => 2],

            // PETERNAKAN
            ['sku' => 'NF-T01', 'name' => 'Konsentrat Pakan Unggas & Ruminansia Booster (Sak 25kg)', 'category' => 'Peternakan', 'unit' => 'Sak', 'price' => 240000, 'stock_on_hand' => 60, 'stock_carried' => 5],
            ['sku' => 'NF-T02', 'name' => 'Mineral Premix Vitamin Ternak Sapi & Kambing 1kg', 'category' => 'Peternakan', 'unit' => 'Pack', 'price' => 38000, 'stock_on_hand' => 100, 'stock_carried' => 15],
            ['sku' => 'NF-T03', 'name' => 'Disinfektan Kandang & Bio-Security Ternak 1L', 'category' => 'Peternakan', 'unit' => 'Botol', 'price' => 52000, 'stock_on_hand' => 80, 'stock_carried' => 10],
        ];

        // Upsert by SKU
        foreach ($productsData as $p) {
            Product::updateOrCreate(
                ['sku' => $p['sku']],
                array_merge($p, [
                    'stock_returned' => 0,
                    'is_active' => true,
                ])
            );
        }

        // Clean up legacy products (e.g., Dimsum, Kecap, etc. if sku PRD-001..004)
        Product::whereIn('sku', ['PRD-001', 'PRD-002', 'PRD-003', 'PRD-004'])->update(['is_active' => false]);

        // 3. Customers / Mitra Tani around Sidoarjo & Surabaya
        $customersData = [
            [
                'code' => 'OUT-001',
                'name' => 'Kios Pertanian Barokah Tani',
                'type' => 'kios',
                'phone' => '081234567890',
                'address' => 'Jl. Raya Sawunggaling No. 45, Jemundo, Taman, Sidoarjo',
                'latitude' => -7.375800,
                'longitude' => 112.698200,
                'is_active' => true,
            ],
            [
                'code' => 'OUT-002',
                'name' => 'Surya Agro Hidroponik & Farm',
                'type' => 'hidroponik',
                'phone' => '081234567891',
                'address' => 'Jl. Raya Taman No. 88, Sepanjang, Kec. Taman, Sidoarjo',
                'latitude' => -7.351200,
                'longitude' => 112.705100,
                'is_active' => true,
            ],
            [
                'code' => 'OUT-003',
                'name' => 'Toko Pakan & Sapronak Makmur Jaya',
                'type' => 'peternakan',
                'phone' => '081234567892',
                'address' => 'Jl. Raya Krian - Wonoayu No. 12, Sidoarjo',
                'latitude' => -7.408500,
                'longitude' => 112.602100,
                'is_active' => true,
            ],
            [
                'code' => 'OUT-000045',
                'name' => 'Distributor Utama CV. Netafarm Indolestari',
                'type' => 'grosir',
                'phone' => '08570205604',
                'address' => 'Jl. Raya Sawunggaling No.150 A, Jemundo, Kec. Taman, Kabupaten Sidoarjo, Jawa Timur 61257',
                'latitude' => -7.376200,
                'longitude' => 112.697500,
                'is_active' => true,
            ],
            [
                'code' => 'OUT-00005',
                'name' => 'Gapoktan Makmur Tani Sepanjang',
                'type' => 'gapoktan',
                'phone' => '08570205604',
                'address' => 'Jl. Ketintang Baru, Wonokromo, Surabaya Selatan',
                'latitude' => -7.312500,
                'longitude' => 112.728900,
                'is_active' => true,
            ],
            [
                'code' => 'OUT-00006',
                'name' => 'Marsudi Tani (Kios Mitra Utama)',
                'type' => 'kios',
                'phone' => '08570205604',
                'address' => 'Jl. Karah 5 No. 14, Jambangan, Surabaya Selatan',
                'latitude' => -7.3095677,
                'longitude' => 112.7196327,
                'is_active' => true,
            ],
        ];

        foreach ($customersData as $c) {
            Customer::updateOrCreate(['code' => $c['code']], $c);
        }

        // 4. Update Task 10 Taking Order with Real Netafarm Agricultural Products
        $task10 = VisitTask::find(10);
        $marsudi = Customer::where('name', 'like', '%Marsudi%')->first();
        $salesUser = User::whereHas('roles', fn ($q) => $q->where('name', 'Sales Motoris'))->first() ?? User::first();

        if ($task10) {
            $prodH01 = Product::where('sku', 'NF-H01')->first(); // Nutrisi AB Mix 5L
            $prodH03 = Product::where('sku', 'NF-H03')->first(); // Rockwool Slab
            $prodP03 = Product::where('sku', 'NF-P03')->first(); // Neem Oil 500ml

            if ($prodH01 && $prodH03 && $prodP03) {
                // Delete previous items of task 10 order
                if ($task10->order) {
                    $task10->order->items()->delete();
                    $task10->order->payments()->delete();
                    $order = $task10->order;
                } else {
                    $order = new SalesOrder;
                    $order->order_number = 'SO-'.now()->format('ymdHis').'-108';
                }

                $qty1 = 5; // 5 x 85.000 = 425.000
                $qty2 = 4; // 4 x 75.000 = 300.000
                $qty3 = 2; // 2 x 65.000 = 130.000
                $totalAmount = (5 * 85000) + (4 * 75000) + (2 * 65000); // 855.000

                $order->fill([
                    'visit_task_id' => $task10->id,
                    'customer_id' => $task10->customer_id ?? ($marsudi ? $marsudi->id : 1),
                    'user_id' => $task10->sales_id ?? $salesUser->id,
                    'order_date' => now()->toDateString(),
                    'status' => 'confirmed',
                    'payment_method' => 'tempo',
                    'due_date' => Carbon::today()->addDays(14),
                    'total_amount' => $totalAmount,
                    'down_payment' => 0,
                    'paid_amount' => 0,
                    'remaining_amount' => $totalAmount,
                    'payment_status' => 'belum_lunas',
                    'notes' => 'Pesanan rutin kios Marsudi Tani: AB Mix Sayur Daun, Rockwool Slab, dan Insektisida Nabati Neem Oil (Tenor 14 Hari)',
                ]);
                $order->save();

                // Order Items
                SalesOrderItem::create([
                    'sales_order_id' => $order->id,
                    'product_id' => $prodH01->id,
                    'quantity' => $qty1,
                    'unit_price' => $prodH01->price,
                    'discount_amount' => 0,
                    'subtotal' => 425000,
                ]);

                SalesOrderItem::create([
                    'sales_order_id' => $order->id,
                    'product_id' => $prodH03->id,
                    'quantity' => $qty2,
                    'unit_price' => $prodH03->price,
                    'discount_amount' => 0,
                    'subtotal' => 300000,
                ]);

                SalesOrderItem::create([
                    'sales_order_id' => $order->id,
                    'product_id' => $prodP03->id,
                    'quantity' => $qty3,
                    'unit_price' => $prodP03->price,
                    'discount_amount' => 0,
                    'subtotal' => 130000,
                ]);

                // Update task visit result text
                $task10->update([
                    'visit_result' => 'Outlet memesan repeat order untuk stok minggu ini: [Pesanan '.$order->order_number.': Rp 855.000 (TEMPO 14 Hari)]. Kios minta dikirim brosur panduan nutrisi hidroponik Netafarm.',
                ]);
            }
        }
    }
}
