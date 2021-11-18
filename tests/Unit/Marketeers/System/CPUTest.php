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
            ['system.cpu.0.model',true],
            ['system.cpu.0.bogomips',true],
            ['system.cpu.temp',true],
        ];
    }

    private function getMockedMarketeer(int $index)
    {
        $test = $this->getMockBuilder(CPU::class)
            ->onlyMethods(['getProcCpuinfo','getThermalDir'])    
            ->getMock();
        $test->method('getProcCpuinfo')->willReturn(file_get_contents(dirname(__FILE__).'/../../../Files/proc/cpuinfo'.$index));
        $test->method('getThermalDir')->willReturn(dirname(__FILE__).'/../../../Files/sys/thermal'.$index);
        return $test;
    }
    
    /**
     * @dataProvider valuesProvider
     */
    public function testValues($index,$path,$param,$expect)
    {
        $test = $this->getMockedMarketeer($index);
        
        $this->assertEquals($expect,$this->invokeMethod($test,$path,$param));        
    }
    
    public function valuesProvider()
    {
        return [
            [1,'getCPUCount',[],4],
            [1,'getCPUVendor',[0],'GenuineIntel'],
            [1,'getCPUModel',[0],'Intel(R) Core(TM) i5-6400 CPU @ 2.70GHz'],
            [1,'getCPUBogomips',[0],5399.81],
            [3,'getCPUVendor',[0],'unknown'],
            [3,'getCPUModel',[0],'ARMv7 Processor rev 3 (v7l)'],
            [3,'getCPUBogomips',[0],108.0],
            [1,'getCPUTemp',[0],32],
        ];
    }
        
    /**
     * @dataProvider getValuesProvider
     */
    public function testGetValues($index,$path,$value,$expect)
    {
        $test = $this->getMockedMarketeer($index);
        
        $response = $test->getItem($path)->get();
        $response_array = json_decode($response,true);
        
        $this->assertEquals($expect,$response_array[$value]);        
    }
    
    public function getValuesProvider()
    {
        return [
            [1,'system.cpu.count','value',4],
            [1,'system.cpu.0.vendor','value','GenuineIntel'],
            [3,'system.cpu.0.vendor','value','unknown'],
            [1,'system.cpu.0.model','value','Intel(R) Core(TM) i5-6400 CPU @ 2.70GHz'],
            [3,'system.cpu.0.model','value','ARMv7 Processor rev 3 (v7l)'],
            [1,'system.cpu.0.bogomips','value',5399.81],
            [3,'system.cpu.0.bogomips','value',108.0],
        ];
    }
        
}
