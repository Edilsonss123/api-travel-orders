<?php

namespace App\Repositories\Travel;

use Illuminate\Database\Eloquent\Collection;

interface IOrderStatusRepository
{
    public function getOrdersStatus(): Collection;
}
