<?php

namespace App\Service;

use App\Entity\PealimBase;
use App\Entity\PealimVocabulary;
use App\Repository\PealimBaseRepository;
use App\Service\Unit\Verb;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use \PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use \PhpOffice\PhpSpreadsheet\RichText\RichText;
use \PhpOffice\PhpSpreadsheet\Style\Color;
use \PhpOffice\PhpSpreadsheet\Style\Fill;

class ExcelBuild
{
    private const POS_START = 3;
    private const POS_FORM = 1;
    private EntityManagerInterface $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function loadStandart(): array
    {
        $words = [];
        $row = 1;

        $pealimVocs = $this->entityManager->createQueryBuilder()
            ->select('v')
            ->from(PealimVocabulary::class, 'v')
            ->where('v.time IN(:time)')
            ->setParameter('time', [Verb::INFINITIVE, Verb::TIME_PRESENT])
            ->addOrderBy('v.pealimBase','ASC')
            ->addOrderBy('v.isPlural','ASC')
            ->addOrderBy('v.time', 'DESC');

//            ->getRepository(PealimBaseRepository::class);

        $vocList = $pealimVocs->getQuery()->getResult();
        $verbs = [];
        /** @var PealimVocabulary $word */
        foreach ($vocList as $word) {
            $base = $word->getPealimBase();
            $current = [];
            $current['word'] = $word->getWord();
            $current['transcription'] = $word->getTranscription();
            $current['translation'] = $base->getTranslation();
            $gender = $word->isMasculine() ? 'м' : 'ж';
            $current['gender'] = $word->getTime() == 'infinitive' ? 'и' : $gender;

            $verbs[] = $current;
        }

        return $verbs;
    }

    private function formatCell(string $word): RichText
    {
        $richText = new RichText();
        preg_match_all('/([^\[]*)\[([^\]]*)\](.*)/', $word, $matches, PREG_SET_ORDER);
        if (count($matches) > 0) {
            $richText->createText($matches[0][1]);
            $payable = $richText->createTextRun($matches[0][2]);
            $payable->getFont()->setBold(true);
            $payable->getFont()->setColor(new Color(Color::COLOR_RED));
            $richText->createText($matches[0][3]);

        } else {
            $richText->createText($word);
        }

        return $richText;
    }

    public function generateStandart()
    {
        $wordMatrix = $this->loadStandart();

        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();

        foreach ($wordMatrix as $row => $wordData) {
            $rownumber = $row + 1;
            $cell = $spreadsheet->getActiveSheet()->getCell("C{$rownumber}");
            $richText = $this->formatCell($wordData['word']);
            $cell->setValue($richText);

            $cell = $spreadsheet->getActiveSheet()->getCell("D{$rownumber}");
            $richText = $this->formatCell($wordData['transcription']);
            $cell->setValue($richText);

            $cell = $spreadsheet->getActiveSheet()->getCell("E{$rownumber}");
            $richText = $this->formatCell($wordData['translation']);
            $cell->setValue($richText);

            $cell = $spreadsheet->getActiveSheet()->getCell("F{$rownumber}");
            $richText = $this->formatCell($wordData['gender']);
            $cell->setValue($richText);

        }

        $writer = new Xlsx($spreadsheet);
        $writer->save('public/vocabul.xlsx');
    }

    public function buildWordMatrix(): array
    {
        $words = [];
        $row = 1;

        $pealimBases = $this->entityManager->getRepository(PealimBase::class)->findAll();

        foreach ($pealimBases as $pealimBase) {
            $vocabularies = $pealimBase->getChildren();
            $row++;
            foreach ($vocabularies as $vocabulary) {
                /** @var PealimVocabulary $vocabulary */
                $position = $this->chooseColumn($vocabulary);
                $positionShift = self::POS_START + $position;
                $words[$row][$positionShift] = sprintf("%s\n%s", $vocabulary->getWord(), $vocabulary->getTranscription());
            }
            $words[$row][0] = $row - 1;
            $words[$row][self::POS_FORM] = $pealimBase->getForm();
            $words[$row][2] = $pealimBase->getRoot();
            $words[$row][3] = ['link' => $pealimBase->getSlug(), 'text' => $pealimBase->getTranslation()];
        }

        foreach ($vocabularies as $vocabulary) {
            /** @var PealimVocabulary $vocabulary */
            $position = $this->chooseColumn($vocabulary);
            $pronoun = $this->choosePronun($vocabulary);
//                $position++;
            $positionShift = self::POS_START + $position;
            $words[1][$positionShift] = $pronoun;
        }

        foreach (Verb::$timeShift as $timeShift) {
            $positionShift = self::POS_START + $timeShift['shift'];
            $words[0][$positionShift] = $timeShift['rus'];
        }

        return $words;
    }

