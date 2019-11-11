<?php
/**
 * Created by PhpStorm.
 * User: Marius
 * Date: 2019-11-08
 * Time: 17:58
 */

namespace App\Services;

use App\Services\Contracts\RatesImporter;
use App\Repositories\CurrencyRepository;

class FxRatesImporter implements RatesImporter
{
    private $currencyRepository;

    public function __construct(CurrencyRepository $currencyRepository)
    {
        $this->currencyRepository = $currencyRepository;
    }

    public function import(string $baseCurrency = 'gbp'): bool
    {
        $fileName = 'http://'.$baseCurrency.'.fxexchangerate.com/rss.xml';
        $fileContents = file_get_contents($fileName);
        if (!$fileContents) {
            echo 'unable to read currency rates from '.$fileName.'. Make sure file exists or
                try again later or use another currency exchange provider';
            return false;
        }

        $data = simplexml_load_string($fileContents);
        if (!$data || empty($data->channel->item)) {
            echo 'Bad XML format. Consider inform Currency exchange provider or use another method for XML parsing.';
            return false;
        }

        $this->currencyRepository->saveFxCurrencies($data->channel->item);

        return true;
    }
}
