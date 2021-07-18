<?php
namespace App\Logger\Diff\Metadata;

interface MetadataInterface
{
    public function getObjectName(): string;

    public function getExcludedSet(): array;

    /**
     * @return array<string> entity:id
     */
    public function genUid(object $object): array;
}
