<?php
declare(strict_types=1);

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:rebuild-category-tree', description: 'Rebuild category tree')]
class RebuildCategoryTreeCommand extends Command
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $this->em->createNativeQuery('CALL rebuild_category_tree()', new ResultSetMapping())->execute();
            $io->success('Category tree rebuilt');
        } catch (\Throwable $e) {
            $io->error($e->getMessage());
            return 1;
        }

        return 0;
    }
}
