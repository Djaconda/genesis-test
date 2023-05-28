<?php

use core\AppLoader;
use Rate\RateModule;

$configManager = AppLoader::getInstance();
$params = $configManager->requireIfExist(__DIR__ . '/params.php', []);
$localGeneralParams = $configManager->loadFile('params-local.php', 'console', []);
$localParams = $configManager->loadInstanceConfig('params-local.php', 'console', []);

set_time_limit(0);
ini_set('memory_limit', '4096M');

$config = [
    'id' => 'app-console',
    'basePath' => $configManager->rootPath . '/console',
    'aliases' => [
        '@runtime' => '@root/runtime/console',
        '@webroot' => '@root/frontend/web',
        '@web' => '/',
    ],
    'bootstrap' => [
        'rate',
    ],
    'controllerNamespace' => 'console\controllers',
    'modules' => [
        'rate' => RateModule::class,
    ],
    'components' => [
    ],
    'params' => array_merge($params, $localGeneralParams, $localParams),
];

return $config;
