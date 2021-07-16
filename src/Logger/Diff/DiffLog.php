<?php
declare(strict_types=1);

namespace App\Logger\Diff;

#[\Attribute]
class DiffLog
{
    public string $factoryClass;

    public ?string $name;

    public array $exclude = [];

    public function __construct(string $factoryClass, string $name = null, array $exclude = [])
    {
        $this->factoryClass = $factoryClass;
        $this->name = $name;
        $this->exclude = $exclude;
    }
}
