<?php

namespace core\contracts;

use Bitfocus\Storage\Hub\StorageHub;
use common\db\SettingsManager;
use common\instance\Instance;
use common\rest\OpsRestClient;
use common\security\Security;
use core\api\rest\MobileDetector;
use core\api\rest\session\DbSession;
use core\api\rest\User;
use core\helpers\Formatter;
use core\os\SystemCommandManager;
use Notification\Domain\Service\Email\Notifier;
use PHPKitchen\DI\Contracts\Container;
use yii\base\Action;
use yii\base\Controller;
use yii\base\Event;
use yii\base\ExitException;
use yii\base\Response;
use yii\caching\Cache;
use yii\console\ErrorHandler;
use yii\console\Request;
use yii\db\Connection;
use yii\i18n\I18N;
use yii\log\Dispatcher;
use yii\log\Logger;
use yii\rbac\DbManager;
use yii\rbac\ManagerInterface;
use yii\redis\Session;
use yii\web\AssetManager;
use yii\web\UrlManager;
use yii\web\View;

/**
 * Base application interface.
 *
 * Application base properties:
 *
 * @property string $controllerNamespacethe namespace that controller classes are located in.
 * @property string $name string the application name.
 * @property string $version string the version of this application.
 * @property string $charset the charset currently used for the application.
 * @property string $language the language that is meant to be used for end users.
 * @property string $sourceLanguage the language that the application is written in.
 * @property Controller $controller the currently active controller instance.
 * @property string|bool $layout the layout that should be applied for views in this application.
 * @property string $requestedRoute the requested route.
 * @property Action $requestedAction the requested Action. If null, it means the request cannot be resolved into an action.
 * @property array $requestedParams the parameters supplied to the requested action.
 * @property array $extensions list of installed Yii extensions.
 * @property array $bootstrap list of components that should be run during the application [[bootstrap()|bootstrapping process]].
 * @property int $state the current application state during a request handling life cycle.
 *
 * Application base methods:
 * @method string getUniqueId() the unique ID of the module.
 * @method setBasePath($path) Sets the root directory of the application and the @app alias.
 * @method int run() Runs the application.
 * @method string getRuntimePath() Returns the directory that stores runtime files.
 * @method setRuntimePath($path) Sets the directory that stores runtime files.
 * @method string getVendorPath() Returns the directory that stores vendor files.
 * @method setVendorPath($path) Sets the directory that stores vendor files.
 * @method string getTimeZone() Returns the time zone used by this application.
 * @method setTimeZone($value) Sets the time zone used by this application.
 * @method Connection getDb() Returns the database connection component.
 * @method Dispatcher getLog() Returns the log dispatcher component.
 * @method \yii\web\ErrorHandler|ErrorHandler getErrorHandler() Returns the error handler component.
 * @method Cache getCache() Returns the cache component.
 * @method \yii\i18n\Formatter getFormatter() Returns the formatter component.
 * @method \yii\web\Request|Request getRequest() Returns the request component.
 * @method \yii\web\Response|\yii\console\Response getResponse() Returns the response component.
 * @method \yii\base\View|View getView() Returns the view object.
 * @method UrlManager getUrlManager() Returns the URL manager for this application.
 * @method I18N getI18n() Returns the internationalization (i18n) component
 * @method ManagerInterface getAuthManager() Returns the auth manager for this application.
 * @method AssetManager getAssetManager() Returns the asset manager.
 * @method Security getSecurity() Returns the security component.
 *
 * Application components available using service locator:
 * @property AssetManager $assetManager The asset manager application component. This property is
 * read-only.
 * @property DbManager $authManager The auth manager application component. Null is returned
 * if auth manager is not configured. This property is read-only.
 * @property string $basePath The root directory of the application.
 * @property Cache $cache The cache application component. Null if the component is not enabled.
 * This property is read-only.
 * @property Connection $db The database connection. This property is read-only.
 * @property \yii\web\ErrorHandler|ErrorHandler $errorHandler The error handler application
 * component. This property is read-only.
 * @property Formatter $formatter The formatter application component. This property is read-only.
 * @property I18N $i18n The internationalization application component. This property is read-only.
 * @property Dispatcher $log The log dispatcher application component. This property is read-only.
 * @property yii\symfonymailer\Mailer $mailer The mailer application component. This property is read-only.
 * @property \yii\web\Request|Request $request The request component. This property is read-only.
 * @property \yii\web\Response|\yii\console\Response $response The response component. This property is
 * read-only.
 * @property string $runtimePath The directory that stores runtime files. Defaults to the "runtime"
 * subdirectory under [[basePath]].
 * @property Security $security The security application component. This property is read-only.
 * @property string $timeZone The time zone used by this application.
 * @property string $uniqueId The unique ID of the module. This property is read-only.
 * @property UrlManager $urlManager The URL manager for this application. This property is read-only.
 * @property string $vendorPath The directory that stores vendor files. Defaults to "vendor" directory under
 * [[basePath]].
 * @property \core\api\rest\View|View $view The view application component that is used to render various view
 * files. This property is read-only.
 * @property User $user The user component. This property is read-only.
 * @property DbSession|Session $session The session component. This property is read-only.
 * @property SettingsManager|array $settings
 * @property OpsRestClient $opsRestClient
 * @property StorageHub $fileStorage
 * @property Notifier $notifier
 * @property SystemCommandManager $commandManager
 * @property Instance $instance
 * @property array $params
 * @property MobileDetector $mobileDetector
 *
 * @mixin \yii\base\Application
 *
 * @author Dmitry Bukavin <4o.djaconda@gmail.com>
 */
