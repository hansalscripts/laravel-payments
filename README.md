# Laravel Payment Gateways

Payment Helper of Payment Gateways ( PayPal - Paymob - Fawry - Thawani - WeAccept - Kashier - Hyperpay - Tap - Opay - Paytabs - Vodafone Cash - Orange Money - Meza Wallet - Etisalat Cash)

## Supported gateways

- [PayPal](https://paypal.com/)
- [PayMob](https://paymob.com/)
- [WeAccept](https://paymob.com/)
- [Kashier](https://kashier.io/)
- [Fawry](https://fawry.com/)
- [HyperPay](https://www.hyperpay.com/)
- [Thawani](https://thawani.om/)
- [Tap](https://www.tap.company/)
- [Opay](https://www.opaycheckout.com/)
- [Paytabs](https://site.paytabs.com/)
- [Binance](https://www.binance.com/en)
- [PerfectMoney](https://PerfectMoney.com/)
- [NowPayments](https://NowPayments.io/)
- [Payeer](https://payeer.com)
- [Telr](https://telr.com)
- [Clickpay](https://clickpay.com.sa/)
- [Coinpayments](https://www.coinpayments.net/)
- [BigPay](https://www.big-pay.com/)
- [Enot](https://enot.io/)
- [PAYCEC](https://www.paycec.com/eg-en)
- [PayPal Credit Cards](https://developer.paypal.com/docs/checkout/standard/)
- [Payrexx](https://payrexx.com/en/)
- [Creptomus](https://cryptomus.com/)
- [E Wallets (Vodafone Cash - Orange Money - Meza Wallet - Etisalat Cash)](https://paymob.com/)

## Installation

```jsx
composer require hansalscripts/laravel-payments
```

## Publish Vendor Files

```jsx
php artisan vendor:publish --tag="laravel-payments"
```

### laravel-payments.php file

```php
<?php
return [

    #PAYMOB
    'PAYMOB_API_KEY' => env('PAYMOB_API_KEY'),
    'PAYMOB_INTEGRATION_ID' => env('PAYMOB_INTEGRATION_ID'),
    'PAYMOB_IFRAME_ID' => env('PAYMOB_IFRAME_ID'),
    'PAYMOB_HMAC' => env('PAYMOB_HMAC'),
    'PAYMOB_CURRENCY'=> env('PAYMOB_CURRENCY',"EGP"),

    .....
];
```

## Web.php MUST Have Route with name “verify-payment”

```php
Route::get('/payments/verify/{payment?}',[FrontController::class,'payment_verify'])->name('verify-payment');
```

## How To Use

```jsx
use HansalScripts\Payments\Classes\PaymobPayment;

$payment = new PaymobPayment();

//pay function
$payment->pay(
	$amount, 
	$user_id = null, 
	$user_first_name = null, 
	$user_last_name = null, 
	$user_email = null, 
	$user_phone = null, 
	$source = null
);

//or use
$payment->setUserId($id)
        ->setUserFirstName($first_name)
        ->setUserLastName($last_name)
        ->setUserEmail($email)
        ->setUserPhone($phone)
        ->setCurrency($currency)
        ->setAmount($amount)
        ->pay();

//pay function response 
[
	'payment_id'=>"", // refrence code that should stored in your orders table
	'redirect_url'=>"", // redirect url available for some payment gateways
	'html'=>"" // rendered html available for some payment gateways
]

//verify function
$payment->verify($request);

//outputs
[
	'success'=>true,//or false
    'payment_id'=>"PID",
	'message'=>"Done Successfully",//message for client
	'process_data'=>""//payment response
]

```
### Factory Pattern Use
you can pass only method name without payment key word like (Fawry,Paymob,Opay ...etc) 
and the factory will return the payment instance for you , use it as you want ;)
```php
    $payment = new \HansalScripts\Payments\Factories\PaymentFactory();
    $payment=$payment->get(string $paymentName)->pay(
	$amount, 
	$user_id = null, 
	$user_first_name = null, 
	$user_last_name = null, 
	$user_email = null, 
	$user_phone = null, 
	$source = null
);;
```
## Some Test Cards

- [Thawani](https://docs.thawani.om/docs/thawani-ecommerce-api/ZG9jOjEyMTU2Mjc3-thawani-test-card)
- [Kashier](https://developers.kashier.io/payment/testing)
- [Paymob](https://docs.paymob.com/docs/card-payments)
- [Fawry](https://developer.fawrystaging.com/docs/testing/testing)
- [Tap](https://developers.tap.company/reference/testing-cards)
- [Opay](https://doc.opaycheckout.com/end-to-end-testing)
- [PayTabs](https://support.paytabs.com/en/support/solutions/articles/60000712315-what-are-the-test-cards-available-to-perform-payments-)

