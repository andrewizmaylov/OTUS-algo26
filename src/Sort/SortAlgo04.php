<?php

declare(strict_types=1);

namespace App\Sort;

/*
 * Для запуска в консоли из папки с файлом набрать php SortAlgo04.php
 */
class SortAlgo04
{
    public const int CAPACITY = 20;
    public const int MASK_SIZE = 9;

    public static function BucketSort(array $array, int $size): array
    {
        $compare = 0;
        $swap = 0;
        $start = microtime(true);

        $max = $array[0];
        for ($i = 1; $i < $size; $i ++) {
            $compare ++;
            if ($array[$i] > $max) {
                $swap ++;
                $max = $array[$i];
            }
        }

        // 10-ти кратное хранилище
        $bucketSize = (int) ($max / self::CAPACITY);
        $buckets = array_fill(0, $bucketSize, []);
        $k = $bucketSize / $max;

        for ($i = 0; $i < $size; $i++) {
            $index = floor($array[$i] * $k);
            $buckets[$index][] = $array[$i];
            if (count($buckets[$index]) === 1) {
                continue;
            }
            // Сортируем в корзине
            for ($j = count($buckets[$index]) - 2; $j >= 0; $j --) {
                $compare ++;
                if ($array[$i] < $buckets[$index][$j]) {
                    $swap ++;
                    $temp = $buckets[$index][$j];
                    $buckets[$index][$j] = $array[$i];
                    $buckets[$index][$j + 1] = $temp;
                }
            }
        }

        $sortedArray = [];
        foreach ($buckets as $bucket) {
            $sortedArray = array_merge($sortedArray, $bucket);
        }

        $time = microtime(true) - $start;
        return ['compare' => $compare, 'swap' => $swap, 'time' => $time];
    }

    public static function generateSimpleWithMax(int $amount, int $max): array
    {
        $result = [];

        for ($i = 0; $i < $amount; $i++) {
            $result[] = rand(1, $max);
        }

        return $result;
    }

    public static function countingSort(array $array, int $size): array
    {
        $compare = 0;
        $swap = 0;
        $start = microtime(true);

        $max = $array[0];
        for ($i = 0; $i < $size; $i++) {
            $compare ++;
            if ($array[$i] > $max) {
                $max = $array[$i];
            }
        }

        $mask = array_fill(0, $max + 1, 0);

        for ($i = $size - 1; $i >= 0; $i--) {
            $mask[$array[$i]] ++;
        }

        for ($i = 0; $i < $max; $i++) {
            $mask[$i + 1] = $mask[$i] + $mask[$i + 1];
        }

        $swap += $size;

        $sorted = $array;
        for ($i = $size - 1; $i >= 0; $i--) {
            $mask[$array[$i]] --;
            $sorted[$mask[$array[$i]]] = $array[$i];
        }

        $time = microtime(true) - $start;
        return ['compare' => $compare, 'swap' => $swap, 'time' => $time];
    }


    public static function radixSort(array $array): array
    {
        $compare = 0;
        $swap = 0;
        $start = microtime(true);

        // create digits count for **1
        $mask = self::createDMask($array, 0, $compare);
        $sorted = self::sortArray($array, $mask, 0, $compare, $swap);

        // create digits count for *1*
        $mask = self::createDMask($sorted, 1, $compare);
        $sorted = self::sortArray($sorted, $mask, 1, $compare, $swap);

        // create digits count for 1**
        $mask = self::createDMask($sorted, 2, $compare);
        self::sortArray($sorted, $mask, 2, $compare, $swap);

        $time = microtime(true) - $start;
        return ['compare' => $compare, 'swap' => $swap, 'time' => $time];
    }

    public static function createDMask(array $array, int $shift, &$compare): array
    {
        $mask = array_fill(0, self::MASK_SIZE + 1, 0);
        foreach ($array as $value) {
            $compare ++;
            $index = intdiv($value, pow(10, $shift)) % 10;
            $mask[$index] ++;
        }

        for ($i = 0; $i < self::MASK_SIZE; $i++) {
            $mask[$i+1] = $mask[$i] + $mask[$i+1];
        }

        return $mask;
    }

    private static function sortArray(array $array, array $mask, int $shift, &$compare, &$swap): array
    {
        $sortedArray = $array;
        for ($i = sizeof($array) - 1; $i >= 0; $i--) {
            $compare ++;
            $swap ++;
            $index = intdiv($array[$i], pow(10, $shift)) % 10;
            $mask[$index] --;
            $sortedArray[$mask[$index]] = $array[$i];
        }

        return $sortedArray;
    }
}

if (php_sapi_name() === 'cli' && basename(__FILE__) === 'SortAlgo04.php') {
    ini_set('memory_limit', '2G');
    $currentMemoryLimit = ini_get('memory_limit');
    echo "Current memory limit: " . $currentMemoryLimit . PHP_EOL;

    echo '| Алгоритм                     | Перестановки  | Сравнения     | Время выполнения   |' . PHP_EOL;
    echo str_repeat('‾', 85) . PHP_EOL;

    foreach ([10, 100, 100, 1000, 10_000, 100_000, 1_000_000] as $size) {
        $array = SortAlgo04::generateSimpleWithMax($size, $size);

        $result = SortAlgo04::BucketSort($array, $size);
        echo '| '
            . str_pad(' Bucket sort: ' . $size, 29)
            . '| ' . str_pad((string) $result['compare'], 14)
            . '| ' . str_pad((string) $result['swap'], 14)
            . '| ' . str_pad((string) $result['time'], 19)
            . PHP_EOL;

        $result = SortAlgo04::countingSort($array, $size);
        echo '| '
            . str_pad(' Counting sort: ' . $size, 29)
            . '| ' . str_pad((string) $result['compare'], 14)
            . '| ' . str_pad((string) $result['swap'], 14)
            . '| ' . str_pad((string) $result['time'], 19)
            . PHP_EOL;

        $result = SortAlgo04::radixSort($array);
        echo '| '
            . str_pad(' Radix sort: ' . $size, 29)
            . '| ' . str_pad((string) $result['compare'], 14)
            . '| ' . str_pad((string) $result['swap'], 14)
            . '| ' . str_pad((string) $result['time'], 19)
            . PHP_EOL;

        echo str_repeat('‾', 85) . PHP_EOL;
    }
}
