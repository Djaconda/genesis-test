<?php

namespace core\base;

use yii\base\InvalidCallException;
use yii\base\UnknownMethodException;
use yii\base\UnknownPropertyException;

/**
 * Implements the *property* feature.
 *
 * A property is defined by a getter method (e.g. `getLabel`), and/or a setter method (e.g. `setLabel`). For example,
 * the following getter and setter methods define a property named `label`:
 *
 * ```php
 * private $_label;
 *
 * public function getLabel() {
 *     return $this->_label;
 * }
 *
 * public function setLabel($value) {
 *     $this->_label = $value;
 * }
 * ```
 *
 * Property names are *case-insensitive*.
 *
 * A property can be accessed like a member variable of an object. Reading or writing a property will cause the invocation
 * of the corresponding getter or setter method. For example,
 *
 * ```php
 * // equivalent to $label = $object->getLabel();
 * $label = $object->label;
 * // equivalent to $object->setLabel('abc');
 * $object->label = 'abc';
 * ```
 *
 * If a property has only a getter method and has no setter method, it is considered as *read-only*. In this case, trying
 * to modify the property value will cause an exception.
 *
 * One can call [[hasProperty()]], [[canGetProperty()]] and/or [[canSetProperty()]] to check the existence of a property.
 *
 * @author Dmitry Bukavin <4o.djaconda@gmail.com>
 */
trait MagicAccessTrait {
    /**
     * Returns the value of an object property.
     *
     * Do not call this method directly as it is a PHP magic method that
     * will be implicitly called when executing `$value = $object->property;`.
     *
     * @param string $name the property name
     *
     * @return mixed the property value
     * @throws UnknownPropertyException if the property is not defined
     * @throws InvalidCallException if the property is write-only
     * @see __set()
     */
    public function __get($name) {
        $result = null;
        $getter = 'get' . $name;
        if ($this->hasMethod($getter)) {
            return $this->$getter();
        }

        if (!$this->runCustomHandler($this->getCustomGetterHandler(), $result, $name)) {
            if ($this->hasMethod('set' . $name)) {
                throw new InvalidCallException('Getting write-only property: ' . static::class . '::' . $name);
            } else {
                throw new UnknownPropertyException('Getting unknown property: ' . static::class . '::' . $name);
            }
        } else {
            return $result;
        }
    }

    /**
     * Sets value of an object property.
     *
     * Do not call this method directly as it is a PHP magic method that
     * will be implicitly called when executing `$object->property = $value;`.
     *
     * @param string $name the property name or the event name
     * @param mixed $value the property value
     *
     * @throws UnknownPropertyException if the property is not defined
     * @throws InvalidCallException if the property is read-only
     * @see __get()
     */
    public function __set($name, $value) {
        $result = null;
        $setter = 'set' . $name;
        if ($this->hasMethod($setter)) {
            $this->$setter($value);
        } elseif (($pos = strpos($name, ' ')) !== false && $pos !== 0) {
            $function = trim(substr($name, 0, $pos));
            $argument = trim(substr($name, $pos));
            if ($this->hasMethod($function)) {
                $this->$function($argument, $value);
            } else {
                throw new InvalidCallException('Trying to set property through not existing method: ' . static::class . '::' . $name);
            }
        } elseif ($this->runCustomHandler($this->getCustomSetterHandler(), $result, $name, $value)) {
            return;
        } elseif ($this->hasMethod('get' . $name)) {
            throw new InvalidCallException('Setting read-only property: ' . static::class . '::' . $name);
        } else {
            throw new UnknownPropertyException('Setting unknown property: ' . static::class . '::' . $name);
        }
    }

    /**
     * Checks if a property is set, i.e. defined and not null.
     *
     * Do not call this method directly as it is a PHP magic method that
     * will be implicitly called when executing `isset($object->property)`.
     *
     * Note that if the property is not defined, false will be returned.
     *
     * @param string $name the property name or the event name
     *
     * @return boolean whether the named property is set (not null).
     * @see http://php.net/manual/en/function.isset.php
     */
    public function __isset($name) {
        $result = null;
        $getter = 'get' . $name;
        if ($this->hasMethod($getter)) {
            return $this->$getter() !== null;
        }

        if ($this->runCustomHandler($this->getCustomIssetHandler(), $result, $name)) {
            return $result;
        }

        return false;
    }

