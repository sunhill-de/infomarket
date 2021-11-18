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
        $this->invokeMethod($test,'checkAllowedChars',['this.is.a.test']);
        $this->assertTrue(true);
    }    
    
    public function testCheckAllowedCharFail()
    {
        $this->expectException(MarketeerException::class);
        $test = new FakeMarketeer();
        $this->invokeMethod($test,'checkAllowedChars',['this.is.*.test']);
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
            ['this.is.a.test','this.is.?.test',['a'],true],            
            ['this.is.a.test','this.is.?.testing',null,false],
            ['this.is.a.test','this.is.?.testing',[],false],            
            ['this.is.a.test','this.is.#.test',null,false],
            ['this.is.a.test','this.is.#.test',[],false],
            ['this.is.1.test','this.is.#.test',null,true],
            ['this.is.1.test','this.is.#.test',[1],true],
            ['this.is.a.test','this.is.?',null,false],
            ['this.is.a.test','this.?.a.?',null,true],
            ['this.is.a.test','this.?.a.?',['is','test'],true],
            ['this.is.a','this.is.?.test',null,false],
            ['this.is.a.test','this.is.*',['a.test'],true]
        ];
    }
    
    /**
     * @dataProvider OffersItemProvider
     * @param unknown $test
     * @param unknown $expect
     */
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

    /**
     * @dataProvider GetItemMethodProvider
     * @param unknown $item
     * @param unknown $prefix
     * @param unknown $postfix
     * @param unknown $expect
     */
    public function testGetItemMethod($item,$prefix,$postfix,$expect)
    {
        $test = new FakeMarketeer();
        
        $this->assertEquals($expect, $this->invokeMethod($test,'getItemMethod',[$item,$prefix,$postfix]));
    }
    
    public function GetItemMethodProvider()
    {
        return [
            ['test.item','','','TestItem'],
            ['test.item','get','','getTestItem'],
            ['test.item','','_writeable','TestItem_writeable'],
            ['nonexisting.item','','',false],
        ];    
    }
    
    public function testGetRestrictionsPass()
    {
        $test = $this->getMockBuilder(FakeMarketeer::class)
        ->onlyMethods(['RestrictedItem_restrictions'])
        ->getMock();
        $test->expects($this->once())->method('RestrictedItem_restrictions')->willReturn(['test']);
        
        $this->assertEquals(['test'],$test->getRestrictions('restricted.item'));
    }
    
    public function testGetRestrictionsFail()
    {
        $this->expectException(MarketeerException::class);
        
        $test = new FakeMarketeer();
        
        $test->getRestrictions('nonexisting.item');
    }
    
    public function testGetReadableDefault()
    {
        $test = new FakeMarketeer();
        
        $this->assertTrue($test->isReadable('test.item'));
    }
 
    public function testGetReadableWriteOnly()
    {
        $test = $this->getMockBuilder(FakeMarketeer::class)
        ->onlyMethods(['WriteOnlyTest_readable'])
        ->getMock();
        $test->expects($this->once())->method('WriteonlyTest_readable')->willReturn(false);
        
        $this->assertFalse($test->isReadable('writeonly.test'));        
    }

    public function testGetWriteableDefault()
    {
        $test = new FakeMarketeer();
        
        $this->assertFalse($test->isWriteable('test.item'));
    }
    
    public function testGetWriteableWriteable()
    {
        $test = $this->getMockBuilder(FakeMarketeer::class)
        ->onlyMethods(['WriteableTest_writeable'])
        ->getMock();
        $test->expects($this->once())->method('WriteableTest_writeable')->willReturn(true);
        
        $this->assertTrue($test->isWriteable('writeable.test'));
    }
    
    /**
     * @dataProvider IsAccessibleProvider
     */
    public function testIsAccessible($user,$restriction,$expect)
    {
        $test = new FakeMarketeer();
        
        $this->assertEquals($expect,$this->invokeMethod($test,'isAccessible',[$user,$restriction]));
    }
    
    public function IsAccessibleProvider()
    {
        return [
            ['anybody','anybody',true],
            ['anybody','user',false],
            ['anybody','advanced',false],
            ['anybody','admin',false],
        
            ['user','anybody',true],
            ['user','user',true],
            ['user','advanced',false],
            ['user','admin',false],

            ['advanced','anybody',true],
            ['advanced','user',true],
            ['advanced','advanced',true],
            ['advanced','admin',false],
            
            ['admin','anybody',true],
            ['admin','user',true],
            ['admin','advanced',true],
            ['admin','admin',true],
            
        ];    
    }
    
    /**
     * @dataProvider GetItemProvider
     * @param unknown $item
     * @param unknown $key
     * @param unknown $value
     */
    public function testGetItem($item,$key,$value)
    {
        $test = new FakeMarketeer();
        $result = $test->getItem($item);        
        if ($key == false) {
            $this->assertFalse($result);
        } else {
            $this->assertEquals($value,$result->getElement($key));
        }
    }
    
    public function GetItemProvider()
    {
        return [
            ['test.item','value',123],
            ['nonexisting.item',false,null],
            ['test.array.2.item','value',222],
            ['restricted.item','error_code','ITEMNOTACCESSIBLE'],
            ['writeonly.test','error_code','ITEMNOTREADABLE'],
        ];        
    }
}
