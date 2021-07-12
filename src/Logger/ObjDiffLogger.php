<?php
declare(strict_types=1);

namespace App\Logger;

class ObjDiffLogger implements ObjDiffLoggerInterface
{
    // todo: описать логирование изменений сущностей
    public function log(object $object, array $changeSet): void
    {
        //var_dump($changeSet);
    }
}
