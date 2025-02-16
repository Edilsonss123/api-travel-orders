<?php

namespace App\Models\Travel;

use Illuminate\Database\Eloquent\Model;
use Venturecraft\Revisionable\RevisionableTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class TravelOrder extends Model
{
    use RevisionableTrait;
    use HasFactory;

    protected $table = 'travel_orders';
    protected $fillable = [
        'travelerName', 'destination', 'departureDate', 'returnDate', 'status'
    ];
    protected $revisionCreationsEnabled = true;
    protected $dontRevision = ['created_at', 'updated_at'];

    public function travelStatus()
    {
        return $this->belongsTo(OrderStatus::class, 'status', 'id');
    }
}
