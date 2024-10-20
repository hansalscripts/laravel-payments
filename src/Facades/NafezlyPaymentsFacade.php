<?php

namespace HansalScripts\Payments\Facades;

use Illuminate\Support\Facades\Facade;

class HansalScriptsPaymentsFacade extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'nafezly_payments';
    }
}