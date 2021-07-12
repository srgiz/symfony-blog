<?php
declare(strict_types=1);

namespace App\Logger;

use Psr\Log\LoggerInterface;

class ObjDiffLogger implements ObjDiffLoggerInterface
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $objLogger)
    {
        $this->logger = $objLogger;
    }

    // todo: описать логирование изменений сущностей
    public function log(string $event, object $object, array $args, array $changeSet): void
    {
        foreach ($args['exclude'] ?? [] as $attr)
        {
            unset($changeSet[$attr]);
        }

        $this->logger->info($event, [
            'obj' => $args['obj'],
            'uid' => call_user_func([$object, $args['uid']]),
            'diff' => $changeSet,
        ]);
    }
}
