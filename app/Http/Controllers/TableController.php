<?php

namespace App\Http\Controllers;

use App\Models\Table;
use Illuminate\Http\Request;

class TableController extends Controller
{
    // check availability function
    public function checkAvailability(Request $request) {

        $date = $request->input('date');
        $guests = $request->input('guests');

        // dd($guests);

        $availableTables = Table::where('capacity', '>=', $guests)
            ->whereDoesntHave('reservations', function ($query) use ($date) {
                $query->where('from_time', '<=', $date.' 23:59:59')
                    ->where('to_time', '>=', $date.' 00:00:00');
            })
            ->get();

        if($availableTables) {

            return response()->json($availableTables);
        }

        return response()->json(['error' => 'No table available'], 404);


    }
}
