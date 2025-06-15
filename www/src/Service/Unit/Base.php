<?php

namespace App\Service\Unit;

use Symfony\Component\Validator\Constraints as Assert;

class Base
{
    private string $slug = '';
    private string $form = '';
    #[Assert\Choice(['Существительное', 'Глагол', 'Местоимение', 'Наречие', 'Прилагательное'])]
    private string $speechPart = '';
    private string $translation = '';
    private string $root = '';

    /**
     * @param string $slug
     */
    public function __construct(string $slug)
    {
        $this->slug = $slug;
    }


    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): Base
    {
        $this->slug = $slug;

        return $this;
    }

    public function getForm(): string
    {
        return $this->form;
    }

    public function setForm(string $form): Base
    {
        $this->form = $form;

        return $this;
    }

    public function getSpeechPart(): string
    {
        return $this->speechPart;
    }

    public function setSpeechPart(string $speechPart): Base
    {
        $this->speechPart = $speechPart;

        return $this;
    }

    public function getTranslation(): string
    {
        return $this->translation;
    }

    public function setTranslation(string $translation): Base
    {
        $this->translation = $translation;

        return $this;
    }

    public function getRoot(): string
    {
        return $this->root;
    }

    public function setRoot(string $root): Base
    {
        $this->root = $root;

        return $this;
    }
}