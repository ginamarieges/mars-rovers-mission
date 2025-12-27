<?php

use App\Domain\Rover\ValueObjects\Direction;
use App\Domain\Rover\ValueObjects\Position;
use App\Domain\Rover\RoverState;
use App\Domain\Rover\World\Grid;
use App\Domain\Rover\World\ObstacleMap;
use App\Domain\Rover\RoverCommandProcessor;

describe('Given a RoverCommandProcessor', function () {
    describe('When the execute method is called with a command sequence with no obstacles and no border collisions', function () {
        test('It should execute all commands and return the final rover state', function () {
            $processor = new RoverCommandProcessor();
            $grid = new Grid(width: 200, height: 200);
            $obstacleMap = ObstacleMap::fromArray([]);

            $initialState = new RoverState(
                position: new Position(x: 0, y: 0),
                direction: Direction::N
            );

            $report = $processor->execute(
                roverState: $initialState,
                commands: 'FFRFF',
                grid: $grid,
                obstacleMap: $obstacleMap
            );
            expect($report->aborted)->toBeFalse();
            expect($report->executedCommands)->toBe(5);
            expect($report->obstaclePosition)->toBeNull();
            expect($report->finalState->position)->toEqual(new Position(x: 2, y:2));
            expect($report->finalState->direction)->toBe(Direction::E);
        });
    });

    describe('When the execute method is called with a command sequencethat tries to move into an obstacle position', function () {
        test('It should stop before the obstacle and report the blocking', function () {
            $processor = new RoverCommandProcessor();
            $grid = new Grid(width: 200, height: 200);
            $obstacleMap = ObstacleMap::fromArray([
                ['x' => 0, 'y' => 2]
            ]);

            $initialState = new RoverState(
                position: new Position(x: 0, y: 0),
                direction: Direction::N
            );

            $report = $processor->execute(
                roverState: $initialState,
                commands: 'FFFF',
                grid: $grid,
                obstacleMap: $obstacleMap
            );
            expect($report->aborted)->toBeTrue();
            expect($report->executedCommands)->toBe(1);
            expect($report->obstaclePosition)->toEqual(new Position(x: 0, y: 2));
            expect($report->finalState->position)->toEqual(new Position(x: 0, y: 1));
            expect($report->finalState->direction)->toBe(Direction::N);
        });
    });

    describe('When the execute method is called with a command sequence that tries to move outside the world', function () {
        test('It should stop before the obstacle and report the blocking', function () {
            $processor = new RoverCommandProcessor();
            $grid = new Grid(width: 200, height: 200);
            $obstacleMap = ObstacleMap::fromArray([]);

            $initialState = new RoverState(
                position: new Position(x: 0, y: 0),
                direction: Direction::S
            );

            $report = $processor->execute(
                roverState: $initialState,
                commands: 'F',
                grid: $grid,
                obstacleMap: $obstacleMap
            );
            expect($report->aborted)->toBeTrue();
            expect($report->executedCommands)->toBe(0);
            expect($report->obstaclePosition)->toEqual(new Position(x: 0, y: -1));
            expect($report->finalState->position)->toEqual(new Position(x: 0, y: 0));
            expect($report->finalState->direction)->toBe(Direction::S);
        });
    });
});
