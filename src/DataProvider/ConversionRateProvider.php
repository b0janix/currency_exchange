<?php

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\ApiPlatform\ExchangeRatesBaseFilter;
use App\ApiPlatform\ExchangeRatesTargetsFilter;
use App\Entity\ConversionRate;
use App\Service\RedisCacheService;
use DateTimeImmutable;
use Exception;
use GuzzleHttp\Psr7\Request;

readonly class ConversionRateProvider implements ContextAwareCollectionDataProviderInterface, RestrictedDataProviderInterface
{
    public function __construct(private RedisCacheService $cacheService)
    {
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return $resourceClass === ConversionRate::class;
    }

    /**
     * @param string $resourceClass
     * @param string|null $operationName
     * @param array $context
     * @throws Exception
     */
    public function getCollection(string $resourceClass, string $operationName = null, array $context = []): array
    {
        $conversionRate = new ConversionRate();

        $base = $context[ExchangeRatesBaseFilter::BASE_FILER_CONTEXT];
        $targets = $context[ExchangeRatesTargetsFilter::TARGETS_FILER_CONTEXT];

        $res = $this->cacheService->getData('data', $base, $targets);

        if ($res) {
            $conversionRate->time_last_update_unix = (int) $res['time_last_update_unix'];
            $conversionRate->time_next_update_unix = (int) $res['time_next_update_unix'];
            $conversionRate->time_last_update_utc = new DateTimeImmutable($res['time_last_update_utc']);
            $conversionRate->time_next_update_utc = new DateTimeImmutable($res['time_next_update_utc']);
            $conversionRate->base_code = $res['base_code'];
            $conversionRate->conversion_rates = $res['conversion_rates'];

            return [$conversionRate];
        }

        return [];
    }
}