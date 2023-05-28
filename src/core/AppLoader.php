<?php

namespace core;

use core\frontend\Application;
use Exception;
use RuntimeException;
use SplFileInfo;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * AppLoader provides a set of methods for loading application and it's configuration
 * files for application sections.
 *
 * Helper allows to load configuration files for specific instance based on {@link instanceConfigsDirTemplate}
 * or to load configuration files from default location specified in {@link configsDirTemplate}.
 * Also helper allows to populate Yii dependency injection container from configuration file.
 *
 * Examples:
 * <pre>
 * $configLoader = core\ConfigManager::instance();
 * $configLoader->rootPath = dirname(dirname(__DIR__));
 *
 * // Get path to common bootstrap (/rootPath/common/config/bootstrap.php) file includes and evaluates it.
 * require($configLoader->getConfigsDir('common') . 'bootstrap.php');
 * // Get path to api (/rootPath/api/config/bootstrap.php) bootstrap file includes and evaluates it.
 * require($configLoader->getConfigsDir('api') . 'bootstrap.php');
 *
 * // Load main configuration from instance's common section. By default main configuration file
 * // is in default location and this method will return main configuration from "rootPath/common/config/main.php"
 * $mainConfig = $configLoader->loadInstanceConfig('main.php', 'common');
 *
 * // Load local man configuration from instance's common section. By default local configuration file
 * // is in instance location and this method will return main configuration from
 * // "rootPath/config/instance/{instance}/common/main-local.php"
 * $mainLocalConfig = $configLoader->loadInstanceConfig('main-local.php', 'common');
 * </pre>
 *
 * @codeCoverageIgnore
 * @author Dmitry Bukavin <4o.djaconda@gmail.com>
 */
class AppLoader {
    public const REQUIRED_TMP_DIRECTORIES = [
        'runtime',
        'templates_compiled',
        'cache',
        'runtime/cache',
    ];
    /**
     * @var string path to the root directory of the application where all of the sections stays.
     */
    public $rootPath;
    /**
     * @var string path template to a default configuration directory.
     */
    public $configsDirTemplate = '{rootPath}/config/general/{section}/';
    /**
     * @var string path template to an instance configuration directory.
     * If there is no requested file in the instance configuration directory
     * manager will use {@link configsDirTemplate} to look for a
     */
    public $instanceConfigsDirTemplate = '{rootPath}/config/instance/{instance}/{section}/';
    private static $_instance;
    protected $_instanceName;

    private function __construct() {
        $this->rootPath = dirname(__DIR__);
    }

