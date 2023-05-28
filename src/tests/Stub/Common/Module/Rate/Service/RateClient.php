<?php

namespace Test\Stub\Common\Module\Rate\Service;

use Rate\Domain\Contract\RateClient as RateClientContract;

final class RateClient implements RateClientContract {
    public float $rate;

    public function getCurrent(): float {
        $this->rate = random_int(10000, 1000000);

        return $this->rate;
    }
}
