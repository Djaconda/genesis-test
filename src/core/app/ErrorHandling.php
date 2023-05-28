<?php

namespace core\app;

use PHPKitchen\DI\Mixins\ContainerAccess;
use PHPKitchen\DI\Mixins\ServiceLocatorAccess;
use Yii;

/**
 * Injects target class with functionality to throw exception.
 *
 * @mixin ContainerAccess
 * @mixin ServiceLocatorAccess
 *
 * @package core\app
 * @author Dmitry Bukavin <4o.djaconda@gmail.com>
 */
trait ErrorHandling {
    /**
     * Throws an exception by specified type.
     *
     * @param mixed $type exception class or type specified in constants.
     * @param array ...$arguments
     */
    public function throwException($type, ...$arguments): void {
        Yii::$app->throwException($type, ...$arguments);
    }
}
