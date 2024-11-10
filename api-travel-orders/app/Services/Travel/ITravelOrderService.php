<?php

namespace App\Services\Travel;

use App\Exceptions\TravelException;
use App\ValueObject\Travel\TravelOrderCreateVO;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use App\ValueObject\Travel\OrderStatusVO;

interface ITravelOrderService
{
    public function getAll(array $filters = [], int $perPage): LengthAwarePaginator;
    public function findById(int $id): TravelException|Model;
    public function create(TravelOrderCreateVO $travelOrderCreateVO): Model;
    public function updateStatus(int $id, OrderStatusVO $status): Model;
}
