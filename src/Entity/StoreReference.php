<?php

namespace App\Entity;

use App\Repository\StoreReferenceRepository;
use App\Service\UploaderHelper;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StoreReferenceRepository::class)]
class StoreReference
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Store::class, inversedBy: 'storeReferences')]
    #[ORM\JoinColumn(nullable: false)]
    private Store $store;

    #[ORM\Column(type: 'string', length: 255)]
    private $filename;

    #[ORM\Column(type: 'string', length: 255)]
    private $originalFilename;

    #[ORM\Column(type: 'string', length: 255)]
    private $mimeType;

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

    // public function setStore(?Store $store): self
    // {
    //     $this->store = $store;
    //
    //     return $this;
    // }

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
}
