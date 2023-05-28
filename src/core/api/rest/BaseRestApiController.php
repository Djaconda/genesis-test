<?php

namespace core\api\rest;

use core\domain\Web\Action\Renderer\JsonApiData;
use core\filters\CorsConfigTrait;
use yii\filters\ContentNegotiator;
use yii\filters\RateLimiter;
use yii\helpers\ArrayHelper;
use yii\web\Response;

/**
 * Base REST API controller.
 * Authenticate user by JWT ->
 * rate limiting ->
 * access check ->
 * gets version number from Accept header
 *
 * Extends front-end controller since we use ordinary (ready) front-end functionality
 * with an extra layer for JSON transformation.
 *
 * @see JsonApiData
 *
 * @author Dmitry Bukavin <4o.djaconda@gmail.com>
 */
abstract class BaseRestApiController extends ManagementController {
    use ApiVersionTrait;
    use ApiExceptionTrait;
    use CorsConfigTrait;

    /**
     * @var bool
     */
    public $enableCsrfValidation = false;

    public function behaviors(): array {
        if ($this->request->isOptions) {
            $corsConfig = $this->getCorsConfig()['corsFilter'] ?? [];
            if ($corsConfig) {
                $corsFilter = $this->container->create($corsConfig);
                $corsFilter->beforeAction($this->action);
            }
            $this->serviceLocator->response->content = '';
            $this->serviceLocator->response->setStatusCode(200)->send();
            $this->serviceLocator->end();
        }

        return ArrayHelper::merge(
            $this->getCorsConfig(),
            ArrayHelper::merge([
                // Rate limiting must be performed after authentication
                'rateLimiter' => [
                    'class' => RateLimiter::class,
                    'enableRateLimitHeaders' => true,
                ],
                [
                    'class' => ContentNegotiator::class,
                    'formats' => [
                        'application/json' => Response::FORMAT_JSON,
                        'application/scim+json' => Response::FORMAT_JSON,
                        'application/xml' => Response::FORMAT_XML,
                    ],
                ],
            ], parent::behaviors())
        );
    }
}
