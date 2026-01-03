<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function checkout(Request $request)
    {
        $user = Auth::user();
        $cartItems = $user->cartItems()->with('product')->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'message' => 'Cart is empty'
            ], 400);
        }

        DB::beginTransaction();

        try {
            // Calculate total
            $total = $cartItems->sum(function ($item) {
                return $item->quantity * $item->product->price;
            });

            // Create order
            $order = Order::create([
                'user_id' => $user->id,
                'total' => $total,
                'status' => 'completed',
            ]);

            // Create order items and reduce stock
            foreach ($cartItems as $cartItem) {
                $product = $cartItem->product;

                // Check stock
                if ($product->stock_quantity < $cartItem->quantity) {
                    DB::rollBack();
                    return response()->json([
                        'message' => "Not enough stock for {$product->name}"
                    ], 400);
                }

                // Create order item
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $cartItem->quantity,
                    'price' => $product->price, // Store current price
                ]);

                // Reduce stock
                $product->decrement('stock_quantity', $cartItem->quantity);
            }

            // Clear cart
            $user->cartItems()->delete();

            DB::commit();

            return response()->json([
                'message' => 'Order placed successfully',
                'order' => $order->load('orderItems.product')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'message' => 'Checkout failed: ' . $e->getMessage()
            ], 500);
        }
    }
}