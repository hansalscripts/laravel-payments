<?php

namespace HansalScripts\Payments\Libraries\Paypal\Core;

class SandboxEnvironment extends PayPalEnvironment
{
    public function __construct($clientId, $clientSecret)
    {
        parent::__construct($clientId, $clientSecret);
    }

    public function baseUrl()
    {
        return "https://api.sandbox.paypal.com";
    }
}
