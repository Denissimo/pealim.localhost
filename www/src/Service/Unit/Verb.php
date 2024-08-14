<?php

namespace App\Service\Unit;

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

    public static function isMasculine(int $x): bool
    {
        return self::$masculine[$x];
    }
    public static function isPlural(int $x): bool
    {
        return self::$plural[$x];
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