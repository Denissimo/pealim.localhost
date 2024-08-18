<?php

namespace App\Service\Unit;

use App\Entity\PealimVocabulary;

class Verb
{
    public const INFINITIVE = 'infinitive';
    public const IMPERATIVE = 'imperative';
    public const TIME_PRESENT = 'present';
    public const TIME_PAST = 'past';
    public const TIME_FUTURE = 'future';
    public static $proertiesRus = [
        'Единственное число' => 0,
        'Множественное число' => 1,
        'Мужской род' => 1,
        'Женский род' => 0,
        'Настоящее время / причастие' => 'present',
        'Прошедшее время' => 'past',
        'Будущее время' => 'future',
        'Повелительное наклонение' => self::IMPERATIVE,
        'Инфинитив' => self::INFINITIVE
    ];

    private static $masculine = [
        0 => false,
        1 => true,
        2 => false,
        3 => true,
        4 => false
    ];

    private static $plural = [
        0 => false,
        1 => false,
        2 => false,
        3 => true,
        4 => true
    ];

    private static $time = [
        0 => self::TIME_PRESENT,
        1 => self::TIME_PAST,
        2 => self::TIME_PAST,
        3 => self::TIME_PAST,
        4 => self::TIME_FUTURE,
        5 => self::TIME_FUTURE,
        6 => self::TIME_FUTURE,
        7 => self::IMPERATIVE,
        8 => self::INFINITIVE,
    ];

    private static $person = [
        0 => null,
        1 => 1,
        2 => 2,
        3 => 3,
        4 => 1,
        5 => 2,
        6 => 3,
        7 => null,
        8 => null,
    ];

    public static $timeShift = [
        self::INFINITIVE => ['shift' => 0, 'rus' => 'Инфинитив'],
        self::TIME_PRESENT => ['shift' => 2, 'rus' => 'Настоящее'],
        self::TIME_PAST => ['shift' => 6, 'rus' => 'Прошедщее'],
        self::TIME_FUTURE => ['shift' => 15, 'rus' => 'Будущее'],
        self::IMPERATIVE => ['shift' => 25, 'rus' => 'Повелительное'],
    ];


    public static $positionShift = [
        0 => [
            0 => [
                0 => ['shift' => 1, 'rus' => 'ты (ж)', 'heb' => 'אַתְּ'],
                1 => ['shift' => 0, 'rus' => 'ты (м)', 'heb' => 'אַתָהּ'],
            ],
            1 => [
                0 => ['shift' => 3, 'rus' => 'вы (ж)', 'heb' => 'אַתֶן'],
                1 => ['shift' => 2, 'rus' => 'вы (м)', 'heb' => 'אַתֶם'],
            ],
        ],
        1 => [
            0 => [
                0 => ['shift' => 0, 'rus' => 'я (ж)', 'heb' => 'אֲנִי'],
                1 => ['shift' => 0, 'rus' => 'я (м)', 'heb' => 'אֲנִי'],
            ],
            1 => [
                0 => ['shift' => 1, 'rus' => 'мы (ж)', 'heb' => 'אֲנַחנוּ'],
                1 => ['shift' => 1, 'rus' => 'мы (м)', 'heb' => 'אֲנַחנוּ'],
            ],
        ],
        2 => [
            0 => [
                0 => ['shift' => 3, 'rus' => 'ты (ж)', 'heb' => 'אַתְּ'],
                1 => ['shift' => 2, 'rus' => 'ты (м)', 'heb' => 'אַתָהּ'],
            ],
            1 => [
                0 => ['shift' => 5, 'rus' => 'вы (ж)', 'heb' => 'אַתֶן'],
                1 => ['shift' => 4, 'rus' => 'вы (м)', 'heb' => 'אַתֶם'],
            ],
        ],
        3 => [
            0 => [
                0 => ['shift' => 7, 'rus' => 'она (ж)', 'heb' => 'הִיא'],
                1 => ['shift' => 6, 'rus' => 'он (м)', 'heb' => 'הוּא'],
            ],
            1 => [
                0 => ['shift' => 9, 'rus' => 'они (ж)', 'heb' => 'הֵן'],
                1 => ['shift' => 8, 'rus' => 'они (м)', 'heb' => 'הֵם'],
            ],
        ],
    ];

    public static function isMasculine(int $x): bool
    {
        return self::$masculine[$x];
    }

    public static function isPlural(TableCell $cell): bool
    {
        $shift = $cell->getColspan() + $cell->getX() -1;

        return self::$plural[$shift];
    }

    public static function getTime(int $y): string
    {
        return self::$time[$y];
    }

    public static function getPerson(int $y): ?int
    {
        return self::$person[$y];
    }
}