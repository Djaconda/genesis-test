<?php

namespace core\base;

use core\app\ErrorHandling;
use core\contracts\Application;
use PHPKitchen\DI\Contracts\ContainerAware;
use PHPKitchen\DI\Contracts\ServiceLocatorAware;
use PHPKitchen\DI\Mixins\ContainerAccess;
use PHPKitchen\DI\Mixins\ServiceLocatorAccess;

/**
 * Extends base Yii model class to apply application injection.
 *
 * @property Application $serviceLocator
 * @method Application getServiceLocator()
 *
 * @author Dmitry Bukavin <4o.djaconda@gmail.com>
 */
abstract class Model extends \yii\base\Model implements ContainerAware, ServiceLocatorAware {
    use ServiceLocatorAccess;
    use ContainerAccess;
    use ErrorHandling;

    public function getLabelOfAttribute($attributeName) {
        $labels = $this->attributeLabels();

        return $labels[$attributeName] ?? ucfirst((string)$attributeName);
    }
}
