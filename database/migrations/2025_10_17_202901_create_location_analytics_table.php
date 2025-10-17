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
        Schema::create('location_analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->string('location_name')->nullable();
            $table->string('location_type')->nullable(); // home, work, gym, restaurant, etc.
            $table->integer('visit_count')->default(1);
            $table->integer('total_time_spent')->default(0); // in seconds
            $table->timestamp('first_visited_at');
            $table->timestamp('last_visited_at');
            $table->json('visit_patterns')->nullable(); // daily, weekly patterns
            $table->json('time_distribution')->nullable(); // time spent by hour/day
            $table->decimal('average_duration', 8, 2)->default(0); // average time per visit
            $table->boolean('is_significant')->default(false); // frequently visited locations
            $table->timestamps();
            
            $table->index(['user_id', 'latitude', 'longitude']);
            $table->index(['user_id', 'visit_count']);
            $table->index(['user_id', 'is_significant']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('location_analytics');
    }
};