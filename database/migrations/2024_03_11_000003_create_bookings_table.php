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
            $table->ulid('id')->primary();
            $table->uuid('customer_id');
            $table->string('vehicle_make');
            $table->string('vehicle_model');
            $table->dateTime('booked_on')->useCurrent();
            $table->string('started_at');
            $table->string('ended_at');
            $table->boolean('customer_confirmed')->default(false);
            $table->boolean('admin_confirmed')->default(false);
            $table->boolean('is_pending')->default(true);
            $table->boolean('is_confirmed')->default(false);
            $table->boolean('is_cancelled')->default(false);
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
