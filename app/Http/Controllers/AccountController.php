<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function dashboard(Request $request): View
    {
        $user = $request->user();

        // مؤشرات سريعة
        $totalOrders      = Order::where('user_id', $user->id)->count();
        $activeOrders     = Order::where('user_id', $user->id)
                                ->whereIn('status', ['pending','processing','shipped'])
                                ->count();
        $unpaidOrders     = Order::where('user_id', $user->id)
                                ->where('payment_status','unpaid')
                                ->count();
        $totalPaidAmount  = (float) Order::where('user_id', $user->id)
                                ->where('payment_status','paid')
                                ->sum('total_amount');

        // آخر 5 طلبات
        $recentOrders = Order::with('user')
            ->where('user_id', $user->id)
            ->latest('created_at')
            ->limit(5)
            ->get();

        return view('account.index', [
            'user'           => $user,
            'totalOrders'    => $totalOrders,
            'activeOrders'   => $activeOrders,
            'unpaidOrders'   => $unpaidOrders,
            'totalPaidAmount'=> $totalPaidAmount,
            'recentOrders'   => $recentOrders,
        ]);
    }
}
