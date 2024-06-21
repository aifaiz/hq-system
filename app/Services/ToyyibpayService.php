<?php 

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ToyyibpayService
{
    private $secret;
    private $categoryID;
    private $url;

    public function __construct($catID, $secret)
    {
        $this->categoryID = $catID;
        $this->secret = $secret;
        $this->url = 'https://toyyibpay.com';

        $env = config('app.env');
        if($env != 'production'):
            $this->url = 'https://dev.toyyibpay.com';
        endif;
    }

    // agent cart pay order
    public function createBill($order)
    {
        $returnURL = route('toyyibpay.agent.order.return');
        $callbackURL = route('toyyibpay.order.callback');

        $params = [
            'userSecretKey' => $this->secret,
            'categoryCode' => $this->categoryID,
            'billName' => config('app.name'),
            'billDescription' => 'Online order',
            'billPriceSetting' => 1,
            'billPayorInfo' => 1,
            'billAmount' => $this->prepareTotal($order->grand_total),
            'billReturnUrl' => $returnURL,
            'billCallbackUrl' => $callbackURL,
            'billExternalReferenceNo' => str_pad($order->id,6,STR_PAD_LEFT),
            'billTo' => $order->customer_name,
            'billEmail' => $order->customer_email,
            'billPhone' => $order->customer_phone,
            'billSplitPayment' => 0,
            'billSplitPaymentArgs' => '',
            'billPaymentChannel' => '0',
            'billContentEmail' => '',
            'billChargeToCustomer' => 0,
            'billExpiryDate' => '',
            'billExpiryDays' => 3,
        ];

        try{

            $url = $this->url.'/index.php/api/createBill';
            $response = Http::asForm()->post($url, $params);

            $re = $response->object();//json_decode();

            $log = json_encode($response->json());
            Log::debug('pay response: '. $log);

            if (isset($re[0]->BillCode) && ! empty(isset($re[0]->BillCode))) {
                $order->fpx_ref = $re[0]->BillCode;
                $order->save();

                $data = [
                    'bill'=>$re[0]->BillCode,
                    'url'=> $this->url.'/'.$re[0]->BillCode
                ];

                return $data;
            }

        }catch(Exception $e){
            Log::debug('could not create bill '. $e->getMessage());
        }

        return false;
    }

    private function prepareTotal($total)
    {
        $ori = str_replace('.', '', $total);

        $cents = bcmul($ori, 1);
        return $cents;
    }

    public function getBillStatus($billCode)
    {
        // Log::debug('billcode: '. $billCode);
        $data = [
            'billCode'=>$billCode,
            // 'billpaymentStatus'=>'1'
        ];

        $response = Http::asForm()->post($this->url.'/index.php/api/getBillTransactions', $data);

        // Log::info('toyyibpay check transaction'. json_encode($response->json()));
        $obj = $response->object();
        
        return $obj[0] ?? null;
    }
}