<?php

declare(strict_types=1);

namespace App\Domain\Rover;

use App\Domain\Rover\World\Grid;
use App\Domain\Rover\World\ObstacleMap;
/**
 * Handles the execution of a sequence of commands for a rover
 *
 * Receives an initial rover state, a list of commands, the world boundaires and the known obstacles
 * applies the commands and returns a report with the final outcome.
 */
final class RoverCommandProcessor
{
      /**
     * Executes the given command sequence starting from an initial rover state.
     *
     * The rover processes commands one by one. Before each move, the next position
     * is checked against the world boundaries and known obstacles.
     *
     * If a command would move the rover outside the grid or into an obstacle,
     * execution stops immediately and an aborted report is returned.
     */
    public function execute(RoverState $roverState, string $commands, Grid $grid, ObstacleMap $obstacleMap
    ): ExecutionReport {
        $currentState = $roverState;
        $executedCommands = 0;
        $usedCommands = '';

        foreach (str_split($commands) as $commandCharacter) {
            $nextDirection = $currentState->direction;

            // Determine how the command affects the rover
            switch ($commandCharacter) {
                case 'L':
                    $nextDirection = $currentState->direction->turnLeft();
                    break;

                case 'R':
                    $nextDirection = $currentState->direction->turnRight();
                    break;

                case 'F':
                    break;

                default:
                    continue 2;
            }
            // Calculate the next position based on the direction
            $nextPosition = $nextDirection->nextPosition($currentState->position);
            // Stop execution if the rover would leave the world
            if (!$grid->isInside($nextPosition)) {
                return new ExecutionReport(
                    finalState: $currentState,
                    aborted: true,
                    obstaclePosition: $nextPosition,
                    executedCommands: $executedCommands,
                    usedCommands: $usedCommands
                );
            }
            // Stop execution if the rover finds an obstacle
            if ($obstacleMap->has($nextPosition)) {
                return new ExecutionReport(
                    finalState: $currentState,
                    aborted: true,
                    obstaclePosition: $nextPosition,
                    executedCommands: $executedCommands,
                    usedCommands: $usedCommands
                );
            }
            // Apply the movement and direction change
            $currentState = $currentState
                ->withDirection(direction: $nextDirection)
                ->withPosition(position: $nextPosition);
            //Add the command to the number of commands executed and the used ones
            $executedCommands++;
            $usedCommands = $usedCommands . $commandCharacter;

        }
        // Returns de new execution report with the currentState, executed commands and usedCommands
        return new ExecutionReport(
            finalState: $currentState,
            aborted: false,
            obstaclePosition: null,
            executedCommands: $executedCommands,
            usedCommands: $usedCommands
        );
    }
}
