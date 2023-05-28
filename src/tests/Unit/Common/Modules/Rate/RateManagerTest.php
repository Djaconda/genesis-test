<?php

namespace Test\Unit\Common\Modules\Rate;

use Rate\Domain\Contract\RateClient;
use Rate\Domain\Service\Rate\RateManager;
use Rate\Domain\Service\Rate\RateStore;
use Test\Unit\Common\TestCase;

/**
 * Test case for the service {@link RateManager}.
 *
 * @see RateManager
 * @coversDefaultClass \Rate\Domain\Service\Rate\RateManager
 *
 * @author Dmitry Bukavin <djaconda@quartsoft.com>
 *
 * @author Dmitry Bukavin <4o.djaconda@gmail.com>
 */
class RateManagerTest extends TestCase {
    /**
     * @before
     */
    public function crearFile() {
        $this->container->create(RateStore::class)->clear();
    }

    /**
     * @covers ::get
     * @covers ::save
     */
    public function test() {
        $manager = $this->getManager();
        $tester = $this->tester;

        $tester->describe('get curent rate from empty storage')
               ->expectThat('manager returns curent rate equals default vale')
               ->see($manager->getCurrent())
               ->isEqualTo((float)0);

        $manager->refresh();
        $tester->describe('get current rate after refresht')
               ->expectThat('manager returns curent rate that was by RateClient')
               ->see($manager->getCurrent())
               ->isEqualTo($this->container->get(RateClient::class)->rate);
    }

    private function getManager(): RateManager {
        return $this->container->create(RateManager::class);
    }
}
