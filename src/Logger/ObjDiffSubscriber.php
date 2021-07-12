<?php
declare(strict_types=1);

namespace App\Logger;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;

class ObjDiffSubscriber implements EventSubscriber
{
    private ObjDiffLoggerInterface $logger;

    public function __construct(ObjDiffLoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $object = $args->getObject();

        if (!$this->toLog($object))
            return;

        $this->log($object, $args->getEntityChangeSet());
    }

    public function postPersist(LifecycleEventArgs $args): void
    {
        $object = $args->getObject();

        if (!$this->toLog($object))
            return;

        $data = $args->getEntityManager()->getUnitOfWork()->getOriginalEntityData($object);
        $changeSet = [];

        foreach ($data as $key => $value)
        {
            $changeSet[$key][1] = $value;
        }

        $this->log($object, $changeSet);
    }

    private function log(object $object, array $changeSet): void
    {
        $this->logger->log($object, $changeSet);
    }

    private function toLog(object $object): bool
    {
        $attributes = (new \ReflectionClass($object))->getAttributes(ObjDiffLogAttr::class);
        return !empty($attributes);
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::preUpdate,
            Events::postPersist,
        ];
    }
}
