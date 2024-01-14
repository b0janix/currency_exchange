<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ENVHelperService
{
    public function __construct(private ParameterBagInterface $params)
    {
    }

    public function getApplicationRootDir(): array|bool|float|int|string|\UnitEnum
    {
        return $this->params->get('kernel.project_dir');
    }

    public function getParameter($parameterName): \UnitEnum|float|int|bool|array|string
    {
        return $this->params->get($parameterName);
    }

    public function generateUrlFromParams(): string
    {
        $url  = $this->getParameter('api.baseurl');
        $url .= $this->getParameter('api.key');
        $url .= '/latest/';
        $url .= $this->getParameter('app.basecurrency');

        return $url;
    }
}