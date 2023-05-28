<?php

use Rate\Domain\Contract\RateClient as RateClientContract;
use Test\Stub\Common\Module\Rate\Service\RateClient;
use yii\caching\DummyCache;

/**
 * Application configuration shared by all applications and test types
 */

defined('YII_DEBUG') or define('YII_DEBUG', false);
defined('YII_ENV') or define('YII_ENV', 'test');

$phpunitTestsDir = dirname(__DIR__);
$configManager->bootstrapDiContainer();
Yii::setAlias('@phpUnitTests', $phpunitTestsDir . '/Common');
Yii::setAlias('@commonTests', $phpunitTestsDir . '/Common');
Yii::setAlias('@coreTests', $phpunitTestsDir . '/Core');
Yii::setAlias('@consoleTests', $phpunitTestsDir . '/Console');
Yii::setAlias('@frontendTests', $phpunitTestsDir . '/Frontend');
Yii::setAlias('@apiTests', $phpunitTestsDir . '/Api');
Yii::setAlias('@Fixture', dirname($phpunitTestsDir) . '/Fixture');
Yii::setAlias('@tests-runtime', $phpunitTestsDir . '/runtime');

Yii::$container->setSingleton(
    RateClientContract::class,
    RateClient::class
);

return [
    'language' => 'en-US',
    'components' => [
        'cache' => DummyCache::class,
        'session' => Session::class,
    ],
];
