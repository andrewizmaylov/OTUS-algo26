<?php

declare(strict_types=1);

namespace App\Sort;

/*
 * Для запуска в консоли из папки с файлом набрать php SortAlgo03.php
 */
class SortAlgo03
{
    public static function quickSort(array $array): array
    {
        $compare = 0;
        $swap = 0;
        $start = microtime(true);

        $size = sizeof($array);
        $left = 0;
        $right = $size - 1;

        self::qSortRecursion($array, $compare, $swap, $left, $right);

        $time = microtime(true) - $start;
        return ['compare' => $compare, 'swap' => $swap, 'time' => $time];
    }

    public static function qSortRecursion(array &$array, int &$compare, int &$swap, int $left, int $right): void
    {
        if ($left >= $right) {
            return;
        }
        $m = self::splitForQuickSort($array, $compare, $swap, $left, $right);
        self::qSortRecursion($array, $compare, $swap, $left, $m - 1);
        self::qSortRecursion($array, $compare, $swap, $m + 1, $right);
    }

    public static function splitForQuickSort(array &$array, int &$compare, int &$swap, int $left, int $right): int
    {
        $pivot = $array[$right];

        $middle = $left - 1;

        for ($i = $left; $i <= $right; $i++) {
            $compare ++;
            if ($array[$i] <= $pivot) {
                $middle ++;
                $swap ++;
                $temp = $array[$middle];
                $array[$middle] = $array[$i];
                $array[$i] = $temp;
            }
        }

        return $middle;
    }



    public static function mergeSort(array $array): array
    {
        $compare = 0;
        $swap = 0;
        $start = microtime(true);

        $size = sizeof($array);
        $left = 0;
        $right = $size - 1;

        self::mergeSortRecursion($array, $compare, $swap, $left, $right);

        $time = microtime(true) - $start;
        return ['compare' => $compare, 'swap' => $swap, 'time' => $time];
    }

    public static function mergeSortRecursion(array &$array, int &$compare, int &$swap, int $left, int $right): void
    {
        if ($left >= $right) {
            return;
        }
        $middle = ($right + $left) >> 1;

        self::mergeSortRecursion($array, $compare, $swap, $left, $middle);
        self::mergeSortRecursion($array, $compare, $swap, $middle + 1, $right);

        self::mergeSorted($array, $compare, $swap, $left, $right, $middle);
    }

    public static function mergeSorted(array &$array, int &$compare, int &$swap, int $left, int $right, int $middle): void
    {
        $i = $left;
        $l = $left;
        $r = $middle + 1;
        $temp = array_fill($left, $right - $left + 1, null);

        while ($l <= $middle && $r <= $right && in_array($i, range($left, $right))) {
            $compare ++;
            $swap ++;
            if ($array[$l] < $array[$r]) {
                $temp[$i] = $array[$l];
                $l++;
            } else {
                $temp[$i] = $array[$r];
                $r++;
            }
            $i ++;
        }
        while ($l <= $middle) {
            $swap ++;
            $temp[$i] = $array[$l];
            $l++;
            $i ++;
        }

        while ($r <= $right) {
            $swap ++;
            $temp[$i] = $array[$r];
            $r++;
            $i ++;
        }

        foreach ($temp as $k => $v) {
            $array[$k] = $v;
        }

        unset($temp);
        gc_collect_cycles();
    }

    public static function generateSimple(int $amount): array
    {
        $result = [];

        for ($i = 0; $i < $amount; $i++) {
            $result[] = rand(1, $amount*2);
        }

        return $result;
    }
}

if (php_sapi_name() === 'cli' && basename(__FILE__) === 'SortAlgo03.php') {
    echo '| Алгоритм                     | Перестановки  | Сравнения     | Время выполнения   |' . PHP_EOL;
    echo str_repeat('‾', 85) . PHP_EOL;

    foreach ([10, 100, 100, 1000, 10_000, 100_000, 1_000_000] as $size) {
        $array = SortAlgo03::generateSimple($size);

        $result = SortAlgo03::quickSort($array);
        echo '| '
            . str_pad(' Quick sort: ' . $size, 29)
            . '| ' . str_pad((string) $result['compare'], 14)
            . '| ' . str_pad((string) $result['swap'], 14)
            . '| ' . str_pad((string) $result['time'], 19)
            . PHP_EOL;

        if ($size <= 10_000) {
            $result = SortAlgo03::mergeSort($array);
            echo '| '
                . str_pad(' Merge sort: ' . $size, 29)
                . '| ' . str_pad((string) $result['compare'], 14)
                . '| ' . str_pad((string) $result['swap'], 14)
                . '| ' . str_pad((string) $result['time'], 19)
                . PHP_EOL;

            echo str_repeat('‾', 85) . PHP_EOL;
        }
    }
}
