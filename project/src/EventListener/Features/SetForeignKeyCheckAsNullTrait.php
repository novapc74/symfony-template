<?php

namespace App\EventListener\Features;

trait SetForeignKeyCheckAsNullTrait
{
    private function setForeignKeyChecksAsNull($args): void
    {
        $args
            ->getObjectManager()
            ->getConnection()
            ->executeStatement('SET FOREIGN_KEY_CHECKS = 0;');
    }
}