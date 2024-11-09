<?php

namespace App\Services\Travel;

use App\Repositories\Travel\IOrderStatusRepository;
use Illuminate\Database\Eloquent\Collection;

class OrderStatusService implements IOrderStatusService
{
    private IOrderStatusRepository $orderStatusRepository;
    public function __construct(IOrderStatusRepository $orderStatusRepository)
    {
        $this->orderStatusRepository = $orderStatusRepository;
    }

    public function getAll(): Collection
    {
        return $this->orderStatusRepository->getOrdersStatus();
    }
}
