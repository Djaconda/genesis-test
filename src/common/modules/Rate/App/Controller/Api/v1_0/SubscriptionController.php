<?php

namespace Rate\App\Controller\Api\v1_0;

use core\api\rest\ApiVersionTrait;
use core\api\rest\BaseRestApiController;
use core\filters\CorsConfigTrait;
use Rate\Domain\Service\Subscription\SubcriptionManager;
use Rate\Domain\Service\Subscription\SubscriptionModel;
use yii\web\BadRequestHttpException;

/**
 * Represents actions for Subscription endpoint
 *
 * @author Dmitry Bukavin <4o.djaconda@gmail.com>
 */
final class SubscriptionController extends BaseRestApiController {
    use ApiVersionTrait;
    use CorsConfigTrait;

    public $defaultAction = 'subscribe';

    public function actionSubscribe(): void {
        $form = $this->container->create(SubscriptionModel::class);
        $form->load($this->serviceLocator->request->post(), '');
        if (!$form->validate()) {
            throw new BadRequestHttpException('Invalid email');
        }

        $this->container->create(SubcriptionManager::class)->addEmail($form->email);
    }
}
