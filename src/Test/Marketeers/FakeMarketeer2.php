<?php

namespace Sunhill\InfoMarket\Test\Marketeers;

use Sunhill\InfoMarket\Marketeers\MarketeerBase;
use Sunhill\InfoMarket\Marketeers\Response\Response;

class FakeMarketeer2 extends MarketeerBase
{

    protected function getOffering(): array
    {
        return [
            'fake2.test'=>'Fake2Test',
            'test.array.nonsense'=>'NonsenseTest'
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
        $response = new Response();
        return $response->OK()->unit(' ')->type('String')->value('ABC');        
    }
    
    protected function getNonsenseTest(): Response
    {
        $response = new Response();
        return $response->OK()->unit(' ')->type('String')->value('NONSENSE');
    }
    
}


