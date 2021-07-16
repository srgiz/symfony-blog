<?php
declare(strict_types=1);

namespace App\Logger\Diff;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Psr\Log\LogLevel;

class DiffSubscriber implements EventSubscriber
{
    private DiffLoggerInterface $logger;

    public function __construct(DiffLoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $this->log(DiffEventEnum::UPDATE, $args->getObject(), $args->getEntityChangeSet());
    }

    public function preRemove(LifecycleEventArgs $args): void
    {
        $this->logger->watch($args->getObject());
    }

    public function postRemove(LifecycleEventArgs $args): void
    {
        $this->log(DiffEventEnum::DELETE, $args->getObject(), []);
    }

    public function postPersist(LifecycleEventArgs $args): void
    {
        $object = $args->getObject();

        $data = $args->getEntityManager()->getUnitOfWork()->getOriginalEntityData($object);
        $changeSet = [];

        foreach ($data as $key => $value)
        {
            $changeSet[$key][1] = $value;
        }

        $this->log(DiffEventEnum::CREATE, $object, $changeSet);
    }

    private function log(string $event, object $object, array $changeSet): void
    {
        $this->logger->log(LogLevel::INFO, $event, $object, $changeSet);
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::preUpdate,
            Events::preRemove,
            Events::postRemove,
            Events::postPersist,
        ];
    }
}
