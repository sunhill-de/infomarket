<?php

namespace Sunhill\InfoMarket\Tests\Unit\System;

use Sunhill\InfoMarket\Test\InfoMarketTestCase;
use Sunhill\InfoMarket\Marketeers\System\Disk;

class DiskTest extends InfoMarketTestCase
{
    
    protected function getMockedDisk($index)
    {
        $test = $this->getMockBuilder(Disk::class)
        ->setMethods(['getLsBlk','getDF'])
        ->getMock();
        $test->method('getLsBlk')->willReturn(file_get_contents(dirname(__FILE__).'/../../../Files/lsblk/lsblk'.$index));
        $test->method('getDF')->willReturn(file_get_contents(dirname(__FILE__).'/../../../Files/df/df'.$index));
        return $test;        
    }
    
    public function testReadDisks1() {
        $test = $this->getMockedDisk(1);
        
        $result = json_decode($this->invokeMethod($test,'getDiskCount')->get());
        $this->assertEquals(4,$result->value);
        $result = json_decode($this->invokeMethod($test,'getPartitionsCount')->get());
        $this->assertEquals(6,$result->value);
        $result = json_decode($this->invokeMethod($test,'getDiskCapacity',[0])->get());
        $this->assertEquals(3000592982016,$result->value);
        $result = json_decode($this->invokeMethod($test,'getDiskCapacity',[2])->get());
        $this->assertEquals(3000592982016,$result->value);
        $result = json_decode($this->invokeMethod($test,'getDiskCapacity',[3])->get());
        $this->assertEquals(3000592982016,$result->value);
    }
}
