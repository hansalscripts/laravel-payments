<?php

namespace HansalScripts\Payments;

use Illuminate\Support\ServiceProvider;
use HansalScripts\Payments\Classes\FawryPayment;
use HansalScripts\Payments\Classes\HyperPayPayment;
use HansalScripts\Payments\Classes\KashierPayment;
use HansalScripts\Payments\Classes\PaymobPayment;
use HansalScripts\Payments\Classes\PayPalPayment;
use HansalScripts\Payments\Classes\PaytabsPayment;
use HansalScripts\Payments\Classes\ThawaniPayment;
use HansalScripts\Payments\Classes\TapPayment;
use HansalScripts\Payments\Classes\OpayPayment;
use HansalScripts\Payments\Classes\PaymobWalletPayment;

class PaymentsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    {
        $this->configure();

        $langPath = 'vendor/payments';
        $langPath = (function_exists('lang_path'))
            ? lang_path($langPath)
            : resource_path('lang/' . $langPath);

        $this->registerPublishing($langPath);

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'laravel-payments');




        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/laravel-payments'),
            __DIR__ . '/../config/laravel-payments.php' => config_path('laravel-payments.php'),
            __DIR__ . '/../resources/lang' => $langPath,
        ], 'laravel-all');

        $this->registerTranslations($langPath);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

        $this->app->bind(PaymobPayment::class, function () {
            return new PaymobPayment();
        });
        $this->app->bind(FawryPayment::class, function () {
            return new FawryPayment();
        });
        $this->app->bind(ThawaniPayment::class, function () {
            return new ThawaniPayment();
        });
        $this->app->bind(PaypalPayment::class, function () {
            return new PaypalPayment();
        });
        $this->app->bind(HyperPayPayment::class, function () {
            return new HyperPayPayment();
        });
        $this->app->bind(KashierPayment::class, function () {
            return new KashierPayment();
        });
        $this->app->bind(TapPayment::class, function () {
            return new TapPayment();
        });
        $this->app->bind(OpayPayment::class, function () {
            return new OpayPayment();
        });
        $this->app->bind(PaymobWalletPayment::class, function () {
            return new PaymobWalletPayment();
        });
        $this->app->bind(PaytabsPayment::class, function () {
            return new PaytabsPayment();
        });
    }

    /**
     * Setup the configuration for HansalScripts Payments.
     *
     * @return void
     */
    protected function configure()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/laravel-payments.php',
            'laravel-payments'
        );
    }
    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations($langPath)
    {
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'laravel-payments');
        $this->loadTranslationsFrom($langPath, 'laravel-payments');
    }
    /**
     * Register the package's publishable resources.
     *
     * @return void
     */
    protected function registerPublishing($langPath)
    {
        $this->publishes([
            __DIR__ . '/../config/laravel-payments.php' => config_path('laravel-payments.php'),
        ], 'laravel-payments');

        $this->publishes([
            __DIR__ . '/../resources/lang' => $langPath,
        ], 'laravel-payments');
        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor'),
        ], 'laravel-payments');
    }
}
