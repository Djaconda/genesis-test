<?php

use core\AppLoader;
use core\helpers\Formatter;
use yii\caching\CacheInterface;
use yii\log\FileTarget;
use yii\mail\MailerInterface;

$configManager = AppLoader::getInstance();
$params = require __DIR__ . '/params.php';
$localGeneralParams = $configManager->loadFile('params-local.php', 'common', []);
$localParams = $configManager->loadInstanceConfig('params-local.php', 'common', []);
$rootPath = $configManager->rootPath;
$commonSectionPath = $rootPath . '/common';
$modulesPath = $commonSectionPath . '/modules/';
$frontendModulesPath = $rootPath . '/frontend/modules/';
$vendorPath = $rootPath . '/vendor/';
$generatedAssetsConfig = $rootPath . '/assets/assets_bundles.php';

set_time_limit(1800);
ini_set('memory_limit', '2048M');

return [
    'name' => 'Genesis Test App',
    'vendorPath' => $rootPath . '/vendor',
    'runtimePath' => $rootPath . '/runtime',
    'aliases' => [
        '@api' => $rootPath . '/api',
        '@common' => $rootPath . '/common',
        '@console' => $rootPath . '/console',
        '@core' => $rootPath . '/core',
        '@contract' => $rootPath . '/common/contract',
        '@data' => $rootPath . '/data',
        '@Rate' => $modulesPath . 'Rate',
        '@User' => $modulesPath . 'User',
    ],
    'modules' => [
    ],
    'components' => [
        'cache' => [
            'class' => CacheInterface::class,
        ],
        'formatter' => [
            'class' => Formatter::class,
            'dateFormat' => 'php:m/d/Y',
        ],
        'mailer' => MailerInterface::class,
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                'errorAndWarning' => [
                    'class' => FileTarget::class,
                    'logFile' => '@runtime/logs/errors.log',
                    'exportInterval' => ERROR_LOGGING_EXPORT_INTERVAL,
                    'enabled' => ERROR_LOGGING_ENABLED,
                    'levels' => ['error', 'warning'],
                ],
                'info' => [
                    'class' => FileTarget::class,
                    'logFile' => '@runtime/logs/info.log',
                    'exportInterval' => INFO_LOGGING_EXPORT_INTERVAL,
                    'enabled' => INFO_LOGGING_ENABLED,
                    'levels' => YII_DEBUG ? ['info', 'trace', 'profile'] : ['info'],
                ],
            ],
        ],
    ],
    'params' => array_merge($params, $localGeneralParams, $localParams),
];
