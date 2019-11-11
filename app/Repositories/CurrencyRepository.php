<?php
/**
 * Created by PhpStorm.
 * User: Marius
 * Date: 2019-11-08
 * Time: 19:01
 */

namespace App\Repositories;

use App\Currency;
use App\CurrencyRate;

class CurrencyRepository
{
    private $currencies = [];
    private $currencyRates = [];

    public function saveFxCurrencies(\SimpleXMLElement $data)
    {
        $this->loadCurrenciesFromDB();

        foreach ($data as $item) {

            preg_match_all('/\(([A-Z]{3})\)/', $item->title, $currentItem);
            preg_match('/([\d\.]+)\s([^=]+)=\s([\d\.]+)\s(.*)/', $item->description, $exchangeRate);

            $xmlItem = new \SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><channel></channel>');
            $xmlItem->baseName = $exchangeRate[2];
            $xmlItem->baseCurrency = $currentItem[1][0];
            $xmlItem->targetName = $exchangeRate[4];
            $xmlItem->targetCurrency = $currentItem[1][1];
            $xmlItem->exchangeRate = $exchangeRate[3];
            $xmlItem->pubDate = $item->pubDate;

            $this->saveBaseCurrency($xmlItem);
            $this->saveTargetCurrency($xmlItem);
            $this->saveCurrencyRate($xmlItem);
        }
    }

    public function saveCurrencies(\SimpleXMLElement $data)
    {
        $this->loadCurrenciesFromDB();

        $currentNode = 0;
        foreach ($data as $item) {
            $currentNode += 1;
            if ($currentNode >= config('currency.FloatRatesConst.FIRST_ITEM_ARR_IDX')) {
                $this->saveBaseCurrency($item);
                $this->saveTargetCurrency($item);
                $this->saveCurrencyRate($item);
            }
        }
    }

    private function loadCurrenciesFromDB()
    {
        $currencies = Currency::all();
        foreach ($currencies as $item) {
            $this->currencies[$item->code] = $item;
        }

        $currencyRates = CurrencyRate::all();
        foreach ($currencyRates as $item) {
            $this->currencyRates[$item->currency_from_id.'_'.$item->currency_to_id] = $item;
        }
    }

    private function saveBaseCurrency($item)
    {
        $currency = strtolower($item->baseCurrency);
        if (!isset($this->currencies[$currency])) {
            $this->currencies[$currency] = new Currency();
            $this->currencies[$currency]->name = $item->baseName;
            $this->currencies[$currency]->code = $currency;
            $this->currencies[$currency]->save();
        }
    }

    private function saveTargetCurrency($item)
    {
        $currency = strtolower($item->targetCurrency);
        if (!isset($this->currencies[$currency])) {
            $this->currencies[$currency] = new Currency();
            $this->currencies[$currency]->name = $item->targetName;
            $this->currencies[$currency]->code = $currency;
            $this->currencies[$currency]->save();
        }
    }

    private function saveCurrencyRate($item)
    {
        $from = strtolower($item->baseCurrency);
        $to = strtolower($item->targetCurrency);

        $fromId = $this->currencies[$from]->id;
        $toId = $this->currencies[$to]->id;

        if (!isset($this->currencyRates[$fromId.'_'.$toId])) {
            $this->currencyRates[$fromId.'_'.$toId] = new CurrencyRate();
            $this->currencyRates[$fromId.'_'.$toId]->currency_from_id = $fromId;
            $this->currencyRates[$fromId.'_'.$toId]->currency_to_id = $toId;
            $this->currencyRates[$fromId.'_'.$toId]->rate = str_replace(',', '', $item->exchangeRate);
            $this->currencyRates[$fromId.'_'.$toId]->provider_updated = date('Y-m-d H:i:s', strtotime($item->pubDate));
            $this->currencyRates[$fromId.'_'.$toId]->save();
        } elseif ($this->currencyRates[$fromId.'_'.$toId]->provider_updated < date('Y-m-d H:i:s', strtotime($item->pubDate))) {
            $this->currencyRates[$fromId.'_'.$toId]->rate = str_replace(',', '', $item->exchangeRate);
            $this->currencyRates[$fromId.'_'.$toId]->provider_updated = date('Y-m-d H:i:s', strtotime($item->pubDate));
            $this->currencyRates[$fromId.'_'.$toId]->save();
        }
    }
}