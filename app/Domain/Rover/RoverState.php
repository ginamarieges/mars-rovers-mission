<?php

declare(strict_types=1);

namespace App\Domain\Rover;

use App\Domain\Rover\ValueObjects\Direction;
use App\Domain\Rover\ValueObjects\Position;

final class RoverState {
    public function __construct(
        public readonly Position $position,
        public readonly Direction $direction
    ){}

    public function withPosition(Position $position): self {
        return new self(
            position: $position,
            direction: $this->direction
        );
    }

    public function withDirection(Direction $direction): self {
        return new self(
            position: $this->position,
            direction: $direction
        );
    }
}
