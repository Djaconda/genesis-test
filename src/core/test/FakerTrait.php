<?php

namespace core\test;

use Faker\Factory;
use Faker\Generator;

/**
 * Extends tests with ability to access faker instance without creating faker each time
 * faker required.
 *
 * @property Generator $faker faker instance;
 *
 * @package core\test
 * @author Dmitry Bukavin <4o.djaconda@gmail.com>
 */
trait FakerTrait {
    /**
     * @var Generator faker instance.
     */
    protected $_faker;

    /**
     * @return Generator
     */
    public function getFaker() {
        if (!isset($this->_faker)) {
            $this->initFaker();
        }

        return $this->_faker;
    }

    protected function initFaker() {
        $this->_faker = Factory::create();
    }
}
