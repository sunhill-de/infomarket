<?php

namespace Sunhill\InfoMarket\Tests\Unit\Market;

use Sunhill\InfoMarket\Test\InfoMarketTestCase;
use Sunhill\InfoMarket\Marketeers\MarketeerBase;
use Sunhill\InfoMarket\Market\InfoMarket;
use Sunhill\InfoMarket\Market\MarketException;
use Sunhill\InfoMarket\Test\Marketeers\FakeMarketeer;
use Sunhill\InfoMarket\Test\Marketeers\FakeMarketeer2;
use Sunhill\InfoMarket\Marketeers\Response\Response;

class InfoMarketTest extends InfoMarketTestCase
{

    public function testInitEmpty()
    {
        $test = new InfoMarket();
        $this->assertTrue(empty($this->getProtectedProperty($test,'marketeers')));
    }
    
    public function testInstallMarketeerByName()
    {
        $test = new InfoMarket();
        $test->installMarketeer(FakeMarketeer::class);
        $this->assertEquals(1,count($this->getProtectedProperty($test,'marketeers')));
        $marketeer = $this->getProtectedProperty($test,'marketeers')[0];
        $this->assertTrue(is_a($marketeer,FakeMarketeer::class));
    }

    public function testInstallMarketeerByObject()
    {
        $test = new InfoMarket();
        $marketeer = new FakeMarketeer();
        $test->installMarketeer($marketeer);
        $this->assertEquals(1,count($this->getProtectedProperty($test,'marketeers')));
        $marketeer = $this->getProtectedProperty($test,'marketeers')[0];
        $this->assertTrue(is_a($marketeer,FakeMarketeer::class));
    }
    
    public function testInstallMarketeerException()
    {
        $this->expectException(MarketException::class);
        $test = new InfoMarket();
        $marketeer = new InfoMarketTest();
        $test->installMarketeer($marketeer);        
    }

    public function testInstallMoreMarketeers()
    {
        $test = new InfoMarket();
        $test->installMarketeer(FakeMarketeer::class);
        $test->installMarketeer(FakeMarketeer2::class);
        $this->assertEquals(2,count($this->getProtectedProperty($test,'marketeers')));
    }
    
    /**
     * @dataProvider HardwiredProvider
     */
    public function testHardwiredInfos($path,$element,$answer)
    {
        $test = new InfoMarket();
        
        $info = $this->invokeMethod($test,'readHardwiredResult',[$path]);
        $info_array = json_decode($info,true);
        $this->assertEquals($answer,$info_array[$element]);
    }
    
    public function HardwiredProvider()
    {
        return [
            ['infomarket.name','value','InfoMarket'],
            ['infomarket.version','result','OK'],
        ];
    }
    
    protected function isJsonStr($string) {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }
    
    public function testReadSingleItem()
    {
        $test = $this->getMockBuilder(FakeMarketeer::class)
            ->onlyMethods(['getItem'])
            ->getMock();
        $test->expects($this->once())->method('getItem')->with('test.item')->willReturn(new Response());
        
        $market = new InfoMarket();
        $market->installMarketeer($test);
        
        $this->assertTrue($this->isJsonStr($this->invokeMethod($market,'readSingleItem',['test.item',null])));      
    }

    public function testReadItem()
    {
        $test = $this->getMockBuilder(FakeMarketeer::class)
            ->onlyMethods(['isReadable','offersItem','getItem'])
            ->getMock();
        $test->expects($this->once())->method('getItem')->with('test.item')->willReturn(new Response());
        
        $market = new InfoMarket();
        $market->installMarketeer($test);
        
        $this->assertTrue($this->isJsonStr($this->invokeMethod($market,'readItem',['test.item',null])));
    }
    
    public function testAppendRequest()
    {
        $test = $this->getMockBuilder(FakeMarketeer::class)
        ->onlyMethods(['getItem'])
        ->getMock();
        $result = new Response();
        $result = $result->OK()->type('Integer')->unit(' ')->value('123');
        $test->expects($this->any())->method('getItem')->with('test.item')->willReturn($result);
        
        $market = new InfoMarket();
        $market->installMarketeer($test);
        
        $result = json_decode($market->readItem('test.item'),true);
        $this->assertEquals('test.item',$result['request']);
    }
    
    public function testReadItemList()
    {
        $test1 = $this->getMockBuilder(FakeMarketeer::class)->onlyMethods(['getItem'])->getMock();
        $test2 = $this->getMockBuilder(FakeMarketeer::class)->onlyMethods(['getItem'])->getMock();
        
        $result1 = new Response();
        $result1->OK()->type('Integer')->unit(' ')->value('123');
        $result2 = new Response();
        $result2->OK()->type('Integer')->unit(' ')->value('234');
        $result3 = new Response();
        $result3->OK()->type('Integer')->unit(' ')->value('345');

        $test1->expects($this->any())->method('getItem')->willReturn($result1);
        $test2->expects($this->any())->method('getItem')->willReturn($result3);
        
        $market = new InfoMarket();
        $market->installMarketeer($test1);
        $market->installMarketeer($test2);
        
        $query = '{ "query":["test.item","test.item3"] }';
        $result = json_decode($market->readItemList($query),true);
        
        $this->assertEquals(123,$result['result'][0]['value']);
    }
    
}
