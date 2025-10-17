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
        Schema::create('trip_statistics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tracking_session_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('total_distance', 10, 2)->default(0); // in kilometers
            $table->integer('total_duration')->default(0); // in seconds
            $table->decimal('average_speed', 8, 2)->default(0); // km/h
            $table->decimal('max_speed', 8, 2)->default(0); // km/h
            $table->decimal('min_speed', 8, 2)->default(0); // km/h
            $table->integer('total_points')->default(0);
            $table->decimal('carbon_footprint', 8, 2)->default(0); // kg CO2
            $table->string('transport_mode')->nullable(); // walking, driving, cycling, public_transport
            $table->json('speed_analysis')->nullable(); // speed distribution data
            $table->json('route_efficiency')->nullable(); // route optimization data
            $table->decimal('battery_usage', 5, 2)->nullable(); // percentage
            $table->timestamps();
            
            $table->index(['user_id', 'created_at']);
            $table->index(['tracking_session_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trip_statistics');
    }
};