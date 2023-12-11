<?php

namespace App\Entity\Feature;

use Doctrine\ORM\Mapping as ORM;

#[ORM\HasLifecycleCallbacks]
trait ExplodeDescriptionTrait
{
    private int $shortDescriptionIndex = 0;
    private int $fullDescriptionIndex = 1;
    private ?string $shortDescription = null;
    private ?string $fullDescription = null;
    private string $delimiterDescription = '<-#%#->';

    public function getShortDescription(): ?string
    {
        return $this->explodeDescription($this->shortDescriptionIndex);
    }

    public function setShortDescription(?string $shortDescription): static
    {
        $this->shortDescription = $shortDescription;

        return $this;
    }

    public function getFullDescription(): ?string
    {
        return $this->explodeDescription($this->fullDescriptionIndex);
    }

    public function setFullDescription(?string $fullDescription): static
    {
        $this->fullDescription = $fullDescription;

        return $this;
    }

    private function explodeDescription(int $type): ?string
    {
        $description = explode($this->delimiterDescription, $this->getDescription());

        return count($description) > 1 ? $description[$type] : null;
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