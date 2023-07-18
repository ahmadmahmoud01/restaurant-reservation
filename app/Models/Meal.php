<?php

namespace App\Models;

use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Meal extends Model
{
    use HasFactory;

    protected $fillable = ['price', 'description', 'quantity_available', 'discount'];

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_details')
            ->using(OrderDetail::class)
            ->withPivot('amount_to_pay');
    }
}
