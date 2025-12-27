<?php

use App\Domain\Rover\ValueObjects\Direction;
use App\Domain\Rover\ValueObjects\Position;
use App\Domain\Rover\RoverState;

describe('Given a RoverState', function () {
    describe('When it is created with a position and a direction', function () {
        test('Then it should expose both as immutable state', function () {
            $startPosition = new Position(x: 3, y:4);
            $startDirection = Direction::E;
            $roverState = new RoverState( position: $startPosition, direction: $startDirection);

            expect($roverState->position)->toBe($startPosition);
            expect($roverState->direction)->toBe($startDirection);
        });
    });
});
