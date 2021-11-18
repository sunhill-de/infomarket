<?php

namespace Sunhill\InfoMarket\Tests\Unit\Marketeers;

use Sunhill\InfoMarket\Test\InfoMarketTestCase;
use Sunhill\InfoMarket\Marketeers\MarketeerBase;
use Sunhill\InfoMarket\Marketeers\Response\Response;
use Sunhill\InfoMarket\Marketeers\MarketeerException;
use Sunhill\InfoMarket\Test\Marketeers\FakeMarketeer;

class MarketeersTest extends InfoMarketTestCase
{

    public function testCheckAllowedCharPass()
    {
        $test = new FakeMarketeer();
        $this->invokeMethod($test,'checkAllowedChar',['this.is.a.test']);
        $this->assertTrue(true);
    }    
    
    public function testCheckAllowedCharFail()
    {
        $this->expectException(MarketeerException::class);
        $test = new FakeMarketeer();
        $this->invokeMethod($test,'checkAllowedChar',['this.is.*.test']);
    }    
    
    /**
     * @dataProvider OfferMatchesProvider
     * @param unknown $test
     * @param unknown $offer
     * @param unknown $expect
     */
    public function testOfferMatches($test,$offer,$variables,$expect)
    {
        $test_obj = new FakeMarketeer();
        if (is_null($variables)) {
            $result = $this->invokeMethod($test_obj, 'offerMatches',[$test,$offer]);
        } else {
            $values = [];
            $result = $this->invokeMethod($test_obj, 'offerMatches',[$test,$offer,&$values]);
        }
        $this->assertEquals($expect,$result);
        if (!is_null($variables)) {
            $this->assertEquals($variables,$values);
        }    
    }
    
    public function OfferMatchesProvider()
    {
        return [
            ['this.is.a.test','this.is.a.test',null,true],
            ['this.is.a.test','this.is.a.test',[],true],            
            ['this.is.a.test','this.is.another,test',null,false],
            ['this.is.a.test','this.is.a',null,false],
            ['this.is.a','this.is.a.test',null,false],
            ['this.is.a.test','this.is.?.test',null,true],
            ['this.is.a.test','this.is.?.test',['a'],null,true],            
            ['this.is.a.test','this.is.?.testing',null,false],
            ['this.is.a.test','this.is.?.testing',[],false],            
            ['this.is.a.test','this.is.#.test',null,false],
            ['this.is.a.test','this.is.#.test',[],false],
            ['this.is.1.test','this.is.#.test',null,true],
            ['this.is.1.test','this.is.#.test',[],true],
            ['this.is.a.test','this.is.?',null,false],
            ['this.is.a.test','this.?.a.?',null,true],
            ['this.is.a.test','this.?.a.?',['is','test'],true],
            ['this.is.a','this.is.?.test',null,false],
            ['this.is.a.test','this.is.*',['a.test'],true]
        ];
    }
    
    public function testOffersItem($test,$expect)
    {
        $test_obj = new FakeMarketeer();
        $this->assertEquals($expect,$test_obj->offersItem($test));
    }

    public function OffersItemProvider()
    {
        return  [
            ['test.item',true],
            ['test.item.more',false],
            ['test.array.some.item',true],
            ['test.array.some.other.item',false],
            ['another.boring.array.test',true],
            ['catchall.test.too',true],
            ['catchall',false],
            ['numeric.1.test',true],
            ['numeric.a.test',false]
        ];    
    }

    public function testGetItemMethodPass()
    {
        $test = new FakeMarketeer();
        $this->assertEquals('getTestItem',$this->invokeMethod($test,'getItemMethod',['test.item']));
    }
    
    public function testGetItemMethodFail()
    {
        $test = new FakeMarketeer();
        $this->assertFalse($this->invokeMethod($test,'getItemMethod',['non.existing.item']));
    }
    
    public function testGetRestrictionsPass()
    {
        $test = $this->getMockBuilder(FakeMarketeer::class)
        ->setMethods(['getRestrictedItem'])
        ->getMock();
        $test->expects($this->once())->method('getRestrictedItem_restrictions')->willReturn(['test']);
        
        $this->assertEquals([],$test->getRestrictions('restricted.item'));
    }
    
    public function testGetRestrictionsFail()
    {
        $this->expectException(MarketeerException::class);
        
        $test = new FakeMarketeer();
        
        $test->getRestrictions('nonexisting.item');
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
