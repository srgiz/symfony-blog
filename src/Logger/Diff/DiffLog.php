<?php
declare(strict_types=1);

namespace App\Logger\Diff;

#[\Attribute]
class DiffLog
{
    public string $metadataClass;

    public ?string $name;

    public array $exclude = [];

    public function __construct(string $metadataClass, string $name = null, array $exclude = [])
    {
        $this->metadataClass = $metadataClass;
        $this->name = $name;
        $this->exclude = $exclude;
    }
}
