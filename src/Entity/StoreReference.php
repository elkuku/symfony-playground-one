<?php

namespace App\Entity;

use App\Repository\StoreReferenceRepository;
use App\Service\UploaderHelper;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\NotBlank;

#[ORM\Entity(repositoryClass: StoreReferenceRepository::class)]
class StoreReference
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups('main')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Store::class, inversedBy: 'storeReferences')]
    #[ORM\JoinColumn(nullable: false)]
    private Store $store;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups('main')]
    private $filename;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['main', 'input'])]
// * @Assert\NotBlank()
// * @Assert\Length(max=100)
    #[NotBlank()]
    private $originalFilename;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups('main')]
    private $mimeType;

    #[ORM\Column(type: 'integer')]
    private int $position = 0;

    public function __construct(Store $store)
    {
        $this->store = $store;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStore(): ?Store
    {
        return $this->store;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }

    public function getOriginalFilename(): ?string
    {
        return $this->originalFilename;
    }

    public function setOriginalFilename(string $originalFilename): self
    {
        $this->originalFilename = $originalFilename;

        return $this;
    }

    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    public function setMimeType(string $mimeType): self
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    public function getFilePath(): string
    {
        return UploaderHelper::STORE_REFERENCE.'/'.$this->getFilename();
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }
}
