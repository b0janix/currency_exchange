<?php

namespace App\Service;

use Doctrine\DBAL\Driver\Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class HTTPHelperService
{
    public function __construct(
        private readonly Client $guzzle
    )
    {

    }

    public function get(string $url): array
    {
        $data = [];

        try {
            $response = $this->guzzle->get($url);

            $contents = $response->getBody()->getContents();

            $data = json_decode($contents, true);

        } catch (Exception | GuzzleException $e) {
            //Log it
        }

        return $data;
    }
}