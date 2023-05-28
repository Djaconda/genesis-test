<?php

namespace core\api;

use common\instance\Instance;
use Exception;
use PHPKitchen\DI\Mixins\ServiceLocatorAccess;
use User\Profile\Domain\Model\User\UserRecord;
use yii\web\IdentityInterface;
use yii\web\User as BaseUser;

// use frontend\components\web\User as BaseUser;

class User extends BaseUser {
    use ServiceLocatorAccess;

    public function getIdentity($autoRenew = true): ?IdentityInterface {
        if (!parent::getIdentity($autoRenew)) {
            if ($autoRenew) {
                $this->renewAuthStatus();
            } else {
                return null;
            }
        }

        $this->setIdentity(UserRecord::findOne(1)); //@TODO

        return parent::getIdentity($autoRenew);
    }

    protected function renewAuthStatus(): void {
        try {
            $app = $this->serviceLocator;
            $userId = $app->user->id;
        } catch (Exception) {
            return;
        }
        if ($userId) {
            $this->setIdentity($this->serviceLocator->instance->getCurrentUser());
        }
    }
}
