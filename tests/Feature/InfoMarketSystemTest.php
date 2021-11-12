<?php

namespace Sunhill\InfoMarket\Tests\Feature\Market;

use Sunhill\InfoMarket\Test\InfoMarketTestCase;
use Sunhill\InfoMarket\Market\InfoMarket;
use Sunhill\InfoMarket\Market\MarketException;
use Sunhill\InfoMarket\Marketeers\System\Uptime;

class InfoMarketSystemTest extends InfoMarketTestCase
{

    public function testReadItem()
    {
        $test = new InfoMarket();
        $test->installMarketeer(Uptime::class);
        
        $answer = json_decode($test->readItem('system.uptime.duration'),true);
        $this->assertEquals('OK',$answer['result']);
    }
    
}
