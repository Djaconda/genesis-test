<?php

namespace core\app;

use core\contracts\Application;
use ReflectionClass;
use Yii;
use yii\base\ErrorException;
use yii\base\Exception;
use yii\base\ExitException;
use yii\base\InvalidArgumentException;
use yii\base\InvalidCallException;
use yii\base\InvalidConfigException;
use yii\base\InvalidRouteException;
use yii\base\InvalidValueException;
use yii\base\NotSupportedException;
use yii\base\UnknownClassException;
use yii\base\UnknownMethodException;
use yii\base\UnknownPropertyException;
use yii\base\UserException;
use yii\console\Exception as ConsoleException;
use yii\db\Exception as DbException;
use yii\db\IntegrityException;
use yii\db\StaleObjectException;
use yii\di\Container;
use yii\log\Logger;
use yii\web\BadRequestHttpException;
use yii\web\ConflictHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\GoneHttpException;
use yii\web\HttpException;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotAcceptableHttpException;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;
use yii\web\TooManyRequestsHttpException;
use yii\web\UnauthorizedHttpException;
use yii\web\UnprocessableEntityHttpException;
use yii\web\UnsupportedMediaTypeHttpException;

/**
 * Trait implements interface defined in {@link Application}
 *
 * @see Application
 *
 * @author Dmitry Bukavin <4o.djaconda@gmail.com>
 */
trait YiiStaticMethodsAccess {
    public $exceptionLoggingEnabled = true;
    protected $_exceptionsClassMap = [
        Application::BASE_EXCEPTION => Exception::class,
        Application::INVALID_CONFIG_EXCEPTION => InvalidConfigException::class,
        Application::DB_EXCEPTION => DbException::class,
        Application::CONSOLE_EXCEPTION => ConsoleException::class,
        Application::HTTP_EXCEPTION => HttpException::class,
        Application::ERROR_EXCEPTION => ErrorException::class,
        Application::EXIT_EXCEPTION => ExitException::class,
        Application::INVALID_CALL_EXCEPTION => InvalidCallException::class,
        Application::INVALID_PARAM_EXCEPTION => InvalidArgumentException::class,
        Application::INVALID_ROUTE_EXCEPTION => InvalidRouteException::class,
        Application::INVALID_VALUE_EXCEPTION => InvalidValueException::class,
        Application::NOT_SUPPORTED_EXCEPTION => NotSupportedException::class,
        Application::UNKNOWN_CLASS_EXCEPTION => UnknownClassException::class,
        Application::UNKNOWN_METHOD_EXCEPTION => UnknownMethodException::class,
        Application::UNKNOWN_PROPERTY_EXCEPTION => UnknownPropertyException::class,
        Application::USER_EXCEPTION => UserException::class,
        Application::DB_INTEGRITY_EXCEPTION => IntegrityException::class,
        Application::DB_STALE_OBJECT_EXCEPTION => StaleObjectException::class,
        Application::HTTP_BAD_REQUEST_EXCEPTION => BadRequestHttpException::class,
        Application::HTTP_CONFLICT_EXCEPTION => ConflictHttpException::class,
        Application::HTTP_FORBIDDEN_EXCEPTION => ForbiddenHttpException::class,
        Application::HTTP_GONE_EXCEPTION => GoneHttpException::class,
        Application::HTTP_METHOD_NOT_ALLOWED_EXCEPTION => MethodNotAllowedHttpException::class,
        Application::HTTP_METHOD_NOT_ACCEPTABLE_EXCEPTION => NotAcceptableHttpException::class,
        Application::HTTP_NOT_FOUND_EXCEPTION => NotFoundHttpException::class,
        Application::HTTP_SERVER_ERROR_EXCEPTION => ServerErrorHttpException::class,
        Application::HTTP_TOO_MANY_REQUESTS_EXCEPTION => TooManyRequestsHttpException::class,
        Application::HTTP_UNAUTHORIZED_EXCEPTION => UnauthorizedHttpException::class,
        Application::HTTP_UNPROCESSABLE_ENTITY_EXCEPTION => UnprocessableEntityHttpException::class,
        Application::HTTP_UNSUPPORTED_MEDIA_TYPE_EXCEPTION => UnsupportedMediaTypeHttpException::class,
    ];

    /**
     * Creates a new object using the given configuration through {@link Yii::createObject()}.
     *
     * @param string|array|callable $type the object type. This can be specified in one of the following forms:
     *
     * - a string: representing the class name of the object to be created
     * - a configuration array: the array must contain a `class` element which is treated as the object class,
     *   and the rest of the name-value pairs will be used to initialize the corresponding object properties
     * - a PHP callable: either an anonymous function or an array representing a class method (`[$class or $object, $method]`).
     *   The callable should return a new instance of the object being created.
     *
     * @param array $params the constructor parameters
     *
     * @return object the created object
     * @throws InvalidConfigException if the configuration is invalid.
     * @deprecated use container the same function from container instead.
     * @see Container
     */
    public function create($type, array $params = []) {
        return Yii::createObject($type, $params);
    }

