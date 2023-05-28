<?php

namespace Rate\Domain\Service\Rate;

use Rate\Domain\Contract\RateClient;

/**
 * Represents sevice for Rate management
 *
 * @author Dmitry Bukavin <4o.djaconda@gmail.com>
 */
final readonly class RateManager {
    public function __construct(
        private RateStore $store,
        private RateClient $client
    ) {
    }

    public function getCurrent(): string {
        return $this->store->get();
    }

    public function refresh(): bool {
        $rate = $this->client->getCurrent();
        $this->store->save($rate);

        return true;
    }
}
