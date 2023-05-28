<?php

namespace core\api;

use core\api\rest\ApiHttpBearerAuth;
use core\api\rest\ApiUserHttpBearerAuth;
use core\app\YiiStaticMethodsAccess;

/**
 * API application class file.
 *
 * @see Application
 * @see YiiStaticMethodsAccess
 *
 * @author Dmitry Bukavin <4o.djaconda@gmail.com>
 */
class Application extends \yii\web\Application implements ApiApplication {
    use YiiStaticMethodsAccess;

    public $modulesOrder = [];

    public function init(): void {
        parent::init();
    }

    public function getResponse() {
        $response = parent::getResponse();
        $contentType = $this->getRequest()->getContentType();
        $jsonTypes = [
            'application/json',
            'application/vnd.api+json',
        ];
        $acceptableContentTypes = array_filter(
            array_keys($this->getRequest()->getAcceptableContentTypes()),
            function ($accept) use ($jsonTypes) {
                return in_array($accept, $jsonTypes);
            }
        );

        if (in_array($contentType, $jsonTypes) || $acceptableContentTypes) {
            $response->format = $response::FORMAT_JSON;
        }

        return parent::getResponse();
    }
}
