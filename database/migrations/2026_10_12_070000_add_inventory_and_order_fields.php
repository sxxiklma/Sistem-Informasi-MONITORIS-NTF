<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedInteger('stock_on_hand')->default(0)->after('price');
            $table->unsignedInteger('stock_carried')->default(0)->after('stock_on_hand');
            $table->unsignedInteger('stock_returned')->default(0)->after('stock_carried');
        });

        Schema::table('sales_order_items', function (Blueprint $table) {
            $table->decimal('discount_amount', 15, 2)->default(0)->after('unit_price');
        });

        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type', 20)->default('string');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_settings');
        Schema::table('sales_order_items', fn (Blueprint $table) => $table->dropColumn('discount_amount'));
        Schema::table('products', fn (Blueprint $table) => $table->dropColumn(['stock_on_hand', 'stock_carried', 'stock_returned']));
    }
};
