<?php

namespace HansalScripts\Payments\Classes;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\Foundation\Application;
use HansalScripts\Payments\Classes\BaseController;
use HansalScripts\Payments\Interfaces\PaymentInterface;
use HansalScripts\Payments\Libraries\Paypal\Core\PayPalHttpClient;
use HansalScripts\Payments\Libraries\Paypal\Core\SandboxEnvironment;
use HansalScripts\Payments\Libraries\Paypal\Core\ProductionEnvironment;
use HansalScripts\Payments\Libraries\Paypal\Orders\OrdersCreateRequest;
use HansalScripts\Payments\Libraries\Paypal\Orders\OrdersCaptureRequest;

class PayPalPayment extends BaseController implements PaymentInterface
{
    private $paypal_client_id;
    private $paypal_secret;
    private $verify_route_name;
    public $paypal_mode;
    public $currency;


    public function __construct()
    {
        $this->paypal_client_id = config('laravel-payments.PAYPAL_CLIENT_ID');
        $this->paypal_secret = config('laravel-payments.PAYPAL_SECRET');
        $this->verify_route_name = config('laravel-payments.VERIFY_ROUTE_NAME');
        $this->paypal_mode = config('laravel-payments.PAYPAL_MODE');
        $this->currency = config('laravel-payments.PAYPAL_CURRENCY');
    }

    /**
     * @param $amount
     * @param null $user_id
     * @param null $user_first_name
     * @param null $user_last_name
     * @param null $user_email
     * @param null $user_phone
     * @param null $source
     * @return array|Application|RedirectResponse|Redirector
     */
    public function pay($amount = null, $user_id = null, $user_first_name = null, $user_last_name = null, $user_email = null, $user_phone = null, $source = null)
    {
        $this->setPassedVariablesToGlobal($amount,$user_id,$user_first_name,$user_last_name,$user_email,$user_phone,$source);
        $required_fields = ['amount'];
        $this->checkRequiredFields($required_fields, 'PayPal');

        if($this->paypal_mode=="live")
            $environment = new ProductionEnvironment($this->paypal_client_id, $this->paypal_secret);
        else
            $environment = new SandboxEnvironment($this->paypal_client_id, $this->paypal_secret);


        if($this->payment_id==null)
            $order_id = uniqid().rand(100000,999999);
        else
            $order_id = $this->payment_id;

        
        $client = new PayPalHttpClient($environment);

        $request = new OrdersCreateRequest();
        $request->prefer('return=representation');
        $request->body = [
            "intent" => "CAPTURE",
            "purchase_units" => [[
                "reference_id" => $order_id,
                "amount" => [
                    "value" => $this->amount,
                    "currency_code" => $this->currency
                ]
            ]],
            "application_context" => [
                "cancel_url" => route($this->verify_route_name, ['payment' => "paypal"]),
                "return_url" => route($this->verify_route_name, ['payment' => "paypal"])
            ]
        ];

        try {
            $response = json_decode(json_encode($client->execute($request)), true);
            return [
                'payment_id'=>$response['result']['id'],
                'html' => "",
                'redirect_url'=>collect($response['result']['links'])->where('rel', 'approve')->firstOrFail()['href']
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => __('laravel-payments::messages.PAYMENT_FAILED'),
                'process_data' => $e
            ];
        }
    }

    /**
     * @param Request $request
     * @return array
     */
    public function verify(Request $request): array
    {

        if($this->paypal_mode=="live")
            $environment = new ProductionEnvironment($this->paypal_client_id, $this->paypal_secret);
        else
            $environment = new SandboxEnvironment($this->paypal_client_id, $this->paypal_secret);
            
        $client = new PayPalHttpClient($environment);

        try {
            $response = $client->execute(new OrdersCaptureRequest($request['token']) );
            $result = json_decode(json_encode($response), true);
            if ($result['result']['status'] == "COMPLETED" && $result['statusCode']==201) {
                return [
                    'success' => true,
                    'payment_id'=>$request['token'],
                    'message' => __('laravel-payments::messages.PAYMENT_DONE'),
                    'process_data' => $result
                ];

            } else {
                return [
                    'success' => false,
                    'payment_id'=>$request['token'],
                    'message' => __('laravel-payments::messages.PAYMENT_FAILED'),
                    'process_data' => $result
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'payment_id'=>$request['token'],
                'message' => __('laravel-payments::messages.PAYMENT_FAILED'),
                'process_data' => $e
            ];
        }
    }
}