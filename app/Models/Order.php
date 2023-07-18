<?php

namespace App\Models;

use App\Models\Meal;
use App\Models\Table;
use App\Models\Customer;
use App\Models\OrderDetail;
use App\Models\Reservation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['total', 'paid', 'date'];

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function waiter()
    {
        return $this->belongsTo(Waiter::class);
    }

    public function meals()
    {
        return $this->belongsToMany(Meal::class, 'order_details')
            ->using(OrderDetail::class)
            ->withPivot('amount_to_pay');
    }

    public function orderDetails() {
        return $this->hasMany(OrderDetail::class);
    }
}
