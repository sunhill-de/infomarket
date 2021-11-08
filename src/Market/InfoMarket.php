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
  
}
