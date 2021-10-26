<?php
/**
 * @file SunhillInfoMarketServiceProvider.php
 * The ServiceProvider for the InfoMarket
 * Lang en
 * Reviewstatus: 2021-10-26
 * Localization: none
 * Documentation: incomplete
 * Tests: none
 * Coverage: unknown
 * Dependencies: 
 * PSR-State: complete
 */

namespace Sunhill\InfoMarket;

use Illuminate\Support\ServiceProvider;
use Sunhill\InfoMarket\Market\InfoMarket;

class SunhillBasicServiceProvider extends ServiceProvider
{
    public function register()
    {        
        $this->app->singleton(InfoMarket::class, function () { return new Checks(); } );
        $this->app->alias(InfoMarket::class,'infomarket');
    }
    
    public function boot()
    {
    }
}
