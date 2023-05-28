<?php

namespace core\contracts;

use Bitfocus\Storage\Hub\StorageHub;
use core\api\rest\MobileDetector;
use core\api\rest\session\DbSession;
use core\log\AuthLogger;
use yii\redis\Session;
use yii\web\User;

/**
 * Interface of the web application.
 *
 * @property string $homeUrl The homepage URL.
 * @property StorageHub $fileStorage The session component. This property is read-only.
 * @property User $user The user component. This property is read-only.
 * @property DbSession|Session $session The session component. This property is read-only.
 * @property MobileDetector $mobileDetector
 * @property AuthLogger $authLogger
 *
 * @author Dmitry Bukavin <4o.djaconda@gmail.com>
 */
interface WebApplication extends Application {
    // Fully inherited
}
