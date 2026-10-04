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
    Schema::create('bookings', function (Blueprint $table) {
        $table->id();
        
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        $table->integer('pax');
        $table->string('pickup');
        $table->string('dropoff');
        $table->string('pickup_time');
        $table->foreignId('rider_id')->nullable()->constrained('riders')->onDelete('set null');
        $table->boolean('is_pick_up')->default(false);
        $table->boolean('is_cancelled')->default(false);
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking');
    }
};
