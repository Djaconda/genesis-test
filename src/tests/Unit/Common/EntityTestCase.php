<?php

namespace Test\Unit\Common;

use core\test\EntityTestTrait;

/**
 * Extends {@link TestCase} to integrate entity test functionality.
 *
 * @see EntityTestTrait
 *
 * @package Test\Unit\Common
 * @author Dmitry Bukavin <djaconda@quartsoft.com>
 */
class EntityTestCase extends DbTestCase {
    use EntityTestTrait;
}
