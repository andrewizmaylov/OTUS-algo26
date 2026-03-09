<?php

declare(strict_types=1);

namespace App\Sort;

/*
 * Для запуска в консоли из папки с файлом набрать php SortAlgo01.php
 */
class SortAlgo01
{
    const int LENGTH = 16;

    public static function bubbleSort(array $array): array
    {
        $size = count($array);

        $compare = 0;
        $swap = 0;
        $start = microtime(true);

        for ($j = 0; $j < $size - 1; $j++) {
            for ($i = $size - 1; $i > $j; $i--) {
                $compare ++;
                if ($array[$i]['key'] < $array[$i-1]['key']) {
                    $swap ++;
                    $value = $array[$i];
                    $array[$i] = $array[$i - 1];
                    $array[$i - 1] = $value;
                }
            }
        }

//        $sorted = array_map(static fn($e) => $e['key'] . ': ' . $e['val'], $array);
//        print_r($sorted);
        $time = microtime(true) - $start;
        return ['compare' => $compare, 'swap' => $swap, 'time' => $time];
    }

    public static function insertSort(array $array): array
    {
        $size = count($array);

        $compare = 0;
        $swap = 0;
        $start = microtime(true);

        for ($i = 1; $i < $size; $i++) {
            for ($j = $i; $j > 0; $j--) {
                $compare ++;
                if ($array[$j]['key'] > $array[$j - 1]['key']) {
                    break;
                }
                $swap ++;
                $value = $array[$j];
                $array[$j] = $array[$j - 1];
                $array[$j - 1] = $value;
            }
        }

        $time = microtime(true) - $start;
        return ['compare' => $compare, 'swap' => $swap, 'time' => $time];
    }

    public static function shellSort(array $array): array
    {
        $size = count($array);

        $compare = 0;
        $swap = 0;
        $start = microtime(true);

        for ($g = $size >> 1; $g > 0; $g >>= 1) {
            for ($i = $g; $i < $size; $i++) {
                for ($k = $i; $k >= $g; $k -= $g) {
                    $compare ++;
                    if ($array[$k]['key'] < $array[$k - $g]['key']) {
                        $swap ++;
                        $value = $array[$k];
                        $array[$k] = $array[$k - $g];
                        $array[$k - $g] = $value;
                    }
                }
            }
        }

        $time = microtime(true) - $start;
        return ['compare' => $compare, 'swap' => $swap, 'time' => $time];
    }

    public static function generate(int $amount): array
    {
        $result = [];

        for ($i = 0; $i < $amount; $i++) {
            $key = rand(1, $amount*4);
            $value = self::randomString();

            $result[] = [
                'key' => $key,
                'val' => $value
            ];
        }

        return $result;
    }

    private static function randomString(): string
    {
        $source = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $str = '';
        for ($i = 0; $i < self::LENGTH; $i++) {
            $str .= $source[rand(0, strlen($source) - 1)];
        }

        return $str;
    }
}

if (php_sapi_name() === 'cli' && basename(__FILE__) === 'SortAlgo01.php') {
    echo '| Алгоритм                     | Перестановки  | Сравнения     | Время выполнения   |' . PHP_EOL;
    echo str_repeat('‾', 85) . PHP_EOL;

    foreach ([10, 100, 100, 1000, 10_000, 50_000] as $size) {
            $array = SortAlgo01::generate($size);

            $result = SortAlgo01::bubbleSort($array);
            echo '| '
                . str_pad(' Bubble sort: ' . $size, 29)
                . '| ' . str_pad((string) $result['compare'], 14)
                . '| ' . str_pad((string) $result['swap'], 14)
                . '| ' . str_pad((string) $result['time'], 19)
                . PHP_EOL;

            $result = SortAlgo01::insertSort($array);
            echo '| '
                . str_pad(' Insert sort: ' . $size, 29)
                . '| ' . str_pad((string) $result['compare'], 14)
                . '| ' . str_pad((string) $result['swap'], 14)
                . '| ' . str_pad((string) $result['time'], 19)
                . PHP_EOL;

            $result = SortAlgo01::shellSort($array);
            echo '| '
                . str_pad(' Shell sort: ' . $size, 29)
                . '| ' . str_pad((string) $result['compare'], 14)
                . '| ' . str_pad((string) $result['swap'], 14)
                . '| ' . str_pad((string) $result['time'], 19)
                . PHP_EOL;

        echo str_repeat('‾', 85) . PHP_EOL;
    }
}
