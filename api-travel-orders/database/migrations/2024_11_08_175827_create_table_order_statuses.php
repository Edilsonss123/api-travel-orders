<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\ValueObject\Travel\OrderStatusVO;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_status', function (Blueprint $table) {
            $table->id();
            $table->string('status')->unique();
            $table->timestamp('created_at')->nullable()->useCurrent();
        });
        DB::table('order_status')->insert([
            ['id' => OrderStatusVO::Requested, 'status' => OrderStatusVO::Requested->name],
            ['id' => OrderStatusVO::Approved, 'status' => OrderStatusVO::Approved->name],
            ['id' => OrderStatusVO::Canceled, 'status' => OrderStatusVO::Canceled->name],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_status');
    }
};
