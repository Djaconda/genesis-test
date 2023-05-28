<?php

use console\stubs\Session;
use yii\caching\DummyCache;

/**
 * Application configuration shared by all applications and test types
 */

defined('YII_DEBUG') or define('YII_DEBUG', false);
defined('YII_ENV') or define('YII_ENV', 'test');

$integrationTestsDir = dirname(__DIR__);
Yii::setAlias('@commonTests', $integrationTestsDir . '/Common');
Yii::setAlias('@coreTests', $integrationTestsDir . '/Core');
Yii::setAlias('@consoleTests', $integrationTestsDir . '/Console');
Yii::setAlias('@frontendTests', $integrationTestsDir . '/Frontend');
Yii::setAlias('@apiTests', $integrationTestsDir . '/Api');
Yii::setAlias('@Fixture', dirname($integrationTestsDir) . '/Fixture');
Yii::setAlias('@tests-runtime', $integrationTestsDir . '/runtime');

return [
    'language' => 'en-US',
    'controllerMap' => [
    ],
    'components' => [
        'cache' => DummyCache::class,
        'session' => Session::class,
    ],
    'params' => [
    ],
];
