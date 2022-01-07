<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\DetailTransaction;
use App\Services\Midtrans\CreateSnapTokenService;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return response()->json([
            "message"=>"success",
            "data"=>Transaction::with('user')->where('user_id',$request->user()->id)->get()
        ],200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'items'=>'array',
            'transaction_shipping_cost'=>'required|numeric'
        ]);
        $total = 0;
        foreach($request->items as $item){
            $product = Product::find($item['product_id']);
            $total += ($product->product_price - ($product->product_price * $product->discount /100)) * $item['product_amount'];
        }
        // return $total;
        $lastId = 0;
        $trans = Transaction::orderBy('id','DESC')->get()->first();
        if($trans){
            $lastId = $trans->id;
        }

        // return $lastId;
        $midtrans = new CreateSnapTokenService($lastId+1,$total,$request->transaction_shipping_cost,$request->items,$request->user());
        try {
            $transaction = Transaction::create([
                'user_id'=>$request->user()->id,
                'transaction_total_price'=>$total,
                'transaction_shipping_cost'=>$request->transaction_shipping_cost,
                'transaction_order_status'=>1,
                'transaction_payment_url'=>$midtrans->getSnapToken()
            ]);
            foreach($request->items as $item){
                $product = Product::find($item['product_id']);
                DetailTransaction::create([
                    'detail_transaction_amount'=>$item['product_amount'],
                    'detail_transaction_sub_total'=>($product->product_price - ($product->product_price * $product->discount /100)) * $item['product_amount'],
                    'transaction_id'=>$transaction->id,
                    'product_id'=>$item['product_id'],
                    'detail_transaction_discount'=>$product->product_discount
                ]);
            }
            return response()->json([
                'message'=>"success",
                'data'=>Transaction::with('detail_transactions')->find($transaction->id)
            ],201);
        } catch (\Throwable $th) {
            return $th;
            return response()->json([
                'message'=>"failed"
            ],400);
        }
    }
    public function dummyStore()
    {
        try {
            $transaction = Transaction::create([
                'user_id'=>1,
                'transaction_total_price'=>50000,
                'transaction_shipping_cost'=>10000,
                'transaction_order_status'=>1,
                'transaction_payment_url'=>'dummy'
            ]);
            DetailTransaction::create([
                'detail_transaction_amount'=>10,
                'detail_transaction_sub_total'=>40000,
                'transaction_id'=>$transaction->id,
                'product_id'=>Product::first()->id,
                'detail_transaction_discount'=>1
            ]);
            return response()->json([
                'message'=>"success",
                'data'=>Transaction::with('detail_transactions')->find($transaction->id)
            ],201);
        } catch (\Throwable $th) {
            // return $th;
            return response()->json([
                'message'=>"failed"
            ],400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function show(Transaction $transaction)
    {
        $transaksi = Transaction::with(['detail_transactions','user'])->find($transaction->id);
        $temp = [];
        foreach($transaksi->detail_transactions as $product){
            $temp[] = DetailTransaction::with('product')->find($product->id);
        }
        $transaksi['products'] = $temp;
        return response()->json([
            "message"=>"success",
            "data"=>$transaksi
        ],200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function edit(Transaction $transaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function updatePesanan(Request $request, Transaction $transaction)
    {
        $request->validate([
            'transaction_order_status'=>'required'
        ]);
        try {
            $transaction->update([
                'transaction_order_status'=>$request->transaction_order_status
            ]);
            return response()->json([
                'message'=>'Berhasil update status pesanan',
                'data'=>Transaction::with('detail_transactions')->find($transaction->id)
            ],200);
        } catch (\Throwable $th) {
            // return $th;
            return response()->json([
                'message'=>'gagal update status pesanan',
            ],400);
        }
    }
    public function updatePembayaran()
    {
        $id = $_GET['order_id'];
        $transaksi = Transaction::with('detail_transactions')->findOrFail($id);
        $transaksi->update([
            'transaction_is_paid'=>true
        ]);
        // return $transaksi;
        foreach($transaksi->detail_transactions as $product){
            $store = Store::find(
                Product::find($product->product_id)->store_id
            );
            // return $store;
            $store->update([
                'store_balance'=>$store->store_balance + $product->detail_transaction_sub_total,
                'store_total_revenue'=>$store->store_total_revenue + $product->detail_transaction_sub_total,
            ]);
        }
        return redirect(env('DOMAIN'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function destroy(Transaction $transaction)
    {
        //
    }
}
