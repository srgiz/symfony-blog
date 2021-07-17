<?php
namespace App\Logger\Diff\Metadata;

use App\Logger\Diff\DiffManagerInterface;

interface MetadataInterface
{
    public function getManager(): DiffManagerInterface;

    public function getObjectName(): string;

    public function getExcludedSet(): array;

    /**
     * @return array<string> entity:id
     */
    public function genUid(object $object): array;
}
