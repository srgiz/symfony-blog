<?php
namespace App\Logger\Diff;

use App\Logger\Diff\Factory\DiffFactoryInterface;

interface DiffManagerInterface
{
    public function watch(object $object): void;

    public function unwatch(object $object): void;

    public function fetchUid(object $object): array;

    public function getFactory(object $object): ?DiffFactoryInterface;
}
