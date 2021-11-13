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
    
    /**
     * @dataProvider ReadDiskProvider
     * @param unknown $index
     * @param unknown $method
     * @param unknown $params
     * @param unknown $expect
     */
    public function testReadDisk($index,$method,$params,$expect)
    {
        $test = $this->getMockedDisk($index);
        
        $result = json_decode($this->invokeMethod($test,$method,$params)->get());
        $this->assertEquals($expect,$result->value);        
    }
    
    public function ReadDiskProvider() 
    {
        return [
            [1,'getDiskCount',[],4],
            [1,'getDiskName',[0],'sda'],
            [1,'getDiskName',[1],'sdb'],
            [1,'getDiskName',[2],'sdc'],
            [1,'getDiskName',[3],'sdd'],
            [1,'getDiskCapacity',[0],3000592982016],
            [1,'getDiskCapacity',[1],240057409536],
            [1,'getDiskCapacity',[2],3000592982016],
            [1,'getDiskCapacity',[3],3000592982016],

            [1,'getPartitionsCount',[],6],
            [1,'getPartitionName',[0],'sda1'],
            [1,'getPartitionName',[1],'sdb1'],
            [1,'getPartitionName',[2],'sdb2'],
            [1,'getPartitionName',[3],'sdb5'],
            [1,'getPartitionName',[4],'sdc1'],
            [1,'getPartitionName',[5],'sdd1'],
            [1,'getPartitionCapacity',[0],3000591450112],
            [1,'getPartitionCapacity',[1],231779336192],
            [1,'getPartitionCapacity',[2],1024],
            [1,'getPartitionCapacity',[3],8275361792],
            [1,'getPartitionCapacity',[4],3000591450112],
            [1,'getPartitionCapacity',[5],3000591450112],
                        
        ];    
    }
    
}
