<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('travel_orders', function (Blueprint $table) {
            $table->id();
            $table->string('travelerName');
            $table->string('destination');
            $table->datetime('departureDate');
            $table->datetime('returnDate');
            $table->foreignId('status')->nullable()
            ->constrained('order_status')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('travel_orders');
    }
};
