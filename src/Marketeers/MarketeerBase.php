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
 * @todo: Merging results? 
 * 
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
        if (strpos($name,'*')) {
            throw new MarketeerException("An item query mustn't contain *: $name");
        }
        foreach ($this->getOffer() as $offer=>$callback) {
            if ($this->offerMatches($name,$offer)) {
                return true;
            }
        }
        return false;
    }

    private function offerMatches(string $search,string $offer): bool
    {
        $result = $this->getVariableParameters($search, $offer);
        
        if (is_bool($result)) {
            return $result;
        } else {
            return true;
        }
    }

    private function getVariableParameters(string $search,string $offer)
    {
        $result = [];
        if (strpos($offer,'*')) {
            $search_parts = explode('.',$search);
            $offer_parts = explode('.',$offer);
            if (count($search_parts) !== count($offer_parts)) {
                return false;
            }
            $i = 0;
            while ($i < count($search_parts)) {
                if ($i>=count($offer_parts)) {
                    return false;
                }
                switch ($offer_parts[$i]) {
                    case '*':
                        $result[] = $search_parts[$i];
                        break;
                    case '*#':
                        if (!is_numeric($search_parts[$i])) {
                            return false;
                        }
                        $result[] = $search_parts[$i];
                        break;
                    default:
                        if ($search_parts[$i] != $offer_parts[$i]) {
                            return false;
                        }
                }
                $i++;
            }
            return $result;
        } else {
            if ($offer == $search) {
                return [];
            } else {
                return false;
            }
        }
    }
    
    /**
     * Used to check, if the given item has some restrictions. It returns an associative
     * array with the field 'read' and 'write' with an associated usergroup. By default
     * this group is 'anybody' which means that anybody can access this information. 
     * @param string $name
     * @throws MarketeerException
     * @return string
     */
    public function getRestrictions(string $name): array
    {
        if ($this->offersItem($name)) {
            return $this->getItemRestrictions($name);
        } else {
            throw new MarketeerException("The item '$name' doesn't exists.");
        }
    }
    
    /**
     * Marketeers can overwrite this method to provide restrictions for the access
     * @param string $name
     * @return NULL
     */
    protected function getItemRestrictions(string $name): array
    {
        return ['read'=>'anybody','write'=>'anybody'];     
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
    public function isWritable(string $name, $credentials = null): bool
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
        for ($i=0;$i<count($allparts);$i++) {
            $allparts[$i] = ucfirst(strtolower($allparts[$i]));            
        }
        return 'get'.implode('',$allparts);        
    }
    
    public function getItem(string $name): Response
    {
        foreach ($this->getOffer() as $offer=>$callback) {
            if (($variables = $this->getVariableParameters($name,$offer)) !== false) {
                return $this->$callback(...$variables);
            }
        }
        throw new MarketeerException("The item '$name' doesn't exists.");
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
