<?php

use core\AppLoader;
use core\log\AuthLogger;
use Rate\RateModule;
use yii\caching\FileCache;
use yii\web\IdentityInterface;
use yii\web\JsonParser;
use yii\web\Request;
use yii\web\Response;

defined('PUBLISH_ASSETS') or define('PUBLISH_ASSETS', false);

$configManager = AppLoader::getInstance();
$params = $configManager->requireIfExist(__DIR__ . '/params.php', []);
$localGeneralParams = $configManager->loadFile('params-local.php', 'api', []);
$localParams = $configManager->loadInstanceConfig('params-local.php', 'api', []);

return [
    'id' => 'app-api',
    'basePath' => $configManager->rootPath . '/api',
    'controllerNamespace' => 'api\controllers',
    'aliases' => [
        '@runtime' => '@root/runtime/api',
    ],
    'bootstrap' => [
    ],
    'modulesOrder' => [
        'rate',
    ],
    'modules' => [
        'rate' => RateModule::class,
    ],
    'components' => [
        'user' => [
            'identityClass' => IdentityInterface::class,
            'enableAutoLogin' => true,
            'enableSession' => false,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'cache' => FileCache::class,
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                "GET,OPTIONS /" => 'site/info',
            ],
        ],
        'response' => [
            'format' => Response::FORMAT_JSON,
            'charset' => 'UTF-8',
        ],
        'request' => [
            'class' => Request::class,
            'enableCsrfCookie' => false,
            'parsers' => [
                'application/json' => JsonParser::class,
                'application/scim+json' => JsonParser::class,
            ],
        ],
    ],
    'params' => array_merge($params, $localGeneralParams, $localParams),
];
