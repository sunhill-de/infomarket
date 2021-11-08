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
            'test.catchall'=>'getTestCatchall',
            'test.array.*.item'=>'getTestArray',
            'another.*.array.*'=>'getAnotherArray'
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
    
    protected function getItemResponse(string $name): Response
    {
        if ($name == 'test.catchall') {
            return $this->getCatchall();
        } else {
            return parent::getItemResponse($name);
        }
    }
    
    protected function getTestItem(): Response
    {
        
    }
    
    protected function getTestCatchall(): Response
    {
        
    }
    
    protected function getTestArray($index): Response
    {
        
    }
    
    protected function getAnotherArray($index1,$index2): Response
    {
        
    }
    
}


