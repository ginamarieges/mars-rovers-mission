export function mockSuccessfulRoverExecution(): void {
    globalThis.fetch = vi.fn(async () => {
        return new Response(
            JSON.stringify({
                position: { x: 2, y: 2 },
                direction: 'E',
                aborted: false,
                executedCommands: 5,
                obstacle: null,
            }),
            {
                status: 200,
                headers: { 'Content-Type': 'application/json' },
            },
        );
    }) as unknown as typeof fetch;
}

export function mockAbortedRoverExecution(): void {
    globalThis.fetch = vi.fn(async () => {
        return new Response(
            JSON.stringify({
                position: { x: 1, y: 1 },
                direction: 'N',
                aborted: true,
                executedCommands: 2,
                obstacle: { x: 1, y: 2 },
            }),
            {
                status: 200,
                headers: { 'Content-Type': 'application/json' },
            },
        );
    }) as unknown as typeof fetch;
}
