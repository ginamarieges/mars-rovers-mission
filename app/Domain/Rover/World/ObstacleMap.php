<?php

declare(strict_types=1);

namespace App\Domain\Rover\World;

use App\Domain\Rover\ValueObjects\Position;

final class ObstacleMap {
    private array $obstacleKeys;

    private function __construct(array $obstacleKeys) {
        $this->obstacleKeys = $obstacleKeys;
    }

    public static function fromArray(array $obstacles): self {
        $obstacleKeys = [];
        foreach($obstacles as $obstacle) {
            $position = new Position(x: $obstacle['x'], y: $obstacle['y']);
            $obstacleKeys[self::positionKey($position)] = true;
        }

        return new self($obstacleKeys);
    }

    private static function positionKey(Position $position): string {
        return $position->x . ',' . $position->y;
    }

    public function has(Position $position): bool {
        return isset($this->obstacleKeys[self::positionKey($position)]);
    }
}
