<?php

namespace Rate\App\Controller\Api\v1_0;

use core\api\rest\ApiVersionTrait;
use core\api\rest\BaseRestApiController;
use core\filters\CorsConfigTrait;
use Rate\Domain\Service\Rate\RateManager;

/**
 * Represents actions for Rate endpoint
 *
 * @author Dmitry Bukavin <4o.djaconda@gmail.com>
 */
final class RateController extends BaseRestApiController {
    use ApiVersionTrait;
    use CorsConfigTrait;

    public $defaultAction = 'info';

    public function actionInfo(): float {
        return $this->container->create(RateManager::class)->getCurrent();
    }
}
