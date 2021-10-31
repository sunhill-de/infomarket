<?php

namespace Sunhill\InfoMarket\Tests\Unit\Marketeers;

use Sunhill\InfoMarket\Test\InfoMarketTestCase;
use Sunhill\InfoMarket\Marketeers\MarketeerBase;
use Sunhill\InfoMarket\Marketeers\Response\Response;
use Sunhill\InfoMarket\Marketeers\MarketeerException;

class FakeMarketeer extends MarketeerBase
{
    protected function getOffering(): array
    {
        return [
            'test.item',
            'test.catchall',
            'test.array.*.item'
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
}

class MarketeersTest extends InfoMarketTestCase
{

    /**
     * @dataProvider CalculateGetterNameProvider
     * @param unknown $test
     * @param unknown $expect
     */
    public function testCalculateGetterName($test_name,$expect)
    {
        $test = new FakeMarketeer();
        $this->assertEquals($expect,$this->invokeMethod($test,'calculateGetterName',[$test_name]));
    }
    
    public function CalculateGetterNameProvider()
    {
        return [
            ['test.this','getTestThis'],
            ['test.this.too','getTestThisToo'],
            ['test.a_seperated','getTestASeperated'],
            ['test.another_seperated.one','getTestAnotherSeperatedOne']
        ];
    }

    public function testUnknownItem()
    {
        $this->expectException(MarketeerException::class);
        $test = new FakeMarketeer();
        $test->getItem('nonexisting.item');
    }
    
    public function testSimpleItem()
    {
        
    }
}
