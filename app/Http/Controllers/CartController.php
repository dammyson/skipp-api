<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use App\Models\Product;
use App\Models\Cart;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\Wallet\FlutterVerificationService;
use App\Services\Wallet\VerificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CartController extends BaseController
{
    public function addToCart(Request $request)
    {
        $user = auth()->user();
    
        $validatedData = $request->validate([
            'product_id' => 'required|string|exists:products,id',
            'store_id' => 'required|string|exists:stores,id',
        ]);
    
        $product = Product::where('id', $validatedData['product_id'])
                          ->where('store_id', $validatedData['store_id'])
                          ->first();
    
        if (!$product) {
            return $this->sendError('Product not found in the specified store.', 404);
        }
    
        $cart = Cart::firstOrCreate([
            'user_id' => $user->id,
            'store_id' => $product->store_id
        ]);
    
        // Add or update the cart item
        $cartItem = $cart->items()->firstOrCreate(
            ['product_id' => $product->id],
            ['quantity' => 1, 'price' => $product->price, 'store_id' => $product->store_id]
        );
    
        if (!$cartItem->wasRecentlyCreated) {
            $cartItem->increment('quantity');
        }
    
        return $this->sendResponse([], 'Product added to cart.');
    }


    public function checkout(Request $request)
    {
        $this->validate($request, [
            'cart_id' => 'required|string',
            'mode' => 'required|string|in:card,wallet',
            'ref'      => 'required_if:mode,card|string',
            'provider'      => 'required_if:mode,card|string|in:fluterwave,paystack'
        ]);

        $user = auth()->user();

    try {
        $cart = Cart::where('user_id', $user->id)
        ->where('id', $request->cart_id)
        ->with('items')->first();


        // Validate if the cart exists and has items
        if (!$cart || $cart->items->isEmpty()) {
            return response()->json(['message' => 'Cart is empty'], 400);
        }

        $totalAmount = $cart->items->sum(function ($item) {
            return $item->quantity * $item->price;
        });


        if($request->mode == "card"){

            if($request->provider == "paystack"){
                $new_top_request = new VerificationService($request->ref);
                $verified_request = $new_top_request->run();
            }else{
                $new_top_request = new FlutterVerificationService($request->ref);
                $verified_request = $new_top_request->run();
            }
    
            if($verified_request['data']['amount'] == $totalAmount){
                return $this->processOrder( $user, $cart, $totalAmount );
            }else{
                return response()->json(['message' => 'Checkout failed', 'error' => "Amount is not equal to the total expected"], 500);
            }
          
          
        }else if($request->mode == "wallet"){
            $wallet = Wallet::where('user_id', $user->id)->first();
            if($wallet->balance >= $totalAmount){
               $wallet->topDown( $totalAmount);
                $wallet = Wallet::where('user_id', $user->id)->first();
              return  $this->processOrder( $user, $cart, $totalAmount );
            }
        }

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['message' => 'Checkout failed', 'error' => $e->getMessage()], 500);
    }

    }

    public function listCart(Request $request)
    {
        $user = auth()->user();

        // Retrieve all carts for the user with related store and items
        $carts = Cart::with(['store', 'items.product'])
            ->where('user_id', $user->id)
            ->get();

        if ($carts->isEmpty()) {
            return $this->sendError('Cart is empty', $errorMessages = [], $code = 500);
        }

        // Prepare the response
        $cartByStore = $carts->map(function ($cart) {
            return [
                'store_name' => $cart->store->name,
                'store_id'   => $cart->store->id,
                'store_logo'   => $cart->store->logo,
                'cart_id'   => $cart->id,
                'items'      => $cart->items->map(function ($item) {
                    return [
                        'product_id'   => $item->product->id,
                        'product_name' => $item->product->title,
                        'product_logo' => $item->product->logo,
                        'quantity'     => $item->quantity,
                        'price'        => $item->price,
                        'total_price'  => $item->quantity * $item->price,
                    ];
                })
            ];
        });
        return $this->sendResponse($cartByStore, 'List of carts');
    }

    public function cartDetails($id)
    {
        DB::beginTransaction();
        try {
            $cart = Cart::with('items')->findOrFail($id);
    
            // Validate if the cart exists and has items
            if (!$cart || $cart->items->isEmpty()) {
                return response()->json(['message' => 'Cart is empty'], 400);
            }
    
             // Calculate total amount
             $totalAmount = $cart->items->sum(function ($item) {
                return $item->quantity * $item->price;
            });
    

            return response()->json([
                'message'      => 'Checkout successful',
                'invoice_id'   =>  $cart,
                'totalAmount' => $totalAmount,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Checkout failed', 'error' => $e->getMessage()], 500);
        }
    }

    private function processOrder($user, $cart, $totalAmount, $fulfilMethod="Not specified" ){
        try {
 
            $invoice = Invoice::create([
                'user_id'    => $user->id,
                'store_id'   => $cart->store_id,
                'total_amount' => $totalAmount,
                'fulfilment_method' => $fulfilMethod
            ]);

            // Create Invoice Items
            foreach ($cart->items as $cartItem) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $cartItem->product_id,
                    'quantity'   => $cartItem->quantity,
                    'price'      => $cartItem->price,
                    'ful'
                ]);

                // Optionally, you can reduce product stock or mark as sold here
                $product = Product::find($cartItem->product_id);
                if ($product && $product->quantity >= $cartItem->quantity) {
                    $product->decrement('quantity', $cartItem->quantity);
                }
            }

            // Create a Transaction
            $transaction = Transaction::create([
                'user_id'      => $user->id,
                'invoice_id'   => $invoice->id,
                'total_amount' => $totalAmount,
                'status'       => 'completed',
                'wallet_balance' => 0
            ]);

            // Clear Cart
            $cart->items()->delete();
            $cart->delete();

            DB::commit();

            return response()->json([
                'message'      => 'Checkout successful',
                'invoice'   => $invoice,
                'transaction' => $transaction,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Checkout failed', 'error' => $e->getMessage()], 500);
        }
    }

    public function updateQuantity(Request $request)
    {
        $validatedData = $request->validate([
            'cart_id' => 'required|string|exists:carts,id',
            'product_id' => 'required|string|exists:products,id',
            'action' => 'required|string|in:increase,decrease',
        ]);

        try {
            $user = auth()->user();

            $cart = Cart::where('user_id', $user->id)
                ->where('id', $validatedData['cart_id'])
                ->with('items')
                ->first();

            if (!$cart) {
                return $this->sendError('Cart not found.', 404);
            }

            $cartItem = $cart->items()->where('product_id', $validatedData['product_id'])->first();

            if (!$cartItem) {
                return $this->sendError('Product not found in the cart.', 404);
            }

            $product = Product::find($validatedData['product_id']);

            if ($validatedData['action'] === 'increase') {
                if ($product->quantity > 0) {
                    $cartItem->increment('quantity');
                    $product->decrement('quantity');
                } else {
                    return $this->sendError('Insufficient product quantity in stock.', 400);
                }
            } elseif ($validatedData['action'] === 'decrease') {
                if ($cartItem->quantity > 1) {
                    $cartItem->decrement('quantity');
                    $product->increment('quantity');
                } else {
                    return $this->sendError('Quantity cannot be less than 1. Use the remove endpoint to delete the item.', 400);
                }
            }

            return $this->sendResponse([], 'Cart item quantity updated.');
        } catch (\Exception $e) {
            return $this->sendError('An error occurred while updating the quantity.', 500, $e->getMessage());
        }
    }

    public function removeItem(Request $request)
    {
        $user = auth()->user();
    
        $validatedData = $request->validate([
            'cart_id' => 'required|string|exists:carts,id',
            'product_id' => 'required|string|exists:products,id',
        ]);

        try {
            $cart = Cart::where('user_id', $user->id)
                ->where('id', $validatedData['cart_id'])
                ->with('items')
                ->first();
    
            if (!$cart) {
                return $this->sendError('Cart not found.', 404);
            }
    
            $cartItem = $cart->items()->where('product_id', $validatedData['product_id'])->first();
    
            if (!$cartItem) {
                return $this->sendError('Product not found in the cart.', 404);
            }
    
            // Restore the product quantity
            $product = Product::find($validatedData['product_id']);
            $product->increment('quantity', $cartItem->quantity);
    
            // Remove the item from the cart
            $cartItem->delete();
    
            // Optionally, delete the cart if it has no items left
            if ($cart->items()->count() === 0) {
                $cart->delete();
            }
    
            return $this->sendResponse([], 'Cart item removed successfully.');
        } catch (\Exception $e) {
            return $this->sendError('An error occurred while removing the item.', 500, $e->getMessage());
        }
    }
    

}
