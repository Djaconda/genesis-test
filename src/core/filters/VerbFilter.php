<?php

namespace core\filters;

use core\app\ErrorHandling;
use core\contracts\Application;
use PHPKitchen\DI\Mixins\ServiceLocatorAccess;
use yii\base\ActionEvent;
use yii\filters\VerbFilter as BaseVerbFilter;
use yii\web\MethodNotAllowedHttpException;

/**
 * VerbFilter class file.
 *
 * @property Application $serviceLocator
 * @method Application getServiceLocator()
 *
 * @author Dmitry Bukavin <4o.djaconda@gmail.com>
 */
class VerbFilter extends BaseVerbFilter {
    use ServiceLocatorAccess;
    use ErrorHandling;

    public $incorrectVerbHandler;

    /**
     * @param ActionEvent $event
     *
     * @return boolean
     * @throws MethodNotAllowedHttpException when the request method is not allowed.
     */
    public function beforeAction($event) {
        $action = $event->action->id;
        if (isset($this->actions[$action])) {
            $verbs = $this->actions[$action];
        } elseif (isset($this->actions['*'])) {
            $verbs = $this->actions['*'];
        } else {
            return $event->isValid;
        }

        $verb = $this->serviceLocator->getRequest()->getMethod();
        $allowed = array_map('strtoupper', $verbs);
        if (!in_array($verb, $allowed, true)) {
            if (is_callable($this->incorrectVerbHandler)) {
                call_user_func($this->incorrectVerbHandler, $event);
            } else {
                $this->handleIncorrectVerb($event);
            }
        }

        return $event->isValid;
    }

    protected function handleIncorrectVerb($event) {
        $event->isValid = false;
        $this->throwException(Application::HTTP_NOT_FOUND_EXCEPTION, 'The page you are looking for does not exist.');
    }
}
