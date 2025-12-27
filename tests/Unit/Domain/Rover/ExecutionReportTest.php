<?php

declare(strict_types=1);

use App\Domain\Rover\ExecutionReport;
use App\Domain\Rover\RoverState;
use App\Domain\Rover\ValueObjects\Direction;
use App\Domain\Rover\ValueObjects\Position;

describe('Given an ExecutionReport', function () {
    describe('When it is created for a successful execution', function () {
        test('It should require obstaclePosition to be null', function () {
            $finalState = new RoverState(
                position: new Position(x: 1, y: 2),
                direction: Direction::N
            );

            $report = new ExecutionReport(
                finalState: $finalState,
                aborted: false,
                obstaclePosition: null,
                executedCommands: 5
            );

            expect($report->aborted)->toBeFalse();
            expect($report->obstaclePosition)->toBeNull();
        });
    });
    describe('When it is created for an aborted execution', function () {
        test('It should require obstaclePosition to be provided', function () {
            $finalState = new RoverState(
                position: new Position(x: 1, y: 2),
                direction: Direction::N
            );

            $report = new ExecutionReport(
                finalState: $finalState,
                aborted: true,
                obstaclePosition: new Position(x: 0, y: -1),
                executedCommands: 0
            );

            expect($report->aborted)->toBeTrue();
            expect($report->obstaclePosition)->toEqual(new Position(x: 0, y: -1));
        });
    });

    describe('When executedCommands is negative', function () {
        test('It should throw an InvalidArgumentException', function () {
            $finalState = new RoverState(
                position: new Position(x: 1, y: 2),
                direction: Direction::N
            );

            expect(fn () => new ExecutionReport(
                finalState: $finalState,
                aborted: false,
                obstaclePosition: null,
                executedCommands: -1
            ))->toThrow(InvalidArgumentException::class);
        });
    });

    describe('When aborted is false but obstaclePosition is provided', function () {
        test('It should throw an InvalidArgumentException', function () {
            $finalState = new RoverState(
                position: new Position(x: 1, y: 2),
                direction: Direction::N
            );

            expect(fn () => new ExecutionReport(
                finalState: $finalState,
                aborted: false,
                obstaclePosition: new Position(x: 5, y: 5),
                executedCommands: 2
            ))->toThrow(InvalidArgumentException::class);
        });
    });
});
