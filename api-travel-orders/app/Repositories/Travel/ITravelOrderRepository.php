<?php

namespace App\Repositories\Travel;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface ITravelOrderRepository
{
    public function getOrders(array $filters, int $perPage): LengthAwarePaginator;
    public function findOrderById($id): null|Model;
    public function createOrder(array $data): Model;
    public function updateOrder($id, array $data): Model;
}
