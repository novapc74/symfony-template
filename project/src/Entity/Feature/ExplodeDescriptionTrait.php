<?php

namespace App\Entity\Feature;

use Doctrine\ORM\Mapping as ORM;

#[ORM\HasLifecycleCallbacks]
trait ExplodeDescriptionTrait
{
    private int $fullDescriptionIndex = 1;
    private int $shortDescriptionIndex = 0;
    private ?string $shortDescription = null;
    private ?string $fullDescription = null;
    private string $delimiterDescription = '<-#%#->';

    public function getShortDescription(): string
    {
        return $this->explodeDescription($this->shortDescriptionIndex);
    }

    public function setShortDescription(?string $shortDescription): static
    {
        $this->shortDescription = $shortDescription;

        return $this;
    }

    public function getFullDescription(): string
    {
        return $this->explodeDescription($this->fullDescriptionIndex);
    }

    public function setFullDescription(?string $fullDescription): static
    {
        $this->fullDescription = $fullDescription;

        return $this;
    }

    private function explodeDescription(int $type): string
    {
        return explode($this->delimiterDescription, $this->getDescription())[$type];
    }

    private function resolveDescription(): void
    {
        $description = implode(
            $this->delimiterDescription,
            [$this->shortDescription ?? $this->getShortDescription(), $this->fullDescription ?? $this->getFullDescription()]
        );

        $this->setDescription($description);
    }

    #[ORM\PreFlush]
    #[ORM\PrePersist]
    public function preFlush(): void
    {
        $this->resolveDescription();
    }
}