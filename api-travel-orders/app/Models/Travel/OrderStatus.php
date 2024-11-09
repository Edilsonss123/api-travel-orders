<?php

namespace App\Models\Travel;

use Illuminate\Database\Eloquent\Model;

class OrderStatus extends Model
{
    protected $table = 'order_status';

    protected $fillable = ['status'];

    public function travelOrders()
    {
        return $this->hasMany(TravelOrder::class);
    }
}
