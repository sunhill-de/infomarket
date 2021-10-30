<?php
/**
 * @file GetterInterface.php
 * Provides the GetterInterface for getting some values
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

interface GetterInterface
{
        
    /**
     * Every readable information element has to define a getValue method that returns
     * the requsted element
     * @param string $name
     * @param unknown $options an optional parameter for further informations (defaults to null)
     * @return Just the value not the json object
     * @throws ElementException
     */    
    public function getValue(string $name,$options = null);
    
    /**
     * Returns if this InfoProvider returns the given Element as readable
     * @param string $name
     * @return bool
     */
    public function hasValue(string $name): bool;
    
}
