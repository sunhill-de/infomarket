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
     * - The value defines the base name of the callback. That means
     *     if the marketeer defines a method get + the base name -> This is the getter for the item
     *     if the marketeer defines a methode base name + _readable -> this is a method that returns if the item is readable
     *     if the marketeer defines a methode base name + _writeable -> this is a method that returns if the item is writeable
     *     if the marketeer defines a methode base name + _restrictions -> this is a method that returns possible restrictions 
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
    private function checkAllowedChars(string $name)
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
                if (!is_null($variables)) {
                    $variables = [];
                }
                return false;
            }
            switch ($offer_parts[$i]) {
                case '#':
                    if (!is_numeric($search_parts[$i])) {
                        // If it is not numeric it doesn't match 
                        if (!is_null($variables)) {
                            $variables = [];
                        }
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
                        if (!is_null($variables)) {
                            $variables = [];
                        }
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
    protected function getItemMethod(string $name, string $prefix = '', 
                                     string $postfix = '', &$variables=null)
    {
        $this->checkAllowedChars($name);

        foreach ($this->getOffer() as $offer=>$callback) {
            if ($this->offerMatches($name,$offer,$variables)) {
                return $prefix.$callback.$postfix;
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
        $variables = [];
        $method = $this->getItemMethod($name,'','_restrictions',$variables);
        if (method_exists($this,$method)) {
            return $this->$method($variables);
        } else {
            return $this->getDefaultRestrictions();
        }
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

    protected function itemIsReadable(string $name): bool
    {
        $method = $this->getItemMethod($name,'','_readable',$variables);
        if (method_exists($this,$method)) {
            return $this->$method($variables);
        } else {
            return true; // Default readable
        }
    }
    
    /**
     * Returns if the given item is writeable or raises an exception if it doesn't exist
     * @param string $name
     * @throws MarketeerException
     * @return bool
     */
    public function isWriteable(string $name, $credentials = null): bool
    {
        if ($this->offersItem($name)) {
            return $this->itemIsWriteable($name);
        } else {
            throw new MarketeerException("The item '$name' doesn't exists.");
        }        
    }

    protected function itemIsWriteable(string $name): bool
    {
        $method = $this->getItemMethod($name,'','_writeable',$variables);
        if (method_exists($this,$method)) {
            return $this->$method($variables);
        } else {
            return false; // Default not writeable
        }        
    }
    
    /**
     * Checks if the given user is on the same or higer level as the given restriction
     * @param unknown $user
     * @param unknown $restriction
     * @throws MarketeerException
     * @return bool
     */
    protected function isAccessible($user,$restriction): bool
    {
        switch ($restriction) {
            case 'anybody':
                return true;
            case 'user':
                return in_array($user,['user','advanced','admin']);
            case 'advanced':
                return in_array($user,['advanced','admin']);
            case 'admin':
                return $user == 'admin';
            default:
                throw new MarketeerException("Unkown user group '$restriction'");
        }
    }

    /**
     * Checks if the item exists, is accessible and readable. If yes the item is returned
     * @param string $name
     * @param string $user
     * @return boolean|\Sunhill\InfoMarket\Marketeers\Response\Response false if not found otherwise response
     */
    public function getItem(string $name,$user = 'anybody')
    {
        $variables = [];
        $method = $this->getItemMethod($name,'get','',$variables);
        
        if ($method === false) {
            return false;
        } else {
            $restrictions = $this->getRestrictions($name);
            if (!$this->isAccessible($user,$restrictions['read'])) {
                $response = new Response();
                return $response->error("The item '$name' is not accessible",'ITEMNOTACCESSIBLE');
            }
            if (!$this->isReadable($name)) {
                $response = new Response();
                return $response->error("The item '$name' is not readable",'ITEMNOTREADABLE');                
            }
            return $this->$method(...$variables);
        }                
    }
    
}
