<?php

namespace core\base;

use yii\web\Response;

/**
 * Helper trait for controllers that provides functions for response building.
 *
 * @property mixed $id
 *
 * @package core\base
 * @author Dmitry Bukavin <4o.djaconda@gmail.com>
 */
trait ControllerResponseBuilder {
    public function createDefaultErrorResponse(
        $message = '',
        $format = Response::FORMAT_JSON,
        string $content = ''
    ) {
        if (empty($message)) {
            $message = 'An internal server error occurred on page "' . $this->id . '".';
        }
        $response = $this->createResponse();
        $response->setFormat($format)
                 ->setName('Error')
                 ->setMessage($message)
                 ->setCode(0)
                 ->setStatus(500)
                 ->setContent($content)
                 ->flagAsFailed();

        return $response;
    }

    public function createDefaultSuccessResponse($content = '', $format = Response::FORMAT_JSON) {
        $response = $this->createResponse();
        $response->setFormat($format)->setContent($content)->flagAsSuccessful();

        return $response;
    }

    /**
     * Create controller response object that should be customized to be returned by controller action.
     *
     * @return ControllerResponse
     */
    protected function createResponse() {
        return $this->container->create(ControllerResponse::class);
    }
}
