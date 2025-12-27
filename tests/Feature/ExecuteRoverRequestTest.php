<?php

use App\Http\Requests\ExecuteRoverRequest;
use Illuminate\Support\Facades\Route;

beforeEach(function () {
    Route::post('/_tests/rover/execute', function (ExecuteRoverRequest $request) {
        return response()->json($request->validated());
    });
});

describe('Given an ExecuteRoverRequest FormRequest', function () {
    describe('when it receives a payload with lowercase direction and mixed-case commands', function() {
        test('It should return 200 and normalize direction and commands to uppercase', function () {
            $payload = [
                'initial' => ['x' => 0, 'y' => 0, 'direction' => 'n'],
                'commands' => 'ffrLrf',
                'obstacles' => [['x' => 10, 'y' => 10]]
            ];

            test()
            ->postJson('/_tests/rover/execute', $payload)
            ->assertOk()
            ->assertJsonPath('initial.direction', 'N')
            ->assertJsonPath('commands', 'FFRLRF');

        });
    });

    describe('When it receives a payload with an invalid command character', function () {
        test('It should return 422 with a validation error for commands', function () {
            $payload = [
                'initial' => ['x' => 0, 'y' => 0, 'direction' => 'N'],
                'commands' => 'fxfrLrf',
                'obstacles' => [['x' => 10, 'y' => 10]]
            ];

            test()
            ->postJson('/_tests/rover/execute', $payload)
            ->assertStatus(422)
            ->assertJsonValidationErrors(['commands']);
        });
    });

    describe('When it receives a payload with out of range initial coordinates', function () {
        test('It should return 422 with a validation errors for initial.x and initial.y', function () {
            $payload = [
                'initial' => ['x' => 200, 'y' => -1, 'direction' => 'N'],
                'commands' => 'ffrLrf',
                'obstacles' => [['x' => 10, 'y' => 10]]
            ];

            test()
            ->postJson('/_tests/rover/execute', $payload)
            ->assertStatus(422)
            ->assertJsonValidationErrors(['initial.x', 'initial.y']);
        });
    });

    describe('When it receives a payload with an obstacle on the rover initial position', function () {
        test('It should return 422 with a validation errors for obstacles', function () {
            $payload = [
                'initial' => ['x' => 3, 'y' => 1, 'direction' => 'N'],
                'commands' => 'ffrLrf',
                'obstacles' => [['x' => 3, 'y' => 1]]
            ];

            test()
            ->postJson('/_tests/rover/execute', $payload)
            ->assertStatus(422)
            ->assertJsonValidationErrors(['obstacles']);
        });
    });

});
