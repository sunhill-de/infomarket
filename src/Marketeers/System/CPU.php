<?php
/**
 * @file CPU.php
 * Provides information about the cpu
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

class CPU extends MarketeerBase
{
    private $cpu_count;
    
    private $path_to_cpu_temp;
    
    /**
     * Returns what items this marketeer offers
     * @return array
     */
    protected function getOffering(): array
    {
        return [
            'cpu.core_count'=>'getCount',
            'cpu.temp'=>'getTemp',
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

    protected function getProcCpuinfo(): string
    {
        return file_get_contents('/proc/cpuinfo');
    }
    
    /**
     * Calculates the number of cpu cores 
     * @return int: The number of cores
     */
    private function getCPUCount(): int
    {
        if (!is_null($this->cpu_count)) {
            return $this->cpu_count;
        }
        $data = $this->getProcCpuinfo();
        $lines = explode("\n",$data);
        $count = 0;
        foreach ($lines as $line) {
            if (substr($line,0,9) == 'processor') {
                $count++;
            }
        }
        $this->cpu_count = $count;
        return $count;    
    }
    
    protected function getCount(): Response
    {
        $response = new Response();
        return $response->OK()->type('Integer')->unit(' ')->semantic('count')->value($this->getCPUCount());
    }
}
