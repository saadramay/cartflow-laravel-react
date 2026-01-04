<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Jobs\SendLowStockNotification;

class CartController extends Controller
{
    // Show cart page
    public function index()
    {
        $cartItems = Auth::user()
            ->cartItems()
            ->with('product')
            ->get();

        $total = $cartItems->sum(function ($item) {
            return $item->quantity * $item->product->price;
        });

        return Inertia::render('Cart/Index', [
            'cartItems' => $cartItems,
            'total' => $total
        ]);
    }

    // Get cart items (API)
    public function getCartItems()
    {
        $cartItems = Auth::user()
            ->cartItems()
            ->with('product')
            ->get();

        $total = $cartItems->sum(function ($item) {
            return $item->quantity * $item->product->price;
        });

        return response()->json([
            'cartItems' => $cartItems,
            'total' => $total
        ]);
    }

    // Add to cart
    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'integer|min:1'
        ]);

        $product = Product::findOrFail($request->product_id);
        $quantity = $request->quantity ?? 1;

        // Check if enough stock
        if ($product->stock_quantity < $quantity) {
            return response()->json([
                'message' => 'Not enough stock available'
            ], 400);
        }

        // Find or create cart item
        $cartItem = CartItem::where('user_id', Auth::id())
            ->where('product_id', $product->id)
            ->first();

        if ($cartItem) {
            // Update quantity
            $newQuantity = $cartItem->quantity + $quantity;
            
            if ($product->stock_quantity < $newQuantity) {
                return response()->json([
                    'message' => 'Not enough stock available'
                ], 400);
            }

            $cartItem->update(['quantity' => $newQuantity]);
        } else {
            // Create new cart item
            $cartItem = CartItem::create([
                'user_id' => Auth::id(),
                'product_id' => $product->id,
                'quantity' => $quantity,
            ]);
        }

        return response()->json([
            'message' => 'Product added to cart',
            'cartItem' => $cartItem->load('product')
        ]);
    }

    // Update cart item quantity
    public function updateQuantity(Request $request, CartItem $cartItem)
    {
        // Ensure cart item belongs to authenticated user
        if ($cartItem->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $product = $cartItem->product;

        // Check stock
        if ($product->stock_quantity < $request->quantity) {
            return response()->json([
                'message' => 'Not enough stock available'
            ], 400);
        }

        $cartItem->update(['quantity' => $request->quantity]);

        return response()->json([
            'message' => 'Cart updated',
            'cartItem' => $cartItem->load('product')
        ]);
    }

    // Remove from cart
    public function removeFromCart(CartItem $cartItem)
    {
        // Ensure cart item belongs to authenticated user
        if ($cartItem->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $cartItem->delete();

        return response()->json([
            'message' => 'Item removed from cart'
        ]);
    }

    // Clear entire cart
    public function clearCart()
    {
        Auth::user()->cartItems()->delete();

        return response()->json([
            'message' => 'Cart cleared'
        ]);
    }
}