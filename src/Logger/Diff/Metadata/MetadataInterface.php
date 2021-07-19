<?php
namespace App\Logger\Diff\Metadata;

interface MetadataInterface
{
    public function getObjectName(): string;

    public function getExcludedSet(): array;

    /**
     * @return array<string, string> ($objectName, $uid)
     */
    public function getRelatedIds(object $object): array;
}
