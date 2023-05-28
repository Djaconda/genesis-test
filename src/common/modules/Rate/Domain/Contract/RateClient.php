<?php

namespace Rate\Domain\Contract;

/**
 * Defines interface of Rate Http Cleint
 *
 * @author Dmitry Bukavin <4o.djaconda@gmail.com>
 */
interface RateClient {
    public function getCurrent(): float;
}
