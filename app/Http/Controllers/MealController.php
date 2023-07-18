<?php

namespace App\Http\Controllers;

use App\Models\Meal;
use Illuminate\Http\Request;

class MealController extends Controller
{
    public function index() {

        $meals = Meal::where('quantity_available', '>', 0)->get();

        return response()->json($meals);

    }
}
