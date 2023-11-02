<?php

namespace App\Entity\Feature;

use Doctrine\ORM\Mapping as ORM;

trait PositionTrait
{
    #[ORM\Column(nullable: true)]
    private ?int $position = null;

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): static
    {
        $this->position = $position;

        return $this;
    }
}