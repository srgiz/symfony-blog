<?php
namespace App\Logger\Diff;

interface DiffLoggerInterface
{
    public function log(string $event, object $object, array $changeSet): void;
}
