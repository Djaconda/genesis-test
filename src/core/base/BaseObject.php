<?php

namespace core\base;

use core\app\ErrorHandling;
use core\contracts\Application;
use PHPKitchen\DI\Contracts\ContainerAware;
use PHPKitchen\DI\Contracts\ServiceLocatorAware;
use PHPKitchen\DI\Mixins\ContainerAccess;
use PHPKitchen\DI\Mixins\ServiceLocatorAccess;
use ReflectionClass;
use yii\base\Configurable;

/**
 * Introduces an important object initialization life cycle. In particular,
 * creating an new instance of Object or its derived class will involve the following life cycles sequentially:
 *
 * 1. the class constructor is invoked;
 * 2. object properties are initialized according to the given configuration;
 * 3. the `init()` method is invoked.
 *
 * In the above, both Step 2 and 3 occur at the end of the class constructor. It is recommended that
 * you perform object initialization in the `init()` method because at that stage, the object configuration
 * is already applied.
 *
 * In order to ensure the above life cycles, if a child class of Object needs to override the constructor,
 * it should be done like the following:
 *
 * ```php
 * public function __construct($param1, $param2, ..., $config = [])
 * {
 *     ...
 *     parent::__construct($config);
 * }
 * ```
 *
 * That is, a `$config` parameter (defaults to `[]`) should be declared as the last parameter
 * of the constructor, and the parent implementation should be called at the end of the constructor.
 *
 * @property Application $serviceLocator
 * @method Application getServiceLocator()
 *
 * @author Dmitry Kolodko <dangel@bitfocus.com>
 */
class BaseObject implements ContainerAware, ServiceLocatorAware, Configurable {
    use MagicAccessTrait;
    use ServiceLocatorAccess;
    use ContainerAccess;
    use ErrorHandling;

    /**
     * Constructor.
     * The default implementation does two things:
     *
     * - Initializes the object with the given configuration `$config`.
     * - Call [[init()]].
     *
     * If this method is overridden in a child class, it is recommended that
     *
     * - the last parameter of the constructor is a configuration array, like `$config` here.
     * - call the parent implementation at the end of the constructor.
     *
     * @param array $config name-value pairs that will be used to initialize the object properties
     */
    public function __construct($config = []) {
        if (!empty($config)) {
            $this->serviceLocator->configureObject($this, $config);
        }
        $this->init();
    }

    /**
     * Initializes the object.
     * This method is invoked at the end of the constructor after the object is initialized with the
     * given configuration.
     */
    public function init(): void {
    }

    protected function canBeInstantiated($classOrInterfaceName) {
        return class_exists($classOrInterfaceName) || interface_exists($classOrInterfaceName);
    }

    protected function isClassImplementsInterface($classOrInterface, $interface) {
        $classReflection = new ReflectionClass($classOrInterface);

        return $classReflection->implementsInterface($interface);
    }
}
