<?php

namespace core\test;

use yii\test\InitDbFixture;

/**
 * Interface DbTestCase
 *
 *
 * @package Test\phpunit
 * @author Dmitry Bukavin <4o.djaconda@gmail.com>
 */
class DbTestCase extends TestCase {
    /**
     * @inheritdoc
     */
    public function globalFixtures() {
        return [
            InitDbFixture::class,
        ];
    }
}
