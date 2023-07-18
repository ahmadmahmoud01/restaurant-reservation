<?php

namespace App\Models;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WaitingList extends Model
{
    use HasFactory;

    protected $fillable = ['number_of_people', 'from_time'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
