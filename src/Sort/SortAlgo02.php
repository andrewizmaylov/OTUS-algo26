<?php

declare(strict_types=1);

namespace App\Sort;

/*
 * Для запуска в консоли из папки с файлом набрать php SortAlgo02.php
 */
class SortAlgo02
{
    public static function heapSort(array $array): array
    {
        $size = sizeof($array);
        $compare = 0;
        $swap = 0;
        $start = microtime(true);

        // Создать кучу
        for ($i = $size >> 1; $i >= 0; $i--) {
            self::heapify($array, $i, $size, $compare, $swap);
        }

        // Отсортировать кучу
        for ($i = $size - 1; $i >= 0; $i--)
        {
            // Сортировка вставками
            $temp = $array[0];
            $array[0] = $array[$i];
            $array[$i] = $temp;

            // Работаем дальше на уменьшенной куче
            self::heapify($array, $i, $size, $compare, $swap);
        }

        $time = microtime(true) - $start;
        return ['compare' => $compare, 'swap' => $swap, 'time' => $time];
    }

    public static function heapify(array &$array, int $root, int $size, int &$compare, int &$swap): void
    {
        $min = $root;
        $left = $root + 1;
        $right = $root + 2;

        $compare += 2;
        if ($left < $size && $array[$left] < $array[$min]) {
            $min = $left;
        }
        if ($right < $size && $array[$right] < $array[$min]) {
            $min = $right;
        }
        if ($min !== $root) {
            $swap ++;

            $value = $array[$root];
            $array[$root] = $array[$min];
            $array[$min] = $value;

            self::heapify($array, $min, $size, $compare, $swap);
        }
    }

    public static function shakeSort($array): array
    {
        $compare = 0;
        $swap = 0;
        $start = microtime(true);

        $l = 0;
        $r = count($array) - 1;

        while ($l < $r) {
            $flag = false;
            for ($i = $l; $i < $r; $i++) {
                $compare ++;
                if ($array[$i] > $array[$i + 1]) {
                    $flag = true;
                    $swap ++;
                    $value = $array[$i];
                    $array[$i] = $array[$i + 1];
                    $array[$i + 1] = $value;
                }
            }
            $r --;
            for ($j = $r; $j > $l; $j--) {
                $compare ++;
                if ($array[$j] < $array[$j - 1]) {
                    $flag = true;
                    $swap ++;
                    $value = $array[$j];
                    $array[$j] = $array[$j - 1];
                    $array[$j - 1] = $value;
                }
            }
            $l++;
            if (!$flag) {
                $time = microtime(true) - $start;
                return ['compare' => $compare, 'swap' => $swap, 'time' => $time];
            }
        }


        $time = microtime(true) - $start;
        return ['compare' => $compare, 'swap' => $swap, 'time' => $time];
    }

    public static function selectSort($array): array
    {
        $size = count($array);

        $compare = 0;
        $swap = 0;
        $start = microtime(true);

        $min = $array[0];
        $minKey = 0;

        for ($i = 1; $i < $size; $i++) {
            $compare ++;
            if ($array[$i] < $min) {
                $swap ++;
                $min = $array[$i];
                $minKey = $i;
            }
            if ($array[$i] === $min) {
                continue;
            }

            $swap ++;
            $array[$minKey] = $array[$i];
            $array[$i] = $min;
        }

        $time = microtime(true) - $start;
        return ['compare' => $compare, 'swap' => $swap, 'time' => $time];
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

if (php_sapi_name() === 'cli' && basename(__FILE__) === 'SortAlgo02.php') {
    echo '| Алгоритм                     | Перестановки  | Сравнения     | Время выполнения   |' . PHP_EOL;
    echo str_repeat('‾', 85) . PHP_EOL;

    foreach ([10, 100, 100, 1000, 10_000, 50_000] as $size) {
        $array = SortAlgo02::generateSimple($size);

        $result = SortAlgo02::shakeSort($array);
        echo '| '
            . str_pad(' Shake sort: ' . $size, 29)
            . '| ' . str_pad((string) $result['compare'], 14)
            . '| ' . str_pad((string) $result['swap'], 14)
            . '| ' . str_pad((string) $result['time'], 19)
            . PHP_EOL;

        $result = SortAlgo02::selectSort($array);
        echo '| '
            . str_pad(' Select sort: ' . $size, 29)
            . '| ' . str_pad((string) $result['compare'], 14)
            . '| ' . str_pad((string) $result['swap'], 14)
            . '| ' . str_pad((string) $result['time'], 19)
            . PHP_EOL;

        $result = SortAlgo02::heapSort($array);
        echo '| '
            . str_pad(' Heap sort: ' . $size, 29)
            . '| ' . str_pad((string) $result['compare'], 14)
            . '| ' . str_pad((string) $result['swap'], 14)
            . '| ' . str_pad((string) $result['time'], 19)
            . PHP_EOL;

        echo str_repeat('‾', 85) . PHP_EOL;
    }
}
