<?php

namespace App\Services\Midtrans;

use Midtrans\Snap;
use App\Models\Product;

class CreateSnapTokenService extends Midtrans
{
    protected $order;

    public function __construct($id, $total, $ongkir, $items, $user)
    {
        parent::__construct();

        $this->id = $id;
        $this->total = $total;
        $this->ongkir = $ongkir;
        $this->items = $items;
        $this->user = $user;
    }

    public function getSnapToken()
    {
        $items = [
                [
                    'id'=>1,
                    'price'=>$this->ongkir,
                    'quantity'=>1,
                    'name'=>'Ongkos Kirim'
                ]
            ];
        foreach($this->items as $item){
            $id = 2;
            $product = Product::find($item['product_id']);
            $items[] = [
                'id'=>$id,
                'price'=>$product->product_price,
                'quantity'=>$item['product_amount'],
                'name'=>$product->product_name
            ];
            $id++;
        }
        $params = [
            'transaction_details' => [
                'order_id' => $this->id,
                'gross_amount' => $this->total,
            ],
            'item_details' => $items,
            'customer_details' => [
                'first_name' => $this->user->fullname,
                'email' => $this->user->email,
                'phone' => $this->user->handphone,
            ]
        ];

        $snapToken = Snap::getSnapUrl($params);

        return $snapToken;
    }
}
