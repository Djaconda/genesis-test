<?php

namespace core\base;

use core\app\ErrorHandling;
use core\contracts\Application;
use PHPKitchen\DI\Contracts\ContainerAware;
use PHPKitchen\DI\Contracts\ServiceLocatorAware;
use PHPKitchen\DI\Mixins\ContainerAccess;
use PHPKitchen\DI\Mixins\ServiceLocatorAccess;
use yii\base\Action;

/**
 * Extends base Yii controller action to apply application injection.
 *
 * @property Application $serviceLocator
 * @method Application getServiceLocator()
 *
 * @author Dmitry Bukavin <4o.djaconda@gmail.com>
 */
abstract class ControllerAction extends Action implements ContainerAware, ServiceLocatorAware {
    use ErrorHandling;
    use ContainerAccess;
    use ServiceLocatorAccess;
}
