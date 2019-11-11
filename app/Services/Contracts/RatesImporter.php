<?php
/**
 * Created by PhpStorm.
 * User: Marius
 * Date: 2019-11-08
 * Time: 17:09
 */

namespace App\Services\Contracts;

interface RatesImporter
{
    public function import(string $baseCurrency): bool;
}