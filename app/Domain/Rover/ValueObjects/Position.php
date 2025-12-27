<?php

declare(strict_types=1);

namespace App\Domain\Rover\ValueObjects;

final class Position {
    public function __construct(
        public readonly int $x,
        public readonly int $y
    ) {

    }
}
