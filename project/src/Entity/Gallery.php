<?php

namespace App\Entity;

use App\Entity\Feature\HasMediaInterface;
use App\Entity\Feature\HasMediaTrait;
use App\Repository\GalleryRepository;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: GalleryRepository::class)]
#[Vich\Uploadable]
class Gallery implements HasMediaInterface
{
    use HasMediaTrait;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Media::class, cascade: ['persist'])]
    private ?Media $image = null;

    #[ORM\Column(nullable: true)]
    private ?int $sort = 0;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function __toString(): string
    {
        return $this->type ?? $this->id;
    }

    public function getImage(): ?Media
    {
        return $this->image;
    }

    public function setImage(?Media $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getSort(): ?int
    {
        return $this->sort;
    }

    public function setSort(?int $sort): static
    {
        $this->sort = $sort;

        return $this;
    }

    public static function allMediaFields(): array
    {
        return ['image'];
    }

    public function getNewImage(): ?Media
    {
        return $this->image;
    }

    public function setNewImage(?Media $image): self
    {
        $this->uploadNewMedia($image, 'image');

        return $this;
    }
}
