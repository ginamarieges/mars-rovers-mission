<?php

declare(strict_types=1);

namespace App\Domain\Rover;

use App\Domain\Rover\ValueObjects\Position;
use InvalidArgumentException;

final class ExecutionReport {
    public function __construct(
        public readonly RoverState $finalState,
        public readonly bool $aborted,
        public readonly ?Position $obstaclePosition,
        public readonly int $executedCommands
    ) {
        if ($executedCommands < 0) {
            throw new InvalidArgumentException('executedCommands cannot be negative.');
        }

        if ($aborted === false && $obstaclePosition !== null) {
            throw new InvalidArgumentException('obstaclePosition must be null when execution is not aborted.');
        }

        if ($aborted === true && $obstaclePosition === null) {
            throw new InvalidArgumentException('obstaclePosition must be provided when execution is aborted.');
        }
    }
}
