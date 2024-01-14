<?php

namespace App\ApiPlatform;

use ApiPlatform\Serializer\Filter\FilterInterface;
use App\Repository\CurrencyRepository;
use Symfony\Component\Asset\Exception\AssetNotFoundException;
use Symfony\Component\HttpFoundation\Request;

class ExchangeRatesBaseFilter implements FilterInterface
{
    const BASE_FILER_CONTEXT = 'exchange_rates_base';
    public function __construct(private CurrencyRepository $currencyRepository)
    {
    }

    public function apply(Request $request, bool $normalization, array $attributes, array &$context): void
    {
        $base = $request->query->get('base_currency');

        $currency = $this->currencyRepository->findOneBy(['currency_code' => $base]);

        if (!$currency) {
           throw new AssetNotFoundException('There is no such base currency');
        }

        $context[self::BASE_FILER_CONTEXT] = $base;
    }

    public function getDescription(string $resourceClass): array
    {
        return [
            'base_currency' => [
                'property' => null,
                'type' => 'string',
                'required' => true,
                'description' => 'Set the base currency code'
            ]
        ];
    }
}