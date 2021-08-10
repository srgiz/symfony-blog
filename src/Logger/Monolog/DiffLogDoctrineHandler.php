<?php
declare(strict_types=1);

namespace App\Logger\Monolog;

use App\Entity\Log\Entity;
use App\Entity\Log\EntityRelation;
use Doctrine\Persistence\ManagerRegistry;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;

/**
 * @phpstan-type FormattedRecord array{message: string, context: mixed[], level: Level, level_name: LevelName, channel: string, datetime: \DateTimeImmutable, extra: mixed[], formatted: mixed}
 */
class DiffLogDoctrineHandler extends AbstractProcessingHandler
{
    private ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $doctrine, $level = Logger::DEBUG, bool $bubble = true)
    {
        parent::__construct($level, $bubble);
        $this->doctrine = $doctrine;
    }

    /**
     * @phpstan-param array FormattedRecord $record
     */
    protected function write(array $record): void
    {
        $context = &$record['context'];

        $log = new Entity();
        $log->setName($context['objectName']);
        $log->setChangeSet($context['changeSet']);
        $log->setCreatedAt($record['datetime']);

        foreach ($context['relatedIds'] as $objectName => $uid)
        {
            $logRelation = new EntityRelation();
            $logRelation->setRelated($objectName . ':' . $uid);
            $log->addRelation($logRelation);

            $this->doctrine->getManager()->persist($logRelation);
        }

        $this->doctrine->getManager()->persist($log);
        $this->doctrine->getManager()->flush();
    }
}
