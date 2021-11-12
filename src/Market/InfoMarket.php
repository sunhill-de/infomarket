<?php
/**
 * @file InfoMarket.php
 * Provides the InfoMarket core class
 * Lang en
 * Reviewstatus: 2021-10-26
 * Localization: none
 * Documentation: complete
 * Tests: 
 * Coverage: unknown
 * Dependencies: none
 * PSR-State: complete
 */

namespace Sunhill\InfoMarket\Market;

use Sunhill\InfoMarket\Marketeers\MarketeerBase;
use Sunhill\InfoMarket\Market\MarketException;
use Sunhill\InfoMarket\Marketeers\Response\Response;

define('CURRENT_VERSION','0.1');

class InfoMarket
{
  
  /**
   * Stores the installed marketeers
   */
  protected $marketeers = [];  
   
  /**
   * Installs a new marketeer that is reachable by this InfoMarket.
   * @param string|MarketeerBase $class if $class is a string than it is resolved to a marketeer 
   * class, if $class is a MarketeerBasse object than this object is inserted
   */
  public function installMarketeer($class)
  {
      if (is_string($class)) {
          $class = new $class();
      }
      if (is_a($class,MarketeerBase::class)) {
          $this->marketeers[] = $class;           
      } else {
          throw new MarketException('Unknown type for installMarketeer.');
      }
  }

  /**
   * Reads a single item given by $path and returns the json answer
   * @param string $path The path to the information
   * @param $credentials The 
   * @return string returns the answer of the first marketeer that offers one
   */
  public function readItem(string $path, $credentials = null): string
  {
      return $this->readSingleItem($path,$credentials);
  }

  /**
   * Reads a list of items given by a json array in $list and return the answer for this
   * items as a json result
   * @param string $list
   * @return string
   */
  public function readItemList(string $list, $credentials = null): string
  {
      
  }
  
  protected function readSingleItem(string $path, $credentials): string
  {
      if ($result = $this->readHardwiredResult($path)) {
        return $result;
      }
      foreach ($this->marketeers as $marketeer) {
          if ($marketeer->offersItem($path)) {
              return $this->getAnswer($marketeer,$path,$credentials);
          }
      }
  }

  /**
   * Hardwired informations are informations that are not routet through a marketeer but answered directly. Mostly for testing purposes
   * @param $path string: The requested path
   * @return string|bool Either the json result (if found) or false (if not found)
   */
  protected function readHardwiredResult(string $path)
  {
    switch ($path) {
      case 'infomarket.name':
          return (new Response())->OK()->request($path)->type('String')->unit(' ')->value('InfoMarket')->get();
      case 'infomarket.version':
          return (new Response())->OK()->request($path)->type('String')->unit(' ')->value(CURRENT_VERSION)->get();
    }
    return false;
  }
  
  protected function getAnswer($marketeer, string $path, $credentials): string 
  {
      if (!$marketeer->isReadable($path)) {
          $response = new Respose();
          return $response->failed()->errorCode('NOTREADABLE')->errorMessage('The item is not readable')->get();
      }
      $restrictions = $marketeer->getRestrictions($path);
      if (($restrictions['read'] !== 'anybody') && ($this->checkRestriction($restriction['read'],$credentials))) {
          $response = new Respose();
          return $response->failed()->errorCode('NOTALLOWED')->errorMessage('You are not allowed to read this item')->get();          
      }
      $response = $marketeer->getItem($path)->request($path);
      return $response->get();
  }
  
}
