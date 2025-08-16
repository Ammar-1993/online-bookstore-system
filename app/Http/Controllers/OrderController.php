<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(): View
    {



        $orders = auth()->user()
            ->orders()
            ->latest()
            ->with('items')   // حمل العناصر مسبقًا
            ->paginate(10);


        $orders = Auth::user()->orders()->latest()->with('items')->paginate(10);
        return view('orders.index', compact('orders'));
    }

    public function show(Order $order): View
    {
        $this->authorize('view', $order);
        $order->load(['items.book', 'user']);
        return view('orders.show', compact('order'));
    }

    public function invoice(Order $order): View
    {
        $this->authorize('view', $order);
        $order->load(['items.book', 'user']);
        return view('orders.invoice', compact('order'));
    }
}
