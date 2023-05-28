<?php

namespace core\console;

use core\app\YiiStaticMethodsAccess;
use yii\console\Application as BaseConsoleApplication;

/**
 * Console application class.
 * Extends {@link BaseConsoleApplication} to implement {@link Application}.
 *
 * @see Application
 * @see YiiStaticMethodsAccess
 *
 * @author Dmitry Bukavin <4o.djaconda@gmail.com>
 */
class Application extends BaseConsoleApplication implements ConsoleApplication {
    use YiiStaticMethodsAccess;

    //Fully inherited
}