    public static function getInstance() {
        if (!isset(self::$_instance)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function loadApplicationForSection($section, $application = Application::class, $yiiFile = false, array $additionalConfigs = []) {
        $rootPath = dirname(__DIR__);

        $commonConfigsDir = $this->getConfigsDir('common');
        $sectionConfigsDir = $this->getConfigsDir('frontend');

        $this->requireIfExist($commonConfigsDir . 'constants.php');
        // temporyry part to support old.php in frontend
        if ($yiiFile) {
            require($yiiFile);
        } else {
            require($rootPath . '/core/Yii.php');
        }

        $this->requireIfExist($commonConfigsDir . 'bootstrap.php');
        $this->requireIfExist($sectionConfigsDir . 'bootstrap.php');

        $this->bootstrapDiContainer();

        $config = $this->loadAppConfigForSection($section, $additionalConfigs);

        $this->ensureTmpDirectoriesExist();

        return Yii::createObject($application, [$config]);
    }

    protected function ensureTmpDirectoriesExist() {
        $tmpPath = Yii::getAlias('@tmp');
        if ($tmpPath) {
            if (!file_exists($tmpPath) && !mkdir($tmpPath) && !is_dir($tmpPath)) {
                throw new RuntimeException(sprintf('Directory "%s" was not created', $tmpPath));
            }
            foreach (self::REQUIRED_TMP_DIRECTORIES as $requiredDir) {
                $fullPath = $tmpPath . DIRECTORY_SEPARATOR . $requiredDir;
                if (!file_exists($fullPath) && !mkdir($fullPath) && !is_dir($fullPath)) {
                    throw new RuntimeException(sprintf('Directory "%s" was not created', $fullPath));
                }
            }
        }
    }

    protected function loadAppConfigForSection($section, array $additionalConfigs = []) {
        $main = $this->loadInstanceConfig('main.php', 'common');
        $mainLocal = $this->loadInstanceConfig('main-local.php', 'common', []);
        $sectionMain = $this->loadInstanceConfig('main.php', $section);
        $sectionLocal = $this->loadInstanceConfig('main-local.php', $section, []);
        $customerPortal = $this->loadInstanceConfig('customer-portal.php', 'common', []);

        // temporary part to support bin/yiic
        $additional = [];
        foreach ($additionalConfigs as $additionalConfig) {
            if (is_array($additionalConfig)) {
                $additional = array_merge($additional, $this->loadInstanceConfig($additionalConfig['name'], $additionalConfig['section']));
            } else {
                $additional = array_merge($additional, $this->requireIfExist($additionalConfig, []));
            }
        }

        return ArrayHelper::merge($main, $mainLocal, $sectionMain, $sectionLocal, $additional, $customerPortal);
    }

    public function bootstrapDiContainer() {
        $containerConfigsDir = $this->getConfigsDir('container');

        $this->populateDiContainerSingletons($containerConfigsDir . 'singletons.php');
        $this->populateDiContainer($containerConfigsDir . 'components.php');
        $this->populateDiContainer($containerConfigsDir . 'ar.php');
        $this->populateDiContainer($containerConfigsDir . 'finders.php');
        $this->populateDiContainerSingletons($containerConfigsDir . 'singletons-local.php');
        $this->populateDiContainerLoadInstanceConfig('components-local.php');
    }

    public function loadFile($file, $section, $defaultValue = null) {
        $filePath = $this->getConfigsDir($section) . $file;

        $isFileExist = file_exists($filePath);
        if (!$isFileExist && !is_null($defaultValue)) {
            $result = $defaultValue;
        } elseif (!$isFileExist) {
            throw new Exception("Configuration file '$filePath' does not exist!");
        } else {
            $result = require($filePath);
        }

        return $result;
    }

    public function loadConfigFile($file, $defaultValue = []) {
        $filePath = Yii::getAlias($file);
        $isFileExist = file_exists($filePath);
        if (!$isFileExist && !is_null($defaultValue)) {
            $result = $defaultValue;
        } elseif (!$isFileExist) {
            throw new Exception("Configuration file '$filePath' does not exist!");
        } else {
            $result = require($filePath);
        }

        return $result;
    }

    public function loadInstanceConfig($file, $section, $defaultValue = null) {
        $filePath = $this->getInstanceConfigsDir($section) . $file;
        if (file_exists($filePath)) {
            $config = require($filePath);
        } else {
            $config = $this->loadFile($file, $section, $defaultValue);
        }

        return $config;
    }

    public function getInstanceConfigsDir($section) {
        $search = [
            '{rootPath}',
            '{section}',
            '{instance}',
        ];
        $replace = [
            $this->rootPath,
            $section,
            $this->getInstanceName(),
        ];

        return str_replace($search, $replace, $this->instanceConfigsDirTemplate);
    }

    public function populateDiContainer($config) {
        if (is_string($config) && file_exists($config)) {
            $appLoader = $this;
            $config = require($config);
        }
        if (!is_array($config) || empty($config)) {
            return;
        }

        foreach ($config as $class => $definition) {
            Yii::$container->set($class, $definition);
        }
    }

    public function populateDiContainerLoadInstanceConfig($file) {
        $section = 'container';
        $config = $this->loadInstanceConfig($file, $section, []);
        if (!is_array($config) || empty($config)) {
            return;
        }

        foreach ($config as $class => $definition) {
            Yii::$container->set($class, $definition);
        }
    }

    public function populateDiContainerSingletons($config) {
        if (is_string($config) && file_exists($config)) {
            $config = require($config);
        }
        if (!is_array($config) || empty($config)) {
            return;
        }

        foreach ($config as $class => $definition) {
            Yii::$container->setSingleton($class, $definition);
        }
    }

    public function requireIfExist($fileName, $defaultValue = false) {
        $file = new SplFileInfo($fileName);
        if ($file->isFile() && $file->isReadable()) {
            // this variable being used in configs
            $configManager = $this;
            $result = require($fileName);
        } else {
            $result = $defaultValue;
        }

        return $result;
    }

    public function getConfigsDir($section) {
        $search = [
            '{rootPath}',
            '{section}',
        ];
        $replace = [
            $this->rootPath,
            $section,
        ];

        return str_replace($search, $replace, $this->configsDirTemplate);
    }

    public function getInstanceName() {
        if (!isset($this->_instanceName)) {
            $this->initInstanceName();
        }

        return $this->_instanceName;
    }

    protected function initInstanceName() {
        if (PHP_SAPI === 'cli') {
            $instance = $this->parseHostFromConsoleArguments();
        } else {
            $instance = $this->parseHostFromRequest();
        }

        if (strpos((string)$instance, '.int.')) {
            $instance = str_replace('.int.', '.', (string)$instance);
        }
        $hostPrefix = substr((string)$instance, 0, 4);
        if ($hostPrefix === 'api.' || $hostPrefix === 'api-') {
            $instance = substr((string)$instance, 4);
        }

        $this->_instanceName = $instance;
    }

    protected function parseHostFromRequest() {
        if (isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST']) {
            $instance = $_SERVER['HTTP_HOST'];
        } elseif (isset($_SERVER['argv']) && $_SERVER['argv']) {
            $key = array_search('-u', $_SERVER['argv'], true);
            $instance = $_SERVER['argv'][$key + 1] ?? '';
        } else {
            $instance = '';
        }

        return $instance;
    }

    protected function parseHostFromConsoleArguments() {
        $arguments = $_SERVER['argv'];
        $instance = '';
        foreach ($arguments as $index => $argument) {
            if (($host = $this->parseHostFromArgument($argument, '-h')) || ($host = $this->parseHostFromArgument($argument, '--host='))) {
                $instance = $host;
                unset($_SERVER['argv'][$index]);
                break;
            }
        }

        return $instance;
    }

    protected function parseHostFromArgument($argument, $argumentKey) {
        if (is_string($argument) && str_starts_with($argument, (string)$argumentKey)) {
            $host = substr($argument, strlen((string)$argumentKey));
        } else {
            $host = false;
        }

        return $host;
    }
}
