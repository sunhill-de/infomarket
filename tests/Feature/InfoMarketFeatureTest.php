<?php

namespace Sunhill\InfoMarket\Tests\Feature\Market;

use Sunhill\InfoMarket\Test\InfoMarketTestCase;
use Sunhill\InfoMarket\Market\InfoMarket;
use Sunhill\InfoMarket\Market\MarketException;
use Sunhill\InfoMarket\Test\Marketeers\FakeMarketeer;
use Sunhill\InfoMarket\Test\Marketeers\FakeMarketeer2;

class InfoMarketFeatureTest extends InfoMarketTestCase
{

    public function testReadItem()
    {
        $test = new InfoMarket();
        $test->installMarketeer(FakeMarketeer::class);
        $test->installMarketeer(FakeMarketeer2::class);
        
        $answer = json_decode($test->readItem('test.item'),true);
        $this->assertEquals(123,$answer['value']);
    }
    
}
