<?php
/**
 * @file InfoElement.php
 * Provides the base class for InfoElements
 * Lang en
 * Reviewstatus: 2021-10-30
 * Localization: none
 * Documentation: complete
 * Tests: Unit/Marketeers/MarketeersTest.php
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
     * Returns an array of items that this Marketeer offers. The result is a associative array:
     * - The key defines the offered path
     * - The value defines the name of the callback
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
     * Raises an exception if $test contains *, # or ?
     */
    private function checkAllowedChars(string $test)
    {
        if (strpos($name,'*')) {
            throw new MarketeerException("An item query mustn't contain *: $name");
        }
        if (strpos($name,'#')) {
            throw new MarketeerException("An item query mustn't contain #: $name");
        }
        if (strpos($name,'?')) {
            throw new MarketeerException("An item query mustn't contain ?: $name");
        }        
    }
    
    /**
     * Tests if the string $search matches to the string $offer.
     * @param $search string: The string to search for. Mustn't contain *, # or ?
     * @param $offer string: The string that offers a possible match
     * @param &$variables null|array: If not null the matches of #,? and * fields are stored here
     * @return bool: True, if the offer matches the search, otherwise false
     */
    private function offerMatches(string $search,string $offer,&$variables=null): bool
    {
        if (!is_null($variables) && !is_array($variables)) {
            $variables = [];
        }
        $search_parts = explode('.',$search);
        $offer_parts = explode('.',$offer);
        
        $i = 0;
        while (true) {
            if (($i == count($search_parts)) && ($i == count($offer_parts))) {
                // At this point the search matches the offer
                return true;
            }
            if (($i == count($search_parts)) || ($i == count($offer_parts))) {
                // At this point either search or offer is shorter, so the offer doesn't match
                return false;
            }
            switch ($offer_parts[$i]) {
                case '#':
                    if (!is_numeric($search_parts[$i])) {
                        // If it is not numeric it doesn't match 
                        return false;
                    }
                    // otherwise treat it like a '?'
                case '?':
                    if (!is_null($variables)) {
                        $variables[] = $search_parts[$i];
                    }
                    break;
                case '*':
                    if (!is_null($variables)) {
                        $temp = [];
                        for ($j = $i; $j < count($search_parts); $j++) {
                            $temp[] = $search_parts[$j];
                        }
                        $variables[] = implode('.',$temp);
                    }    
                    return true;
                    break;
                default:
                    if ($search_parts[$i] != $offer_parts[$i]) {
                       return false;
                    }
            }
            $i++;
        }    
    }

    /**
     * Checks if the marketeer offers the given item
     * @param string $name The item to search for
     * @return bool, True if the marketeer offers this item otherwise false
     */
    public function offersItem(string $name): bool
    {
        $this->checkAllowedChars($name);

        foreach ($this->getOffer() as $offer=>$callback) {
            if ($this->offerMatches($name,$offer)) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * If the given item is offered then it returns the name of the item-method otherwise false
     * @param $name string: The item to search for
     * @returns false|string see above
     */
    protected function getItemMethod(string $name)
    {
        $this->checkAllowedChars($name);

        foreach ($this->getOffer() as $offer=>$callback) {
            if ($this->offerMatches($name,$offer)) {
                return $callback;
            }
        }
        return false;    
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
     * This method look for a method that is names 'item-method'_restrictions. if found returns its value
     * otherwise return the default restrictions
     * @param string $name
     * @return array
     */
    protected function getItemRestrictions(string $name): array
    {
        $method = $this->
    }
    
    protected function getDefaultRestrictions(): array
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
