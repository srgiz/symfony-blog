<?php

declare(strict_types=1);

namespace App\Symfony\Command;

use App\Domain\Blog\UseCase\CreateUser\CreateUserCommand as UseCaseCommand;
use App\Domain\Blog\UseCase\CreateUser\CreateUserUseCase;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'user:create')]
class UserCreateCommand extends Command
{
    public function __construct(
        private readonly CreateUserUseCase $useCase,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED)
            ->addArgument('password', InputArgument::REQUIRED)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            ($this->useCase)(new UseCaseCommand($input->getArgument('email'), $input->getArgument('password')));

            $output->writeln('OK');

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $output->writeln($e->getMessage());

            return self::FAILURE;
        }
    }
}
