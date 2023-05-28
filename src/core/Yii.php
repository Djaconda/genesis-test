<?php

use core\log\Logger;
use core\ParameterizedContainer;
use PHPKitchen\DI\Autoload\ClassLoader;
use yii\BaseYii;

/**
 * Yii bootstrap file.
 */
$yii2Path = dirname(__DIR__) . '/vendor/yiisoft/yii2/';
require($yii2Path . 'BaseYii.php');

/**
 * Yii is a helper class serving common framework functionalities.
 *
 * It extends from [[\yii\BaseYii]] which provides the actual implementation.
 * By writing your own Yii class, you can customize some functionalities of [[\yii\BaseYii]].
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 * @phan-file-suppress PhanUndeclaredTypeParameter
 */
class Yii extends BaseYii {
    private static $_logger;

    public static function getLogger() {
        if (self::$_logger !== null) {
            return self::$_logger;
        }

        return self::$_logger = static::createObject(Logger::class);
    }

    /**
     * Sets the logger object.
     *
     * @param Logger $logger the logger object.
     */
    public static function setLogger($logger) {
        self::$_logger = $logger;
    }
}

spl_autoload_register(['Yii', 'autoload'], true, true);
Yii::$classMap = require($yii2Path . 'classes.php');
Yii::$classMap['core\app\ApplicationInjectionTrait'] = __DIR__ . '/app/ApplicationInjectionTrait.php';
Yii::$container = new PHPKitchen\DI\Container();
spl_autoload_register([ClassLoader::class, 'loadClass']);
