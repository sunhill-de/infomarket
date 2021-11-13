<?php
/**
 * @file Disk.php
 * Provides information about the Disk(s)
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

class Disk extends MarketeerBase
{
    
    private $disks = null;
    
    private $partitions = null;
    
    private $raids = null;
    
    protected function getLsBlk()
    {
        exec('lsblk -r',$output);
        
        return $output;   
    }
    
    protected function getMdstat()
    {
        if (file_exists('/proc/mdstat')) {
            return file_get_contents('/proc/mdstat');
        } else {
            return '';
        }            
    }
    
    protected function getDF()
    {
        exec('df--output',$output);
        
        return $output;        
    }
    
    private function readDisks()
    {
        $lsblk = $this->getLsBlk(); // Read the output from lsblk -r
        $lines = explode("\n",$lsblk);
        foreach($lines as $line) {
            if (empty($line)) {
                break;
            }
            list($name,$id,$rm,$size,$ro,$type) = explode(' ',$line);
            if (isset($line[6])) {
                $mount = $line[6];
            } else {
                $mount = null;
            }
            switch ($type) {
                case 'disk':
                    if (is_null($this->disks)) {
                        $this->disks = [];
                    }
                    $this->disks[$name] = ['ID'=>$id,'removable'=>$rm,'size'=>$size,'readonly'=>$ro];
                    break;
                case 'part':
                    if (is_null($this->partitions)) {
                        $this->partitions = [];
                    }
                    $this->partitions[$name] = ['ID'=>$id,'removable'=>$rm,'size'=>$size,'readonly'=>$ro,'mount'=>$mount];
                    break;
            }
        }
    }
    
    private function readPartitions()
    {
        $df = $this->getDF();
        $lines = explode("\n",$df);
        foreach ($lines as $line) {
            if (empty($line)) {
                break;
            }
            if ($line[0] == '/') {
                list($file,$type,$inodes,$inodes_used,$inodes_free,
                     $inodes_percent,$size,$used,$free,$percent,$file,$mount) = explode(' ',$line);                
                     if (isset($this->partitions[$file])) {
                         $this->partitions[$file]['used'] = $used;
                         $this->partitions[$file]['free'] = $free;
                     }
            }
        }            
    }
    
    private function readRaids()
    {
        
    }
    
    protected function readIt() 
    {
        $this->readDisks();
        $this->readPartitions();
        $this->readRaids();
    }
    
    /**
     * Returns what items this marketeer offers
     * @return array
     */
    protected function getOffering(): array
    {
        return [
            'disk.count'=>'getDiskCount',
            'disk.*.capacity'=>'getDiskCapacity',
            'disk.*.name'=>'getDiskName',
            'disk.*.vendor'=>'getDiskVendor',
            
            'partitions.count'=>'getPartititionCount',
            'partitions.*.name'=>'getPartititionName',
            'partitions.*.capacity'=>'getPartitionCapacity',
            'partitions.*.used.bytes'=>'getPartititionUsedBytes',
            'partitions.*.used.capacity'=>'getPartititionUsedCapacity',
            'partitions.*.used.percent'=>'getPartititionUsedPercent',
            'partitions.*.free.bytes'=>'getPartititionFreeBytes',
            'partitions.*.free.capacity'=>'getPartititionFreeCapacity',
            'partitions.*.free.percent'=>'getPartititionFreePercent',
            
            'raid.count'=>'getRaidCount',
            'raid.*.name'=>'getRaidName',
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

    private function getAnythingByIndex($list,int $index) 
    {
        $i = 0;
        foreach ($list as $key => $value) {
            if ($index == $i++) {
                return $key;
            }
        }
        return false;
    }
    
    private function getDiskByIndex(int $index)
    {
        return $this->getAnythingByIndex($index);
    }
    
    private function check()
    {
        if (is_null($this->disks)) {
            $this->readIt();
        }        
    }
    
    protected function getDiskCount() : Response
    {
        $this->check();
        
        $return = new Response();
        return $return->number(count($this->disks));
    }
    
    protected function getDiskCapacity($index) : Response
    {
        $this->check();
        
        $disk = $this->getAnythingByIndex($this->disks,$index);
        $return = new Response();
        return $return->type('Integer')->unit('C')->semantic('capacity')->value($this->disks[$disk]['size']);
    }

    protected function getDiskName($index): Response
    {
        $this->check();
        
        $disk = $this->getAnythingByIndex($this->disks,$index);
        $return = new Response();
        return $return->type('String')->unit(' ')->value($disk);        
    }
    
    protected function getPartitionsCount() : Response
    {
        $this->check();
        
        $return = new Response();
        return $return->number(count($this->partitions));
    }
    
    protected function getPartitionName($index) : Response
    {
        $this->check();
        
        $disk = $this->getAnythingByIndex($this->partitions,$index);
        $return = new Response();
        return $return->type('String')->unit(' ')->value($disk);        
    }
    
    protected function getPartitionCapacity($index) : Response
    {
        $this->check();
        
        $disk = $this->getAnythingByIndex($this->partitions,$index);
        $return = new Response();
        return $return->type('String')->unit(' ')->value($this->partitions[$disk]['size']);
    }
}
