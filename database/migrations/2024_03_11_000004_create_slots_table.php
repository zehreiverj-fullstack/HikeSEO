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
        Schema::create('slots', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->ulid('book_id');
            $table->dateTime('started_at');
            $table->dateTime('ended_at');
            $table->boolean('customer_confirmed')->default(false);
            $table->boolean('admin_confirmed')->default(false);
            $table->boolean('is_pending')->default(true);
            $table->boolean('is_confirmed')->default(false);
            $table->boolean('is_cancelled')->default(false);
            $table->timestamps();

            $table->foreign('book_id')->references('id')->on('bookings')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('slots');
    }
};
