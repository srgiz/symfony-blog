<?php
declare(strict_types=1);

namespace App\Logger\Diff;

use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;

class DiffSubscriber implements EventSubscriberInterface
{
    private RemoveWatcherInterface $removeWatcher;

    private DiffLoggerInterface $logger;

    public function __construct(RemoveWatcherInterface $removeWatcher, DiffLoggerInterface $logger)
    {
        $this->removeWatcher = $removeWatcher;
        $this->logger = $logger;
    }

    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $this->log(DiffEvents::UPDATE, $args->getObject(), $args->getEntityChangeSet());
    }

    public function preRemove(LifecycleEventArgs $args): void
    {
        $this->removeWatcher->setPrimaryKeys($args->getObjectManager(), $args->getObject());
    }

    public function postRemove(LifecycleEventArgs $args): void
    {
        $object = $args->getObject();
        $primaryKeys = $this->removeWatcher->getPrimaryKeys($object);
        $reflectionClass = $args->getObjectManager()->getClassMetadata($object::class)->getReflectionClass();
        $changeSet = [];

        foreach ($primaryKeys as $key => $value)
        {
            $property = $reflectionClass->getProperty($key);
            $property->setAccessible(true);
            $property->setValue($object, $value);
            $changeSet[$key][0] = $value;
        }

        $args->getEntityManager()->getUnitOfWork()->computeChangeSet($args->getEntityManager()->getClassMetadata($object::class), $object);
        $computeChangeSet = $args->getEntityManager()->getUnitOfWork()->getEntityChangeSet($object);
        $args->getEntityManager()->getUnitOfWork()->clearEntityChangeSet(spl_object_hash($object));

        foreach ($computeChangeSet ?? [] as $field => $values)
        {
            if (isset($values[1]))
            {
                $changeSet[$field][0] = $values[1];
            }
        }

        $this->log(DiffEvents::DELETE, $object, $changeSet);
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

        $this->log(DiffEvents::CREATE, $object, $changeSet);
    }

    private function log(string $event, object $object, array $changeSet): void
    {
        $this->logger->log($event, $object, $changeSet);
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
