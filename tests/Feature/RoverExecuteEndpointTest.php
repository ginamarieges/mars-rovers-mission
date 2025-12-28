<?php

describe('Given the rover execute API endpoint', function () {
    describe('When it receives a valid payload with mixed-case commands and direction', function () {
        test('It should return the expected execution report', function () {
            $payload = [
                'initial' => [
                    'x' => 0,
                    'y' => 0,
                    'direction' => 'n',
                ],
                'commands' => 'fFrFf', // should normalize to FFRFF
                'obstacles' => [],
            ];

            $response = $this->postJson('/api/rover/execute', $payload);

            $response->assertOk();

            $response->assertJson([
                'position' => ['x' => 2, 'y' => 2],
                'direction' => 'E',
                'aborted' => false,
                'executedCommands' => 5,
                'obstacle' => null,
            ]);

            $response->assertJsonStructure([
                'position' => ['x', 'y'],
                'direction',
                'aborted',
                'executedCommands',
                'obstacle',
            ]);

        });
    });

    describe('When it receives a payload that would move into an obstacle', function () {
        test('It should stop before the obstacle and report the blocking position', function () {
            $payload = [
                'initial' => [
                    'x' => 0,
                    'y' => 0,
                    'direction' => 'N',
                ],
                'commands' => 'FFFF',
                'obstacles' => [
                    ['x' => 0, 'y' => 2],
                ],
            ];

            $response = $this->postJson('/api/rover/execute', $payload);

            $response->assertOk();

            $response->assertJson([
                'position' => ['x' => 0, 'y' => 1],
                'direction' => 'N',
                'aborted' => true,
                'executedCommands' => 1,
                'obstacle' => ['x' => 0, 'y' => 2],
            ]);
        });
    });

    describe('When it receives a payload that would move outside the grid', function () {
        test('It should abort and report the blocking outside position', function () {
            $payload = [
                'initial' => [
                    'x' => 0,
                    'y' => 0,
                    'direction' => 'S',
                ],
                'commands' => 'F',
                'obstacles' => [],
            ];

            $response = $this->postJson('/api/rover/execute', $payload);

            $response->assertOk();

            $response->assertJson([
                'position' => ['x' => 0, 'y' => 0],
                'direction' => 'S',
                'aborted' => true,
                'executedCommands' => 0,
                'obstacle' => ['x' => 0, 'y' => -1],
            ]);
        });
    });
});
