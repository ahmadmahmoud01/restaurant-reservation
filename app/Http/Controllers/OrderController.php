<?php

namespace App\Http\Controllers;

use App\Models\Meal;
use App\Models\Order;
use App\Models\Table;
use App\Models\OrderItem;
use App\Models\OrderDetail;
use App\Models\Reservation;
use Illuminate\Http\Request;

class OrderController extends Controller
{

    // place order function
    public function placeOrder(Request $request) {

        $tableId = $request->input('table_id');
        $reservationId = $request->input('reservation_id');
        $customerId = $request->input('customer_id');
        $waiterId = $request->input('waiter_id');
        $meals = request()->input('meals');
        $paid = $request->input('paid');
        $date = now();



        // Get reservation details
        $reservation = Reservation::find($reservationId);


        if (!$reservation) {
            return response()->json(['error' => 'Reservation not found'], 404);
        }

        $table = Table::find($reservation->table_id);

        if (!$table) {
            return response()->json(['error' => 'Table not found'], 404);
        }

        $order = new Order;
        $order->table_id = $tableId;
        $order->reservation_id = $reservationId;
        $order->customer_id = $customerId;
        $order->waiter_id = $waiterId;
        $order->paid = $paid;
        $order->date = $date;

        $order->save();

        $total = 0;

        // Calculate total amount due and create order details
        foreach ($meals as $meal) {
            $mealId = $meal['meal_id'];
            $quantity = $meal['quantity'];

            $meal = Meal::find($mealId);

            if (!$meal) {
                return response()->json(['error' => 'Meal not found'], 404);
            }

            if ($meal->quantity_available < $quantity) {
                return response()->json(['error' => 'Insufficient quantity of meal available'], 400);
            }

            $amountToPay = $meal->price * $quantity;
            $total += $amountToPay;

            $orderDetail = new OrderDetail;
            $orderDetail->meal_id = $mealId;
            $orderDetail->order_id = $order->id;
            // $orderDetail->quantity = $quantity;
            $orderDetail->amount_to_pay = $amountToPay;


            $order->orderDetails()->save($orderDetail);

            // Update meal quantity
            $meal->quantity_available -= $quantity;
            $meal->save();
        }

         // Apply discounts
        $discount = $reservation->customer->discount;

        if ($discount > 0) {
            $discountAmount = $total * ($discount / 100);
            $total -= $discountAmount;
        }

        $order->update(['total' => $total]);

        return response()->json($order);


    }


    //checkout and print invoice
    public function getInvoice($id) {

        $order = Order::with('orderDetails.meal')->find($id);

        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        $invoice = [
            'order_id' => $order->id,
            'table_id' => $order->table_id,
            'customer_id' => $order->customer_id,
            'waiter_id' => $order->waiter_id,
            'total' => $order->total,
            'paid' => $order->paid,
            'date' => $order->date,
            'order_details' => []
        ];

        foreach ($order->orderDetails as $orderDetail) {

            $meal = $orderDetail->meal;
            $invoice['order_details'][] = [
                'meal_id' => $meal->id,
                'description' => $meal->description,
                'price' => $meal->price,
                'quantity' => $orderDetail->quantity,
                'amount_to_pay' => $orderDetail->amount_to_pay
            ];

        }

        return response()->json($invoice);

    }


}
