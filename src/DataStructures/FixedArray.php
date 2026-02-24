<?php

declare(strict_types=1);

namespace App\DataStructures;

use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use SplFixedArray;

class FixedArray
{
    public int $realloc = 1;

    public string $name = 'Фиксированный массив';

    protected SplFixedArray $fixedArray;

    public function __construct(
        protected int $size = 3,
        protected int $count = 0,
        public ?LoggerInterface $logger = null,
    ) {
        $this->fixedArray = new SplFixedArray($this->size);
        $this->logger ??= new Logger('error');
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getRealloc(): int
    {
        return $this->realloc;
    }

    public function getFixedArray(): SplFixedArray
    {
        return $this->fixedArray;
    }

    public function put(mixed $el): void
    {
        $index = $this->count;

        $this->fixedArray[$index] = $el;
        $this->count ++;
    }



    public function getSize(): int
    {
        return $this->size;
    }

    public function setSize(int $size): void
    {
        $this->size = $size;
    }
}
