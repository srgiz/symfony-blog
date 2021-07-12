<?php
namespace App\Logger;

interface ObjDiffLoggerInterface
{
    public function log(string $event, object $object, array $args, array $changeSet): void;
}
