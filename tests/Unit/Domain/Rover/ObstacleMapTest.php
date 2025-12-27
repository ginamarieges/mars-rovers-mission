<?php
use App\Domain\Rover\ValueObjects\Position;
use App\Domain\Rover\World\ObstacleMap;

describe('Given an ObstacleMap', function () {
    describe('When it is created from an obstacle array', function () {
        test('Then it should detect whether a position contains an obstacle', function () {
            $obstacleMap = ObstacleMap::fromArray([
                ['x' => 10, 'y' => 10],
                ['x' => 5, 'y' => 7],
            ]);

            expect($obstacleMap->has(new Position(x: 10, y:10)))->toBeTrue();
            expect($obstacleMap->has(new Position(x: 5, y:7)))->toBeTrue();
            expect($obstacleMap->has(new Position(x: 0, y:10)))->toBeFalse();
            expect($obstacleMap->has(new Position(x: 2, y:4)))->toBeFalse();

        });
    });
});
