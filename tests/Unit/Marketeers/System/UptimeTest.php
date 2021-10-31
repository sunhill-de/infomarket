<?php

namespace Sunhill\InfoMarket\Tests\Unit\System;

use PHPUnit\Framework\TestCase;
use Sunhill\InfoMarket\Elements\System\Uptime;

class UptimeTest extends TestCase
{
    
    /**
     * copied and modified from https://stackoverflow.com/questions/18558183/phpunit-mockbuilder-set-mock-object-internal-property
     * Returns the value of the property "$property_name" of object "$object"
     * @param unknown $object
     * @param unknown $property_name
     */
    public function getProtectedProperty(&$object,$property_name) {
        $reflection = new \ReflectionClass($object);
        $reflection_property = $reflection->getProperty($property_name);
        $reflection_property->setAccessible(true);
        
        return $reflection_property->getValue($object);
    }
    
    /**
     * copied from https://jtreminio.com/blog/unit-testing-tutorial-part-iii-testing-protected-private-methods-coverage-reports-and-crap/
     * Calls the protected or private method "$methodName" of the object $object with the given parameters and
     * returns its result
     * @param unknown $object
     * @param unknown $methodName
     * @param array $parameters
     * @return unknown
     */
    public function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
        
        return $method->invokeArgs($object, $parameters);
    }
   
    public function testReadSuccess()
    {
        $test = new Uptime();
        $data = $this->invokeMethod($test,'getData');
        $this->assertFalse(empty($data));
    }
    
    public function testHasElement($element,$expect)
}
