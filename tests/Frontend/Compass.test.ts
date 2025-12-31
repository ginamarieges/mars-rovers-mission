import Compass from '@/components/Compass.vue';
import { render, screen } from '@testing-library/vue';

function renderCompass(roverDirection: 'N' | 'E' | 'S' | 'W') {
    return render(Compass, {
        props: { roverDirection },
    });
}
describe('Given a Compass component', () => {
    describe('when it is rendered with the rover direction N', () => {
        test('it should show the letter N in the roverDirectionLabel', () => {
            renderCompass('N');

            expect(screen.getByText('N')).toBeInTheDocument();
        });

        test('it should show the image of the rover', async () => {
            renderCompass('N');
            expect(screen.getByAltText('Rover')).toBeInTheDocument();
        });
    });
});
