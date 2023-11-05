<?php

declare(strict_types=1);

namespace App\Symfony\Command;

use App\Symfony\Messenger\Transport\Manticore\ManticoreTransport;
use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'messenger:manticore:init')]
class InitMessengerManticoreCommand extends Command
{
    public function __construct(
        private readonly ManagerRegistry $doctrine,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption(name: 'connection', mode: InputArgument::OPTIONAL, default: 'manticore')
            ->addOption(name: 'table_name', mode: InputArgument::OPTIONAL, default: ManticoreTransport::DEFAULT_TABLE)
            ->addOption(name: 'force')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            /** @var Connection $connection */
            $connection = $this->doctrine->getConnection($input->getOption('connection'));

            if ($input->getOption('force')) {
                $connection->executeStatement('DROP TABLE IF EXISTS ' . $input->getOption('table_name'));
            }

            $connection->executeStatement("CREATE TABLE {$input->getOption('table_name')}(id bigint, queue_name string, message_class string, body text attribute, headers json, created_at timestamp, failed_at timestamp)");

            $output->writeln('OK');
            return self::SUCCESS;
        } catch (\Throwable $e) {
            $output->writeln($e->getMessage());
            return self::FAILURE;
        }
    }
}
