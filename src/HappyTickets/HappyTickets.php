<?php

declare(strict_types=1);

namespace App\HappyTickets;

/*
 * Для запуска в консоли из папки с файлом набрать php HappyTickets.php
 */
class HappyTickets
{
    public static function execute(int $base): int
    {
        // Define Zero Case for given base
        $zeroCase = self::defineZeroCase($base);

        // Create DP Matrix and fill it
        $matrix = self::createDPMatrix($zeroCase);

        // Perform final calculation. Return results
        return self::calculateResults($matrix);
    }

    private static function defineZeroCase(int $base): array
    {
        if ($base === 1) {
            return array_pad([1], $base * 9 + 1, 0);
        }

        $digits = $base - 1;
        $maxSum = $digits * 9;
        $resultLength = $base * 9 + 1;

        $prev = array_fill(0, $maxSum + 1, 0);
        $prev[0] = 1;

        for ($pos = 1; $pos <= $digits; $pos++) {
            $curr = array_fill(0, $maxSum + 1, 0);
            for ($sum = 0; $sum <= $maxSum; $sum++) {
                if ($prev[$sum] === 0) {
                    continue;
                }
                for ($d = 0; $d <= 9; $d++) {
                    $nextSum = $sum + $d;
                    if ($nextSum <= $maxSum) {
                        $curr[$nextSum] += $prev[$sum];
                    }
                }
            }
            $prev = $curr;
        }

        return array_pad($prev, $resultLength, 0);
    }

    private static function createDPMatrix(array $zeroCase): array
    {
        $matrix = [];
        $matrix[] = $zeroCase;
        for ($i = 1; $i <= 9; $i++) {
            array_pop($zeroCase);
            array_unshift($zeroCase, 0);
            $matrix[] = $zeroCase;
        }

        return $matrix;
    }

    public static function calculateResults(array $matrix): int
    {
        $count = 0;
        $size = count($matrix[0]);
        for ($x = 0; $x < $size; $x++) {
            $columnSum = 0;
            for ($y = 0; $y <= 9; $y++) {
                $columnSum += $matrix[$y][$x];
            }

            $count += pow($columnSum, 2);
        }

        return $count;
    }
}


if (php_sapi_name() === 'cli' && basename(__FILE__) === 'HappyTickets.php') {
    $cases = [
        [1, 10],
        [2, 670],
        [3, 55252],
        [4, 4816030],
        [5, 432457640],
        [6, 39581170420],
        [7, 3671331273480],
        [8, 343900019857310],
        [9, 32458256583753952],
        [10, 3081918923741896840],
    ];

    foreach ($cases as $case) {
        $result = HappyTickets::execute($case[0]);
    var_dump($result);
    var_dump($case[1]);
        echo PHP_EOL . 'Расчет для ' . $case[0] * 2 . ' значных значений' . PHP_EOL;
        echo PHP_EOL . ($result === $case[1])
            ? 'Тест пройден. Возможное количество комбинаций ' . $case[1]
            : "Ошибка, {$result} !== {$case[1]}";
    }

    echo PHP_EOL;
}
