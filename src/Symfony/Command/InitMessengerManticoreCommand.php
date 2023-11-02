<?php

declare(strict_types=1);

namespace App\Symfony\Command;

use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'messenger:init:manticore')]
class InitMessengerManticoreCommand extends Command
{
    public function __construct(
        private readonly Connection $manticoreConnection,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $this->manticoreConnection->executeStatement('DROP TABLE failed_message');
            $this->manticoreConnection->executeStatement('CREATE TABLE failed_message(id bigint, queue_name string, message_class string, body text, headers json, created_at timestamp, failed_at timestamp)');
            $output->writeln('OK');
            return self::SUCCESS;
        } catch (\Throwable $e) {
            $output->writeln($e->getMessage());
            return self::FAILURE;
        }
    }
}
