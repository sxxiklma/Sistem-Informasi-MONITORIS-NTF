<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('visits', function (Blueprint $table) {
            $table->unsignedInteger('route_distance_meters')->nullable()->after('distance_from_home_meters');
            $table->unsignedInteger('route_duration_seconds')->nullable()->after('route_distance_meters');
            $table->longText('route_geometry')->nullable()->after('route_duration_seconds');
        });
    }

    public function down(): void
    {
        Schema::table('visits', function (Blueprint $table) {
            $table->dropColumn(['route_distance_meters', 'route_duration_seconds', 'route_geometry']);
        });
    }
};
