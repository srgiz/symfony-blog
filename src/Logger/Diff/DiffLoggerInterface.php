<?php
namespace App\Logger\Diff;

interface DiffLoggerInterface
{
    public function getManager(): DiffManagerInterface;

    public function log(string $level, string $event, object $object, array $changeSet): void;
}
