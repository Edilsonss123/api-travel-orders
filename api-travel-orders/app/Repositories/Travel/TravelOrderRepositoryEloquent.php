<?php

namespace App\Repositories\Travel;

use App\Models\Travel\TravelOrder;
use App\Repositories\Repository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class TravelOrderRepositoryEloquent extends Repository implements ITravelOrderRepository
{
    protected function model(): TravelOrder
    {
        return new TravelOrder();
    }

    public function getOrders(array $filters): Collection
    {
        $orders = $this->model->select([
            "travel_orders.id",
            "travel_orders.travelerName",
            "travel_orders.destination",
            "travel_orders.departureDate",
            "travel_orders.returnDate",
            "travel_orders.status",
            "order_status.status as statusDescription",
            "travel_orders.created_at"
        ])
        ->join("order_status", "order_status.id", "=", "travel_orders.status")
        ->when(!empty($filters["status"]), function ($where) use ($filters) {
            $where->where("status", $filters["status"]);
        })
        ->orderby("travel_orders.created_at")
        ->orderby("travel_orders.status")
        ->orderby("travel_orders.destination")
        ->get();
        return $orders;
    }

    public function findOrderById($id): null|Model
    {
        return $this->model::find($id);
    }

    public function createOrder(array $data): Model
    {
        return $this->model::create($data);
    }

    public function updateOrder($id, array $data): Model
    {
        $order = $this->model::findOrFail($id);
        $order->update($data);
        return $order;
    }
}
