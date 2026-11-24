<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visit_tasks', function (Blueprint $table) {
            $table->id();
            $table->string('task_code', 40)->unique(); // e.g. TASK-2026-00001
            $table->foreignId('sales_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('supervisor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('customer_id')->constrained('customers')->restrictOnDelete();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->dateTime('scheduled_at');
            $table->dateTime('due_at')->nullable();
            $table->text('purpose')->nullable();
            $table->text('target_result')->nullable(); // hasil yang diharapkan
            $table->text('instructions')->nullable();
            $table->enum('priority', ['rendah', 'normal', 'tinggi', 'mendesak'])->default('normal');
            $table->string('status', 40)->default('MENUNGGU_RESPONS');
            // accepted/rejected at
            $table->timestamp('responded_at')->nullable();
            // check in
            $table->timestamp('check_in_at')->nullable();
            $table->decimal('check_in_latitude', 10, 7)->nullable();
            $table->decimal('check_in_longitude', 10, 7)->nullable();
            $table->decimal('check_in_distance_meters', 10, 2)->nullable(); // jarak dari outlet saat check-in
            // check out
            $table->timestamp('check_out_at')->nullable();
            $table->decimal('check_out_latitude', 10, 7)->nullable();
            $table->decimal('check_out_longitude', 10, 7)->nullable();
            $table->text('visit_result')->nullable(); // hasil kunjungan
            $table->text('visit_notes')->nullable();
            $table->timestamps();

            $table->index(['sales_id', 'status']);
            $table->index(['supervisor_id', 'status']);
            $table->index(['scheduled_at', 'status']);
            $table->index(['customer_id']);
        });

        Schema::create('visit_task_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('visit_tasks')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->string('response', 20); // DITERIMA / DITOLAK
            $table->string('rejection_category', 80)->nullable(); // kategori alasan tolak
            $table->text('reason')->nullable(); // alasan lengkap
            $table->timestamp('responded_at');
            $table->timestamps();

            $table->index(['task_id', 'response']);
        });

        Schema::create('visit_task_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('visit_tasks')->cascadeOnDelete();
            $table->foreignId('changed_by')->constrained('users')->restrictOnDelete();
            $table->string('from_status', 40)->nullable();
            $table->string('to_status', 40);
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable(); // data tambahan (coords, dll)
            $table->timestamp('changed_at');
            $table->timestamps();

            $table->index(['task_id', 'changed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visit_task_histories');
        Schema::dropIfExists('visit_task_responses');
        Schema::dropIfExists('visit_tasks');
    }
};
