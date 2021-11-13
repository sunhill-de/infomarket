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
    private $cpu_info;
    
    private $path_to_cpu_temp;
    
    /**
     * Returns what items this marketeer offers
     * @return array
     */
    protected function getOffering(): array
    {
        return [
            'system.cpu.count'=>'getCount',
            'system.cpu.*.vendor'=>'getVendor',
            'system.cpu.*.model'=>'getModel',
        ];
    }
       
    protected function itemIsReadable(string $item): bool
    {
        return true;
    }
    
    protected function itemIsWriteable(string $item): bool
    {
        return false;
    }

    protected function getProcCpuinfo(): string
    {
        return file_get_contents('/proc/cpuinfo');
    }
    
    protected function readCPUInfo()
    {
        $this->cpu_info = [];
        
        $cpu_info = $this->getProcCpuinfo();
        $current_cpu = [];
        
        $lines = explode("\n",$cpu_info);
        foreach ($lines as $line) {
            if (strpos($line,':')) {
                list($key,$value) = explode(':',$line);
                $key = strtolower(trim($key));
                $value = trim($value);
                
                switch ($key) {
                    case 'processor':
                        if (count($current_cpu)) {
                            $this->cpu_info[] = $current_cpu;
                            $current_cpu = [];
                        }
                        break;
                    case 'vendor_id':
                        $current_cpu['vendor'] = $value;
                        break;
                    case 'model name':
                        $current_cpu['model'] = $value;
                        break;
                    case 'bogomips':
                        $current_cpu['bogomips'] = $value;
                        break;
                }
            }
        }
        $this->cpu_info[] = $current_cpu;
    }
    
    protected function readIt()
    {
        $this->readCPUInfo();    
    }
    
    private function check()
    {
        if (is_null($this->cpu_info)) {
            $this->readIt();
        }
    }
    
    /**
     * Calculates the number of cpu cores 
     * @return int: The number of cores
     */
    private function getCPUCount(): int
    {
        $this->check();
        return count($this->cpu_info);
    }
    
    /**
     * Calculates the number of cpu cores
     * @return int: The number of cores
     */
    private function getCPUVendor(int $index): string
    {
        $this->check();
        return isset($this->cpu_info[$index]['vendor'])?$this->cpu_info[$index]['vendor']:'unknown';
    }
    
    /**
     * Calculates the number of cpu cores
     * @return int: The number of cores
     */
    private function getCPUModel(int $index): string
    {
        $this->check();
        return isset($this->cpu_info[$index]['model'])?$this->cpu_info[$index]['model']:'unknown';
    }
    
    protected function getCount(): Response
    {
        $response = new Response();
        return $response
        ->OK()
        ->update('late')
        ->type('Integer')
        ->unit(' ')
        ->semantic('number')
        ->value($this->getCPUCount());
    }
    
    protected function getVendor($index): Response
    {
        $result = new Response();
        return $result
        ->OK()
        ->update('late')
        ->type('String')
        ->unit(' ')
        ->semantic('name')
        ->value($this->getCPUVendor($index));
    }

    protected function getModel($index): Response
    {
        $result = new Response();
        return $result
        ->OK()
        ->update('late')
        ->type('String')
        ->unit(' ')
        ->semantic('name')
        ->value($this->getCPUModel($index));
    }
    
}
