<?php

namespace App\Entity;

use App\Repository\PealimVocabularyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\UX\Turbo\Attribute\Broadcast;

#[ORM\Entity(repositoryClass: PealimVocabularyRepository::class)]
#[Broadcast]
class PealimVocabulary
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 64)]
    private ?string $slug = null;

    #[ORM\Column(length: 255)]
    private ?string $word = null;

    #[ORM\Column(length: 255)]
    private ?string $transcription = null;

    #[ORM\Column(length: 255)]
    private ?string $russian = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $root = null;

    #[ORM\Column(length: 32)]
    private ?string $speechPart = null;

    #[ORM\Column(length: 16)]
    private ?string $form = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isMasculine = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isPlural = null;

    #[ORM\Column(length: 16, nullable: true)]
    private ?string $time = null;

    #[ORM\Column(nullable: true)]
    private ?int $person = null;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'children')]
    private ?self $parent = null;

    /**
     * @var Collection<int, self>
     */
    #[ORM\OneToMany(targetEntity: self::class, mappedBy: 'parent')]
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

    public function getWord(): ?string
    {
        return $this->word;
    }

    public function setWord(string $word): static
    {
        $this->word = $word;

        return $this;
    }

    public function getSpeechPart(): ?string
    {
        return $this->speechPart;
    }

    public function setSpeechPart(string $speechPart): static
    {
        $this->speechPart = $speechPart;

        return $this;
    }

    public function getForm(): ?string
    {
        return $this->form;
    }

    public function setForm(string $form): static
    {
        $this->form = $form;

        return $this;
    }

    public function isMasculine(): ?bool
    {
        return $this->isMasculine;
    }

    public function setMasculine(?bool $isMasculine): static
    {
        $this->isMasculine = $isMasculine;

        return $this;
    }

    public function isPlural(): ?bool
    {
        return $this->isPlural;
    }

    public function setPlural(?bool $isPlural): static
    {
        $this->isPlural = $isPlural;

        return $this;
    }

    public function getTime(): ?string
    {
        return $this->time;
    }

    public function setTime(?string $time): static
    {
        $this->time = $time;

        return $this;
    }

    public function getRussian(): ?string
    {
        return $this->russian;
    }

    public function setRussian(string $russian): static
    {
        $this->russian = $russian;

        return $this;
    }

    public function getPerson(): ?int
    {
        return $this->person;
    }

    public function setPerson(?int $person): static
    {
        $this->person = $person;

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

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): static
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function addChild(self $child): static
    {
        if (!$this->children->contains($child)) {
            $this->children->add($child);
            $child->setParent($this);
        }

        return $this;
    }

    public function removeChild(self $child): static
    {
        if ($this->children->removeElement($child)) {
            // set the owning side to null (unless already changed)
            if ($child->getParent() === $this) {
                $child->setParent(null);
            }
        }

        return $this;
    }

    public function getTranscription(): ?string
    {
        return $this->transcription;
    }

    public function setTranscription(string $transcription): static
    {
        $this->transcription = $transcription;

        return $this;
    }
}
