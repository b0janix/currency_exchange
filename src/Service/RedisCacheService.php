<?php

declare(strict_types=1);

namespace App\Service;

use DateTime;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use SymfonyBundles\RedisBundle\Redis\ClientInterface;

readonly class RedisCacheService
{
    public function __construct(
        private ClientInterface $redis,
        private EntityManagerInterface $entityManager
    )
    {
    }

    public function get(string $key): ?string
    {
        return $this->redis->get($key);
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function getData(string $key, string $base, array $targets): array
    {
        if (!$this->has($key)) {

            $today = new DateTime('today', new \DateTimeZone('Europe/Skopje'));
            $today->setTime(1, 0);
            $todayString = $today->format('Y-m-d H:i:s');

            $tomorrow = new DateTime('tomorrow', new \DateTimeZone('Europe/Skopje'));
            $tomorrow->setTime(1, 0);
            $tomorrowString = $tomorrow->format('Y-m-d H:i:s');

            $conn = $this->entityManager->getConnection();

            $targets[] = $base;

            $placeholders = array_map(fn ($item) => ':' . $item, $targets);
            $boundValues = array_combine($targets, $targets);

            $sql = <<<SQL
                SELECT `exchange_rate_metadata`.*, `exchange_rate`.`rate`, `currency`.`currency_code` 
                FROM `exchange_rate_metadata`                
                INNER JOIN `exchange_rate` ON `exchange_rate_metadata`.`id` = `exchange_rate`.`metadata_id`
                INNER JOIN `currency` ON `exchange_rate`.`currency_id` = `currency`.`id`
                WHERE `exchange_rate_metadata`.`base_currency_code` = :base                
                AND `exchange_rate_metadata`.`created_at` >= :today
                AND `exchange_rate_metadata`.`created_at` <= :tomorrow                
                SQL;

            $sql .= "AND (`currency`.`currency_code` IN (" . implode(',', $placeholders) . ")) 
                ORDER BY `exchange_rate_metadata`.`created_at` DESC";

            $stmt = $conn->prepare($sql);
            $result = $stmt->executeQuery([
                'today' => $todayString,
                'tomorrow' => $tomorrowString,
                'base' => $base,
                ...$boundValues
            ]);

            $resArray = $result->fetchAllAssociative();

            $apiRes = [];

            if ($resArray) {
                $apiRes['time_last_update_unix'] = $resArray[0]['time_last_update_unix'];
                $apiRes['time_next_update_unix'] = $resArray[0]['time_next_update_unix'];
                $apiRes['time_last_update_utc'] = $resArray[0]['time_last_update_utc'];
                $apiRes['time_next_update_utc'] = $resArray[0]['time_next_update_utc'];
                $apiRes['base_code'] = $resArray[0]['base_currency_code'];

                foreach ($resArray as $item) {
                    if (!isset($apiRes['conversion_rates'][$item['currency_code']])) {
                        $apiRes['conversion_rates'][$item['currency_code']] = $item['rate'];
                    }
                }

                $this->set('data', json_encode($apiRes));
            }

            return $apiRes;
        }

        $apiRes = json_decode($this->redis->get($key), true);

        if ($apiRes['base_code'] === $base) {

            $conversionRates = [];

            foreach ($targets as $target) {
                if (isset($apiRes['conversion_rates'][$target])) {
                    $conversionRates[$target] = $apiRes['conversion_rates'][$target];
                }
            }

            $apiRes['conversion_rates'] = $conversionRates;

            return $apiRes;
        }

        return [];
    }

    public function findArrayValue(string $key): ?array
    {
        return $this->redis->hgetall($key);
    }

    public function set(string $key, string $value): void
    {
        $this->redis->set($key, $value);
        //$this->redis->expire($key, $this->ttl);
    }

    public function setArray(string $key, array $value): void
    {
        $this->redis->hmset($key, $value);
        //$this->redis->expire($key, $this->ttl);
    }

    public function remove(string $key): void
    {
        $this->redis->remove($key);
    }

    public function has(string $key): bool
    {
        return (bool) $this->redis->get($key);
    }
}