<?php

declare(strict_types=1);

namespace App\Domain\Rover;

use App\Domain\Rover\ValueObjects\Position;
use InvalidArgumentException;

final class ExecutionReport {
    public function __construct(
        public readonly RoverState $finalState,         // State of the rover, it contains position and direction
        public readonly bool $aborted,                  // Whether execution was aborted due to an obstacle or world boundary
        public readonly ?Position $obstaclePosition,    // Position that caused the abort (obstacle or outside the grid), null if not aborted
        public readonly int $executedCommands,          // Number of commands that were successfully executed
        public readonly string $usedCommands            // Commands that were applied before stopping
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
