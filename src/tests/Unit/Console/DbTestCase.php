<?php

namespace Test\Unit\Console;

use core\test\DbTestCase as BaseDbTestCase;

/**
 * @inheritdoc
 */
class DbTestCase extends BaseDbTestCase {
    public $appConfig = '@Test/Unit/Config/Console/main.php';
}
