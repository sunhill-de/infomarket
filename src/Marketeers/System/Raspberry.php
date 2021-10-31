<?php
/**
 * @file Raspberry.php
 * Provides the Information specific for raspberrys
 * Lang en
 * Reviewstatus: 2021-10-30
 * Localization: none
 * Documentation: complete
 * Tests:
 * Coverage: unknown
 * Dependencies: none
 * PSR-State: complete
 */

namespace Sunhill\InfoMarket\Marketeers\System;

use Sunhill\InfoMarket\Marketeers\MarketeerBase;
use Sunhill\InfoMarket\Marketeers\Response\Response;

class Raspberry extends MarketeerBase
{
    /**
     * Returns what items this marketeer offers
     * @return array
     */
    protected function getOffering(): array
    {
        return [
        ];
    }
       
    protected function itemIsReadable(string $item): bool
    {
        return true;
    }
    
    protected function itemIsWriteable(string $item): bool
    {
        return false;
    }
               
}