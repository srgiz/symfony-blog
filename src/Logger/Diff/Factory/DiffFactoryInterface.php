<?php
namespace App\Logger\Diff\Factory;

use App\Logger\Diff\DiffManagerInterface;

interface DiffFactoryInterface
{
    public function getManager(): DiffManagerInterface;

    public function objectName(): string;

    public function excludedSet(): array;

    /**
     * @return array<string> entity:id
     */
    public function generateUid(object $object): array;
}