    public function generateCommon()
    {
        $wordMatrix = $this->buildWordMatrix();
        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();

        foreach ($wordMatrix as $row => $wordList) {
            $rownumber = $row + 1;
            foreach ($wordList as $column => $word) {
                $columnLetter = Coordinate::stringFromColumnIndex($column + 1);
                $cellName = $columnLetter . ($rownumber);
                $richText = new RichText();
                if (is_array($word)) {
                    $richText->createText($word['text']);
                    $cell = $spreadsheet->getActiveSheet()->getCell($cellName);
                    $cell->setValue($richText);
                    $url = 'https://www.pealim.com' . $word['link'];
                    $cell->getHyperlink()->setUrl($url);
//                    $activeWorksheet->getStyle($cellName)->getAlignment()->setWrapText(true);
                    continue;
                }
                preg_match_all('/([^\[]*)\[([^\]]*)\](.*)/', $word, $matches, PREG_SET_ORDER);
                if (count($matches) > 0) {
                    $richText->createText($matches[0][1]);
                    $payable = $richText->createTextRun($matches[0][2]);
                    $payable->getFont()->setBold(true);
//                    $payable->getFont()->setItalic(true);
                    $payable->getFont()->setColor(new Color(Color::COLOR_RED));
                    $richText->createText($matches[0][3]);

                } else {
                    $richText->createText($word);
                }
                $spreadsheet->getActiveSheet()->getCell($cellName)->setValue($richText);
//                $activeWorksheet->setCellValue($cellName, $word);
                $activeWorksheet->getStyle($cellName)->getAlignment()->setWrapText(true);
            }
            if (isset($wordList[self::POS_FORM])) {
                $cellsDiiapasone = sprintf('A%d:D%d', $rownumber, $rownumber);
                $binyan = $wordList[self::POS_FORM];
                $color = Verb::getBinyanColor($binyan);

                $spreadsheet->getActiveSheet()
                    ->getStyle($cellsDiiapasone)
                    ->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setRGB($color);
            }
        }

//        foreach (Verb::$timeShift as $key => $time) {
//            $columnLetter = Coordinate::stringFromColumnIndex($time['shift'] + self::POS_START + 2);
//            $cellName = $columnLetter . '1';
//            $activeWorksheet->setCellValue($cellName, $time['rus']);
//            foreach (Verb::$positionShift as $person) {
//                foreach ($person as $plural) {
//                    foreach ($plural as $masculine) {
//                        $columnLetterPos = Coordinate::stringFromColumnIndex($time['shift'] + $masculine['shift'] + self::POS_START + 1);
//                        $cellNamePos = $columnLetterPos . '2';
//                        $activeWorksheet->setCellValue($cellNamePos, $masculine['heb']);
//                    }
//                }
//            }
//        }

        $writer = new Xlsx($spreadsheet);
        $writer->save('public/verbs0.xlsx');
    }

    private function chooseColumn(PealimVocabulary $vocabulary): int
    {
        $time = $vocabulary->getTime();
        $isPlural = (int)$vocabulary->isPlural();
        $isMasculine = (int)$vocabulary->isMasculine();
        $person = (int)$vocabulary->getPerson();
        $timeShift = Verb::$timeShift[$time]['shift'];
        $positionShift = Verb::$positionShift[$person][$isPlural][$isMasculine]['shift'];

        return $timeShift + $positionShift;
    }

    private function choosePronun(PealimVocabulary $vocabulary): string
    {
        $isPlural = (int)$vocabulary->isPlural();
        $isMasculine = (int)$vocabulary->isMasculine();
        $person = (int)$vocabulary->getPerson();
        $pronoun = Verb::$positionShift[$person][$isPlural][$isMasculine]['rus'] . "\n"
            . Verb::$positionShift[$person][$isPlural][$isMasculine]['heb'];

        return $pronoun;
    }
}