<?php

namespace HansalScripts\Payments\Classes;

use Illuminate\Http\Request;

use HansalScripts\Payments\Interfaces\PaymentInterface;
use HansalScripts\Payments\Classes\BaseController;
use Illuminate\Support\Facades\Http;

class BinancePayment extends BaseController implements PaymentInterface 
{
    public $binance_api;
    public $binance_secret;
    public $verify_route_name;

    public function __construct()
    {
        $this->binance_api = config('laravel-payments.BINANCE_API');
        $this->binance_secret = config('laravel-payments.BINANCE_SECRET');
        $this->verify_route_name = config('laravel-payments.VERIFY_ROUTE_NAME');
    }


    /**
     * @param $amount
     * @param null $user_id
     * @param null $user_first_name
     * @param null $user_last_name
     * @param null $user_email
     * @param null $user_phone
     * @param null $source
     * @return string[]
     * @throws MissingPaymentInfoException
     */
    public function pay($amount = null, $user_id = null, $user_first_name = null, $user_last_name = null, $user_email = null, $user_phone = null, $source = null): array
    {
        $this->setPassedVariablesToGlobal($amount,$user_id,$user_first_name,$user_last_name,$user_email,$user_phone,$source);
        $required_fields = ['amount'];
        $this->checkRequiredFields($required_fields, 'BINANCE');

        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $nonce = '';
        for($i=1; $i <= 32; $i++)
        {
            $pos = mt_rand(0, strlen($chars) - 1);
            $char = $chars[$pos];
            $nonce .= $char;
        }
        $ch = curl_init();


        if($this->payment_id==null)
            $unique_id = uniqid().rand(100000,999999);
        else
            $unique_id = $this->payment_id;

        $timestamp = round(microtime(true) * 1000);
        // Request body
        $data = array(
           "env" => [
                 "terminalType" => "APP" 
            ], 
           "merchantTradeNo" => $unique_id, 
           "orderAmount" => $this->amount, 
           "currency" => "BUSD", 
           "goods" => [
                "goodsType" => "02", 
                "goodsCategory" => "Z000", 
                "referenceGoodsId" => $unique_id, 
                "goodsName" => "Credit", 
                "goodsDetail" => "Credit" 
            ]
        ); 
     

        $json_request = json_encode($data);
        $payload = $timestamp."\n".$nonce."\n".$json_request."\n";
        $binance_pay_key = $this->binance_api;
        $binance_pay_secret = $this->binance_secret;
        $signature = strtoupper(hash_hmac('SHA512',$payload,$binance_pay_secret));
        $response = Http::withHeaders([
            "Content-Type"=>"application/json",
            "BinancePay-Timestamp"=>$timestamp,
            "BinancePay-Nonce"=>$nonce,
            "BinancePay-Certificate-SN"=>$binance_pay_key,
            "BinancePay-Signature"=>$signature,
        ])
        ->withoutVerifying()
        ->post('https://bpay.binanceapi.com/binancepay/openapi/v3/order',$data)->json();


        if($response['status']== "SUCCESS")
            return [
                'payment_id'=>$unique_id,
                'html'=>$response['errorMessage'],
                'redirect_url'=>$response['checkoutUrl']
            ];
        return [
            'payment_id'=>$unique_id,
            'html'=>$response['errorMessage'],
            'redirect_url'=>""
        ];
    }

    /**
     * @param Request $request
     * @return array|void
     */
    public function verify(Request $request)
    {

        $payload = $request->getContent();
        $signature = $request->header('X-MAC-Signature');
        $secretKey = $this->binance_secret; // Replace with your actual Binance secret key

        $computedSignature = hash_hmac('sha256', $payload, $secretKey);

        if ($signature === $computedSignature) {
            // Signature verification successful
            $data = json_decode($payload, true);
            $paymentId = $data['paymentId']; // Retrieve the payment ID
            $paymentStatus = $data['paymentStatus']; // Retrieve the payment status

            if ($paymentStatus === 'completed') {
                return [
                    'success' => true,
                    'payment_id'=>"",
                    'message' => __('laravel-payments::messages.PAYMENT_DONE'),
                    'process_data' => $request->all()
                ];
            } 
        }
        return [
            'success' => false,
            'payment_id'=>"",
            'message' => __('laravel-payments::messages.PAYMENT_FAILED'),
            'process_data' => $request->all()
        ];
    }

}