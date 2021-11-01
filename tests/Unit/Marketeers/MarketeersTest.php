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

    /**
     * @dataProvider OfferMatchesProvider
     * @param unknown $test
     * @param unknown $offer
     * @param unknown $expect
     */
    public function testOfferMatches($test,$offer,$expect)
    {
        $test_obj = new FakeMarketeer();
        $this->assertEquals($expect,$this->invokeMethod($test_obj, 'offerMatches',[$test,$offer]));
    }
    
    public function OfferMatchesProvider()
    {
        return [
            ['this.is.a.test','this.is.a.test',true],
            ['this.is.a.test','this.is.another,test',false],
            ['this.is.a.test','this.is.*.test',true],
            ['this.is.a.test','this.is.*#.test',false],
            ['this.is.a.test','this.is.*',false],
            ['this.is.a.test','this.*.a.*',true],            
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
        $test = $this->getMockBuilder(FakeMarketeer::class)
        ->setMethods(['getTestItem'])
        ->getMock();
        $test->expects($this->once())->method('getTestItem');
        $test->getItem('test.item');
    }
    
    public function testWithParameter()
    {
        $test = $this->getMockBuilder(FakeMarketeer::class)
        ->setMethods(['getTestArray'])
        ->getMock();
        $test->expects($this->once())->method('getTestArray')->with('test');
        $test->getItem('test.array.test.item');        
    }
    
    public function testWithMoreParameter()
    {
        $test = $this->getMockBuilder(FakeMarketeer::class)
        ->setMethods(['getAnotherArray'])
        ->getMock();
        $test->expects($this->once())->method('getAnotherArray')->with('test','item');
        $test->getItem('another.test.array.item');
    }
}
