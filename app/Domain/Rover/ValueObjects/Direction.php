<?php

declare(strict_types=1);

namespace App\Domain\Rover\ValueObjects;

enum Direction: string {
    case N = 'N';
    case E = 'E';
    case S = 'S';
    case W = 'W';

    public function turnLeft() {
        return match ($this) {
            self::N => self::W,
            self::W => self::S,
            self::S => self::E,
            self::E => self::N,
        };
    }

    public function turnRight() {
        return match ($this) {
            self::N => self::E,
            self::W => self::N,
            self::S => self::W,
            self::E => self::S,
        };
    }

    public function nextPosition(Position $currentPosition): Position {
        return match ($this) {
            self::N => new Position(
                x: $currentPosition->x,
                y: $currentPosition->y + 1
            ),
            self::E => new Position(
                x: $currentPosition->x + 1,
                y: $currentPosition->y
            ),
            self::S => new Position(
                x: $currentPosition->x,
                y: $currentPosition->y - 1
            ),
            self::W => new Position(
                x: $currentPosition->x - 1,
                y: $currentPosition->y
            ),
        };
    }
}
