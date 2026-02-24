<?php

namespace App\AlgebraicAlgorithms;

/*
 * Для запуска в консоли из папки с файлом набрать php GreatestCommonDivisor.php
 */
class GreatestCommonDivisor
{
    public static function withSimpleRecursion(int|string $a, int|string $b): string
    {
        if (bccomp($a, '0') === 0) {
            return $b;
        }
        if (bccomp($b, '0') === 0) {
            return $a;
        }
        if (bccomp($a, $b) === 0) {
            return $a;
        }

        return bccomp($a, $b) === 1
            ? self::withSimpleRecursion(bcmod($a, $b), $b)
            : self::withSimpleRecursion(bcmod($b, $a), $a);
    }

    public static function withStein(int|string $a, int|string $b): string
    {
        if (bccomp($a, '0') === 0 || bccomp($b, '0') === 0) {
            return '1';
        }
        if (bccomp($a, $b) === 0) {
            return $a;
        }

        if (bcmod($a, '2') === '0' && bcmod($b, '2') === '0') {
            $a = bcdiv($a, '2');
            $b = bcdiv($b, '2');

            $result = (string) self::withStein($a, $b);
            return bcmul('2', $result);
        }
        if (bcmod($a, '2') !== '0' && bcmod($b, '2') === '0') {
            $b = bcdiv($b, '2');

            return self::withStein($a, $b);
        }
        if (bcmod($a, '2') === '0' && bcmod($b, '2') !== '0') {
            $a = bcdiv($a, '2');

            return self::withStein($a, $b);
        }
        if (bcmod($a, '2') !== '0' && bcmod($b, '2') !== '0') {
            if (bccomp($a, $b) === 1) {
                $a = bcdiv(bcsub($a, $b), '2');
            } else {
                $b = bcdiv(bcsub($b, $a), '2');
            }

            return self::withStein($a, $b);
        }

        return '1';
    }
}

if (php_sapi_name() === 'cli' && basename(__FILE__) === 'GreatestCommonDivisor.php') {
    $cases = [
        [20,30,10],
        [100,1,1],
        [123456789,2,1],
        [1234567890,12,6],
        [9876543210,123456789,9],
        [1073676287,997,1],
        [
            '1298074214633706835075030044377087',
            '8564657687654654657',
            1
        ],
        [
            '123426017006182806728593424683999798008235734137469123231828679',
            '162259276829213363391578010288127',
            1
        ],
        [
            '123426017006182806728593424683999798008235734137469123231828678',
            '162259276829213363391578010288126',
            6
        ],
        [
            '30414093201713378043612608166064768844377641568960512000000000000',
            '608281864034267560872252163321295376887552831379210240000000000',
            '608281864034267560872252163321295376887552831379210240000000000'
        ],
    ];

    echo PHP_EOL;
    echo 'Простая рекурсия' . PHP_EOL;
    echo str_repeat('-', 50) . PHP_EOL;
    foreach ($cases as $case) {
        $result = GreatestCommonDivisor::withSimpleRecursion($case[0], $case[1]);
        if ($result != $case[2]) {
            echo '⚠️ Ошибка: ' . $result . ' != '. $case[2] . PHP_EOL;
        } else {
            echo "🚀 Тест пройден" . PHP_EOL;
        }
    }

    echo PHP_EOL;
    echo 'Алгоритм Штейна' . PHP_EOL;
    echo str_repeat('-', 50) . PHP_EOL;
    foreach ($cases as $case) {
        $result = GreatestCommonDivisor::withStein($case[0], $case[1]);
        if ($result != $case[2]) {
            echo '⚠️ Ошибка: ' . $result . ' != '. $case[2] . PHP_EOL;
        } else {
            echo "🚀 Тест пройден" . PHP_EOL;
        }
    }
}
