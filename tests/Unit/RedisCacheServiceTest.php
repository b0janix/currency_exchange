<?php

namespace App\Tests\Unit;

use App\Service\HTTPHelperService;
use App\Service\RedisCacheService;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;
use SymfonyBundles\RedisBundle\Redis\Client;

class RedisCacheServiceTest extends TestCase
{
    public function testCacheGet()
    {
        $redisClient = new Client([]);
        $entityManager = $this->createMock(EntityManager::class);
        $cacheService = new RedisCacheService($redisClient, $entityManager);

        $cacheService->set('test', 'test');

        $test= $cacheService->get('test');

        $this->assertEquals('test', $test);

        $cacheService->remove('test');

        $this->assertNull($cacheService->get('test'));
    }
}