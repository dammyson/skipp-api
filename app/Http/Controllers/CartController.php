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
use App\Services\Wallet\VerificationService;
use Illuminate\Support\Facades\DB;
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
            ['quantity' => 1, 'price' => $product->price,  'store_id' => $product->store_id],
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
            'ref'      => 'required_if:mode,card|string'
        ]);

        $user = auth()->user();

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
            $new_top_request = new VerificationService($request->ref);
            $verified_request = $new_top_request->run();
    
            if($verified_request['data']['amount'] == $totalAmount){
                $this->processOrder( $user, $cart, $totalAmount );
            }else{

            }
          
          
        }else if($request->mode == "wallet"){

            $wallet = Wallet::where('user_id', $user->id)->first();
            if($wallet->balance >= $totalAmount){
               $wallet->topDown( $totalAmount);
                $wallet = Wallet::where('user_id', $user->id)->first();
                $this->processOrder( $user, $cart, $totalAmount );
            }else{

            }
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
                'cart_id'   => $cart->id,
                'items'      => $cart->items->map(function ($item) {
                    return [
                        'product_id'   => $item->product->id,
                        'product_name' => $item->product->title,
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

    private function processOrder($user, $cart, $totalAmount ){
        try {
 
            $invoice = Invoice::create([
                'user_id'    => $user->id,
                'store_id'   => $cart->store_id,
                'total_amount' => $totalAmount,
            ]);

            // Create Invoice Items
            foreach ($cart->items as $cartItem) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $cartItem->product_id,
                    'quantity'   => $cartItem->quantity,
                    'price'      => $cartItem->price,
                ]);

                // Optionally, you can reduce product stock or mark as sold here
                $product = Product::find($cartItem->product_id);
                if ($product && $product->stock >= $cartItem->quantity) {
                    $product->decrement('stock', $cartItem->quantity);
                }
            }

            // Create a Transaction
            $transaction = Transaction::create([
                'user_id'      => $user->id,
                'invoice_id'   => $invoice->id,
                'total_amount' => $totalAmount,
                'status'       => 'completed',
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
}
