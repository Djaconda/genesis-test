<?php

namespace Test\Unit\Api;

use core\test\DbTestCase as BaseDbTestCase;

/**
 * @inheritdoc
 */
class DbTestCase extends BaseDbTestCase {
    public $appConfig = '@Test/Unit/Config/Api/main.php';
}
