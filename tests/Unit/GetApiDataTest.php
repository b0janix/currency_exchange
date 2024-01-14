<?php

namespace App\Tests\Unit;

use App\Service\HTTPHelperService;
use PHPUnit\Framework\TestCase;

class GetApiDataTest extends TestCase
{
    public function testGetData()
    {
        $data = json_decode(file_get_contents('tests/Files/api_response.json'), true);

        $mock = $this->createMock(HTTPHelperService::class);

        $mock->expects($this->once())
             ->method('get')
             ->willReturn($data);

        $this->assertEquals($data, $mock->get('https://www.exchangerate-api.com/'));
    }
}