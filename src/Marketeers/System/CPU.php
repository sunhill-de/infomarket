<?php
/**
 * @file CPU.php
 * Provides information about the cpu
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

class CPU extends MarketeerBase
{
    /**
     * Returns what items this marketeer offers
     * @return array
     */
    protected function getOffering(): array
    {
        return [
            'count',
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

    private function getCPUCount(): int
    {
        
    }
    
    protected function getCount(): Response
    {
        
    }
}