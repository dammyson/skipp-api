<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use App\Models\Product;
use App\Models\Cart;
use Illuminate\Support\Facades\Validator;

class CartController extends BaseController
{
    public function addToCart(Request $request)
    {
        $user = auth()->user();

        $product = Product::find($request->product_id);
;
        $cart = Cart::firstOrCreate([
            'user_id' => $user->id,
            'store_id' => $product->store_id
        ]);

        // Add or update the cart item
        $cartItem = $cart->items()->firstOrCreate(
            ['product_id' => $product->id],
            ['quantity' => 1, 'price' => $product->price]
        );

        if (!$cartItem->wasRecentlyCreated) {
            $cartItem->increment('quantity');
        }

        return response()->json(['message' => 'Product added to cart']);
    }
}
