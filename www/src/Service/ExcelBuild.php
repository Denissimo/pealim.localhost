<?php

namespace App\Service;

use App\Entity\PealimBase;
use App\Entity\PealimVocabulary;
use App\Service\Unit\Verb;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use \PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class ExcelBuild
{
    private const POS_START = 5;
    private EntityManagerInterface $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildWordMatrix(): array
    {
        $words = [];
        $row = 0;
        $pealimBases = $this->entityManager->getRepository(PealimBase::class)->findAll();

        foreach ($pealimBases as $pealimBase) {
            $vocabularies = $pealimBase->getChildren();
            $row++;
            $positionStart = 5;
            foreach ($vocabularies as $vocabulary) {
                /** @var PealimVocabulary $vocabulary */
                $position = $this->chooseColumn($vocabulary);
//                $position++;
                $positionShift = $positionStart + $position;
                $words[$row][$positionShift] = sprintf("%s\n%s", $vocabulary->getWord(), $vocabulary->getTranscription());
            }
            $words[$row][1] = $pealimBase->getSlug();
            $words[$row][2] = $pealimBase->getForm();
            $words[$row][3] = $pealimBase->getRoot();
            $words[$row][4] = $pealimBase->getTranslation();
        }

        return $words;
    }

    public function generate()
    {
        $wordMatrix = $this->buildWordMatrix();
        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();

        foreach ($wordMatrix as $row => $wordList) {
            foreach ($wordList as $column => $word) {
                $columnLetter = Coordinate::stringFromColumnIndex($column + 1);
                $cellName = $columnLetter . ($row + 1);
                $activeWorksheet->setCellValue($cellName, $word);
                $activeWorksheet->getStyle('A1')->getAlignment()->setWrapText(true);
            }
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save('verbs.xlsx');
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
}