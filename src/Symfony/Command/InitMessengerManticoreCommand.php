<?php

declare(strict_types=1);

namespace App\Symfony\Command;

use App\Symfony\Messenger\Transport\Manticore\ManticoreTransport;
use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'messenger:manticore:init')]
class InitMessengerManticoreCommand extends Command
{
    public function __construct(
        private readonly Connection $manticoreConnection,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption(name: 'table_name', mode: InputArgument::OPTIONAL, default: ManticoreTransport::DEFAULT_TABLE)
            ->addOption(name: 'force')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            if ($input->getOption('force')) {
                $this->manticoreConnection->executeStatement('DROP TABLE IF EXISTS ' . $input->getOption('table_name'));
            }

            $this->manticoreConnection->executeStatement("CREATE TABLE {$input->getOption('table_name')}(id bigint, queue_name string, message_class string, body text, headers json, created_at timestamp, failed_at timestamp)");

            $output->writeln('OK');
            return self::SUCCESS;
        } catch (\Throwable $e) {
            $output->writeln($e->getMessage());
            return self::FAILURE;
        }
    }
}
