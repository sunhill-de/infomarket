<?php

namespace Sunhill\InfoMarket\Test\Marketeers;

use Sunhill\InfoMarket\Marketeers\MarketeerBase;
use Sunhill\InfoMarket\Marketeers\Response\Response;

class FakeMarketeer2 extends MarketeerBase
{

    protected function getOffering(): array
    {
        return [
            'fake2.test'=>'getFake2Test'
            
        ];
    }
    
    protected function itemIsReadable(string $name, $credentials): bool
    {
        return true;        
    }
    
    protected function itemIsWriteable(string $name, $credentials): bool
    {
        return false;    
    }
    
    protected function getFake2Test(): Response
    {
        
    }
    
}


