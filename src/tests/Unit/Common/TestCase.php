<?php

namespace Test\Unit\Common;

use ReflectionClass;
use ReflectionException;
use ReflectionObject;

/**
 * @inheritdoc
 */
class TestCase extends \core\test\TestCase {
    public $appConfig = '@Test/Unit/Config/Common/main.php';

    /**
     * Invokes a inaccessible method.
     *
     * @param object|string $object
     * @param string $methodName
     * @param array $args
     *
     * @return mixed
     * @throws ReflectionException
     */
    protected function invokeMethod($object, string $methodName, array $args = []) {
        $reflection = is_object($object)
            ? new ReflectionObject($object)
            : new ReflectionClass($object);

        $methodReflection = $reflection->getMethod($methodName);
        $methodReflection->setAccessible(true);
        $result = $methodReflection->invokeArgs($object, $args);
        $methodReflection->setAccessible(false);

        return $result;
    }

    /**
     * Sets an inaccessible object property to a designated value.
     *
     * @param $object
     * @param $propertyName
     * @param $value
     * @param bool $revoke whether to make property inaccessible after setting
     *
     * @throws ReflectionException
     */
    protected function setInaccessibleProperty($object, $propertyName, $value, bool $revoke = true): void {
        $class = new ReflectionClass($object);
        while (!$class->hasProperty($propertyName)) {
            $class = $class->getParentClass();
        }
        $property = $class->getProperty($propertyName);
        $property->setAccessible(true);
        $property->setValue($object, $value);
        if ($revoke) {
            $property->setAccessible(false);
        }
    }

    /**
     * Gets an inaccessible object property.
     *
     * @param $object
     * @param $propertyName
     * @param bool $revoke whether to make property inaccessible after getting
     *
     * @return mixed
     * @throws ReflectionException
     */
    protected function getInaccessibleProperty($object, $propertyName, bool $revoke = true) {
        $class = new ReflectionClass($object);
        while (!$class->hasProperty($propertyName)) {
            $class = $class->getParentClass();
        }
        $property = $class->getProperty($propertyName);
        $property->setAccessible(true);
        $result = $property->getValue($object);
        if ($revoke) {
            $property->setAccessible(false);
        }

        return $result;
    }
}
