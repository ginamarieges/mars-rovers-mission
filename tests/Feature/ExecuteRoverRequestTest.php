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

});
