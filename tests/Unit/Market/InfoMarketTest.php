<?php

namespace Sunhill\InfoMarket\Tests\Unit\Market;

use Sunhill\InfoMarket\Test\InfoMarketTestCase;
use Sunhill\InfoMarket\Marketeer\MarketeerBase;
use Sunhill\InfoMarket\Market\InfoMarket;
use Sunhill\InfoMarket\Market\MarketException;
use Sunhill\InfoMarket\Test\Marketeers\FakeMarketeer;
use Sunhill\InfoMarket\Test\Marketeers\FakeMarketeer2;

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
}
