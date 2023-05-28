<?php

use yii\helpers\ArrayHelper;

/**
 * Application config for console unit tests
 */
$configManager->bootstrapDiContainer();

$commonMain = require(YII_APP_BASE_PATH . '/config/general/common/main.php');
$commonMainLocal = require(YII_APP_BASE_PATH . '/config/general/common/main-local.php');
$sectionMain = require(YII_APP_BASE_PATH . '/config/general/console/main.php');
$sectionMainLocal = $configManager->requireIfExist(YII_APP_BASE_PATH . '/config/general/console/main-local.php');
$testMain = require(dirname(__DIR__) . '/main.php');
$testMainLocal = require(dirname(__DIR__) . '/main-local.php');
$config = [
    'id' => 'app-console',
    'basePath' => dirname(__DIR__, 2),
];

return ArrayHelper::merge($commonMain, $commonMainLocal, $sectionMain, $sectionMainLocal, $testMain, $testMainLocal, $config);
