export type Direction = 'N' | 'E' | 'S' | 'W';

export type Position = {
    x: number;
    y: number;
};

export type Obstacle = Position;

export type ExecuteRoverResponse = {
    position: { x: number; y: number };
    direction: Direction;
    aborted: boolean;
    executedCommands: number;
    obstacle: { x: number; y: number } | null;
};

export type ViewportOrigin = { x: number; y: number };
