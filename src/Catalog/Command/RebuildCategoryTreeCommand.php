<?php
declare(strict_types=1);

namespace App\Catalog\Command;

use App\Catalog\Category\CategoryTreeBuilder;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:catalog:rebuild-category-tree', description: 'Пересоздание связей дерева категорий')]
class RebuildCategoryTreeCommand extends Command
{
    private CategoryTreeBuilder $builder;

    public function __construct(CategoryTreeBuilder $builder)
    {
        parent::__construct();
        $this->builder = $builder;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $this->builder->rebuild();
            $io->success('Category tree rebuilt');
        } catch (\Throwable $e) {
            $io->error($e->getMessage());
            return 1;
        }

        return 0;
    }
}
