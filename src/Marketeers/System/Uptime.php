<?php
/**
 * @file Uptime.php
 * Provides the Information from /proc/uptime
 * Lang en
 * Reviewstatus: 2021-10-30
 * Localization: none
 * Documentation: complete
 * Tests:
 * Coverage: unknown
 * Dependencies: none
 * PSR-State: complete
 */

namespace Sunhill\InfoMarket\Marketeers\System;

use Sunhill\InfoMarket\Marketeers\MarketeerBase;
use Sunhill\InfoMarket\Marketeers\Response\Response;

class Uptime extends MarketeerBase
{
    protected function getData()
    {
        $data = file_get_contents('/proc/uptime');
        return $data;
    }
    
    protected function getCPUCount()
    {
        $data = file_get_contents('/proc/cpuinfo');
        $lines = explode("\n",$data);
        $count = 0;
        foreach ($lines as $line) {
            if (substr($line,0,9) == 'processor') {
                $count++;
            }
        }
        return $count;    
    }
    
    /**
     * Returns what items this marketeer offers
     * @return array
     */
    protected function getOffering(): array
    {
        return [
          'uptime.seconds'=>'getUptimeSeconds',
          'uptime.duration'=>'getUptimeDuration',
          'idletime.seconds'=>'getIdletimeSeconds',
          'idletime.duration'=>'getIdletimeDuration',
          'average_idletime.seconds'=>'getAverageIdletimeSeconds',
          'average_idletime.duration'=>'getAverageIdletimeDuration'
        ];
    }
       
    protected function itemIsReadable(string $item, $credentials): bool
    {
        return true;
    }
    
    protected function itemIsWriteable(string $item, $credentials): bool
    {
        return false;
    }
    
    private function stripData() 
    {
        $data = str_replace("\n","",$this->getData());
        return explode(' ',$data);           
    }
    
    protected function getUptimeSeconds()
    {
        $response = new Response();
        $uptime = (int)($this->stripData()[0]);
        return $response->OK()->type('Integer')->unit('s')->semantic('uptime')->value($uptime);
    }
 
    protected function getUptimeDuration()
    {
        $response = new Response();
        $uptime = (int)($this->stripData()[0]);
        return $response->OK()->type('Integer')->unit('d')->semantic('uptime')->value($uptime);
    }
    
    protected function getIdletimeSeconds()
    {
        $response = new Response();
        $uptime = (int)($this->stripData()[1]);
        return $response->OK()->type('Integer')->unit('s')->semantic('uptime')->value($uptime);
    }
    
    protected function getIdletimeDuration()
    {
        $response = new Response();
        $uptime = (int)($this->stripData()[1]);
        return $response->OK()->type('Integer')->unit('d')->semantic('uptime')->value($uptime);
    }
    
    protected function getAverageIdletimeSeconds()
    {
        $response = new Response();
        $uptime = (int)($this->stripData()[1]/$this->getCPUCount());
        return $response->OK()->type('Integer')->unit('s')->semantic('uptime')->value($uptime);
    }
    
    protected function getAverageIdletimeDuration()
    {
        $response = new Response();
        $uptime = (int)($this->stripData()[1]/$this->getCPUCount());
        return $response->OK()->type('Integer')->unit('d')->semantic('uptime')->value($uptime);
    }
    
    
}