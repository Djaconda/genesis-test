<?php

use core\AppLoader;

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'test');

defined('YII_APP_BASE_PATH') or define('YII_APP_BASE_PATH', dirname(__DIR__, 2));
require_once(YII_APP_BASE_PATH . '/vendor/autoload.php');

$commonConfigsDir = YII_APP_BASE_PATH . '/config/general/common/';

require_once(YII_APP_BASE_PATH . '/core/AppLoader.php');
$configManager = AppLoader::getInstance();

require_once(YII_APP_BASE_PATH . '/core/Yii.php');
Yii::setAlias('runtime', YII_APP_BASE_PATH . '/tests/Unit/runtime');
Yii::setAlias('@runtime', YII_APP_BASE_PATH . '/tests/Unit/runtime');

require_once($commonConfigsDir . 'bootstrap.php');

error_reporting(E_ALL ^ E_DEPRECATED);

// set correct script paths
$_SERVER['SERVER_NAME'] = 'localhost';
$_SERVER['SERVER_PORT'] = '80';

Yii::setAlias('@Test', dirname(__DIR__));
Yii::setAlias('@Fixture', dirname(__DIR__) . '/Fixture');
