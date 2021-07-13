<?php
namespace App\Logger;

interface ObjDiffLoggerInterface
{
    public function watch(object $object): void;

    public function log(string $level, string $event, object $object, array $changeSet): void;
}
