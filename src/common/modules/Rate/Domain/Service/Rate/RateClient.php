<?php

namespace Rate\Domain\Service\Rate;

use core\log\mixins\Logging;
use Exception;
use Psr\Http\Client\ClientInterface;
use Rate\Domain\Contract\RateClient as RateClientContract;

/**
 * Represents Rate Http Cleint
 *
 * @author Dmitry Bukavin <4o.djaconda@gmail.com>
 */
final class RateClient implements RateClientContract {
    use Logging;

    public const API_URL = 'https://api.coingecko.com/api/v3/simple/price';

    public function __construct(private readonly ClientInterface $httpClient) {
    }

    public function getCurrent(): float {
        $rate = 0;
        try {
            $response = $this->httpClient->request('GET', self::API_URL, [
                'query' => [
                    'ids' => 'bitcoin',
                    'vs_currencies' => 'uah',
                    'precision' => 7,
                ],
            ]);

            $data = json_decode($response->getBody(), true, 512, JSON_THROW_ON_ERROR);
            $rate = $data['bitcoin']['uah'];
        } catch (Exception $e) {
            $this->logError('Rate resresh error ' . $e->getMessage());
        }

        return $rate;
    }
}
