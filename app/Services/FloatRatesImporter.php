<?php
/**
 * Created by PhpStorm.
 * User: Marius
 * Date: 2019-11-08
 * Time: 17:06
 */

namespace App\Services;

use App\Services\Contracts\RatesImporter;
use App\Repositories\CurrencyRepository;

class FloatRatesImporter implements RatesImporter
{
    private $currencyRepository;

    public function __construct(CurrencyRepository $currencyRepository)
    {
        $this->currencyRepository = $currencyRepository;
    }

    public function import(string $baseCurrency = 'gbp'): bool
    {
        $fileName = 'http://www.floatrates.com/daily/'.$baseCurrency.'.xml';
        $fileContents = file_get_contents($fileName);
        if (!$fileContents) {
            echo 'unable to read currency rates from '.$fileName.'. Make sure file exists or
                try again later or use another currency exchange provider';
            return false;
        }

        $data = simplexml_load_string($fileContents);
        if (!$data) {
            echo 'Bad XML format. Consider inform Currency exchange provider or use another method for XML parsing.';
            return false;
        }


        if (!$this->xmlFormatValid($data, $baseCurrency)) {
            echo 'unable to parse XML. Consider that XML format has changed';
            return false;
        }

        $this->currencyRepository->saveCurrencies($data);

        return true;
    }

    private function xmlFormatValid(\SimpleXMLElement $data, string $baseCurrency): bool
    {
        $currentNode = 0;
        foreach ($data as $item) {
            $currentNode += 1;
            if (
                $currentNode === config('currency.FloatRatesConst.BASE_CURR_ARR_IDX') &&
                (
                    !isset($item[0]) ||
                    strtolower($item[0]) !== $baseCurrency
                )
            ) {
                return false;
            }

            if ($currentNode === config('currency.FloatRatesConst.DATE_UPDATED_ARR_IDX')) {
                if (!isset($item[0])) {
                    return false;
                }
                $timestamp = strtotime($item[0]);
                if (date('Y-m-d', $timestamp) === '1970-01-01') {
                    return false;
                }
            }

            if (
                $currentNode >= config('currency.FloatRatesConst.FIRST_ITEM_ARR_IDX') &&
                (
                    !isset($item->title) ||
                    !isset($item->baseCurrency) ||
                    !isset($item->targetCurrency) ||
                    !isset($item->exchangeRate) ||
                    !isset($item->inverseRate)
                )
            ) {
                return false;
            }
        }

        return true;
    }
}