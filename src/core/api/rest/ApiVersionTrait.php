<?php

namespace core\api\rest;

use core\contracts\Application;

/**
 * Gets version parameter from Accept header. Must be used with yii\filters\ContentNegotiator behavior.
 *
 * @property Application $serviceLocator
 * @method Application getServiceLocator()
 *
 * @author Dmitry Bukavin <4o.djaconda@gmail.com>
 */
trait ApiVersionTrait {
    protected function getApiVersion(): string {
        $acceptParams = $this->serviceLocator->response->acceptParams;
        $defaultVersion = ($this->serviceLocator->params['apiVersions'] ?? ['1.0'])[0];

        return $acceptParams['version'] ?? $defaultVersion;
    }
}
