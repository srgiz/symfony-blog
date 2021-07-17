<?php
namespace App\Logger\Diff;

use App\Logger\Diff\Metadata\MetadataInterface;

interface DiffManagerInterface
{
    public function getMetadataClass(string $className): ?MetadataInterface;

    public function clear(): void;
}
