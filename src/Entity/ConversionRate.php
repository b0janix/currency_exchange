<?php

namespace App\Entity;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use DateTimeImmutable;
use Symfony\Component\Serializer\Annotation\Groups;
use App\ApiPlatform\ExchangeRatesBaseFilter;
use App\ApiPlatform\ExchangeRatesTargetsFilter;

/**
 *
 * @ApiFilter(ExchangeRatesBaseFilter::class)
 * @ApiFilter(ExchangeRatesTargetsFilter::class)
 *
 * @ApiResource(
 *     shortName="exchange-rates",
 *     normalizationContext={"groups"={"exchange-rates:read"}},
 *     collectionOperations={"get"},
 *     itemOperations={}
 * )
 */
class ConversionRate
{
    /**
     * @Groups({"exchange-rates:read"})
     */
    public DateTimeImmutable $createdAt;

    /**
     * @Groups({"exchange-rates:read"})
     */
    public int $time_last_update_unix;

    /**
     * @Groups({"exchange-rates:read"})
     */
    public DateTimeImmutable $time_last_update_utc;

    /**
     * @Groups({"exchange-rates:read"})
     */
    public int $time_next_update_unix;

    /**
     * @Groups({"exchange-rates:read"})
     */
    public DateTimeImmutable $time_next_update_utc;

    /**
     * @Groups({"exchange-rates:read"})
     */
    public string $base_code;

    /**
     * @Groups({"exchange-rates:read"})
     */
    public array $conversion_rates;
}