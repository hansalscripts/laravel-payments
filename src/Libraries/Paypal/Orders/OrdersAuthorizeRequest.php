<?php

// This class was generated on Wed, 01 Aug 2018 16:35:39 PDT by version 0.1.0-dev+0ee05a-dirty of Braintree SDK Generator
// OrdersAuthorizeRequest.php
// @version 0.1.0-dev+0ee05a-dirty
// @type request
// @data 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
// DO NOT EDIT

namespace HansalScripts\Libraries\Paypal\Orders;

use PayPalHttp\HttpRequest;

class OrdersAuthorizeRequest extends HttpRequest
{
    function __construct($orderId)
    {
        parent::__construct("/v2/checkout/orders/{order_id}/authorize?", "POST");

        $this->path = str_replace("{order_id}", urlencode($orderId), $this->path);
        $this->headers["Content-Type"] = "application/json";
    }


    public function payPalClientMetadataId($payPalClientMetadataId)
    {
        $this->headers["PayPal-Client-Metadata-Id"] = $payPalClientMetadataId;
    }
    public function payPalRequestId($payPalRequestId)
    {
        $this->headers["PayPal-Request-Id"] = $payPalRequestId;
    }
    public function prefer($prefer)
    {
        $this->headers["Prefer"] = $prefer;
    }
}
