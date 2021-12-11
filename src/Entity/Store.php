<?php

namespace App\Entity;

use App\Repository\StoreRepository;
use App\Service\UploaderHelper;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StoreRepository::class)]
class Store
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\ManyToMany(targetEntity: Tag::class, inversedBy: 'stores')]
    private $tags;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $imageFilename;

    #[ORM\OneToMany(mappedBy: 'store', targetEntity: StoreReference::class)]
    private $storeReferences;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
        $this->storeReferences = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|Tag[]
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        $this->tags->removeElement($tag);

        return $this;
    }

    public function getImageFilename(): ?string
    {
        return $this->imageFilename;
    }

    public function setImageFilename(?string $imageFilename): self
    {
        $this->imageFilename = $imageFilename;

        return $this;
    }

    public function getImagePath()
    {
        return UploaderHelper::STORE_IMAGES_PATH.'/'.$this->getImageFilename();
    }

    /**
     * @return Collection|StoreReference[]
     */
    public function getStoreReferences(): Collection
    {
        return $this->storeReferences;
    }

    // public function addStoreReference(StoreReference $storeReference): self
    // {
    //     if (!$this->storeReferences->contains($storeReference)) {
    //         $this->storeReferences[] = $storeReference;
    //         $storeReference->setStore($this);
    //     }
    //
    //     return $this;
    // }
    //
    // public function removeStoreReference(StoreReference $storeReference): self
    // {
    //     if ($this->storeReferences->removeElement($storeReference)) {
    //         // set the owning side to null (unless already changed)
    //         if ($storeReference->getStore() === $this) {
    //             $storeReference->setStore(null);
    //         }
    //     }
    //
    //     return $this;
    // }
}
