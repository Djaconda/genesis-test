<?php

namespace core\api;

use core\api\rest\mixins\AdvancedRateLimiter;
use core\app\ErrorHandling;
use core\base\ControllerActionLogger;
use core\base\ControllerResponseBuilder;
use core\contracts\Application;
use PHPKitchen\DI\Contracts\ContainerAware;
use PHPKitchen\DI\Contracts\ServiceLocatorAware;
use PHPKitchen\DI\Mixins\ContainerAccess;
use PHPKitchen\DI\Mixins\ServiceLocatorAccess;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\rest\Controller as BaseController;

/**
 * Controller class file.
 *
 * @property Application $serviceLocator
 * @method Application getServiceLocator()
 *
 * @author Dmitry Bukavin <4o.djaconda@gmail.com>
 */
class Controller extends BaseController implements ContainerAware, ServiceLocatorAware {
    use ControllerActionLogger;
    use ControllerResponseBuilder;
    use ServiceLocatorAccess;
    use ContainerAccess;
    use ErrorHandling;
    use AdvancedRateLimiter;

    public function behaviors(): array {
        $parentBehaviors = parent::behaviors();
        $this->addRateLimiterConfig($parentBehaviors);

        return ArrayHelper::merge($parentBehaviors, [
            'access' => [
                'class' => AccessControl::class,
                'rules' => $this->accessRules(),
            ],
        ]);
    }

    protected function accessRules(): array {
        return [
            ['allow' => true],
        ];
    }

    protected function shouldLogAction(): bool {
        return API_CONTROLLERS_INFO_LOGGING_ENABLED;
    }
}
