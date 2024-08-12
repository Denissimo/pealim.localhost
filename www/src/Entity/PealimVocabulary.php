<?php

namespace App\Entity;

use App\Repository\PealimVocabularyRepository;
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
}
