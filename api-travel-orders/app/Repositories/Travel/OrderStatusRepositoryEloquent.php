<?php

namespace App\Repositories\Travel;

use App\Models\Travel\OrderStatus;
use App\Repositories\Repository;
use Illuminate\Database\Eloquent\Collection;

class OrderStatusRepositoryEloquent extends Repository implements IOrderStatusRepository
{
    protected function model(): OrderStatus
    {
        return new OrderStatus();
    }
    public function getOrdersStatus(): Collection
    {
        return $this->model->select(["id", "status"])
        ->orderBy("id")
        ->get();
    }
}
