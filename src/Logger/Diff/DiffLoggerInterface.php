<?php
namespace App\Logger\Diff;

interface DiffLoggerInterface
{
    public function watch(object $object): void;

    public function log(string $level, string $event, object $object, array $changeSet): void;
}
