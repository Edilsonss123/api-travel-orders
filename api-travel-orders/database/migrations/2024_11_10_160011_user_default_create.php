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
        DB::table("users")->insert([
            "name" => "travel-order-test-2024",
            "email" => "travel-order-test-2024@gmail.com",
            "email_verified_at" => now(),
            "password" => Hash::make("travel-order-test-2024"),
            "remember_token" => Str::random(10)
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table("users")->where("name", "travel-order-test-2024")->delete();
    }
};
