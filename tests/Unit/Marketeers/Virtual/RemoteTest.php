<?php

namespace Sunhill\InfoMarket\Tests\Unit\Virtual;

use Sunhill\InfoMarket\Test\InfoMarketTestCase;
use Sunhill\InfoMarket\Marketeers\Virtual\Remote;

class RemoteTest extends InfoMarketTestCase
{

    public function testSetAndGetRemoteServer()
    {
        $test = new Remote();
        $result = $test->setRemoteServer('192.168.0.1');
        $this->assertEquals('192.168.0.1',$result->getRemoteServer());
    }

    public function testSetAndGetRemotePort()
    {
        $test = new Remote();
        $result = $test->setRemotePort(123);
        $this->assertEquals(123,$result->getRemotePort());
    }
    
}
