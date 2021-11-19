<?php
/**
 * @file Remote.php
 * Provides access to remote InfoMarketServer
 * Lang en
 * Reviewstatus: 2021-11-15
 * Localization: none
 * Documentation: complete
 * Tests:
 * Coverage: unknown
 * Dependencies: none
 * PSR-State: complete
 */

namespace Sunhill\InfoMarket\Marketeers\Virtual;

use Sunhill\InfoMarket\Marketeers\MarketeerBase;
use Sunhill\InfoMarket\Marketeers\Response\Response;

class Remote extends MarketeerBase
{

    protected $remote_server;
    
    protected $remote_port = 15616;
    
    protected $mount_point;
    
    /**
     * Sets the ip or hostname for the remote server
     * @param string $server
     * @return Remote
     */
    public function setRemoteServer(string $server): Remote
    {
        $this->remote_server = $server;
        return $this;
    }

    /**
     * Returns the ip or hostname of the remote server
     * @return string
     */
    public function getRemoteServer(): string
    {
        return $this->remote_server;    
    }
    
    public function setRemotePort(int $port): Remote
    {
        $this->remote_port = $port;
        return $this;
    }
    
    public function getRemotePort(): int
    {
        return $this->remote_port;    
    }
    
    protected function accessServer(string $query)
    {
        
    }
        
}
