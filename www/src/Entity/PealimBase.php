<?php

namespace App\Entity;

use App\Repository\PealimBaseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PealimBaseRepository::class)]
class PealimBase
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 64)]
    private ?string $slug = null;

    #[ORM\Column(length: 255)]
    private ?string $translation = null;

    #[ORM\Column(length: 64, nullable: true)]
    private ?string $root = null;

    #[ORM\Column(length: 32)]
    private ?string $speech_part = null;

    #[ORM\Column(length: 32, nullable: true)]
    private ?string $form = null;

    /**
     * @var Collection<int, PealimVocabulary>
     */
    #[ORM\OneToMany(targetEntity: PealimVocabulary::class, mappedBy: 'pealimBase')]
    private Collection $children;

    public function __construct()
    {
        $this->children = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getTranslation(): ?string
    {
        return $this->translation;
    }

    public function setTranslation(string $translation): static
    {
        $this->translation = $translation;

        return $this;
    }

    public function getRoot(): ?string
    {
        return $this->root;
    }

    public function setRoot(?string $root): static
    {
        $this->root = $root;

        return $this;
    }

    public function getSpeechPart(): ?string
    {
        return $this->speech_part;
    }

    public function setSpeechPart(string $speech_part): static
    {
        $this->speech_part = $speech_part;

        return $this;
    }

    public function getForm(): ?string
    {
        return $this->form;
    }

    public function setForm(?string $form): static
    {
        $this->form = $form;

        return $this;
    }

    /**
     * @return Collection<int, PealimVocabulary>
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function addChild(PealimVocabulary $child): static
    {
        if (!$this->children->contains($child)) {
            $this->children->add($child);
            $child->setPealimBase($this);
        }

        return $this;
    }

    public function removeChild(PealimVocabulary $child): static
    {
        if ($this->children->removeElement($child)) {
            // set the owning side to null (unless already changed)
            if ($child->getPealimBase() === $this) {
                $child->setPealimBase(null);
            }
        }

        return $this;
    }
}
