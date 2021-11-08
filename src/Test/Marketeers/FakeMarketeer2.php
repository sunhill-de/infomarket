<?php

namespace Sunhill\InfoMarket\Test\Marketeers;

use Sunhill\InfoMarket\Marketeers\MarketeerBase;
use Sunhill\InfoMarket\Marketeers\Response\Response;

class FakeMarketeer extends MarketeerBase
{
    protected $indicator;
    
    protected function getOffering(): array
    {
        return [
            'fake2.test'=>'getFake2Test'
            
        ];
    }
    
    protected function itemIsReadable(string $name): bool
    {
        return true;        
    }
    
    protected function itemIsWriteable(string $name): bool
    {
        return false;    
    }
    
    protected function getFake2Test(): Response
    {
        
    }
    
}


