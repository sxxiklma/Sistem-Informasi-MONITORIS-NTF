<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // --- GPS TRACKING SESSIONS ---
        Schema::create('tracking_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('task_id')->nullable()->constrained('visit_tasks')->nullOnDelete();
            $table->timestamp('started_at');
            $table->timestamp('ended_at')->nullable();
            $table->string('status', 20)->default('active'); // active / ended / expired
            $table->unsignedInteger('total_points')->default(0);
            $table->decimal('total_distance_meters', 12, 2)->default(0);
            $table->timestamps();

            $table->index(['sales_id', 'status']);
            $table->index(['started_at']);
        });

        // --- GPS LOCATION POINTS ---
        Schema::create('sales_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('session_id')->constrained('tracking_sessions')->cascadeOnDelete();
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->decimal('accuracy', 8, 2)->nullable(); // meters
            $table->decimal('speed', 8, 2)->nullable(); // m/s
            $table->decimal('heading', 6, 2)->nullable(); // degrees
            $table->timestamp('recorded_at'); // timestamp dari device
            $table->timestamp('received_at'); // timestamp server menerima
            $table->timestamps();

            $table->index(['sales_id', 'session_id', 'recorded_at']);
            $table->index(['session_id', 'recorded_at']);
        });

        // --- IN-APP NOTIFICATIONS ---
        Schema::create('app_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recipient_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('sender_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('type', 60); // task_assigned, task_accepted, task_rejected, etc.
            $table->string('title');
            $table->text('message');
            $table->string('entity_type', 60)->nullable(); // visit_tasks, etc.
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->string('action_url')->nullable(); // URL ke halaman terkait
            $table->json('data')->nullable(); // payload tambahan
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['recipient_id', 'read_at', 'created_at']);
            $table->index(['entity_type', 'entity_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('app_notifications');
        Schema::dropIfExists('sales_locations');
        Schema::dropIfExists('tracking_sessions');
    }
};
