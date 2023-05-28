<?php

use core\AppLoader;

error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_STRICT);
ini_set('display_errors', 'off');
$configManager = AppLoader::getInstance();
$configManager->requireIfExist($configManager->loadInstanceConfig('bootstrap-local.php', 'common'));

// whether to regenerate url rules cache each request. Should be 'false' on production
defined('FORCE_REGENERATE_URL_RULE_CACHE') or define('FORCE_REGENERATE_URL_RULE_CACHE', false);

// -------------- CONSTANTS REQUIRED FOR LOGGING CONFIGURATION ------------------------
defined('INFO_LOGGING_ENABLED') or define('INFO_LOGGING_ENABLED', true);
defined('BIM_API_INFO_LOGGING_ENABLED') or define('BIM_API_INFO_LOGGING_ENABLED', true);
defined('INFO_LOGGING_EXPORT_INTERVAL') or define('INFO_LOGGING_EXPORT_INTERVAL', 1000);
defined('ERROR_LOGGING_ENABLED') or define('ERROR_LOGGING_ENABLED', true);
defined('ERROR_LOGGING_EXPORT_INTERVAL') or define('ERROR_LOGGING_EXPORT_INTERVAL', 1000);
defined('SYSTEM_INFO_LOGGING_ENABLED') or define('SYSTEM_INFO_LOGGING_ENABLED', false);
defined('CORE_INFO_LOGGING_ENABLED') or define('CORE_INFO_LOGGING_ENABLED', false);
defined('FRONTEND_CONTROLLERS_INFO_LOGGING_ENABLED') or define('FRONTEND_CONTROLLERS_INFO_LOGGING_ENABLED', false);
defined('CONSOLE_CONTROLLERS_INFO_LOGGING_ENABLED') or define('CONSOLE_CONTROLLERS_INFO_LOGGING_ENABLED', true);
defined('API_CONTROLLERS_INFO_LOGGING_ENABLED') or define('API_CONTROLLERS_INFO_LOGGING_ENABLED', true);

$rootPath = $configManager->rootPath;
$commonSectionPath = $rootPath . '/common';
$frontendSectionPath = $rootPath . '/frontend';

Yii::setAlias('root', $rootPath);
Yii::setAlias('api', $rootPath . '/api');
Yii::setAlias('common', $rootPath . '/common');
Yii::setAlias('console', $rootPath . '/console');
Yii::setAlias('core', $rootPath . '/core');
Yii::setAlias('frontend', $rootPath . '/frontend');
Yii::setAlias('runtime', $rootPath . '/runtime');
Yii::setAlias('tmp', $rootPath . '/runtime');
Yii::setAlias('vendor', $rootPath . '/vendor');

