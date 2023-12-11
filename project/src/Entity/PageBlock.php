<?php

namespace App\Entity;

use App\Enum\MediaCache;
use App\Enum\PageBlockType;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\ArrayShape;
use App\Entity\Feature\HasMediaTrait;
use App\Repository\PageBlockRepository;
use App\Entity\Feature\HasMediaInterface;
use App\Entity\Feature\CacheMediaPathTrait;
use Doctrine\Common\Collections\Collection;
use App\Entity\Feature\ExplodeDescriptionTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Entity\Feature\ExplodeDescriptionInterface;
use Doctrine\Common\Collections\ReadableCollection;
use Symfony\Component\Serializer\Annotation\SerializedName;

#[ORM\Entity(repositoryClass: PageBlockRepository::class)]
class PageBlock implements HasMediaInterface, ExplodeDescriptionInterface
{
    use HasMediaTrait, CacheMediaPathTrait, ExplodeDescriptionTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['base'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $title = null;

    #[Groups(['base'])]
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[Groups(['base'])]
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $html = null;

    #[ORM\ManyToOne(cascade: ['persist'])]
    private ?Media $image = null;

    #[ORM\OrderBy(['sort' => 'ASC'])]
    #[ORM\OneToMany(mappedBy: 'pageBlock', targetEntity: Gallery::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $gallery;

    #[Groups(['base'])]
    #[ORM\Column(length: 255)]
    private string $layout;

    #[Groups(['base'])]
    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $sort = null;

    #[ArrayShape([
        'Обязательный' => 'string',
        'Цитата' => 'string',
        'Описание + Картинка' => 'string',
        'Заголовок + Описание + Картинка' => 'string',
        'Заголовок + HTML + Галлерея' => 'string',
    ])]
    public static function getAvailableType(): array
    {
        return [
            'Обязательный' => PageBlockType::REQUIRED->value,
            'Цитата' => PageBlockType::QUOTE->value,
            'Описание + Картинка' => PageBlockType::DESCRIPTION_IMAGE->value,
            'Заголовок + Описание + Картинка' => PageBlockType::TITLE_DESCRIPTION_IMAGE->value,
            'Заголовок + HTML + Галлерея' => PageBlockType::TITLE_HTML_GALLERY->value,
        ];
    }

    public function __construct()
    {
        $this->gallery = new ArrayCollection();
    }

    public function __toString(): string
    {
        $data = array_flip(self::getAvailableType());

        return $data[$this->layout] ?? 'новый';
    }

    #[Groups(['base'])]
    #[SerializedName('gallery')]
    public function getGalleryData(): ?ReadableCollection
    {
        $galleryCollection = $this->getGallery()->map(fn(Gallery $gallery) => $this->getMediaData($gallery->getImage()));

        return $galleryCollection->count() ? $galleryCollection : null;
    }

    #[Groups(['base'])]
    #[SerializedName('image')]
    public function getImageData(): ?array
    {
        $image = $this->getImage();

        return $image ? $this->getMediaData($image) : null;
    }

    private function getMediaData(Media $media): array
    {
        $path = $this->getMediaPath($media);

        return [
            'img' => $path,
            'webp' => "$path.webp",
        ];
    }

    private function getMediaPath(Media $media): string
    {
        $relativePath = MediaCache::MediaCacheFolder->value . MediaCache::MediumImageFilter->value . MediaCache::UploadMediaFolder->value;
        $absolutePath = MediaCache::Domain->value . $relativePath;

        return $absolutePath . $media->getImageName();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getHtml(): ?string
    {
        return $this->html;
    }

    public function setHtml(?string $html): static
    {
        $this->html = $html;

        return $this;
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

    /**
     * @return Collection<int, Gallery>
     */
    public function getGallery(): Collection
    {
        return $this->gallery;
    }

    public function addGallery(Gallery $gallery): static
    {
        if (!$this->gallery->contains($gallery)) {
            $this->gallery->add($gallery);
            $gallery->setPageBlock($this);
        }

        return $this;
    }

    public function removeGallery(Gallery $gallery): static
    {
        if ($this->gallery->removeElement($gallery)) {
            // set the owning side to null (unless already changed)
            if ($gallery->getPageBlock() === $this) {
                $gallery->setPageBlock(null);
            }
        }

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

    public function getLayout(): string
    {
        return $this->layout;
    }

    public function setLayout(string $layout): static
    {
        $this->layout = $layout;

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
}
