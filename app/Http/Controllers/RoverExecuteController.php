<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domain\Rover\ExecutionReport;
use App\Domain\Rover\RoverCommandProcessor;
use App\Domain\Rover\RoverState;
use App\Domain\Rover\ValueObjects\Direction;
use App\Domain\Rover\ValueObjects\Position;
use App\Domain\Rover\World\Grid;
use App\Domain\Rover\World\ObstacleMap;
use App\Http\Requests\ExecuteRoverRequest;
use Illuminate\Http\JsonResponse;

final class RoverExecuteController extends Controller {
    public function __invoke(ExecuteRoverRequest $request, RoverCommandProcessor $roverCommandProcessor): JsonResponse {
        $validatedPayload = $request->validated();
        $grid = new Grid(width: 200, height: 200);
        $obstacleMap = ObstacleMap::fromArray($validatedPayload['obstacles'] ?? []);
        $initialState = new RoverState(
            position: new Position(
                x: $validatedPayload['initial']['x'],
                y: $validatedPayload['initial']['y']
            ),
            direction: Direction::from($validatedPayload['initial']['direction'])
        );

        $executionReport = $roverCommandProcessor->execute(
            roverState: $initialState,
            commands: $validatedPayload['commands'],
            grid: $grid,
            obstacleMap: $obstacleMap
        );

        return response()->json($this->executionReportToArray($executionReport));
    }

    private function executionReportToArray(ExecutionReport $executionReport): array {
        $obstaclePosition = $executionReport->obstaclePosition;

        return [
            'position' => [
                'x' => $executionReport->finalState->position->x,
                'y' => $executionReport->finalState->position->y,
            ],
            'direction' => $executionReport->finalState->direction->value,
            'aborted' => $executionReport->aborted,
            'executedCommands' => $executionReport->executedCommands,
            'obstacle' => $obstaclePosition === null ? null : ['x' => $obstaclePosition->x, 'y' => $obstaclePosition->y],
        ];
    }
}
