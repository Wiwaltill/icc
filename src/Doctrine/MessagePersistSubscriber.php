<?php

namespace App\Doctrine;

use App\Entity\Message;
use App\Event\MessageCreatedEvent;
use App\Event\MessageUpdatedEvent;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * This listener listens for new messages being created and
 * dispatches an event into the EventDispatcher
 */
class MessagePersistSubscriber implements EventSubscriber {

    private EventDispatcherInterface $dispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher) {
        $this->dispatcher = $eventDispatcher;
    }

    public function postPersist(LifecycleEventArgs $eventArgs) {
        $entity = $eventArgs->getEntity();

        if($entity instanceof Message) {
            $this->dispatcher->dispatch(new MessageCreatedEvent($entity));
        }
    }

    /**
     * @inheritDoc
     */
    public function getSubscribedEvents(): array {
        return [
            Events::postPersist
        ];
    }
}