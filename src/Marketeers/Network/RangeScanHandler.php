<?php 

namespace Sunhill\Systemmonitor\Lib;

class RangeScanHandler extends HandlerBase {
    
    const Timeout = 600; // 5 Minutes
    
    const OneYear = 60*60*24*365;
    
    protected function DoParseProtocol($protocol) {
        $lines = explode("\n",$protocol);
        $header = array_shift($lines);
        $footer = array_pop($lines);
        array_pop($lines); // Ignore that the monitor is online (it's obvious)
        $monitor = array_pop($lines); // The IP Line of the Monitor
        
        if (count($lines)%3) {
            throw new \Exception("Unexpected linecount");
        }
        
        $i=0;
        while ($i<count($lines)) {
            $deviceBlock = $lines[$i]."\n".$lines[$i+1]."\n".$lines[$i+2]."\n";
            $this->handleDevice($deviceBlock);
            $i += 3;
        }
        $this->OutdateEntries();
        $this->Summarize();
    }
    
    /**
     * Searches for outdated entries
     */
    protected function OutdateEntries() {
        foreach ($this->config as $mac => $entry) {
            if ($entry['IsOnline'] && ($entry['LastSeen'] + RangeScanHandler::Timeout < $this->GetCurrentTime())) {
                $this->OutdateEntry($mac);
            }
        }
    }
    
    protected function Summarize() {
        foreach ($this->config as $mac => $entry) {
            if (count($entry['LastOnline']) > 1) { // There must be at least one episode
                $i=0;
                while ($i < count($entry['LastOnline'])-1) {
                    if ($entry['LastOnline'][$i+1] < $this->GetCurrentTime()-RangeScanHandler::OneYear) {
                        $this->config[$mac]['Online']['LastUpdate'] = $this->GetCurrentTime();                        
                        $this->config[$mac]['Online']['Count']++;
                        $this->config[$mac]['Online']['Total']+= $entry['LastOnline'][$i+1]-$entry['LastOnline'][$i];
                        // Remove the entries from the LastOnline array
                        array_shift($this->config[$mac]['LastOnline']);
                        array_shift($this->config[$mac]['LastOnline']);
                    }
                    $i += 2;
                }
            }
        }
    }
    
    /**
     * Marks the entry as outdated
     * @param unknown $mac
     */
    protected function OutdateEntry($mac) {
        $this->config[$mac]['LastOnline'][] = $this->config[$mac]['LastSeen'];
        $this->config[$mac]['IsOnline'] = false;
    }
    
    protected function handleDevice($deviceBlock) {
        $IP           = $this->ExtractIP($deviceBlock);
        $mac          = $this->ExtractMAC($deviceBlock);
        $hostname     = $this->ExtractHostname($deviceBlock);
        $latency      = $this->ExtractLatency($deviceBlock);
        $manufacturer = $this->ExtractManufacturer($deviceBlock);
        if (isset($this->config[$mac])) {
            if ($this->IsOnline()) {
                $this->handleKnownOnlineDevice($IP,$mac,$hostname,$latency,$manufacturer);                
            } else {
                $this->handleKnownOfflineDevice($IP,$mac,$hostname,$latency,$manufacturer);                
            }
        } else {
            $this->handleNewDevice($IP,$mac,$hostname,$latency,$manufacturer);
        }
    }
    
    /**
     * This device was already stores and was online the last time
     * @param unknown $IP
     * @param unknown $mac
     * @param unknown $hostname
     * @param unknown $latency
     * @param unknown $manufacturer
     */
    protected function handleKnownOnlineDevice($IP,$mac,$hostname,$latency,$manufacturer) {
        $this->updateLastSeen($mac,$latency); // Just update LastSeen
    }
    
    /**
     * This device was already stored but offline the last time
     * @param unknown $IP
     * @param unknown $mac
     * @param unknown $hostname
     * @param unknown $latency
     * @param unknown $manufacturer
     */
    protected function handleKnownOfflineDevice($IP,$mac,$hostname,$latency,$manufacturer) {
        $this->CreateNewEpisode($mac,$IP);
        $this->updateLastSeen($mac,$latency);
    }

    /**
     * This device was not stored before
     * @param unknown $IP
     * @param unknown $mac
     * @param unknown $hostname
     * @param unknown $latency
     * @param unknown $manufacturer
     */
    protected function handleNewDevice($IP,$mac,$hostname,$latency,$manufacturer) {
        $this->config[$mac] = [
            'MAC'=>$mac,
            'Manufacturer'=>$manufacturer,
            'HostName'=>$hostname,
            'FirstSeen'=>$this->GetCurrentTime(),
            'LastOnline'=>[]
        ];
        
        $this->CreateNewEpisode($mac,$ip);
        $this->updateLastSeen($mac,$latency);        
    }
    
    /**
     * Creates a new episode in the LastOnline array
     * @param unknown $mac
     */
    protected function CreateNewEpisode($mac,$ip) {
        $this->config[$mac]['LastOnline'][] = $this->GetCurrentTime();    
        $this->config[$mac]['IsOnline'] = true;
        $this->config[$mac]['LastIP'] = $ip;
    }
    
    /**
     * Updates the LastSeen field
     * @param unknown $mac
     */
    protected function updateLastSeen($mac,$latency) {
        $this->config[$mac]['LastSeen'] = $this->GetCurrentTime();
        $this->config[$mac]['LastLatency'] = $latency;
    }
    
    protected function ExtractIP($deviceBlock) {
        if (preg_match('/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}(?:\/\d{2})?/',$deviceBlock,$matches)) {
            return $matches[0];
        } else {
            return null;
        }
    }
    
    protected function ExtractMAC($deviceBlock) {
        if (preg_match('/[0-9A-F]{2}:[0-9A-F]{2}:[0-9A-F]{2}:[0-9A-F]{2}:[0-9A-F]{2}:[0-9A-F]{2}/',$deviceBlock,$matches)) {
            return $matches[0];
        } else {
            return null;
        }        
    }
    
    protected function ExtractHostname($deviceBlock) {
        if (preg_match('/([a-zA-Z]+\.)+[a-zA-Z]+/',$deviceBlock,$matches)) {
            return $matches[0];
        } else {
            return null;
        }        
    }
    
    protected function ExtractLatency($deviceBlock) {
        if (preg_match('/\(([0-9.]+)s/',$deviceBlock,$matches)) {
            return $matches[1];
        } else {
            return null;
        }        
    }
    
    protected function ExtractManufacturer($deviceBlock) {
        if (preg_match('/[0-9A-F]{2}:[0-9A-F]{2}:[0-9A-F]{2}:[0-9A-F]{2}:[0-9A-F]{2}:[0-9A-F]{2} \(([a-zA-Z0-9 ()]+)\)/',$deviceBlock,$matches)) {
            return $matches[1];
        } else {
            return null;
        }        
    }
}