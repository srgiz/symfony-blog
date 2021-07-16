<?php
namespace App\Logger\Diff\Factory;

interface DiffFactoryInterface
{
    public function objectName(): string;

    public function excludedSet(): array;

    /**
     * @return array<string> entity:id
     */
    public function generateUid(object $object): array;
}
