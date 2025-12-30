import RoverIndexPage from '@/pages/Rover/Index.vue';
import userEvent from '@testing-library/user-event';
import { render, screen } from '@testing-library/vue';
import { mockAbortedRoverExecution, mockSuccessfulRoverExecution } from './mocks/roverApiMocks';

describe('Given the Rover index page', () => {
    describe('when the user executes valid commands', () => {
        test('then it should call the API and clear the input', async () => {
            const user = userEvent.setup();
            mockSuccessfulRoverExecution();

            render(RoverIndexPage, {
                props: { gridSize: 20, worldSize: 200 },
            });

            const commandsInput = screen.getByPlaceholderText('Commands e.g. FFRFFL');
            await user.type(commandsInput, 'ffrff');

            await user.click(screen.getByRole('button', { name: /execute/i }));

            expect(globalThis.fetch).toHaveBeenCalledTimes(1);

            const fetchCallArguments = (globalThis.fetch as unknown as ReturnType<typeof vi.fn>).mock.calls[0];
            expect(fetchCallArguments[0]).toBe('/api/rover/execute');

            expect(commandsInput).toHaveValue('');
        });
    });
    describe('when the API reports an aborted execution,', () => {
        test('then it should show an aborted message', async () => {
            const user = userEvent.setup();
            mockAbortedRoverExecution();

            render(RoverIndexPage, {
                props: { gridSize: 20, worldSize: 200 },
            });

            const commandsInput = screen.getByPlaceholderText('Commands e.g. FFRFFL');
            await user.type(commandsInput, 'FF');

            await user.click(screen.getByRole('button', { name: /execute/i }));

            expect(await screen.findByText(/aborted/i)).toBeInTheDocument();
        });
    });
});
