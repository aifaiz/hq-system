<?php
namespace App\Services;

use App\Helpers\SettingsHelper;
use App\Models\AgentUser;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Log;

class OrderService
{
    public function processOrder($agentID, $distributorID, $items, $customerDetails)
    {
        // process order
        // Log::debug('items', ['aid'=>$agentID,'did'=>$distributorID,'items'=>$items]);return '#';
        $baseComm = AgentUser::find($agentID)->value('comm_amount');

        $settings = SettingsHelper::getDistributorSettings($distributorID);
        $deliveryPrice = $settings['DELIVERY_PRICE'];
        $subTotal = 0;
        $grandTotal = 0;
        $agentComm = 0;

        $order = new Order;
        $order->agent_id = $agentID;
        $order->distributor_id = $distributorID;
        $order->delivery_price = $deliveryPrice;
        $order->customer_name = $customerDetails['name'];
        $order->customer_phone = $customerDetails['phone'];
        $order->customer_email = $customerDetails['email'];
        $order->address = $customerDetails['address'];
        $order->save();

        $orderID = $order->id;

        foreach($items as $item):
            $orderItem = new OrderItem;
            $orderItem->order_id = $orderID;
            $orderItem->product_id = $item['id'];
            $orderItem->qty = $item['qty'];
            $orderItem->amount = $item['total'];
            $orderItem->save();

            $subTotal = $subTotal + $item['total'];
            $commission = $item['qty'] * $baseComm;
            $agentComm = $agentComm + $commission;
        endforeach;

        $grandTotal = $subTotal + $deliveryPrice;

        $order->sub_total = $subTotal;
        $order->grand_total = $grandTotal;
        $order->agent_comm = $agentComm;
        $order->save();

        $freshOrder = $order->fresh();

        $toyyibPay = new ToyyibpayService($settings['TOYYIBPAY_CATEGORY'], $settings['TOYYIBPAY_SECRET']);
        $response = $toyyibPay->createBill($freshOrder);

        if($response != false):
            $freshOrder->fpx_ref = $response['bill'];
            $freshOrder->save();
            return $response['url'];
        endif;

        return false;
    }
}