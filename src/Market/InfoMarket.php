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

class InfoMarket
{
  
  /**
   * Stores the installed marketeers
   */
  protected $marketeers = [];  
  
  public function __construct()
  {
    $this->installMarketeers();
  }
  
  protected function installMarketeers()
  {
  }
  
  protected function installMarketeer(string $class_name,string $mount_point)
  {
  }
  
}
