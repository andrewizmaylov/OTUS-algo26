<?php

declare(strict_types=1);

namespace App\HarryPotterSquares;

/*
 * Для запуска в консоли из папки с файлом набрать php MagicSquareCases.php
 */
class MagicSquareCases
{
    protected array $matrix;

    public function __construct(
        public readonly int $size,
        public readonly array $condition,
    )
    {
        $this->matrix = array_fill(0, $this->size, array_fill(0, $this->size, ''));

        $this->printMatrix();
    }

    public function printMatrix(): void
    {
        $condition = $this->condition['closure'];
        $total = $this->size * $this->size;

        for ($i = 0; $i < $total; $i++) {
            $x = $i % $this->size;
            $y = (int)($i / $this->size);

            $this->matrix[$y][$x] = $condition($x, $y);
        }

        echo PHP_EOL . $this->condition['title'] . PHP_EOL;

        foreach ($this->matrix as $row) {
            echo implode(' ', $row) . PHP_EOL;
        }
    }
}

if (php_sapi_name() === 'cli' && basename(__FILE__) === 'MagicSquareCases.php') {
    $size = 25;

    $cases = [
        [
            'title' => 'Case 1',
            'closure' => fn ($x, $y) => $x > $y ? '#' : '.',
        ],
        [
            'title' => 'Case 2',
            'closure' => fn ($x, $y) => $x === $y ? '#' : '.',
        ],
        [
            'title' => 'Case 3',
            'closure' => fn ($x, $y) => $y === $size - $x - 1 ? '#' : '.',
        ],
        [
            'title' => 'Case 4',
            'closure' => fn ($x, $y) => $y < $size - $x + 5 ? '#' : '.',
        ],
        [
            'title' => 'Case 5',
            'closure' => fn ($x, $y) => $x < ($y + 1) * 2 && $x >= ($y) * 2 ? '#' : '.',
        ],
        [
            'title' => 'Case 6',
            'closure' => fn ($x, $y) => $y < 10 || $x < 10 ? '#' : '.',
        ],
        [
            'title' => 'Case 7',
            'closure' => fn ($x, $y) => $x >= $size - 9 && $y >= $size - 9 ? '#' : '.',
        ],
        [
            'title' => 'Case 8',
            'closure' => fn ($x, $y) => $x * $y === 0 ? '#' : '.',
        ],
        [
            'title' => 'Case 9',
            'closure' => function ($x, $y) use ($size) {
                $shift = 14;
                return ($x >= $size - $shift + $y) || ($x <= -($size - $shift) + $y) ? '#' : '.';
            },
        ],
        [
            'title' => 'Case 10',
            'closure' => fn ($x, $y) => $x < ($y + 1) * 2 && $x > $y ? '#' : '.',
        ],
        [
            'title' => 'Case 11',
            'closure' => fn ($x, $y) => in_array($x, [1, $size - 2]) || in_array($y, [1, $size - 2]) ? '#' : '.',
        ],

        [
            'title' => 'Case 13',
            'closure' => fn ($x, $y) => $y >= ($size - 5) - $x && $y < ($size + 4) - $x ? '#' : '.',
        ],

        [
            'title' => 'Case 15',
            'closure' => function ($x, $y) use ($size) {
                $shift = 15;
                $shift2 = 4;
                return ($x >= $size - $shift + $y && $x < $size - $shift2 + $y)
                    || ($x <= -($size - $shift) + $y && $x > -($size - $shift2) + $y) ? '#' : '.';
            },
        ],
//        [
//            'title' => 'Case 16',
//            'closure' => function ($x, $y) use ($size) {
//                $shift = 3;
//
//            },
//        ],


    ];

    foreach ($cases as $case) {
        new MagicSquareCases(
            $size,
            $case
        );
    }
}
