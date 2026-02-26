<?php

declare(strict_types=1);

namespace App\BitArithmetic;

use Exception;

require_once __DIR__ . "/BitsBoard.php";

class ChessGame
{
    /**
     * @throws Exception
     */
    public static function execute($cell, $actor): array
    {
        $moves = gmp_init(0);

        $moves = match($actor) {
            'king' => self::calculateKing($cell, $moves),
            'horse' => self::calculateHorse($cell, $moves),
            'tower' => self::calculateTower($cell, $moves),
            'officer' => self::calculateOfficer($cell, $moves),
            'queen' => self::calculateQueen($cell, $moves),
            default => throw new Exception("Unknown moves")
        };

        $moves = gmp_and($moves, gmp_init('0xFFFFFFFFFFFFFFFF'));

        $steps = gmp_popcount($moves);
        return [
            $steps,
            gmp_strval($moves)
        ];
    }

    /**
     * @param $cell
     * @return int[]
     */
    public static function getCelParams($cell): array
    {
        $row = (int) ($cell / 8);
        $column = $cell % 8;

        return array($row, $column);
    }

    private static function calculateHorse($cell, $moves)
    {
        $cell = BitsBoard::initPosition($cell);
        // Маскируем столбец A (клетки 0,8,16,...) — используем при сдвиге >> чтобы H не прыгал на A
        $notA = gmp_init('0xFEFEFEFEFEFEFEFE');
        $notBA = gmp_init('0xFCFCFCFCFCFCFCFC');
        // Маскируем столбец H (клетки 7,15,23,...) — используем при сдвиге << чтобы A не прыгал на H
        $notH = gmp_init('0x7F7F7F7F7F7F7F7F');
        $notGH = gmp_init('0x3F3F3F3F3F3F3F3F');

        $moves =  gmp_or($moves, gmp_and(gmp_or(gmp_div($cell, gmp_pow(2, 10)), gmp_mul($cell, gmp_pow(2, 6))), $notGH));
        $moves =  gmp_or($moves, gmp_and(gmp_or(gmp_div($cell, gmp_pow(2, 17)), gmp_mul($cell, gmp_pow(2, 15))), $notH));
        $moves =  gmp_or($moves, gmp_and(gmp_or(gmp_div($cell, gmp_pow(2, 15)), gmp_mul($cell, gmp_pow(2, 17))), $notA));
        return gmp_or($moves, gmp_and(gmp_or(gmp_div($cell, gmp_pow(2, 6)), gmp_mul($cell, gmp_pow(2, 10))), $notBA));
    }

    private static function calculateTower($cell, $moves)
    {
        // По переданной клетке рассчитываем строку и столбец
        list($row, $column) = self::getCelParams($cell);

        // Для выбранной клетки строим вертикальную маску
        $mask = '0x' . str_repeat(sprintf('%02x', 2 ** $column), 8);

        // Для выбранной клетки дополняем горизонтальные маски
        for ($i = $row * 8; $i <= $row * 8 + 7; $i++) {
            $mask = gmp_or($mask, BitsBoard::initPosition($i));
        }

        $moves =  gmp_or($moves, $mask);
        return  gmp_and($moves, gmp_com(BitsBoard::initPosition($cell)));
    }

    /**
     * Слон ходит по четырем диагоналям:
     * - NE (право-вверх): += 9  => умножение на 2^9, маска notA (без переноса из H в A)
     * - NW (лево-вверх):  += 7  => умножение на 2^7, маска notH (без переноса из A в H)
     * - SE (право-вниз) -= 7 => деление на 2^7, маска notA
     * - SW (лево-вниз): -= 9 => деление на 2^9, маска notH
     */
    private static function calculateOfficer($cell, $moves)
    {
        list($row, $column) = self::getCelParams($cell);
        $actor = BitsBoard::initPosition($cell);

        $notA = gmp_init('0xFEFEFEFEFEFEFEFE');
        $notH = gmp_init('0x7F7F7F7F7F7F7F7F');

        $stepsNE = min(7 - $row, 7 - $column);
        $stepsNW = min(7 - $row, $column);
        $stepsSE = min($row, 7 - $column);
        $stepsSW = min($row, $column);

        // NE
        for ($i = 1; $i <= $stepsNE; $i++) {
            $moves = gmp_or($moves, gmp_and(gmp_mul($actor, gmp_pow(2, 9 * $i)), $notA));
        }
        // NW
        for ($i = 1; $i <= $stepsNW; $i++) {
            $moves = gmp_or($moves, gmp_and(gmp_mul($actor, gmp_pow(2, 7 * $i)), $notH));
        }
        // SE
        for ($i = 1; $i <= $stepsSE; $i++) {
            $moves = gmp_or($moves, gmp_and(gmp_div($actor, gmp_pow(2, 7 * $i)), $notA));
        }
        // SW
        for ($i = 1; $i <= $stepsSW; $i++) {
            $moves = gmp_or($moves, gmp_and(gmp_div($actor, gmp_pow(2, 9 * $i)), $notH));
        }

        $boardMask = gmp_init('0xFFFFFFFFFFFFFFFF');
        return gmp_and($moves, gmp_and(gmp_com($actor), $boardMask));
    }

