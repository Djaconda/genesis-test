<?php

use User\Account\App\Controller\Api\v1_0\ApiGeneralController;
use User\Account\App\Controller\Api\v1_0\AuthTokenController;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\NotAcceptableHttpException;
use yii\web\NotFoundHttpException;
use yii\web\TooManyRequestsHttpException;
use yii\web\UnauthorizedHttpException;
use yii\web\UnprocessableEntityHttpException;

return [
    /**
     * A list of all released REST API versions. Ordered from the actual (the topmost) to the oldest.
     * Example:
     * [
     *      '2.0',
     *      '1.1',
     *      '1.0',
     * ]
     */
    'apiVersions' => [
        '1.0',
    ],
    // An amount of available nested entities. Example, max 2 entities: query clients with agency->userUpdated
    'apiMaxNestedEntities' => 5,
    // Max fields amount that can be requested for an entity using GET "fields" parameter
    'apiMaxFieldsAmount' => 50,
    // Max records in a list
    'apiMaxPerPage' => 300,
    'corsOrigins' => array_map(function ($port) {
        return "http://localhost:$port";
    }, range(4200, 4299)),
    'notLoggableExceptions' => [
        UnprocessableEntityHttpException::class,
        NotFoundHttpException::class,
        ForbiddenHttpException::class,
        BadRequestHttpException::class,
        TooManyRequestsHttpException::class,
        UnauthorizedHttpException::class,
        NotAcceptableHttpException::class,
    ],
    // Rate limiting
    'apiRateLimiting' => [
        'requests' => 100,
        'perSeconds' => 60,
    ],
    // Advanced rate limiting
    'rateLimiter' => [
        // Enable/disable rate limiter
        'enabled' => true,
        // Enable/disable 'default' limits if there are no special rules for a controller
        // CAUTION! If enabled, it will be applied to all controllers
        'enabledDefault' => false,
        /**
         * ip - requests limit by user's IP address
         * ipSec - window in seconds
         * user - requests limit by user's ID
         * userSec - window in seconds
         * only - an array of actions to apply limits to, example ['edit', 'switch-agency'].
         *        If only = null, limits will be applied to all actions
         * except - an array of action, apply limits to all actions except these
         */
        'default' => [
            [
                'only' => null,
                'except' => [],
                'ip' => 100,
                'ipSec' => 60,
                'user' => 100,
                'userSec' => 60,
            ],
        ],
        /**
         * Example of config for a separate controller
         *      InfoController::class => [
         *          ['only' => ['edit'], 'ip' => 10, 'ipSec' => 60, 'user' => 10, 'userSec' => 60],
         *          ['except' => ['edit'], 'ip' => 100, 'ipSec' => 60, 'user' => 100, 'userSec' => 100],
         *      ],
         */
        ApiGeneralController::class => [['ip' => 5, 'ipSec' => 60]],
        AuthTokenController::class => [['ip' => 5, 'ipSec' => 60]],
    ],
];
