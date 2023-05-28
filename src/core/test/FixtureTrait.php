<?php

namespace core\test;

use PHPKitchen\DI\Mixins\ContainerAccess;
use PHPKitchen\DI\Mixins\ServiceLocatorAccess;

/**
 * Allows to use faker integration and app DI in fixtures.
 *
 * @see FakerTrait
 *
 * @package core\test
 * @author Dmitry Bukavin <4o.djaconda@gmail.com>
 */
trait FixtureTrait {
    use ServiceLocatorAccess;
    use ContainerAccess;
    use FakerTrait;
}
