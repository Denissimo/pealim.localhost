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

    #[ORM\Column(length: 255)]
    private ?string $word = null;

    #[ORM\Column(length: 255)]
    private ?string $transcription = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isMasculine = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isPlural = null;

    #[ORM\Column(length: 16, nullable: true)]
    private ?string $time = null;

    #[ORM\Column(nullable: true)]
    private ?int $person = null;

    #[ORM\ManyToOne(inversedBy: 'children')]
    private ?PealimBase $pealimBase = null;


    public function getId(): ?int
    {
        return $this->id;
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

    public function getSlug(): ?string
    {
        return $this->pealimBase->getSlug();
    }

    public function setSlug(string $slug): static
    {
        $this->pealimBase->setSlug($slug);

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
        return  $this->pealimBase->getSpeechPart();
    }

    public function setSpeechPart(string $speechPart): static
    {
        $this->pealimBase->setSpeechPart($speechPart);

        return $this;
    }

    public function getForm(): ?string
    {
        return  $this->pealimBase->getForm();
    }

    public function setForm(string $form): static
    {
        $this->pealimBase->setForm($form);

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
        return $this->pealimBase->getTranslation();
    }

    public function setRussian(string $russian): static
    {
        $this->pealimBase->setTranslation($russian);

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
        return $this->pealimBase->getRoot();
    }

    public function setRoot(?string $root): static
    {
        $this->pealimBase->setRoot($root);

        return $this;
    }

    public function getPealimBase(): ?PealimBase
    {
        return $this->pealimBase;
    }

    public function setPealimBase(?PealimBase $pealimBase): static
    {
        $this->pealimBase = $pealimBase;

        return $this;
    }

}
