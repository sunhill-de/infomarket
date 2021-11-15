<?php

namespace Sunhill\InfoMarket\Tests\Feature\Market;

use Sunhill\InfoMarket\Test\InfoMarketTestCase;
use Sunhill\InfoMarket\Market\InfoMarket;
use Sunhill\InfoMarket\Market\MarketException;
use Sunhill\InfoMarket\Test\Marketeers\FakeMarketeer;
use Sunhill\InfoMarket\Test\Marketeers\FakeMarketeer2;

class InfoMarketFeatureTest extends InfoMarketTestCase
{

    /**
     * @dataProvider ReadItemProvider
     * @param unknown $path
     * @param unknown $result
     * @param unknown $value
     */
    public function testReadItem($path,$result,$value)
    {
        $test = new InfoMarket();
        $test->installMarketeer(FakeMarketeer::class);
        $test->installMarketeer(FakeMarketeer2::class);
        
        $answer = json_decode($test->readItem($path),true);
        $this->assertEquals($result,$answer['result']);
        if ($result == 'OK') {
            $this->assertEquals($value,$answer['value']);
        } else {
            $this->assertEquals($value,$answer['error_code']);            
        }
    }
 
    public function ReadItemProvider()
    {
        return [
          ['test.item','OK',123],
          ['test.array.2.item','OK',222],
          ['another.2.array.3','OK',6],
          ['fake2.test','OK','ABC'],
          ['test.array.nonsense','OK','NONSENSE'],
            
          ['not.existing.item','FAILED','ITEMNOTFOUND']
        ];
    }
}
