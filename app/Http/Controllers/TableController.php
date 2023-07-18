<?php

namespace App\Http\Controllers;

use App\Models\Table;
use Illuminate\Http\Request;

class TableController extends Controller
{
    // check availability function
    public function checkAvailability($id, Request $request) {

        $date = $request->input('date');
        $guests = $request->input('guests');
        $table = Table::find($id);
        $table_available = false;

        // dd($guests);
        if($table->capacity < $guests) {

            return response()->json(['error' => 'Table capacity is less than guests'], 400);

        }
        $availablity = $table->whereDoesntHave('reservations', function ($query) use ($date) {
                $query->where('from_time', '<=', $date)
                    ->where('to_time', '>=', $date);
            })->get();

        foreach($availablity as $av) {
            if($av->id == $id) {
                $table_available = true;
            }
        }

        if($table_available == true) {

            return response()->json(['success' => 'Table is available'], 200);

        } else {


            return response()->json(['error' => 'Table is not available'], 400);

        }



    }
}
