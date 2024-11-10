<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Travel\TravelOrder;

class TravelSeeder extends Seeder
{
    public function run()
    {
        // Cria 10 registros de TravelOrder
        TravelOrder::factory()->count(10)->create();
    }
}
