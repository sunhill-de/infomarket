<?php

namespace Sunhill\InfoMarket\Test\Marketeers;

use Sunhill\InfoMarket\Marketeers\MarketeerBase;
use Sunhill\InfoMarket\Marketeers\Response\Response;

class FakeMarketeer extends MarketeerBase
{
    protected function getOffering(): array
    {
        return [
            'test.item'=>'getTestItem',
            'restricted.item'=>'getRestrictedItem',
            'readonly.test'=>'getReadonlyTest',
            'writeonly.test'=>'getWriteonlyTest',
            'test.array.?.item'=>'getTestArray',
            'another.?.array.?'=>'getAnotherArray',
            'catchall.*'=>'getCatchAll',
            'numeric.#.test'=>'getNumericTest'
        ];
    }

    protected function getRestrictedItem_restrictions()
    {
        return ['read'=>'Admin','write'=>'Admin'];
    }
    
    protected function getReadonlyTest_writeable()
    {
        return false;
    }
    
    protected function getWriteonlyTest_readable()
    {
        return false;
    }
    
    protected function getTestItem(): Response
    {
        $response = new Response();
        return $response->OK()->unit(' ')->type('Integer')->value(123);
    }
    
    protected function getCatchall(): Response
    {
        
    }
    
    protected function getTestArray($index): Response
    {
        $response = new Response();
        return $response->OK()->unit(' ')->type('Integer')->value(111*$index);        
    }
    
    protected function getAnotherArray($index1,$index2): Response
    {
        $response = new Response();
        return $response->OK()->unit(' ')->type('Integer')->value($index1*$index2);        
    }
    
}


