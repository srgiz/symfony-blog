<?php
namespace App\Logger;

interface ObjDiffLoggerInterface
{
    public function log(object $object, array $changeSet): void;
}
