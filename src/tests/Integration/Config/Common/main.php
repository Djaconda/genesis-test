<?php

use yii\helpers\ArrayHelper;

/**
 * Application config for common Unit tests
 */
$configManager->bootstrapDiContainer();

$commonMain = require(YII_APP_BASE_PATH . '/config/general/common/main.php');
$commonMainLocal = require(YII_APP_BASE_PATH . '/config/general/common/main-local.php');
$testMain = require(dirname(__DIR__) . '/main.php');
$testMainLocal = require(dirname(__DIR__) . '/main-local.php');
$config = [
    'id' => 'app-common',
    'basePath' => dirname(__DIR__, 4),
];

return ArrayHelper::merge($commonMain, $commonMainLocal, $testMain, $testMainLocal, $config);
