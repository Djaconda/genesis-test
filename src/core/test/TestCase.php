<?php

namespace core\test;

use core\AppLoader;
use core\frontend\Application;
use DMS\PHPUnitExtensions\ArraySubset\ArraySubsetAsserts;
use PHPKitchen\CodeSpecs\Mixin\TesterInitialization;
use PHPKitchen\DI\Container;
use PHPKitchen\DI\Mixins\ServiceLocatorAccess;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\UnknownMethodException;
use yii\base\UnknownPropertyException;
use yii\test\BaseActiveFixture;
use yii\test\FixtureTrait;

/**
 * @property \core\contracts\Application $serviceLocator
 * @method Application getServiceLocator()
 *
 * @mixin FixtureTrait;
 * @mixin FakerTrait;
 * @mixin TesterInitialization;
 */
class TestCase extends \PHPUnit\Framework\TestCase {
    use FixtureTrait;
    use FakerTrait;
    use ServiceLocatorAccess;
    use TesterInitialization;

    /**
     * @var array|string the application configuration that will be used for creating an application instance for each test.
     * You can use a string to represent the file path or path alias of a configuration file.
     * The application configuration array may contain an optional `class` element which specifies the class
     * name of the application instance to be created. By default, a [[\yii\web\Application]] instance will be created.
     */
    public $appConfig = '@Test/phpunit/config/main.php';
    /**
     * @var Application application instance
     */
    public $app;
    /**
     * @var Container dependency injection container
     */
    public $container;

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
     */
    public function __get($name) {
        $fixture = $this->getFixture($name);
        if ($fixture !== null) {
            return $fixture;
        }

        throw new UnknownPropertyException('Getting unknown property: ' . static::class . '::' . $name);
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
        $fixture = $this->getFixture($name);
        if ($fixture instanceof BaseActiveFixture) {
            return $fixture->getModel(reset($params));
        }

        throw new UnknownMethodException('Unknown method: ' . static::class . "::$name()");
    }

    /**
     * @before
     */
    protected function bootstrapDependencies(): void {

        $this->destroyApplication();
        $this->app = $this->mockApplication();
        $this->unloadFixtures();
        $this->loadFixtures();
        $this->executeBeforeTest();
    }

    /**
     * This method is called before a test is executed but after {@link setUp()}.
     * Override this method to run actions before each test.
     */
    protected function executeBeforeTest(): void {
    }

    /**
     * @inheritdoc
     */
    protected function tearDown(): void {
        $this->executeAfterTest();
        $this->unloadFixtures();
        parent::tearDown();
    }

    /**
     * This method is called after a test is executed and before {@link tearDown()}.
     * Override this method to run actions after each test.
     */
    protected function executeAfterTest(): void {
    }

    /**
     * Mocks up the application instance.
     *
     * @param array $config the configuration that should be used to generate the application instance.
     * If null, [[appConfig]] will be used.
     *
     * @return \yii\web\Application|\yii\console\Application the application instance
     * @throws InvalidConfigException if the application configuration is invalid
     */
    protected function mockApplication($config = null) {
        Yii::$container = new Container();
        $this->container = Yii::$container;
        $configManager = AppLoader::getInstance();
        $config ??= $this->appConfig;
        if (is_string($config)) {
            $configFile = Yii::getAlias($config);
            if (!is_file($configFile)) {
                throw new InvalidConfigException("The application configuration file does not exist: $config");
            }
            $config = require($configFile);
        }
        if (is_array($config)) {
            if (!isset($config['class'])) {
                $config['class'] = Application::class;
            }
            $app = Yii::createObject($config);
            Yii::$app = $app;

            return $app;
        }

        throw new InvalidConfigException('Please provide a configuration array to mock up an application.');
    }

    /**
     * Destroys the application instance created by [[mockApplication]].
     */
    protected function destroyApplication(): void {
        if (Yii::$app) {
            Yii::$app->db->close();
            Yii::$app = null;
            Yii::$container = null;
        }
        $this->container = null;
        $this->app = null;
    }

    public function getContainer(): Container {
        return $this->container;
    }
}
