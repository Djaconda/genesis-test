<?php

namespace core\api\rest;

use core\api\rest\mixins\AdvancedRateLimiter;
use core\contracts\WebApplication;
use yii\web\View;

/**
 * Controller is a base class for all of the application controllers.
 *
 * @property WebApplication $serviceLocator
 * @property View|View $view The view object that can be used to render views or view files.
 *
 * @author Dmitry Bukavin <4o.djaconda@gmail.com>
 */
class Controller extends \core\web\Controller {
    use AdvancedRateLimiter;

    public const ACCESS_CONTROLL_BEHAVIOR = 'access';
}
