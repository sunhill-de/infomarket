<?php
/**
 * @file InfoElement.php
 * Provides the base class for InfoElements
 * Lang en
 * Reviewstatus: 2021-10-30
 * Localization: none
 * Documentation: complete
 * Tests:
 * Coverage: unknown
 * Dependencies: none
 * PSR-State: complete
 */

namespace Sunhill\InfoMarket\Elements;

use Sunhill\InfoMarket\Elements\ElementException;

abstract class InfoElement
{
        
    public function hasItem(string $name): bool
    {
        return $this->itemExsists($name);    
    }
    
    abstract protected function itemExists(string $name): bool;
    
    function isReadable(string $name): bool
    {
        if ($this->hasItem($name)) {
            return $this->itemIsReadable($name);
        } else {
            throw new ElementException("The item '$name' doesn't exists.");
        }
    }
    
    function isWritable(string $name): bool
    {
        if ($this->hasItem($name)) {
            return $this->itemIsWritable($name);
        } else {
            throw new ElementException("The item '$name' doesn't exists.");
        }        
    }
}
