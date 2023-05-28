<?php

namespace Test\Unit\Common;

use core\test\DbTestCase as BaseDbTestCase;

/**
 * @inheritdoc
 */
class DbTestCase extends BaseDbTestCase {
    public $appConfig = '@Test/Unit/Config/Common/main.php';
}
