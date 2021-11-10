<?php

namespace Sunhill\InfoMarket\Tests\Unit\System;

use Sunhill\InfoMarket\Test\InfoMarketTestCase;
use Sunhill\InfoMarket\Marketeers\System\CPU;

class CPUTest extends InfoMarketTestCase
{
    /**
     * @dataProvider OffersItemProvider
     * @param unknown $element
     * @param unknown $expect
     */
    public function testOffersItem($element,$expect)
    {
        $test = new CPU();
        $this->assertEquals($expect,$test->offersItem($element));
    }
    
    public function OffersItemProvider()
    {
        return [
            ['system.cpu.count',true],
            ['system.cpu.0.vendor',true],
        ];
    }

    private function getMockedMarketeer()
    {
        $test = $this->getMockBuilder(Uptime::class)
            ->setMethods([' getProcCpuinfo'])    
            ->getMock();
        $test->method(' getProcCpuinfo')->willReturn(file_get_contents(dirname(__FILE__).'/../../../Files/proc/cpuinfo'));
        return $test;
    }
    
    /**
     * @dataProvider valuesProvider
     */
    public function testValues($path,$param,$expect)
    {
        $test = $this->getMockedMarketeer();
        
        $this->assertEquals($expect,$this->invokeMethod($test,$path,$param));        
    }
    
    public function valuesProvider()
    {
        return [
            ['getCPUCount',null,4],
            ['getVendor',[0],'GenuineIntel']
        ];
    }
        
    /**
     * @dataProvider getValuesProvider
     */
    public function testGetValues($path,$value,$expect)
    {
        $test = $this->getMockedMarketeer();
        
        $reponse = $test->getItem($path);
        $reponse_array = json_decode($response,true);
        
        $this->assertEquals($expect,$response_array[$value]);        
    }
    
    public function getValuesProvider()
    {
        return [
            ['system.cpu.count','value',4],
            ['system.cpu.0.vendor','value','GenuineIntel'],
        ];
    }
        
}
