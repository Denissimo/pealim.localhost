<?php

namespace App\Service\Unit;

class Word
{
    private string $hebrew;

    private string $transcription;

    public function __construct(string $content)
    {
        $contentReplaced = preg_replace(['/<b>/', '/<\/b>/'], ['[', ']'], $content);
        $hebrewTpl = '/[\\x{0590}-\\x{05FF}]+/Uu';
        $hebrewTpl = '/<span class="menukad">(.+)<\/span>/Uu';
        $transTpl = '/<div class="transcription">([^<]*)<\/div>/Uu';
        preg_match_all($hebrewTpl, $contentReplaced, $hebrew);
        preg_match_all($transTpl, $contentReplaced, $trans);
//        $this->hebrew = implode('', $hebrew[0]);
        $this->hebrew = $hebrew[1][0] ?? '';
        $this->transcription = $trans[1][0] ?? '';
    }

    public function getHebrew(): string
    {
        return $this->hebrew;
    }

    public function getTranscription(): string
    {
        return $this->transcription;
    }
}