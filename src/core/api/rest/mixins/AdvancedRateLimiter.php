<?php

namespace core\api\rest\mixins;

use core\contracts\Application;
use thamtech\ratelimiter\handlers\RateLimitHeadersHandler;
use thamtech\ratelimiter\handlers\RetryAfterHeaderHandler;
use thamtech\ratelimiter\handlers\TooManyRequestsHttpExceptionHandler;
use thamtech\ratelimiter\RateLimiter;

/**
 * Adds advanced rate limiting config into behaviors array.
 * To be used within Controller behaviors() method
 *
 * @link https://github.com/thamtech/yii2-ratelimiter-advanced
 * @link https://github.com/thamtech/yii2-ratelimiter-advanced/blob/master/docs/recipes.md
 *
 * @property Application $serviceLocator
 *
 * @author Dmitry Bukavin <4o.djaconda@gmail.com>
 */
trait AdvancedRateLimiter {
    protected function addRateLimiterConfig(&$behaviors) {
        $rateLimiterConfig = $this->serviceLocator->params['rateLimiter'] ?? [];
        if ($rateLimiterConfig['enabled'] ?? null) {
            $configs = $rateLimiterConfig[$this::class] ?? [];
            if (!$configs) {
                $configs = $rateLimiterConfig['enabledDefault'] ? $rateLimiterConfig['default'] : [];
            }
            foreach ($configs as $key => $limitCase) {
                $behaviors["rateLimiter$key"] = $this->formRateLimiterConfig($limitCase);
            }
        }
    }

    protected function formRateLimiterConfig(array $limits): array {
        $ipRequests = $limits['ip'] ?? null;
        $ipPerSeconds = $limits['ipSec'] ?? null;
        $userRequests = $limits['user'] ?? null;
        $userPerSeconds = $limits['userSec'] ?? null;
        $only = $limits['only'] ?? null;
        $except = $limits['except'] ?? [];
        $definitions = [];
        if ($ipRequests && $ipPerSeconds) {
            $definitions['ip'] = [
                // at most 'limit' requests within 'window' seconds
                'limit' => $ipRequests,
                'window' => $ipPerSeconds,
                // this causes a separate rate limit to be tracked for each IP address
                'identifier' => function ($context, $rateLimitId) {
                    return $_SERVER['REMOTE_ADDR'] ?? $context->request->getUserIP();
                },
            ];
        }
        if ($userRequests && $userPerSeconds) {
            $definitions['user'] = [
                // at most 'limit' requests within 'window' seconds
                'limit' => $userRequests,
                'window' => $userPerSeconds,
                // this causes a separate rate limit to be tracked for each user ID
                'identifier' => function ($context, $rateLimitId) {
                    return $this->serviceLocator->instance->getCurrentUserId();
                },
            ];
        }

        return $definitions ? [
            'class' => RateLimiter::class,
            'only' => $only,
            'except' => $except,
            'components' => [
                'rateLimit' => [
                    'definitions' => $definitions,
                ],
                'allowanceStorage' => [
                    // use Yii::$app->cache component
                    'cache' => 'cache',
                ],
            ],
            // add X-Rate-Limit-* HTTP headers to the response
            'as rateLimitHeaders' => RateLimitHeadersHandler::class,
            // add Retry-After HTTP header to the response
            'as retryAfterHeader' => RetryAfterHeaderHandler::class,
            // throw TooManyRequestsHttpException when the limit is exceeded
            'as tooManyRequestsException' => TooManyRequestsHttpExceptionHandler::class,
        ] : [];
    }
}
