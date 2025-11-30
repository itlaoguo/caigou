<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Modules\Purchase\Models\PurchaseOrder;

class submitPurchaseOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:submit-purchase-order';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $allNotSubmitPurchaseOrders = DB::table('purchase_order')->where('status',0)->get()->toArray();

        if(!empty($allNotSubmitPurchaseOrders)) {

            $purchaseOrder = new PurchaseOrder();

            foreach ($allNotSubmitPurchaseOrders as $notSubmitPurchaseOrder) {

                $parameters = [
//                    'id'=>$notSubmitPurchaseOrder->id,
                    'id'=>'202411'.$notSubmitPurchaseOrder->id,
                    'product_id'=>$notSubmitPurchaseOrder->product_id,
                    'product_title'=>$notSubmitPurchaseOrder->product_title,
                    'sku_id'=>$notSubmitPurchaseOrder->sku_id,
                    'sku_title'=>$notSubmitPurchaseOrder->sku_title,
                    'price'=>$notSubmitPurchaseOrder->price,
                    'product_pic_url'=>$notSubmitPurchaseOrder->product_pic_url,
                    'purchaser_id'=>$notSubmitPurchaseOrder->purchaser_id,
                    'quantity'=>$notSubmitPurchaseOrder->quantity,
                    'address_detail'=>$notSubmitPurchaseOrder->address_detail,
                    'receiver'=>$notSubmitPurchaseOrder->receiver,
                    'receiver_phone'=>$notSubmitPurchaseOrder->receiver_phone
                ];

                try {

                    $result = $purchaseOrder->createPurchaseOrder($parameters);
                    $purchaseOrderId = $result['purchaseOrderId'];

                    DB::table('purchase_order')
                        ->where('id', $notSubmitPurchaseOrder->id)
                        ->update([
                            'purchase_order_id' => $purchaseOrderId,
                            'status' => 1,
                        ]);

                }catch (\Exception $e) {

                    DB::table('purchase_order')
                        ->where('id', $notSubmitPurchaseOrder->id)
                        ->update([
                            'error' => $e->getMessage(),
                            'status' => 2,
                        ]);
                }

            }

        }


    }
}
