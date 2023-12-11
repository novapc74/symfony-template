<?php

namespace App\EventSubscriber;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\ConsoleEvents;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ConsoleCommandEventSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ConsoleEvents::COMMAND => 'onCommand',
        ];
    }

    /**
     * @throws Exception
     */
    public function onCommand(ConsoleCommandEvent $event): void
    {
        if ($event->getCommand()->getName() === 'doctrine:fixtures:load') {
            $this->runCustomTruncate();
        }
    }

    /**
     * @throws Exception
     */
    private function runCustomTruncate(): void
    {
        $connection = $this->entityManager->getConnection();

        $connection->executeStatement('SET FOREIGN_KEY_CHECKS = 0');

        $purger = new ORMPurger($this->entityManager);
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_DELETE);
        $purger->purge();

        $connection->executeStatement('SET FOREIGN_KEY_CHECKS = 1');
    }
}