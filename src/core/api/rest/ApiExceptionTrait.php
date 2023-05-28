<?php

namespace core\api\rest;

use core\contracts\Application;
use Exception;
use Throwable;

/**
 * Handles REST API exceptions (validation, rate limit, etc.), this exceptions should not be logged
 *
 * @method Application getServiceLocator()
 *
 * @author Dmitry Bukavin <4o.djaconda@gmail.com>
 */
trait ApiExceptionTrait {
    public function runAction($id, $params = []) {
        try {
            return parent::runAction($id, $params);
        } catch (Exception $exception) {
            $this->handleActionException($exception);
        }
    }

    public function beforeAction($action): bool {
        try {
            return parent::beforeAction($action);
        } catch (Exception $exception) {
            $this->handleActionException($exception);
        }
    }

    private function handleActionException(Throwable $exception): void {
        if (in_array($exception::class, $this->getServiceLocator()->params['notLoggableExceptions'])) {
            $this->getServiceLocator()->log->targets['errorAndWarning']->enabled = false;
        }
        throw $exception;
    }
}
