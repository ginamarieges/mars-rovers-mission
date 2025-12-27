<?php

use App\Domain\Rover\ValueObjects\Position;
use App\Domain\Rover\World\Grid;

describe('Given a Grid world of 200x200', function () {
    describe('When checks a position inside the bounds', function () {
        test('Then it should consider it inside the grid', function () {
            $grid = new Grid(width: 200, height:200);

            expect($grid->isInside(new Position(x:0, y:0)))->toBeTrue();
            expect($grid->isInside(new Position(x:3, y:180)))->toBeTrue();
            expect($grid->isInside(new Position(x:199, y:199)))->toBeTrue();
        });
    });

     describe('When checks a position outside the bounds', function () {
        test('Then it should consider it outside the grid', function () {
            $grid = new Grid(width: 200, height:200);

            expect($grid->isInside(new Position(x:-1, y:0)))->toBeFalse();
            expect($grid->isInside(new Position(x:3, y:200)))->toBeFalse();
            expect($grid->isInside(new Position(x:200, y:199)))->toBeFalse();
        });
    });
});