interface Application {
    public const BASE_EXCEPTION = 1;
    public const INVALID_CONFIG_EXCEPTION = 2;
    public const DB_EXCEPTION = 3;
    public const CONSOLE_EXCEPTION = 4;
    public const HTTP_EXCEPTION = 5;
    public const ERROR_EXCEPTION = 6;
    public const EXIT_EXCEPTION = 7;
    public const INVALID_CALL_EXCEPTION = 8;
    public const INVALID_PARAM_EXCEPTION = 9;
    public const INVALID_ROUTE_EXCEPTION = 10;
    public const INVALID_VALUE_EXCEPTION = 11;
    public const NOT_SUPPORTED_EXCEPTION = 12;
    public const UNKNOWN_CLASS_EXCEPTION = 13;
    public const UNKNOWN_METHOD_EXCEPTION = 14;
    public const UNKNOWN_PROPERTY_EXCEPTION = 15;
    public const USER_EXCEPTION = 16;
    public const DB_INTEGRITY_EXCEPTION = 17;
    public const DB_STALE_OBJECT_EXCEPTION = 18;
    public const HTTP_BAD_REQUEST_EXCEPTION = 19;
    public const HTTP_CONFLICT_EXCEPTION = 20;
    public const HTTP_FORBIDDEN_EXCEPTION = 21;
    public const HTTP_GONE_EXCEPTION = 22;
    public const HTTP_METHOD_NOT_ALLOWED_EXCEPTION = 23;
    public const HTTP_METHOD_NOT_ACCEPTABLE_EXCEPTION = 24;
    public const HTTP_NOT_FOUND_EXCEPTION = 25;
    public const HTTP_SERVER_ERROR_EXCEPTION = 26;
    public const HTTP_TOO_MANY_REQUESTS_EXCEPTION = 27;
    public const HTTP_UNAUTHORIZED_EXCEPTION = 28;
    public const HTTP_UNPROCESSABLE_ENTITY_EXCEPTION = 29;
    public const HTTP_UNSUPPORTED_MEDIA_TYPE_EXCEPTION = 30;

    /**
     * Creates a new object using the given configuration
     *
     * @param string|array|callable $type the object type.
     * @param array $params the constructor parameters
     *
     * @return object the created object
     * @deprecated use container the same function from container instead.
     */
    public function create($type, array $params = []);

    /**
     * Creates a new object using the given configuration
     *
     * @param string|array|callable $type the object type.
     * @param array $params the constructor parameters
     *
     * @return object the created object
     * @deprecated since {@link create()} available
     * @see {@link create()}
     */
    public function createObject($type, array $params = []);

    /**
     * Translates a message to the specified language.
     *
     * @param string $category the message category.
     * @param string $message the message to be translated.
     * @param array $params the parameters that will be used to replace the corresponding placeholders in the message.
     * @param string $language the language code (e.g. `en-US`, `en`).
     *
     * @return string the translated message.
     */
    public function translate($category, $message, $params = [], $language = null);

    /**
     * Returns the component instance with the specified ID.
     *
     * @param string $id component ID (e.g. `db`).
     * @param bool $throwException whether to throw an exception if `$id` is not registered with the locator before.
     *
     * @return object|null the component of the specified ID. If `$throwException` is false and `$id`
     * is not registered before, null will be returned.
     * @see has()
     * @see set()
     */
    public function get($id, $throwException = true);

    /**
     * Terminates the application.
     * This method replaces the `exit()` function by ensuring the application life cycle is completed
     * before terminating the application.
     *
     * @param int $status the exit status (value 0 means normal exit while other values mean abnormal exit).
     * @param Response $response the response to be sent. If not set, the default application [[response]] component will be used.
     *
     * @throws ExitException if the application is in testing mode
     */
    public function end($status = 0, $response = null);

    /**
     * Registers a component definition with this locator.
     *
     * @param string $id component ID (e.g. `db`).
     * @param mixed $definition the component definition to be registered with this locator.
     */
    public function set($id, $definition);

    /**
     * Throws an exception by specified type.
     *
     * @param mixed $type exception class or type specified in constants.
     * @param array ...$arguments
     */
    public function throwException($type, ...$arguments);

    public function getExceptionClassByType($type);

    public function addExceptionClass($class, $type = null);

    /**
     * @return Container
     */
    public function getContainer();

    public function getAlias($alias, $throwException = true);

    public function getRootAlias($alias);

    public function setAlias($alias, $path);

    public function autoload($className);

    public function configureObject($object, $properties);

    /**
     * @return Logger
     */
    public function getLogger();

    public function trigger($name, Event $event = null);
}
