<?php

declare(strict_types=1);

namespace App\AlgebraicAlgorithms;

/*
 * Для запуска в консоли из папки с файлом набрать php NumberInPow.php
 */
class NumberInPow
{
    public static function executeIterative(float $x, int $pow): float
    {
        if ($pow === 0) {
            return 1;
        }
        if ($pow < 0) {
            return 1 / self::executeIterative($x, -$pow);
        }

        if ($pow % 2 === 0) {
            $r = (float) self::executeIterative($x, $pow / 2);
            return $r * $r;
        } else {
            $pow = $pow / 2;
            $r = (float) self::executeIterative($x, (int) $pow);
            return $r * $r * $x;
        }
    }

    public static function executeBinary(float $a, int $pow): float
    {
        if ($pow < 0) {
            return 1 / self::executeBinary($a, -$pow);
        }

        $result = 1.0;  // Начальный результат
        $base = $a;      // Текущее основание (будет возводиться в квадрат)
        $exponent = $pow; // Текущая степень

        while ($exponent > 0) {
            // Шаг A: Проверяем младший бит
            if ($exponent & 1) {
                $result *= $base;  // Умножаем результат на текущее основание
            }
            // Шаг B: Возводим основание в квадрат
            $base *= $base;
            // Шаг C: Сдвигаем степень вправо (делим на 2)
            $exponent >>= 1;
        }

        return $result;
    }
}

if (php_sapi_name() === 'cli' && basename(__FILE__) === 'NumberInPow.php') {
    $cases = [
        [2, 56, '7.2057594037928E+16'],
        [5.0, 0, '1.0'],
        [2.0, 3, '8.0'],
        [1.5, 2, '2.25'],
        [0.0, 5, '0.0'],
        [0.0, 0, '1.0'], // or 0.0 depending on implementatio
        [-2.0, 4, '16.0'],
        [-3.0, 3, '-27.0'],
        [1.1, 10, '2.5937424601'],
        [1.0001, 10000, '2.7181459268244'], // approximates
        [0.0001, 2, '1.0E-8'],
        [1000.0, 3, '1000000000.0'],
        [1.0, 100, '1.0'],
        [-1.0, 100, '1.0'],
        [-1.0, 101, '-1.0'],
        [2.0, -2, '0.25'],
        [-1.0, -2, '1.0'],
        [-2.0, -2, '0.25'],
        [-2.0, -3, '-0.125'],
    ];

    $start = microtime(true);
    echo PHP_EOL;
    echo 'Итеративное возведение в степень' . PHP_EOL;
    echo str_repeat('-', 50) . PHP_EOL;
    foreach ($cases as $case) {
        $result = NumberInPow::executeIterative($case[0], $case[1]);
        $text = ((string) $result == $case[2])
            ? "🚀 Тест пройден. Расчет для $case[0] ** $case[1] = $case[2]"
            : "⚠️ Ошибка, {$result} !== {$case[2]}";
        echo PHP_EOL . $text;
    }
    echo PHP_EOL . str_repeat('-', 50) . PHP_EOL;
    echo PHP_EOL;
    $iterative = microtime(true) - $start;

    $start = microtime(true);
    echo 'Возведение в степень. Бинарный алгоритм' . PHP_EOL;
    echo str_repeat('-', 50) . PHP_EOL;
    foreach ($cases as $case) {
        $result = NumberInPow::executeBinary($case[0], $case[1]);
        $text = ((string) $result == $case[2])
            ? "🚀 Тест пройден. Расчет для $case[0] ** $case[1] = $case[2]"
            : "⚠️ Ошибка, {$result} !== {$case[2]}";
        echo PHP_EOL . $text;
    }
    echo PHP_EOL . str_repeat('-', 50) . PHP_EOL;
    echo PHP_EOL;
    $binary = microtime(true) - $start;

    echo "Итеративный алгоритм: $iterative, Бинарный алгоритм: $binary" . PHP_EOL;
    echo PHP_EOL . str_repeat('-', 50) . PHP_EOL;
}
