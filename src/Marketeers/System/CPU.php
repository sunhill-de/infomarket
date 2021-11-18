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
        
    private $cpu_temp;
    
    /**
     * Returns what items this marketeer offers
     * @return array
     */
    protected function getOffering(): array
    {
        return [
            'system.cpu.count'=>'Count',
            'system.cpu.#.vendor'=>'Vendor',
            'system.cpu.#.model'=>'Model',
            'system.cpu.#.bogomips'=>'Bogomips',
            'system.cpu.temp'=>'Temp',            
        ];
    }
       
    protected function getProcCpuinfo(): string
    {
        return file_get_contents('/proc/cpuinfo');
    }
    
    protected function getThermalDir(): string
    {
        return '/sys/class/thermal';    
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
    
    protected function readCPUTemp()
    {
        $dir = $this->getThermalDir();
        
        $directory = dir($dir);
        while (false !== ($entry = $directory->read())) {
            if (substr($entry,0,12) == 'thermal_zone') {
                $type = trim(file_get_contents($dir.'/'.$entry.'/type'));
                if (($type == 'x86_pkg_temp') || ($type == 'cpu-thermal')) {
                    $temp = trim(file_get_contents($dir.'/'.$entry.'/temp'));
                    
                    $this->cpu_temp = $temp/1000;
                }
            }
        }
    }
    
    protected function readIt()
    {
        $this->readCPUInfo();
        $this->readCPUTemp();
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
    
    /**
     * Calculates the number of cpu cores
     * @return int: The number of cores
     */
    private function getCPUBogomips(int $index): string
    {
        $this->check();
        return isset($this->cpu_info[$index]['bogomips'])?$this->cpu_info[$index]['bogomips']:0;
    }
    
    /**
     * Calculates the number of cpu cores
     * @return int: The number of cores
     */
    private function getCPUTemp(): string
    {
        $this->check();
        return isset($this->cpu_temp)?$this->cpu_temp:0;
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
    
    protected function getBogomips($index): Response
    {
        $result = new Response();
        return $result
        ->OK()
        ->update('late')
        ->type('Float')
        ->unit(' ')
        ->semantic('name')
        ->value($this->getCPUBogomips($index));
    }
    
    protected function getTemp(): Response
    {
        $result = new Response();
        return $result
        ->OK()
        ->update('late')
        ->type('Float')
        ->unit('C')
        ->semantic('temp')
        ->value($this->getCPUTemp());
    }
    
}
