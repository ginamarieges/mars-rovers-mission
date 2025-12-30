<template>
    <div
        v-if="props.isOpen"
        ref="modalContainerElement"
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        role="dialog"
        aria-modal="true"
        aria-label="How to use the Mars Rover Mission simulator"
        tabindex="0"
    >
        <div class="absolute inset-0 bg-black/60" aria-label="Close modal" @click="close"></div>

        <div class="relative z-10 max-h-[90vh] w-full max-w-2xl overflow-hidden rounded-xl bg-white shadow-lg">
            <div class="flex items-start justify-between gap-4 border-b px-4 py-3">
                <h2 class="text-lg font-semibold">How to use this simulator</h2>
                <button type="button" class="rounded px-2 py-1 text-sm text-gray-600 hover:bg-gray-100" @click="close">Close</button>
            </div>

            <div class="max-h-[calc(90vh-56px)] space-y-4 overflow-y-auto px-4 py-4 text-sm text-gray-700">
                <div>
                    <h3 class="font-semibold">What you’re doing</h3>
                    <p>
                        You’re guiding a rover around Mars. The world is a 200×200 grid (from <strong>0</strong> to <strong>199</strong> on both
                        axes). The rover starts at a position, facing a direction, and then follows your command sequence.
                    </p>
                </div>

                <div>
                    <h3 class="font-semibold">Commands</h3>
                    <p>Use these letters to control the rover:</p>
                    <ul class="mt-2 list-disc space-y-1 pl-5">
                        <li><strong>F</strong> — move one step forward</li>
                        <li><strong>L</strong> — move one step to the left</li>
                        <li><strong>R</strong> — move one step to the right</li>
                    </ul>
                </div>

                <div>
                    <h3 class="font-semibold">Placing obstacles</h3>
                    <p>
                        You can place obstacles on the grid by clicking cells. Clicking again removes the obstacle. You can’t place an obstacle on the
                        rover’s current position.
                    </p>
                </div>

                <div>
                    <h3 class="font-semibold">What happens if the rover is blocked</h3>
                    <p>
                        Before moving forward, the rover checks the next cell. If the next cell is blocked, the rover stops and the remaining commands
                        are ignored.
                    </p>
                    <ul class="mt-2 list-disc space-y-1 pl-5">
                        <li>If the next cell has an obstacle, the rover stops in the last safe cell.</li>
                        <li>If the next move would leave the world (past 0..199), the rover also stops.</li>
                    </ul>
                </div>

                <div>
                    <h3 class="font-semibold">Good to know</h3>
                    <p>
                        The grid on screen shows a “window” of the full world. As the rover travels, the view follows it so you don’t lose sight of
                        the rover.
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { nextTick, onBeforeUnmount, ref, watch } from 'vue';
const props = defineProps<{
    isOpen: boolean;
}>();
const modalContainerElement = ref<HTMLDivElement | null>(null);
const emit = defineEmits<{
    (event: 'close'): void;
}>();
function close(): void {
    emit('close');
}

function handleKeydown(event: KeyboardEvent): void {
    if (event.key === 'Escape') {
        close();
    }
}

watch(
    () => props.isOpen,
    async (isOpen) => {
        if (isOpen) {
            document.body.classList.add('overflow-hidden');

            await nextTick();
            modalContainerElement.value?.focus();
            window.addEventListener('keydown', handleKeydown);
        } else {
            document.body.classList.remove('overflow-hidden');
            window.removeEventListener('keydown', handleKeydown);
        }
    },
);

onBeforeUnmount(() => {
    document.body.classList.remove('overflow-hidden');
    window.removeEventListener('keydown', handleKeydown);
});
</script>
