<?php

namespace core\filters;

use core\contracts\Application;
use yii\filters\Cors;
use yii\helpers\ArrayHelper;

/**
 * @property Application $serviceLocator
 *
 * @author Dmitry Bukavin <4o.djaconda@gmail.com>
 */
trait CorsConfigTrait {
    public function getCorsConfig(): array {
        $instanceUrl = 'genesis.local';
        $instanceUnsafeUrl = str_replace('https', 'http', (string)$instanceUrl);

        $corsOrigins = ArrayHelper::merge(
            $this->serviceLocator->params['corsOrigins'],
            [
                $instanceUrl,
                $instanceUnsafeUrl,
            ]
        );

        return [
            'corsFilter' => [
                'class' => Cors::class,
                'cors' => [
                    'Origin' => $corsOrigins,
                    'Access-Control-Allow-Credentials' => true,
                    'Access-Control-Request-Headers' => [
                        'Origin',
                        'X-Requested-With',
                        'Content-Type',
                        'accept',
                        'Authorization',
                        'Access-Control-Allow-Origin',
                    ],
                    'Access-Control-Request-Method' => [
                        'GET',
                        'POST',
                        'PUT',
                        'PATCH',
                        'DELETE',
                        'HEAD',
                        'OPTIONS',
                    ],
                    'Access-Control-Max-Age' => 86400,
                ],

            ],
        ];
    }
}
