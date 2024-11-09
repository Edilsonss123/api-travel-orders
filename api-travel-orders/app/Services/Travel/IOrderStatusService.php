<?php

namespace App\Services\Travel;

use Illuminate\Database\Eloquent\Collection;

interface IOrderStatusService
{
    public function getAll(): Collection;
}
