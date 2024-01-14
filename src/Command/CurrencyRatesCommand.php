<?php

namespace App\Command;

use App\Entity\Currency;
use App\Entity\ExchangeRate;
use App\Entity\ExchangeRateMetadata;
use App\Repository\CurrencyRepository;
use App\Repository\ExchangeRateMetadataRepository;
use App\Service\ENVHelperService;
use App\Service\HTTPHelperService;
use App\Service\ORMHelperService;
use App\Service\RedisCacheService;
use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use GuzzleHttp\Client;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:currency:rates',
    description: 'Fetches exchange rates and saves them to the database',
)]
class CurrencyRatesCommand extends Command
{
    public function __construct(
        private readonly ENVHelperService $envHelper,
        private readonly ORMHelperService $ormHelper,
        private readonly RedisCacheService $cacheService,
        private readonly HTTPHelperService $httpHelper,
        private readonly LoggerInterface $logger,
        $name = null
    )
    {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->addArgument('base_currency', InputArgument::REQUIRED, 'Base currency')
            ->addArgument('target_currencies', InputArgument::IS_ARRAY|InputArgument::REQUIRED, 'Target currencies');
    }

    /**
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $base = $input->getArgument('base_currency');

        $targetCurrencies = $input->getArgument('target_currencies');

        $data = $this->httpHelper->get($this->envHelper->generateUrlFromParams());


        if (!isset($data['result']) || $data['result'] !== 'success') {
            $this->logger->error('You didnt get the data: ' . $data['error-type'] ?? 'third party api error');
            $output->write('You didnt get the data: ' . $data['error-type'] ?? 'third party api error');
            return 0;
        }

        try {
            $data = $this->ormHelper->handleDBSave($data, $base, $targetCurrencies);
            $data = json_encode($data);
            $this->cacheService->set("data", $data);
        } catch (Exception $e) {
            $this->logger->error('Something went wrong:' . $e->getMessage() . ' ' . $e->getCode());
            $output->write('Something went wrong:' . $e->getMessage() . ' ' . $e->getCode());
            return 0;
        }

        $output->write('Success!');

        return 1;
    }

}
