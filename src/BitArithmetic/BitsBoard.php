<?php

declare(strict_types=1);

namespace App\BitArithmetic;

class BitsBoard
{
    public static function printBoard(int $bitboard, int $selected, string $mask): void
    {
        $RESET = "\033[0m";
        $INVERT = "\033[7m";
        $BOLD = "\033[1m";
        $MOVE_STYLE = "\033[42m\033[30m";

        $gmpMask = gmp_init($mask);

        for ($i = 1; $i <= $bitboard; $i++) {
            $k = $bitboard * $bitboard - $bitboard * $i;
            for ($j = 0; $j < $bitboard; $j++) {
                $output = $k;
                $output = $output > 9 ? $output : '0' . $output;
                if ($k === $selected) {
                    echo $INVERT . $BOLD ." $output " . $RESET;
                } else if (gmp_testbit($gmpMask, $k)) {
                    echo $MOVE_STYLE . $BOLD ." $output " . $RESET;
                } else {
                    echo "[$output]";
                }
                $k++;
            }
            echo PHP_EOL;
        }
    }

    public static function initPosition(int $cellNumber): string
    {
        return gmp_strval(gmp_init(1) << $cellNumber);
    }
}

