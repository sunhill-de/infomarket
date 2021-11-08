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
            'readonly.test'=>'getReadonlyTest',
            'writeonly.test'=>'getWriteonlyTest',
            'test.array.*.item'=>'getTestArray',
            'another.*.array.*'=>'getAnotherArray'
        ];
    }
    
    protected function itemIsReadable(string $name,$credentials): bool
    {
        switch ($name) {
            case 'writeonly.test':
                return false;
            default:
                return true;
        }
    }
    
    protected function itemIsWriteable(string $name, $credentials): bool
    {
        switch ($name) {
            case 'readonly.test':
                return false;
            default:
                return true;
        }
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


