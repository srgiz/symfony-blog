<?php
namespace App\Logger;

interface ObjDiffLoggerInterface
{
    public function log(object $object, array $args, array $changeSet): void;
}
