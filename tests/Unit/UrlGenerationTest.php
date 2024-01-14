<?php

namespace App\Tests\Unit;

use App\Service\ENVHelperService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

class UrlGenerationTest extends TestCase
{
    public function testUrlGeneration()
    {
        $envHelper = $this->createMock(ENVHelperService::class);

        $envHelper
            ->expects($this->exactly(3))
            ->method('getParameter')
            ->will($this->onConsecutiveCalls('https://v6.exchangerate-api.com/v6/', 'key', 'EUR'));

        $url = $this->generateUrlFromParams($envHelper);

        $this->assertEquals('https://v6.exchangerate-api.com/v6/key/latest/EUR', $url);
    }

    //this is a replica of the real method in ENVHelperService
    private function generateUrlFromParams(MockObject $mock): string
    {
        $url  = $mock->getParameter('api.baseurl');
        $url .= $mock->getParameter('api.key');
        $url .= '/latest/';
        $url .= $mock->getParameter('app.basecurrency');

        return $url;
    }
}