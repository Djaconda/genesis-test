<?php

namespace core\web;

use core\app\ErrorHandling;
use core\base\ControllerActionLogger;
use core\base\ControllerResponseBuilder;
use core\contracts\Application;
use PHPKitchen\DI\Contracts\ContainerAware;
use PHPKitchen\DI\Contracts\ServiceLocatorAware;
use PHPKitchen\DI\Mixins\ContainerAccess;
use PHPKitchen\DI\Mixins\ServiceLocatorAccess;
use PHPKitchen\Domain\Web\Mixins\ControllerActionsManagement;
use yii\web\Controller as BaseWebController;
use yii\web\Request;
use yii\web\Response;

/**
 * Base class for all of the web controllers.
 *
 * @property Request $request
 * @property Response $response
 *
 * @property Application $serviceLocator
 * @method Application getServiceLocator()
 *
 * @author Dmitry Bukavin <4o.djaconda@gmail.com>
 */
class Controller extends BaseWebController implements ContainerAware, ServiceLocatorAware {
    use ErrorHandling;
    use ControllerActionLogger;
    use ControllerResponseBuilder;
    use ContainerAccess;
    use ServiceLocatorAccess;
    use ControllerActionsManagement;

    protected function getRequest() {
        return $this->serviceLocator->request;
    }

    protected function getResponse() {
        return $this->serviceLocator->response;
    }

    protected function getSession() {
        return $this->serviceLocator->session;
    }

    protected function shouldLogAction(): bool {
        return FRONTEND_CONTROLLERS_INFO_LOGGING_ENABLED;
    }
}
