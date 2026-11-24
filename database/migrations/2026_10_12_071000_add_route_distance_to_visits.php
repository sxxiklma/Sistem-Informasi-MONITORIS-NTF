<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('visits', function (Blueprint $table) {
            $table->decimal('home_latitude', 10, 7)->nullable()->after('planned_date');
            $table->decimal('home_longitude', 10, 7)->nullable()->after('home_latitude');
            $table->unsignedInteger('distance_from_home_meters')->nullable()->after('home_longitude');
            $table->string('pic_name')->nullable()->after('outcome');
            $table->string('pic_phone', 30)->nullable()->after('pic_name');
            $table->unsignedInteger('visit_order')->nullable()->after('pic_phone');
            $table->string('purpose')->nullable()->after('visit_order');
        });
    }

    public function down(): void
    {
        Schema::table('visits', function (Blueprint $table) {
            $table->dropColumn(['home_latitude', 'home_longitude', 'distance_from_home_meters', 'pic_name', 'pic_phone', 'visit_order', 'purpose']);
        });
    }
};
