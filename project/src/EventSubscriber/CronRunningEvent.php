<?php

namespace App\EventSubscriber;

use Symfony\Component\Messenger\Event\WorkerRunningEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class ExtractFailedEvent
 * @package App\Event
 */
class CronRunningEvent implements EventSubscriberInterface
{
    # TODO https://dev.to/fadymr/use-symfony-messenger-without-supervisor-3cl6
    public function onWorkerRunning(WorkerRunningEvent $event): void
    {
        if ($event->isWorkerIdle()) {
            $event->getWorker()->stop();
        }
    }

    /**
     * @return array<string>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            WorkerRunningEvent::class => 'onWorkerRunning',
        ];
    }
}