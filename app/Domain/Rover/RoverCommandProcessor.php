<?php

declare(strict_types=1);

namespace App\Domain\Rover;

use App\Domain\Rover\World\Grid;
use App\Domain\Rover\World\ObstacleMap;

final class RoverCommandProcessor {
    public function execute(
        RoverState $roverState,
        string $commands,
        Grid $grid,
        ObstacleMap $obstacleMap
    ): ExecutionReport {
        $currentState = $roverState;
        $executedCommands = 0;

        foreach(str_split($commands) as $commandCharacter) {
            if ($commandCharacter === 'L') {
                $currentState = $currentState->withDirection(
                    direction: $currentState->direction->turnLeft()
                );
                $executedCommands++;
                continue;
            }
            if ($commandCharacter === 'R') {
                $currentState = $currentState->withDirection(
                    direction: $currentState->direction->turnRight()
                );
                $executedCommands++;
                continue;
            }
            $nextPosition = $currentState->direction->nextPosition($currentState->position);

            if(!$grid->isInside($nextPosition)) {
                return new ExecutionReport(
                    finalState: $currentState,
                    aborted: true,
                    obstaclePosition: $nextPosition,
                    executedCommands: $executedCommands
                );
            }
            if($obstacleMap->has($nextPosition)) {
                 return new ExecutionReport(
                    finalState: $currentState,
                    aborted: true,
                    obstaclePosition: $nextPosition,
                    executedCommands: $executedCommands
                );
            }
            $currentState = $currentState->withPosition(position: $nextPosition);
            $executedCommands++;
        }

        return new ExecutionReport(
            finalState: $currentState,
            aborted: false,
            obstaclePosition: null,
            executedCommands: $executedCommands
        );
    }
}
