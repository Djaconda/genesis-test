<?php

use yii\helpers\ArrayHelper;

/**
 * Application config for api Unit tests
 */

$configManager->bootstrapDiContainer();

$commonMain = require(YII_APP_BASE_PATH . '/config/general/common/main.php');
$commonMainLocal = require(YII_APP_BASE_PATH . '/config/general/common/main-local.php');
$sectionMain = require(YII_APP_BASE_PATH . '/config/general/api/main.php');
$sectionMainLocal = $configManager->requireIfExist(YII_APP_BASE_PATH . '/config/general/api/main-local.php');
$testMain = require(dirname(__DIR__) . '/main.php');
$testMainLocal = require(dirname(__DIR__) . '/main-local.php');
$config = [
    'id' => 'app-api',
    'basePath' => dirname(__DIR__, 2),
];

return ArrayHelper::merge($commonMain, $commonMainLocal, $sectionMain, $sectionMainLocal, $testMain, $testMainLocal, $config);
