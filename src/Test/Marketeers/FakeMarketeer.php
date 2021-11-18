<?php

namespace Sunhill\InfoMarket\Test\Marketeers;

use Sunhill\InfoMarket\Marketeers\MarketeerBase;
use Sunhill\InfoMarket\Marketeers\Response\Response;

class FakeMarketeer extends MarketeerBase
{
    protected function getOffering(): array
    {
        return [
            'test.item'=>'TestItem',
            'restricted.item'=>'RestrictedItem',
            'writeable.test'=>'WriteableTest',
            'writeonly.test'=>'WriteonlyTest',
            'test.array.?.item'=>'TestArray',
            'another.?.array.?'=>'AnotherArray',
            'catchall.*'=>'CatchAll',
            'numeric.#.test'=>'NumericTest'
        ];
    }

    protected function RestrictedItem_restrictions()
    {
        return ['read'=>'admin','write'=>'admin'];
    }
    
    protected function WriteableTest_writeable()
    {
        return true;
    }
    
    protected function WriteonlyTest_readable()
    {
        return false;
    }
    
    protected function WriteonlyTest_writeable()
    {
        return true;
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


