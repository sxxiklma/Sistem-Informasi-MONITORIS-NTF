<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('customer_types', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->string('badge_color', 30)->default('amber'); // amber, sky, emerald, purple, indigo, rose, slate
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
        });

        // Seed initial default types to match existing customer classifications
        $now = now();
        $defaults = [
            [
                'code' => 'toko',
                'name' => 'Toko Retail / Kelontong',
                'description' => 'Outlet toko eceran / kelontong umum barang kebutuhan harian',
                'badge_color' => 'amber',
                'sort_order' => 1,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'code' => 'kios',
                'name' => 'Kios / Lapak Pasar',
                'description' => 'Kios kecil atau lapak di lingkungan pasar tradisional',
                'badge_color' => 'sky',
                'sort_order' => 2,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'code' => 'warung',
                'name' => 'Warung / Agen Kecil',
                'description' => 'Warung kelontong pemukiman atau sub-agen kecil',
                'badge_color' => 'emerald',
                'sort_order' => 3,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'code' => 'grosir',
                'name' => 'Grosir / Agen Besar',
                'description' => 'Toko grosir partai besar atau distributor wilayah',
                'badge_color' => 'purple',
                'sort_order' => 4,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'code' => 'minimarket',
                'name' => 'Minimarket Modern',
                'description' => 'Toko swalayan atau minimarket mandiri modern',
                'badge_color' => 'indigo',
                'sort_order' => 5,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'code' => 'reseller',
                'name' => 'Reseller / Dropshipper',
                'description' => 'Mitra reseller non-toko fisik atau perorangan',
                'badge_color' => 'rose',
                'sort_order' => 6,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('customer_types')->insert($defaults);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_types');
    }
};
