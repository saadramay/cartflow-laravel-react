<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\DailySalesReport;

class SendDailySalesReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
        //
    }

    public function handle(): void
    {
        // Get today's orders
        $orders = Order::whereDate('created_at', today())
            ->with('orderItems.product', 'user')
            ->get();

        // Calculate totals
        $totalOrders = $orders->count();
        $totalRevenue = $orders->sum('total');
        $totalItems = $orders->sum(function ($order) {
            return $order->orderItems->sum('quantity');
        });

        // Get admin user
        $admin = User::where('email', 'admin@cartflow.com')->first();

        if ($admin && $totalOrders > 0) {
            Mail::to($admin->email)->send(
                new DailySalesReport($orders, $totalOrders, $totalRevenue, $totalItems)
            );
        }
    }
}