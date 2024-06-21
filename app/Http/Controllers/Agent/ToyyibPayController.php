<?php

namespace App\Http\Controllers\Agent;

use App\Helpers\SettingsHelper;
use App\Http\Controllers\Controller;
use App\Models\DistributorProductQty;
use App\Models\Order;
use App\Services\StockService;
use App\Services\ToyyibpayService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ToyyibPayController extends Controller
{

    public function validatePayment(Request $re)
    {
        $billCode = $re->billcode;

        $order = Order::with(['items','agent'])->where('fpx_ref', $billCode)->first();
        $distributorID = $order->distributor_id;
        $settings = SettingsHelper::getDistributorSettings($distributorID);
        $toyyibPay = new ToyyibpayService($settings['TOYYIBPAY_CATEGORY'],$settings['TOYYIBPAY_SECRET']);
        $response = $toyyibPay->getBillStatus($billCode);

        if($response != null):
            $payStatus = $response->billpaymentStatus;
            if($payStatus == '1'):
                $order->pay_status = 'PAID';
                $order->pay_at = Carbon::now()->format('Y-m-d H:i:s');
                $order->save();

                // deduct stock
                (new StockService)->deductStock($order->distributor_id, $order->items);

            endif;

            return redirect(route('agent.order.completed', ['refcode'=>$order->agent->refcode,'billCode'=>$billCode]));
        endif;

        // dd($billCode, ['pay_status'=>$response->billpaymentStatus],$response);
    }

    public function validateCallback(Request $re)
    {

        echo 'OK';exit;
    }
}
