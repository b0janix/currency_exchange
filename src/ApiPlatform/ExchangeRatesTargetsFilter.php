<?php

namespace App\ApiPlatform;

use ApiPlatform\Serializer\Filter\FilterInterface;
use App\Repository\CurrencyRepository;
use Symfony\Component\HttpFoundation\Request;

class ExchangeRatesTargetsFilter implements FilterInterface
{
    const TARGETS_FILER_CONTEXT = 'exchange_rates_targets';

    public function apply(Request $request, bool $normalization, array $attributes, array &$context): void
    {
        $targets = $request->query->get('target_currencies');

        $targets = explode(',', $targets);

        $targets = array_map(function($item) {
            $item = preg_replace("/[^A-Z]/", '', $item);
            return trim($item);
        }, $targets);

        $context[self::TARGETS_FILER_CONTEXT] = $targets;
    }

    public function getDescription(string $resourceClass): array
    {
        return [
            'target_currencies' => [
                'property' => null,
                'type' => 'string',
                'required' => true,
                'description' => 'Set the target currency codes. Please leave no empty spaces after the comas.',
            ]
        ];
    }
}