    private static function calculateQueen($cell, $moves)
    {
        $moves1 = self::calculateOfficer($cell, $moves);
        $moves2 = self::calculateTower($cell, $moves);

        return gmp_or(gmp_strval($moves1), gmp_strval($moves2));
    }

    private static function calculateKing($cell, $moves)
    {
        $cell = BitsBoard::initPosition($cell);
        // Маскируем столбец A (клетки 0,8,16,...) — используем при сдвиге >> чтобы H не прыгал на A
        $notA = gmp_init('0xFEFEFEFEFEFEFEFE');
        // Маскируем столбец H (клетки 7,15,23,...) — используем при сдвиге << чтобы A не прыгал на H
        $notH = gmp_init('0x7F7F7F7F7F7F7F7F');

        $moves = gmp_or($moves, gmp_mul($cell, gmp_pow(2, 8)));
        $moves = gmp_or($moves, gmp_div($cell, gmp_pow(2, 8)));

        $moves = gmp_or($moves, gmp_and(gmp_mul($cell, 2), $notA));
        $moves = gmp_or($moves, gmp_and(gmp_div($cell, 2), $notH));

        $moves = gmp_or($moves, gmp_and(gmp_mul($cell, gmp_pow(2, 9)), $notA));
        $moves = gmp_or($moves, gmp_and(gmp_div($cell, gmp_pow(2, 9)), $notH));

        $moves = gmp_or($moves, gmp_and(gmp_mul($cell, gmp_pow(2, 7)), $notH));
        return gmp_or($moves, gmp_and(gmp_div($cell, gmp_pow(2, 7)), $notA));
    }
}

if (php_sapi_name() === 'cli' && basename(__FILE__) === 'ChessGame.php') {
    $cases = [
        [
            'title' => 'Проверка для короля',
            'actor' => 'king',
            'assertions' => [
                [0, 3, '770'],
                [1, 5, '1797'],
                [7, 3, '49216'],
                [8, 5, '197123'],
                [10, 8, '920078'],
                [15, 5, '12599488'],
                [54, 8, '16186183351374184448'],
                [55, 5, '13853283560024178688'],
                [56, 3, '144959613005987840'],
                [63, 3, '4665729213955833856'],
            ],
        ],
        [
            'title' => 'Проверка для коня',
            'actor' => 'horse',
            'assertions' => [
                [0, 2, '132096'],
                [1, 3, '329728'],
                [2, 4, '659712'],
                [36, 8, '11333767002587136'],
                [47, 4, '4620693356194824192'],
                [48, 3, '288234782788157440'],
                [54, 4, '1152939783987658752'],
                [55, 3, '2305878468463689728'],
                [56, 2, '1128098930098176'],
                [63, 2, '9077567998918656'],
            ],
        ],
        [
            'title' => 'Проверка для ладьи',
            'actor' => 'tower',
            'assertions' => [
                [0, 14, '72340172838076926'],
                [1, 14, '144680345676153597'],
                [2, 14, '289360691352306939'],
                [36, 14, '1157443723186933776'],
                [47, 14, '9259541023762186368'],
                [48, 14, '143553341945872641'],
                [54, 14, '4665518383679160384'],
                [55, 14, '9259260648297103488'],
                [56, 14, '18302911464433844481'],
                [63, 14, '9187484529235886208'],
            ],
        ],
        [
            'title' => 'Проверка для слона',
            'actor' => 'officer',
            'assertions' => [
                [0, 7, '9241421688590303744'],
                [1, 7, '36099303471056128'],
                [2, 7, '141012904249856'],
                [36, 13, '9386671504487645697'],
                [47, 7, '2323857683139004420'],
                [48, 7, '144117404414255168'],
                [54, 9, '11529391036782871041'],
                [55, 7, '4611756524879479810'],
                [56, 7, '567382630219904'],
                [63, 7, '18049651735527937'],
            ],
        ],
        [
            'title' => 'Проверка для Ферзя',
            'actor' => 'queen',
            'assertions' => [
                [0, 21, '9313761861428380670'],
                [1, 21, '180779649147209725'],
                [2, 21, '289501704256556795'],
                [36, 27, '10544115227674579473'],
                [47, 21, '11583398706901190788'],
                [48, 21, '287670746360127809'],
                [54, 23, '16194909420462031425'],
                [55, 21, '13871017173176583298'],
                [56, 21, '18303478847064064385'],
                [63, 21, '9205534180971414145'],
            ],
        ],
    ];

    foreach ($cases as $case) {
        echo PHP_EOL;
        echo $case['title'] . PHP_EOL;
        echo str_repeat('-', 50) . PHP_EOL;

        foreach ($case['assertions'] as $assertion) {
            $result = ChessGame::execute($assertion[0], $case['actor']);
            $maskMatch = ($result[1] === $assertion[2]);
            $stepsMatch = ((int) $result[0] === $assertion[1]);
            BitsBoard::printBoard(8, $assertion[0], $result[1]);
            $text = ($stepsMatch && $maskMatch)
                ? "🚀 Тест пройден. Для клетки $assertion[0] количество ходов $result[0], маска: $result[1]"
                : "⚠️ Ошибка! Для клетки $assertion[0] количество ходов $result[0] должно быть $assertion[1], получена маска: $result[1] должна быть  $assertion[2]";
            echo $text . PHP_EOL . PHP_EOL;
        }

        echo PHP_EOL . str_repeat('-', 50) . PHP_EOL;
    }
}
