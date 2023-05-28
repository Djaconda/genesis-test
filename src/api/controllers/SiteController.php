<?php

namespace api\controllers;

use core\api\Controller;
use core\api\rest\ApiVersionTrait;
use yii\web\HttpException;
use yii\web\Response;

/**
 * Site controller
 */
class SiteController extends Controller {
    use ApiVersionTrait;

    public $errorStatuses = [
        // INFORMATIONAL CODES
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        // SUCCESS CODES
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-status',
        208 => 'Already Reported',
        // REDIRECTION CODES
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Switch Proxy',
        // Deprecated
        307 => 'Temporary Redirect',
        // CLIENT ERROR
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Time-out',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Large',
        415 => 'Unsupported Media Type',
        416 => 'Requested range not satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        425 => 'Unordered Collection',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        // SERVER ERROR
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Time-out',
        505 => 'HTTP Version not supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        511 => 'Network Authentication Required',
    ];

    public function init(): void {
        $this->serviceLocator->response->format = Response::FORMAT_JSON;
    }

    public function actionInfo() {
        $requestedVersion = $this->getApiVersion();

        return [
            'requested_version' => $this->getApiVersion(),
            'versions_available' => ($this->serviceLocator->params['apiVersions'] ?? ['1.0']),
            'documentation' => "/api-doc?version=$requestedVersion",
        ];
    }

    public function actionError() {
        $app = $this->serviceLocator;
        if (($exception = $app->getErrorHandler()->exception) === null) {
            // action has been invoked not from error handler, but by direct route, so we display '404 Not Found'
            $app->response->statusCode = 404;
            $exception = new HttpException(404, $app->translate('yii', 'Page not found.'));
        }

        $statusCode = $exception->statusCode;
        $message = $this->errorStatuses[$statusCode] ?? $exception->getMessage();

        return [
            'message' => $message,
            'status' => $statusCode,
        ];
    }
}
