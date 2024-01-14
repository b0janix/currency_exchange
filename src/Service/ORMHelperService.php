<?php

namespace App\Service;

use App\Entity\Currency;
use App\Entity\ExchangeRate;
use App\Entity\ExchangeRateMetadata;
use App\Repository\CurrencyRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

readonly class ORMHelperService
{
    public function __construct(
        private CurrencyRepository     $currencyRepository,
        private EntityManagerInterface $entityManager
    )
    {
    }

    public function handleDBSave(array $data, string $base, array $targetCurrencies): array
    {
        $exchangeRateMetadata = $this->generateExchangeRateMetadata($data, $base);
        $this->entityManager->persist($exchangeRateMetadata);
        $this->entityManager->flush();

        $redisData = [];

        foreach ([$base, ...$targetCurrencies] as $currencyCode) {
            if (isset($data['conversion_rates'][$currencyCode])) {
                $currency = $this->currencyRepository->findOneBy(['currency_code' => $currencyCode]);

                if (!$currency) {
                    $currency = new Currency();
                    $currency->setCurrencyCode($currencyCode);

                    $exchangeRate = $this->generateExchangeRate($data, $currency, $currencyCode, $exchangeRateMetadata);

                    $this->entityManager->persist($currency);
                    $this->entityManager->persist($exchangeRate);
                    $this->entityManager->flush();

                    $redisData[$currencyCode] = (float) $data['conversion_rates'][$currencyCode];
                    continue;
                }

                $exchangeRate = $this->generateExchangeRate($data, $currency, $currencyCode, $exchangeRateMetadata);

                $this->entityManager->persist($exchangeRate);
                $this->entityManager->flush();

                $redisData[$currencyCode] = (float) $data['conversion_rates'][$currencyCode];
            }
        }

        unset($data['result']);
        $data['conversion_rates'] = $redisData;

        return $data;
    }

    /**
     * @throws Exception
     */
    public function generateExchangeRateMetadata(array $data, string $baseCode): ExchangeRateMetadata
    {
        $metadata = new ExchangeRateMetadata();

        $metadata->setTimeLastUpdateUnix($data["time_last_update_unix"]);
        $metadata->setTimeLastUpdateUtc(new DateTimeImmutable($data["time_last_update_utc"]));
        $metadata->setTimeNextUpdateUnix($data["time_next_update_unix"]);
        $metadata->setTimeNextUpdateUtc(new DateTimeImmutable($data["time_next_update_utc"]));
        $metadata->setBaseCurrencyCode($baseCode);

        return $metadata;

    }

    public function generateExchangeRate(
        array $data,
        Currency $currency,
        string $currencyCode,
        ExchangeRateMetadata $metadata
    ): ExchangeRate
    {
        $exchangeRate = new ExchangeRate();
        $exchangeRate->setRate($data['conversion_rates'][$currencyCode]);
        $exchangeRate->setCurrency($currency);
        $exchangeRate->setMetadata($metadata);

        return $exchangeRate;
    }
}