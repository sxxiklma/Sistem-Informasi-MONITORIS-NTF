<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sales_orders', function (Blueprint $table) {
            $table->string('payment_method', 30)->default('tunai')->after('status');
            $table->string('payment_status', 30)->default('lunas')->after('payment_method'); // lunas, sebagian, belum_lunas, jatuh_tempo
            $table->decimal('down_payment', 15, 2)->default(0)->after('total_amount');
            $table->decimal('paid_amount', 15, 2)->default(0)->after('down_payment');
            $table->decimal('remaining_amount', 15, 2)->default(0)->after('paid_amount');
            $table->date('due_date')->nullable()->after('remaining_amount');

            $table->index(['payment_status', 'due_date']);
        });

        Schema::create('order_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_order_id')->constrained()->cascadeOnDelete();
            $table->string('payment_number', 40)->nullable()->unique();
            $table->date('payment_date');
            $table->decimal('amount', 15, 2);
            $table->string('payment_method', 30)->default('tunai'); // tunai, transfer
            $table->text('notes')->nullable();
            $table->foreignId('received_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['sales_order_id', 'payment_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_payments');

        Schema::table('sales_orders', function (Blueprint $table) {
            $table->dropIndex(['payment_status', 'due_date']);
            $table->dropColumn([
                'payment_method',
                'payment_status',
                'down_payment',
                'paid_amount',
                'remaining_amount',
                'due_date',
            ]);
        });
    }
};
