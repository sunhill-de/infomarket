<?php

namespace Sunhill\InfoMarket\Test;

use PHPUnit\Framework\TestCase;

class InfoMarketTestCase extends TestCase
{
    
    /**
     * copied and modified from https://stackoverflow.com/questions/18558183/phpunit-mockbuilder-set-mock-object-internal-property
     * Sets the value of the property "$property_name" of object "$object" to value "$value"
     * @param unknown $object
     * @param unknown $property_name
     * @param unknown $value
     */
    public function setProtectedProperty(&$object,$property_name,$value) {
        $reflection = new \ReflectionClass($object);
        $reflection_property = $reflection->getProperty($property_name);
        $reflection_property->setAccessible(true);
        
        $reflection_property->setValue($object, $value);
    }
    
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
    
}
