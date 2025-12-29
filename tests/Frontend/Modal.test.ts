import Modal from '@/components/Modal.vue';
import userEvent from '@testing-library/user-event';
import { render, screen } from '@testing-library/vue';

describe('Given a Modal component', () => {
    describe('when it is open', () => {
        test('it should show the heading "How to use this simulator"', () => {
            render(Modal, {
                props: { isOpen: true },
            });

            const headingText = 'How to use this simulator';
            const heading = screen.getByRole('heading', { name: headingText });

            expect(heading).toBeInTheDocument();
        });

        test('it should emit close when Close button is clicked', async () => {
            const user = userEvent.setup();
            const renderResult = render(Modal, {
                props: { isOpen: true },
            });
            const buttonText = /close/i;
            const closeButton = screen.getByRole('button', { name: buttonText });

            expect(closeButton).toBeInTheDocument();

            await user.click(closeButton);

            expect(renderResult.emitted().close).toBeTruthy();
        });
    });
});
