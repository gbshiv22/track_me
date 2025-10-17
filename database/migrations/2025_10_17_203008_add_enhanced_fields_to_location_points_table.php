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
        Schema::table('location_points', function (Blueprint $table) {
            $table->decimal('speed', 8, 2)->nullable()->after('accuracy'); // km/h
            $table->decimal('heading', 5, 2)->nullable()->after('speed'); // degrees
            $table->decimal('altitude', 8, 2)->nullable()->after('heading'); // meters
            $table->integer('battery_level')->nullable()->after('altitude'); // percentage
            $table->boolean('is_offline')->default(false)->after('battery_level');
            $table->timestamp('synced_at')->nullable()->after('is_offline');
            $table->json('metadata')->nullable()->after('synced_at'); // additional data
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('location_points', function (Blueprint $table) {
            $table->dropColumn([
                'speed',
                'heading', 
                'altitude',
                'battery_level',
                'is_offline',
                'synced_at',
                'metadata'
            ]);
        });
    }
};