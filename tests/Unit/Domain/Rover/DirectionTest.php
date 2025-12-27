<?php

use App\Domain\Rover\ValueObjects\Position;
use App\Domain\Rover\ValueObjects\Direction;

describe('Given a Direction', function () {
    describe('When the turnLeft method is called', function () {
        test('Then it should rotate counter-clockwise', function () {
            expect(Direction::N->turnLeft())->toBe(Direction::W);
            expect(Direction::W->turnLeft())->toBe(Direction::S);
            expect(Direction::S->turnLeft())->toBe(Direction::E);
            expect(Direction::E->turnLeft())->toBe(Direction::N);
        });
    });
    describe('When the turnRight method is called', function () {
        test('Then it should rotate clockwise', function () {
            expect(Direction::N->turnRight())->toBe(Direction::E);
            expect(Direction::E->turnRight())->toBe(Direction::S);
            expect(Direction::S->turnRight())->toBe(Direction::W);
            expect(Direction::W->turnRight())->toBe(Direction::N);
        });
    });
    describe('When the nextPosition method is called with a position', function () {
        test('Then it should return the next position', function () {
            $startPosition = new Position(x:10, y:10);

            expect(Direction::N->nextPosition($startPosition))->toEqual(new Position(x: 10, y:11));
            expect(Direction::E->nextPosition($startPosition))->toEqual(new Position(x: 11, y:10));
            expect(Direction::S->nextPosition($startPosition))->toEqual(new Position(x: 10, y:9));
            expect(Direction::W->nextPosition($startPosition))->toEqual(new Position(x: 9, y:10));
        });
    });

});
