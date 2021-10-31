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

namespace Sunhill\InfoMarket\Marketeers;

use Sunhill\InfoMarket\Marketeers\MarketeerException;
use Sunhill\InfoMarket\Marketeers\Response\Response;

abstract class MarketeerBase
{
    
    /**
     * Returns an array of items that this Marketeer offers
     * @return unknown
     */
    public function getOffer(): array
    {
        return $this->getOffering();    
    }
    
    /**
     * Returns an array of string that name every avaiable item that this marketeer offers
     * @return array
     */
    abstract protected function getOffering(): array;
    
    /**
     * Checks if the marketeer offers the given item
     * @param string $name
     * @return bool
     */
    public function offersItem(string $name): bool
    {
        return in_array($name,$this->getOffer());    
    }

    /**
     * Returns if the given item is readable or raises an exception if it doesn't exist
     * @param string $name
     * @throws MarketeerException
     * @return bool
     */
    public function isReadable(string $name): bool
    {
        if ($this->offersItem($name)) {
            return $this->itemIsReadable($name);
        } else {
            throw new MarketeerException("The item '$name' doesn't exists.");
        }
    }

    /**
     * This method does the check, if the item is readable at all. It is sure that the
     * item exists if this method is called.
     * @param string $name
     * @return bool
     */
    abstract protected function itemIsReadable(string $name): bool;
    
    /**
     * Returns if the given item is writeable or raises an exception if it doesn't exist
     * @param string $name
     * @throws MarketeerException
     * @return bool
     */
    public function isWritable(string $name): bool
    {
        if ($this->offersItem($name)) {
            return $this->itemIsWritable($name);
        } else {
            throw new MarketeerException("The item '$name' doesn't exists.");
        }        
    }

    /**
     * This method does the check, if the item is writeable at all. It is sure that the
     * item exists if this method is called.
     * @param string $name
     * @return bool
     */
    abstract protected function itemIsWriteable(string $name): bool;
    
    private function calculateGetterName(string $name): string
    {
        $parts = explode('.',$name);
        $allparts = [];
        for ($i=0;$i<count($parts);$i++) {
            $subparts = explode('_',$parts[$i]);
            $allparts = array_merge($allparts,$subparts);
        }
        for ($i=0;$i<count($subparts);$i++) {
            $subparts[$i] = ucfirst(strtolower($subparts[$i]));            
        }
        return 'get'.implode('',$allparts);        
    }
    
    public function getItem(string $name): Response
    {
        if ($this->offersItem($name)) {
            $method_name = $this->calculateGetterName($name);
            if (method_exists($this,$method_name)) {
                return $this->$method_name();
            } else {
                return $this->getItemResponse($name);
            }
        } else {
            throw new MarketeerException("The item '$name' doesn't exists.");
        }        
    }

    /**
     * Gets the response for this item
     * @param string $name
     * @return Response
     */
    protected function getItemResponse(string $name): Response
    {
        throw new MarketeerException("The item '$name' has no response method.");
    }
    
}
