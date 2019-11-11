<?php

namespace App\Providers;

use App\Services\Contracts\RatesImporter;
use Illuminate\Support\ServiceProvider;
use App\Services\FloatRatesImporter;
use App\Services\FxRatesImporter;

class CurrencyExchangeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->resolveRatesImporter();
    }

    private function resolveRatesImporter()
    {
        $concrete = FxRatesImporter::class;
        if (config('currency.provider') === 'FloatRates') {
            $concrete = FloatRatesImporter::class;
        }

        app()->bind(RatesImporter::class, $concrete);
    }
}
