<?php

namespace App\Tests\Functional;

use App\Service\HTTPHelperService;
use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GETEndpointDataTest extends WebTestCase
{

    public function testGetEndpointData()
    {
        $client = new HTTPHelperService((new Client()));

        $res = $client->get('http://127.0.0.1:8000/api/exchange-rates?page=1&base=EUR&targets=USD%2CGBP%2CEUR');

        if ($res) {
            $this->assertArrayHasKey('hydra:member', $res);
            $this->assertArrayHasKey(0, $res['hydra:member']);
            $this->assertArrayHasKey('conversion_rates', $res['hydra:member'][0]);
            $this->assertArrayHasKey('base_code', $res['hydra:member'][0]);
            $this->assertEquals('EUR', $res['hydra:member'][0]['base_code']);
        } else {
            $this->assertEmpty($res);
        }

    }

    public function testGetEndpointData1()
    {
        $client = new HTTPHelperService((new Client()));

        $res = $client->get('http://127.0.0.1:8000/api/exchange-rates?page=1&base=GBP&targets=USD%2CGBP%2CEUR');

        if ($res) {
            $this->assertArrayHasKey('hydra:member', $res);
            $this->assertEmpty($res['hydra:member']);
        } else {
            $this->assertEmpty($res);
        }

    }
}