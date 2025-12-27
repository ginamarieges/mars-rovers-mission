<?php

declare(strict_types=1);

namespace App\Domain\Rover\World;

use App\Domain\Rover\ValueObjects\Position;

final class Grid {
    public function __construct(
        public readonly int $width,
        public readonly int $height
    ) {}

    public function isInside(Position $position): bool {
        return $position->x >= 0
        && $position->x <= $this->width - 1
        && $position->y >= 0
        && $position->y <= $this->height - 1;
    }
}
