<?php

declare(strict_types=1);

namespace App\BitArithmetic;

/*
 * Для запуска в консоли из папки с файлом набрать php GetOnes.php
 */
class GetOnes
{
    public static function simpleCalculation(int $x): int
    {
        $count = 0;
        $mask = 0x01;

        while ($mask > 0) {
            if (($x & $mask) > 0) {
                $count++;
            }
            $mask <<= 1;
        }

        return $count;
    }

    public static function algoritmKoornigan(int $x): int
    {
        $count = 0;

        while ($x > 0) {
            $count ++;
            $x &= ($x-1);
        }

        return $count;
    }

    public static function withPreCalculation(int $x): int
    {
        $count = 0;
        $data = [];
        for ($y = 0; $y < 16; $y++) {
            $data[$y] = self::simpleCalculation($y);
        }

        return $data[($x & 0xF000) >> 12] + $data[($x & 0x0F00) >> 8] + $data[($x & 0xF0) >> 4] + $data[$x & 0x0F];
    }
}

if (php_sapi_name() === 'cli' && basename(__FILE__) === 'GetOnes.php') {
    $cases = [
        [1, 1],
        [16, 1],
        [15, 4],
        [32767, 15],
        [0xAAAA, 8],
        [0b111101, 5],
    ];

    echo PHP_EOL;
    echo 'Простой расчет' . PHP_EOL;
    echo str_repeat('-', 50) . PHP_EOL;
    foreach ($cases as $case) {
        $result = GetOnes::simpleCalculation($case[0]);
        $text = ($result === $case[1])
            ? "🚀 Тест пройден."
            : "⚠️ Ошибка, {$result} !== {$case[1]} для {$case[0]}";
        echo PHP_EOL . $text;
    }
    echo PHP_EOL . str_repeat('-', 50) . PHP_EOL;
    echo PHP_EOL;

    echo 'Алгоритм Кернигана' . PHP_EOL;
    echo str_repeat('-', 50) . PHP_EOL;
    foreach ($cases as $case) {
        $result = GetOnes::algoritmKoornigan($case[0]);
        $text = ($result === $case[1])
            ? "🚀 Тест пройден."
            : "⚠️ Ошибка, {$result} !== {$case[1]} для {$case[0]}";
        echo PHP_EOL . $text;
    }
    echo PHP_EOL . str_repeat('-', 50) . PHP_EOL;
    echo PHP_EOL;

    echo 'С предварительной калькуляцией' . PHP_EOL;
    echo str_repeat('-', 50) . PHP_EOL;
    foreach ($cases as $case) {
        $result = GetOnes::withPreCalculation($case[0]);
        $text = ($result === $case[1])
            ? "🚀 Тест пройден."
            : "⚠️ Ошибка, {$result} !== {$case[1]} для {$case[0]}";
        echo PHP_EOL . $text;
    }
    echo PHP_EOL . str_repeat('-', 50) . PHP_EOL;
}
