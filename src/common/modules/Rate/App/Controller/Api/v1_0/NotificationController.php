<?php

namespace Rate\App\Controller\Api\v1_0;

use core\api\rest\ApiVersionTrait;
use core\api\rest\BaseRestApiController;
use core\filters\CorsConfigTrait;
use Rate\Domain\Service\Notification\Notifier;
use Rate\Domain\Service\Rate\RateManager;
use Rate\Domain\Service\Subscription\SubscriptionManager;

/**
 * Represents actions for Notification endpoint
 *
 * @author Dmitry Bukavin <4o.djaconda@gmail.com>
 */
final class NotificationController extends BaseRestApiController {
    use ApiVersionTrait;
    use CorsConfigTrait;

    public $defaultAction = 'notify';

    public function actionNotify(): void {
        $rate = $this->container->create(RateManager::class)->getCurrent();
        $manager = $this->container->create(SubscriptionManager::class);
        $notifier = $this->container->create(Notifier::class);

        $notifier->notify($manager->getEmails(), $rate);
    }
}
