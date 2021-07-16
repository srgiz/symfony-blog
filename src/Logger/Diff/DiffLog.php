<?php
declare(strict_types=1);

namespace App\Logger\Diff;

#[\Attribute]
class DiffLog
{
    public string $uidClass;

    public ?string $name;

    public array $exclude = [];

    public function __construct(string $uidClass, string $name = null, array $exclude = [])
    {
        $this->uidClass = $uidClass;
        $this->name = $name;
        $this->exclude = $exclude;
    }
}
