<?php

namespace App\Services\Travel;

use App\Exceptions\TravelException;
use App\Repositories\Travel\ITravelOrderRepository;
use App\ValueObject\Travel\TravelOrderCreateVO;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use App\ValueObject\Travel\OrderStatusVO;

class TravelOrderService implements ITravelOrderService
{
    const LIMITE_POR_PAGINA = 100;
    private ITravelOrderRepository $travelOrderRepository;
    public function __construct(ITravelOrderRepository $travelOrderRepository)
    {
        $this->travelOrderRepository = $travelOrderRepository;
    }

    public function getAll(array $filters, int $perPage): LengthAwarePaginator
    {
        $perPage = $this->validatePerPage($perPage);
        $orders = $this->travelOrderRepository->getOrders($filters, $perPage);
        return $orders;
    }
    private function validatePerPage(int $perPage): int
    {
        if ($perPage <= 0) {
            return self::LIMITE_POR_PAGINA;
        }
        if ($perPage > self::LIMITE_POR_PAGINA) {
            return self::LIMITE_POR_PAGINA;
        }
        return $perPage;
    }

    public function findById(int $id): TravelException|Model
    {
        $order = $this->travelOrderRepository->findOrderById($id);
        if (!$order) {
            throw new TravelException("Entity not found", 404);
        }
        return $order;
    }

    public function create(TravelOrderCreateVO $travelOrderCreateVO): Model
    {
        $order = $this->travelOrderRepository->createOrder($travelOrderCreateVO->toArray());
        return $order;
    }

    public function updateStatus(int $id, OrderStatusVO $status): Model
    {
        $orderModel = $this->findById($id);
        $this->validateChangeStatus($orderModel, $status);
        $order = $this->travelOrderRepository->updateOrder($orderModel->id, [
            "status" => $status->value
        ]);
        return $order;
    }

    private function validateChangeStatus($currentStatus, OrderStatusVO $newStatus): void
    {
        if ($newStatus->value == $currentStatus->status) {
            throw new TravelException("The status has already been " . strtolower($currentStatus->travelStatus->status), 400);
        }
        if ($newStatus->value == OrderStatusVO::Requested->value) {
            throw new TravelException("Unable to change status from " . strtolower($currentStatus->travelStatus->status) . " to requested", 400);
        }
        if ($currentStatus->status == OrderStatusVO::Canceled->value && $newStatus->value == OrderStatusVO::Approved->value) {
            throw new TravelException("Unable to change status from " . strtolower($currentStatus->travelStatus->status) . " to " . strtolower(OrderStatusVO::Approved->name), 400);
        }
    }
}
