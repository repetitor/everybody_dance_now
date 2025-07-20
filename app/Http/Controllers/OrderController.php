<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function store()
    {
        $user = new User();
        $user->setAttribute('name', 'test');
        $user->setAttribute('email', 'test@tt.uu');
        $user->setAttribute('password', 'pwd');
        $user->save();


        $order = new Order();
        $order->setAttribute('user_id', 1);
        $order->setAttribute('number', 123);
        $order->setAttribute('total_amount', 10);
        $order->save();
    }

    public function update()
    {
        $orderId = 1;

        $order = Order::find($orderId);
        $order->setAttribute('status', 'shipped');
        $order->save();

        $order = Order::find($orderId);
        $order->setAttribute('status', 'completed');
        $order->save();
    }
}
