<?php

namespace Sunhill\InfoMarket\Tests\Unit\System;

use Sunhill\InfoMarket\Test\InfoMarketTestCase;
use Sunhill\InfoMarket\Marketeers\System\Uptime;

class UptimeTest extends InfoMarketTestCase
{
    
    public function testReadSuccess()
    {
        $test = new Uptime();
        $data = $this->invokeMethod($test,'getData');
        $this->assertFalse(empty($data));
    }
    
    /**
     * @dataProvider OffersItemProvider
     * @param unknown $element
     * @param unknown $expect
     */
    public function testOffersItem($element,$expect)
    {
        $test = new Uptime();
        $this->assertEquals($expect,$test->offersItem($element));
    }
    
    public function OffersItemProvider()
    {
        return [
            ['system.uptime.seconds',true],
            ['system.uptime.duration',true],
            ['system.idletime.seconds',true],
            ['system.idletime.duration',true],
            ['system.average_idletime.seconds',true],
            ['system.average_idletime.duration',true],
            ['some.item',false]
        ];
    }
    
    public function testGetUptimeSeconds()
    {
        $test = $this->getMockBuilder(Uptime::class)
            ->setMethods(['getData'])    
            ->getMock();
        $test->method('getData')->willReturn("1131440.44 4488358.31\n");
        $response = $test->getItem('system.uptime.seconds');
        $this->assertEquals(1131440,$response->getElement('value'));
    }
    
    public function testGetUptimeDuration()
    {
        $test = $this->getMockBuilder(Uptime::class)
        ->setMethods(['getData'])
        ->getMock();
        $test->method('getData')->willReturn("1131440.44 4488358.31\n");
        $response = $test->getItem('system.uptime.duration');
        $this->assertEquals('13 days and 2 hours',$response->getElement('human_readable_value'));
    }
    
    public function testGetIdletimeSeconds()
    {
        $test = $this->getMockBuilder(Uptime::class)
        ->setMethods(['getData'])
        ->getMock();
        $test->method('getData')->willReturn("1131440.44 4488358.31\n");
        $response = $test->getItem('system.idletime.seconds');
        $this->assertEquals(4488358,$response->getElement('value'));
    }
    
    public function testGetIdletimeDuration()
    {
        $test = $this->getMockBuilder(Uptime::class)
        ->setMethods(['getData'])
        ->getMock();
        $test->method('getData')->willReturn("1131440.44 4488358.31\n");
        $response = $test->getItem('system.idletime.duration');
        $this->assertEquals('51 days and 22 hours',$response->getElement('human_readable_value'));
    }
    
    public function testGetAverageIdletimeSeconds()
    {
        $test = $this->getMockBuilder(Uptime::class)
        ->setMethods(['getData','getCPUCount'])
        ->getMock();
        $test->method('getData')->willReturn("1131440.44 4488358.31\n");
        $test->method('getCPUCount')->willReturn(4);
        $response = $test->getItem('system.average_idletime.seconds');
        $this->assertEquals((int)(4488358/4),$response->getElement('value'));
    }
    
    public function testGetAverageIdletimeDuration()
    {
        $test = $this->getMockBuilder(Uptime::class)
        ->setMethods(['getData'])
        ->getMock();
        $test->method('getData')->willReturn("1131440.44 4488358.31\n");
        $response = $test->getItem('system.average_idletime.duration');
        $this->assertEquals('12 days and 23 hours',$response->getElement('human_readable_value'));
    }
    
}
