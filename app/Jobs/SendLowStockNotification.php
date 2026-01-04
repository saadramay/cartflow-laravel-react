<?php

namespace App\Jobs;

use App\Models\Product;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\LowStockAlert;
use Illuminate\Support\Facades\Cache;

class SendLowStockNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $product;

    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    public function handle(): void
    {
        // Prevent duplicate notifications within 1 hour
        $cacheKey = 'low_stock_notified_' . $this->product->id;

        if (Cache::has($cacheKey)) {
            return;
        }

        // Get admin user
        $admin = User::where('email', 'admin@cartflow.com')->first();

        if ($admin) {
            Mail::to($admin->email)->send(new LowStockAlert($this->product));
        }
    }
}
