<?php

namespace Sunhill\InfoMarket\Tests\Unit\Response;

use Sunhill\InfoMarket\Test\InfoMarketTestCase;
use Sunhill\InfoMarket\Marketeers\Response\Response;

class ResponseTest extends InfoMarketTestCase
{
        
    protected function getElements(&$test)
    {
        return $this->getProtectedProperty($test,'elements');        
    }
    
    protected function getElement(&$test,string $element)
    {
        return $this->getElements($test)->$element;
    }
    
    public function testInitializesEmpty()
    {
        $test = new Response();
        $this->assertTrue(empty((array)$this->getElements($test)));    
    }
    
    public function testReturnAEmptyJson()
    {
        $test = new Response();
        $this->assertEquals('{',$test->get()[0]);
    }
    
    public function testAddEntry()
    {
        $test = new Response();
        $this->invokeMethod($test,'setElement',['some','value']);
        $this->assertEquals('value',$this->getElement($test,'some'));
    }
    
    public function testOK()
    {
        $test = new Response();
        $test->OK();
        $this->assertEquals('OK',$this->getElement($test,'result'));
    }
    
    public function testFailed()
    {
        $test = new Response();
        $test->Failed();
        $this->assertEquals('FAILED',$this->getElement($test,'result'));        
    }
    
    public function testRequest()
    {
        $test = new Response();
        $test->Request('test.request');
        $this->assertEquals('test.request',$this->getElement($test,'request'));        
    }

    /**
     * @dataProvider TypeProvider
     */
    public function testType($in_type,$in_subtype,$out_type,$out_subtype = null)
    {
        $test = new Response();
        try {
            $test->type($in_type,$in_subtype);            
        } catch (\Exception $e) {
            if ($out_type == 'except') {
                $this->assertTrue(true);
                return;
            }
            throw $e;
        }
        $this->assertEquals($out_type,$this->getElement($test,'type'));
        if (!is_null($out_subtype)) {
            $this->assertEquals($out_subtype,$this->getElement($test,'subtype'));
        } else {
            $this->assertFalse($test->hasElement('subtype'));
        }
    }
    
    public function TypeProvider()
    {
        return [
            ['Integer',null,'Integer'],
            ['integer',null,'Integer'],
            ['float',null,'Float'],            
            ['string',null,'String'],
            ['boolean',null,'Boolean'],
            ['date',null,'Date'],
            ['time',null,'Time'],
            ['datetime',null,'Datetime'],
            ['none',null,'except'],
            ['Array','Integer','Array','Integer'],
            ['Array','none','except'],
            ['Array','Array','except'],
            ['Record','SomeEntry','Record','SomeEntry']
        ];    
    }
    
    /**
     * @dataProvider UnitProvider
     * @param unknown $unit
     * @param unknown $out_unit_int
     * @param unknown $out_unit
     * @throws \Exception
     */
    public function testUnit($unit,$out_unit_int,$out_unit)
    {
        $test = new Response();
        try {
            $test->unit($unit);
        } catch (\Exception $e) {
            if ($out_unit_int == 'except') {
                $this->assertTrue(true);
                return;
            }
            throw $e;
        }
        $this->assertEquals($out_unit_int,$this->getElement($test,'unit_int'));
        if (!is_null($out_unit)) {
            $this->assertEquals($out_unit,$this->getElement($test,'unit'));
        }
    }
    
    public function UnitProvider()
    {
        return [
            ['s','s','s'],
            ['C','C','°C'],
            ['p','p','mmHg'],
            ['m','m','m'],
            ['c','c','cm'],
            ['l','l','lx'],
            ['M','M','MB'],
            ['G','G','GB'],
            ['T','T','TB'],
            ['P','P','%'],
            ['?','except',null],
            ['d','d',null],
            ['K','K',null],
            [' ',' ',''],
        ];        
    }

    /**
     * @dataProvider SemanticProvider
     * @param unknown $unit
     * @param unknown $out_unit_int
     * @param unknown $out_unit
     * @throws \Exception
     */
    public function testSemantic($unit,$out_unit_int,$out_unit)
    {
        $test = new Response();
        try {
            $test->semantic($unit);
        } catch (\Exception $e) {
            if ($out_unit_int == 'except') {
                $this->assertTrue(true);
                return;
            }
            throw $e;
        }
        $this->assertEquals($out_unit_int,$this->getElement($test,'semantic_int'));
        if (!is_null($out_unit)) {
            $this->assertEquals($out_unit,$this->getElement($test,'semantic'));
        }
    }
    
    public function SemanticProvider()
    {
        return [
            ['air_temp','air_temp','Air temperature'],
            ['unknown','except',null],
            
        ];
    }
    
    /**
     * @dataProvider ValueProvider
     */
    public function testValue($unit,$human_readable,$value)
    {
        $test = new Response();
        if (!is_null($unit))
        {
            $test->unit($unit);
        }
        try {
            $test->value($value);
        } catch (\Exception $e) {
            if ($human_readable == 'except') {
                $this->assertTrue(true);
            }
            return;
            throw $e;
        }
        $this->assertEquals($value,$this->getElement($test,'value'));
        $this->assertEquals($human_readable,$this->getElement($test,'human_readable_value'));
    }
        
    public function ValueProvider() 
    {
        return [
            [null,'except',3.2],
            ['m','3.2 m',3.2],
            [' ','3.2',3.2],
// Test durations            
            ['d','1 second',1],
            ['d','45 seconds',45],            
            ['d','1 minute and 1 second',61],
            ['d','1 minute and 25 seconds',85],
            ['d','2 minutes and 1 second',121],
            ['d','2 minutes and 25 seconds',145],            
            ['d','1 hour and 1 minute',60*60+60+35],
            ['d','1 hour and 2 minutes',60*60+60*2+35],
            ['d','2 hours and 1 minute',60*60*2+60+35],
            ['d','2 hours and 2 minutes',60*60*2+60*2+35],            
            ['d','1 day and 1 hour',60*60*24+60*60+35],
            ['d','1 day and 2 hours',60*60*24+2*60*60+35],
            ['d','2 days and 1 hour',60*60*24*2+60*60+35],
            ['d','2 days and 2 hours',60*60*24*2+60*60*2+35],            
            ['d','1 year and 1 day',60*60*24*365+60*60*24+60*60+35],
            ['d','1 year and 2 days',60*60*24*365+60*60*24*2+2*60*60+35],
            ['d','2 years and 1 day',60*60*24*365*2+60*60*24+60*60+35],
            ['d','2 years and 2 days',60*60*24*365*2+60*60*24*2+60*60*2+35],     
// Test Capacity
            ['K','1 Byte',1],
            ['K','2 Byte',2],
            ['K','1 kB',1000],
            ['K','1 kB',1001],
            ['K','1.1 kB',1100],        
            ['K','1 MB',1000*1000],
            ['K','1 MB',1000*1010],
            ['K','1.1 MB',1000*1100],
            ['K','1 GB',1000*1000*1000],
            ['K','1 GB',1000*1010*1000],
            ['K','1.1 GB',1000*1100*1000],
            ['K','1 TB',1000*1000*1000*1000],
            ['K','1 TB',1000*1010*1000*1000],
            ['K','1.1 TB',1000*1100*1000*1000],
        ];        
    }
    
}
