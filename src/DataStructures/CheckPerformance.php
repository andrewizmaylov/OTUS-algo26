<?php

declare(strict_types=1);

namespace App\DataStructures;


use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

class CheckPerformance
{
    private readonly LoggerInterface $logger;

    private array $classes = [
        VectorArray::class,
        RangeArray::class,
        FactorArray::class,
    ];

    public function __construct()
    {
        $this->logger = new Logger('stack');
        $this->logger->pushHandler(new StreamHandler('./results.log', Level::Debug));
        $this->logger->pushHandler(new StreamHandler('php://stdout', Level::Debug));
    }

    public function handle(int $total): void
    {
        set_time_limit(0);

        $msg = "Вставка " . number_format($total, 0, null, '_') . " значений:";
        $this->logger->debug($msg);

        foreach ($this->classes as $class) {
            if ($class === VectorArray::class && $total > 10_000) {
                $this->logger->debug("$class умирает с размером данных более 10_000");
                continue;
            }
            $instance = new $class();
            $processStart = microtime(true);
            for ($i = 0; $i < $total; $i++) {
                $instance->put($i);
            }
            $elapsedTime = microtime(true) - $processStart;
            $this->logger->debug("  [$instance->name] realloc: $instance->realloc, время: " . round($elapsedTime, 2) . " с");
        }
    }
}

if (php_sapi_name() === 'cli' && basename(__FILE__) === 'CheckPerformance.php') {
    require_once __DIR__ . '/../../vendor/autoload.php';

    set_time_limit(0);

    // VectorArray (+1) is O(n²) — 100k+ elements can take hours; use smaller set for full run
    // For long run (FactorArray/RangeArray OK; VectorArray 100k+ very slow):
     $cases = [100, 1000, 10_000, 100_000, 1000_000, 10_000_000];

    foreach ($cases as $case) {
        (new CheckPerformance())->handle($case);
    }
}