    /**
     * Sets an object property to null.
     *
     * Do not call this method directly as it is a PHP magic method that
     * will be implicitly called when executing `unset($object->property)`.
     *
     * Note that if the property is not defined, this method will do nothing.
     * If the property is read-only, it will throw an exception.
     *
     * @param string $name the property name
     *
     * @throws InvalidCallException if the property is read only.
     * @see http://php.net/manual/en/function.unset.php
     */
    public function __unset($name) {
        $result = null;
        $setter = 'set' . $name;
        if ($this->hasMethod($setter)) {
            $this->$setter(null);
        } elseif ($this->runCustomHandler($this->getCustomUnsetHandler(), $result, $name)) {
            return;
        } elseif ($this->hasMethod('get' . $name)) {
            throw new InvalidCallException('Unsetting read-only property: ' . static::class . '::' . $name);
        }
    }

    /**
     * Calls the named method which is not a class method.
     *
     * Do not call this method directly as it is a PHP magic method that
     * will be implicitly called when an unknown method is being invoked.
     *
     * @param string $name the method name
     * @param array $params method parameters
     *
     * @return mixed the method return value
     * @throws UnknownMethodException when calling unknown method
     */
    public function __call($name, $params) {
        $result = null;
        if ($this->runCustomHandler($this->getCustomCallHandler(), $result, $name, $params)) {
            return $result;
        }
        throw new UnknownMethodException('Calling unknown method: ' . static::class . "::$name()");
    }

    /**
     * Returns a value indicating whether a property is defined.
     * A property is defined if:
     *
     * - the class has a getter or setter method associated with the specified name
     *   (in this case, property name is case-insensitive);
     * - the class has a member variable with the specified name (when `$checkVars` is true);
     *
     * @param string $name the property name
     * @param boolean $checkVars whether to treat member variables as properties
     *
     * @return boolean whether the property is defined
     * @see canGetProperty()
     * @see canSetProperty()
     */
    public function hasProperty($name, $checkVars = true) {
        return $this->canGetProperty($name, $checkVars) || $this->canSetProperty($name, false);
    }

    /**
     * Returns a value indicating whether a property can be read.
     * A property is readable if:
     *
     * - the class has a getter method associated with the specified name
     *   (in this case, property name is case-insensitive);
     * - the class has a member variable with the specified name (when `$checkVars` is true);
     *
     * @param string $name the property name
     * @param boolean $checkVars whether to treat member variables as properties
     *
     * @return boolean whether the property can be read
     * @see canSetProperty()
     */
    public function canGetProperty($name, $checkVars = true) {
        return method_exists($this, 'get' . $name) || ($checkVars && property_exists($this, $name));
    }

    /**
     * Returns a value indicating whether a property can be set.
     * A property is writable if:
     *
     * - the class has a setter method associated with the specified name
     *   (in this case, property name is case-insensitive);
     * - the class has a member variable with the specified name (when `$checkVars` is true);
     *
     * @param string $name the property name
     * @param boolean $checkVars whether to treat member variables as properties
     *
     * @return boolean whether the property can be written
     * @see canGetProperty()
     */
    public function canSetProperty($name, $checkVars = true) {
        return method_exists($this, 'set' . $name) || ($checkVars && property_exists($this, $name));
    }

    /**
     * Returns a value indicating whether a method is defined.
     *
     * The default implementation is a call to php function `method_exists()`.
     * You may override this method when you implemented the php magic method `__call()`.
     *
     * @param string $name the method name
     *
     * @return boolean whether the method is defined
     */
    public function hasMethod($name) {
        $result = false;
        if (method_exists($this, $name)) {
            $result = true;
        } else {
            $this->runCustomHandler($this->getCustomCallHandler(), $result, $name);
        }

        return $result;
    }

    protected function runCustomHandler($name, &$handlerResult, ...$arguments) {
        $result = false;
        if (is_callable($name)) {
            $handlerResult = call_user_func_array($name, $arguments);
            $result = true;
        } elseif (is_string($name) && method_exists($this, $name)) {
            $handlerResult = call_user_func_array([$this, $name], $arguments);
            $result = true;
        }

        return $result;
    }

    protected function getCustomGetterHandler() {
        return 'handleMissingGetter';
    }

    protected function getCustomSetterHandler() {
        return 'handleMissingSetter';
    }

    protected function getCustomIssetHandler() {
        return 'handleIssetCall';
    }

    protected function getCustomUnsetHandler() {
        return 'handleUnsetCall';
    }

    protected function getCustomCallHandler() {
        return 'handleCall';
    }

    protected function getCustomHasMethodHandler() {
        return 'handleHasMethod';
    }
}
