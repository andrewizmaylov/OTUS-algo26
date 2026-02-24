<?php

declare(strict_types=1);

namespace App\DataStructures;

use SplFixedArray;

class VectorArray extends FixedArray
{
    public string $name = 'Динамический массив + 1';

    public function put(mixed $el): void
    {
        try {
            if ($this->count < $this->size) {
                $this->fixedArray[$this->count] = $el;
                $this->count++;
            } else {
                $this->realloc ++;
                $this->size = $this->size + 1;
                $newArray = new SplFixedArray($this->size);
                foreach ($this->fixedArray as $key => $value) {
                    $newArray[$key] = $value;
                }
                $newArray[$this->count] = $el;
                $this->count ++;
                unset($this->fixedArray);
                $this->fixedArray = $newArray;
                unset($newArray);
            }
        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage());
        }
    }
}
