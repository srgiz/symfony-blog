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
        $this->log(ObjDiffEventEnum::UPDATE, $args->getObject(), $args->getEntityChangeSet());
    }

    public function preRemove(LifecycleEventArgs $args): void
    {
        $this->log(ObjDiffEventEnum::DELETE, $args->getObject(), []);
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

        $this->log(ObjDiffEventEnum::CREATE, $object, $changeSet);
    }

    private function log(string $event, object $object, array $changeSet): void
    {
        $attribute = (new \ReflectionClass($object))->getAttributes(ObjDiffLogAttr::class)[0] ?? null;

        if (!$attribute)
            return;

        $this->logger->log($event, $object, $attribute->getArguments(), $changeSet);
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::preUpdate,
            Events::preRemove,
            Events::postPersist,
        ];
    }
}
