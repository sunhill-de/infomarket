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

namespace Sunhill\InfoMarket\Elements\System;

use Sunhill\InfoMarket\Elements\InfoElement;

class Uptime extends InfoElement
{
    protected function getData()
    {
        $data = file_get_contents('/proc/uptime');
        return $data;
    }
    
    protected function itemExists(string $name): bool
    {
        switch ($name) {
            case 'uptime':
            case 'idle':
                return true;
            default:
                return false;
        }
    }
    
    protected function itemIsReadable(): bool
    {
        return true;
    }
    
    protected function itemIsWritable(): bool
    {
        return false;
    }
}