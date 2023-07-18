<?php

namespace App\Http\Controllers;

use App\Models\Table;
use App\Models\Reservation;
use App\Models\WaitingList;
use Illuminate\Http\Request;

class ReservationController extends Controller
{

    //reserve table
    public function reserveTable(Request $request)
    {

        $tableId = $request->input('table_id');
        $customerId = $request->input('customer_id');
        $fromTime = $request->input('from_time');
        $toTime = $request->input('to_time');

        $table = Table::find($tableId);

        if (!$table) {
            return response()->json(['error' => 'Table not found'], 404);
        }

        $reservation = new Reservation;
        $reservation->table_id = $tableId;
        $reservation->customer_id = $customerId;
        $reservation->from_time = $fromTime;
        $reservation->to_time = $toTime;


        // Check if table is available
        $conflictingReservations = Reservation::where('table_id', $tableId)
            ->where(function ($query) use ($fromTime, $toTime) {
                $query->whereBetween('from_time', [$fromTime, $toTime])
                    ->orWhereBetween('to_time', [$fromTime, $toTime])
                    ->orWhere(function ($query) use ($fromTime, $toTime) {
                        $query->where('from_time', '<=', $fromTime)
                            ->where('to_time', '>=', $toTime);
                    });
            })->get();

        if ($conflictingReservations->count() > 0) {
            // Table is already reserved for the requested time
            // Check if the maximum capacity of tables has been reached
            if (Table::whereDoesntHave('reservations', function ($query) use ($fromTime, $toTime) {
                $query->whereBetween('from_time', [$fromTime, $toTime])
                    ->orWhereBetween('to_time', [$fromTime, $toTime])
                    ->orWhere(function ($query) use ($fromTime, $toTime) {
                        $query->where('from_time', '<=', $fromTime)
                            ->where('to_time', '>=', $toTime);
                    });
                })->count() > 0) {
                // Maximum capacity of tables has not been reached
                $reservation->save();
                return response()->json($reservation);
            } else {
                // Maximum capacity of tables has been reached
                // Add customer to waiting list
                $waitingList = new WaitingList;
                $waitingList->table_id = $tableId;
                $waitingList->customer_id = $customerId;
                $waitingList->from_time = $fromTime;
                $waitingList->to_time = $toTime;
                $waitingList->save();

                return response()->json(['message' => 'Table not available. Added to waiting list.']);
            }
        } else {
            // Table is available
            $reservation->save();
            return response()->json($reservation);
        }
    }
}
