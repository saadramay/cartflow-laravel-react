<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    // Show products page
    public function index()
    {
        $products = Product::all();
        
        return Inertia::render('Products/Index', [
            'products' => $products
        ]);
    }

    // API endpoint to get all products
    public function getProducts()
    {
        return response()->json(Product::all());
    }

    // API endpoint to get single product
    public function show(Product $product)
    {
        return response()->json($product);
    }
}