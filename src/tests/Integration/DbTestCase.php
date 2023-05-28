<?php

namespace Test\Integration;

use core\test\DbTestCase as BaseDbTestCase;

/**
 * @inheritdoc
 */
class DbTestCase extends BaseDbTestCase {
    public $appConfig = '@Test/Integration/Config/Common/main.php';
}