<?php

namespace App\Service;

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
        $slugs = $this->loadSlugs();
        $pealimRepository = $this->entityManager->getRepository(PealimVocabulary::class);
        foreach ($slugs as $slug) {
            $vocabularies = $pealimRepository->findBySlug($slug);
            $row++;
            foreach ($vocabularies as $vocabulary) {
                /** @var PealimVocabulary $vocabulary */
                $position = $this->chooseColumn($vocabulary);
                $words[$row][$position] = sprintf("%s\n%s", $vocabulary->getWord(), $vocabulary->getTranscription());
            }
            $words[$row][1] = $vocabulary->getSlug();
            $words[$row][2] = $vocabulary->getForm();
            $words[$row][3] = $vocabulary->getRoot();
            $words[$row][5] = $vocabulary->getRussian();
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

        $coefMasculine = $vocabulary->isMasculine() ? 2 : 0;
        $coefPlural = $vocabulary->isPlural() ? 1 : 0;
        $coefMascPlur = $coefMasculine + $coefPlural;
        $slideTime = 0;
        $n = 0;
        switch (true) {
            case $vocabulary->getTime() == Verb::INFINITIVE:
                $n = 4;
                break;
            case $vocabulary->getTime() == Verb::TIME_PRESENT:
                $slideTime = 6;
                break;
            case $vocabulary->getTime() == Verb::TIME_PAST && $vocabulary->getPerson() == 1:
                $slideTime = 10;
                break;
            case $vocabulary->getTime() == Verb::TIME_PAST && $vocabulary->getPerson() == 2:
                $slideTime = 14;
                break;
            case $vocabulary->getTime() == Verb::TIME_PAST && $vocabulary->getPerson() == 3:
                $slideTime = 18;
                break;
            case $vocabulary->getTime() == Verb::TIME_FUTURE && $vocabulary->getPerson() == 1:
                $slideTime = 22;
                break;
            case $vocabulary->getTime() == Verb::TIME_FUTURE && $vocabulary->getPerson() == 2:
                $slideTime = 26;
                break;
            case $vocabulary->getTime() == Verb::TIME_FUTURE && $vocabulary->getPerson() == 3:
                $slideTime = 30;
                break;
        }

        return $n + $slideTime + $coefMascPlur;
    }

    private function loadSlugs()
    {
        $con = $this->entityManager->getConnection();
//        $sql = "SELECT * FROM messenger_messages WHERE queue_name = :queue_name";
        $sql = "select p.slug from pealim_vocabulary p GROUP BY p.slug;";
        $stmt = $con->prepare($sql);
//        $resultSet = $stmt->executeQuery(['queue_name' => 'competition_333']);
        $resultSet = $stmt->executeQuery();

        return $resultSet->fetchAllAssociative();
    }
}