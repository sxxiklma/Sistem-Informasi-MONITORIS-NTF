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
        Schema::table('visit_tasks', function (Blueprint $table) {
            $table->string('documentation_image')->nullable()->after('visit_notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visit_tasks', function (Blueprint $table) {
            $table->dropColumn('documentation_image');
        });
    }
};
