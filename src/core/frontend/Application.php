<?php

namespace core\frontend;

use core\api\rest\User;
use core\app\YiiStaticMethodsAccess;
use core\contracts\WebApplication;
use yii\web\Application as BaseWebApplication;

/**
 * Web application class.
 * Extends {@link BaseWebApplication} to implement {@link Application}.
 *
 * @method User getUser()
 *
 * @see Application
 * @see YiiStaticMethodsAccess
 *
 * @author Dmitry Bukavin <4o.djaconda@gmail.com>
 */
class Application extends BaseWebApplication implements WebApplication {
    use YiiStaticMethodsAccess;

    public $modulesOrder = [];
}