    /**
     * Creates a new object using the given configuration through {@link Yii::createObject()}.
     *
     * @param string|array|callable $type the object type.
     * @param array $params the constructor parameters
     *
     * @return object the created object
     * @deprecated since {@link create()} available
     * @see {@link create()}
     */
    public function createObject($type, array $params = []) {
        return Yii::createObject($type, $params);
    }

    /**
     * Translates a message to the specified language.
     *
     * This is a shortcut method of [[\yii\i18n\I18N::translate()]].
     *
     * The translation will be conducted according to the message category and the target language will be used.
     *
     * You can add parameters to a translation message that will be substituted with the corresponding value after
     * translation. The format for this is to use curly brackets around the parameter name as you can see in the following example:
     *
     * ```php
     * $username = 'Alexander';
     * echo \Yii::t('app', 'Hello, {username}!', ['username' => $username]);
     * ```
     *
     * Further formatting of message parameters is supported using the [PHP intl extensions](http://www.php.net/manual/en/intro.intl.php)
     * message formatter. See [[\yii\i18n\I18N::translate()]] for more details.
     *
     * @param string $category the message category.
     * @param string $message the message to be translated.
     * @param array $params the parameters that will be used to replace the corresponding placeholders in the message.
     * @param string $language the language code (e.g. `en-US`, `en`). If this is null, the current
     * [[\yii\base\Application::language|application language]] will be used.
     *
     * @return string the translated message.
     */
    public function translate($category, $message, $params = [], $language = null) {
        return Yii::t($category, $message, $params, $language);
    }

    /**
     * Throws an exception by specified type.
     *
     * @param string|\Exception $type exception class or type specified in constants.
     * @param array ...$arguments
     *
     * @throws \Exception
     */
    public function throwException($type, ...$arguments) {
        if (is_object($type)) {
            $exception = $type;
        } else {
            $exception = $this->createException($type, ...$arguments);
        }
        if ($this->exceptionLoggingEnabled) {
            $this->logException($exception, $arguments);
        }
        throw $exception;
    }

    /**
     * @param \Exception $exception the exception being converted
     * @param array $arguments
     */
    protected function logException($exception, $arguments) {
        [, , $caller] = debug_backtrace(false, 3);
        $calledFunction = @$caller['class'] . @$caller['type'] . @$caller['function'];
        $exceptionClass = $exception::class;
        $logMessage = "From \"$calledFunction\" ";
        $logMessage .= "triggered exception \"$exceptionClass\" ";
        $logMessage .= "with constructor arguments: " . print_r($arguments, true);
        if (!empty($caller['args'])) {
            $logMessage .= "\n Caller arguments: " . print_r($caller['args'], true);
        }
        $logMessage .= 'Message: ' . $exception->getMessage();
        $this->getLogger()->logWithTrace($logMessage, Logger::LEVEL_ERROR, 'exceptions');
    }

    /**
     * @param $type
     * @param array ...$arguments
     *
     * @return \Exception
     */
    public function createException($type, ...$arguments) {
        if (class_exists($type)) {
            $exceptionClass = $type;
        } else {
            $exceptionClass = $this->getExceptionClassByType($type);
        }
        $reflection = new ReflectionClass($exceptionClass);

        return $reflection->newInstanceArgs($arguments);
    }

    public function getExceptionClassByType($type) {
        return $this->_exceptionsClassMap[$type] ?? Exception::class;
    }

    public function addExceptionClass($class, $type = null) {
        if ($type === null) {
            $type = $class;
        }
        $this->_exceptionsClassMap[$type] = $class;
    }

    /**
     * @return Container
     */
    public function getContainer() {
        return Yii::$container;
    }

    /**
     * @param $alias
     * @param bool $throwException
     *
     * @return bool|string
     */
    public function getAlias($alias, $throwException = true) {
        return Yii::getAlias($alias, $throwException);
    }

    /**
     * @param $alias
     *
     * @return bool|string
     */
    public function getRootAlias($alias) {
        return Yii::getRootAlias($alias);
    }

    /**
     * @param $alias
     * @param $path
     */
    public function setAlias($alias, $path) {
        Yii::setAlias($alias, $path);
    }

    /**
     * @param $className
     *
     * @return bool|void
     */
    public function autoload($className) {
        return Yii::autoload($className);
    }

    /**
     * @param $object
     * @param $properties
     *
     * @return object
     */
    public function configureObject($object, $properties) {
        return Yii::configure($object, $properties);
    }

    /**
     * @return \core\log\Logger
     */
    public function getLogger() {
        return Yii::getLogger();
    }
}
