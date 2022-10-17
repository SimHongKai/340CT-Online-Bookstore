<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Auth;
use DB;

class OrderController extends Controller
{
    /**
     * Show User's Profile Page
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function orderHistoryView()
    {
        $orders = DB::table('orders')
                ->orderBy('created_at', 'DESC')
                ->paginate(10);
        return view('order.orderHistory')->with(compact('orders'));
    }

    /**
     * Show User's Profile Page
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function orderDetailsView(Request $request)
    {
        $order = Order::find($request->orderID);
        $orderItems = DB::table('orderitem')
                    ->select('orderitem.qty', 'orderitem.ISBN13', 'stock.coverImg', 'stock.bookTitle', 'stock.retailPrice')
                    ->join('stock', 'orderitem.ISBN13', '=', 'stock.ISBN13')
                    ->where('orderitem.orderID', '=', $order->orderID)
                    ->get();
        return view('order.orderDetails')->with(compact('order', 'orderItems'));
    }
}
