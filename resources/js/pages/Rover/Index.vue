<script setup lang="ts">
import Modal from '@/components/Modal.vue';
import { Direction, ExecuteRoverResponse, Obstacle, Position, ViewportOrigin } from '@/types/Rover/types';
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';

const props = defineProps<{
    gridSize: number;
    worldSize: number;
}>();

const roverPosition = ref<Position>({ x: 0, y: 0 });
const roverDirection = ref<Direction>('N');
const commandsInput = ref<string>('');
const obstacles = ref<Map<string, true>>(new Map());
const isExecuting = ref<boolean>(false);
const executionErrorMessage = ref<string | null>(null);
const abortedMessage = ref<string | null>(null);
const isMobile = computed<boolean>(() => viewportWidth.value < 768);
const isTablet = computed<boolean>(() => viewportWidth.value >= 768 && viewportWidth.value < 1280);
const viewportOrigin = ref<ViewportOrigin>({ x: 0, y: 0 });
const viewportWidth = ref<number>(window.innerWidth);
const isHowToModalOpen = ref<boolean>(false);

const displayGridColumns = computed<number>(() => {
    if (isMobile.value) return 10;
    if (isTablet.value) return 20;
    return 30;
});
const displayGridRows = computed<number>(() => {
    if (isMobile.value) return 10;
    if (isTablet.value) return 12;
    return 10;
});
const roverArrow = computed<string>(() => {
    if (roverDirection.value === 'N') return '↑';
    if (roverDirection.value === 'E') return '→';
    if (roverDirection.value === 'S') return '↓';
    return '←';
});
const gridCells = computed<Position[]>(() => {
    const cells: Position[] = [];

    for (let localY = displayGridRows.value - 1; localY >= 0; localY--) {
        for (let localX = 0; localX < displayGridColumns.value; localX++) {
            cells.push({
                x: viewportOrigin.value.x + localX,
                y: viewportOrigin.value.y + localY,
            });
        }
    }

    return cells;
});

function handleResize(): void {
    viewportWidth.value = window.innerWidth;
}

function worldBorderClasses(cell: Position): Record<string, boolean> {
    return {
        'border-l-2 border-black': cell.x === 0,
        'border-r-2 border-black': cell.x === props.worldSize - 1,
        'border-b-2 border-black': cell.y === 0,
        'border-t-2 border-black': cell.y === props.worldSize - 1,
    };
}

function openHowToModal(): void {
    isHowToModalOpen.value = true;
}

function closeHowToModal(): void {
    isHowToModalOpen.value = false;
}

function clamp(value: number, min: number, max: number): number {
    return Math.min(Math.max(value, min), max);
}

function centerViewportOn(position: Position): void {
    const halfColumns = Math.floor(displayGridColumns.value / 2);
    const halfRows = Math.floor(displayGridRows.value / 2);

    const maxOriginX = props.worldSize - displayGridColumns.value;
    const maxOriginY = props.worldSize - displayGridRows.value;

    viewportOrigin.value = {
        x: clamp(position.x - halfColumns, 0, maxOriginX),
        y: clamp(position.y - halfRows, 0, maxOriginY),
    };
}

centerViewportOn(roverPosition.value);

function positionKey(position: Position): string {
    return `${position.x},${position.y}`;
}

function isObstacle(position: Position): boolean {
    return obstacles.value.has(positionKey(position));
}

function toggleObstacle(position: Position): void {
    const key = positionKey(position);

    if (obstacles.value.has(key)) {
        obstacles.value.delete(key);
        return;
    }

    if (position.x === roverPosition.value.x && position.y === roverPosition.value.y) {
        return;
    }

    obstacles.value.set(key, true);
}

function obstaclesToArray(): Obstacle[] {
    const obstacleArray: Obstacle[] = [];

    for (const obstacleKey of obstacles.value.keys()) {
        const [xString, yString] = obstacleKey.split(',');
        obstacleArray.push({ x: Number(xString), y: Number(yString) });
    }

    return obstacleArray;
}

function normalizeCommandsForUserInput(rawCommands: string): string {
    return rawCommands.trim().toUpperCase();
}

async function executeCommands(): Promise<void> {
    abortedMessage.value = null;
    executionErrorMessage.value = null;

    const normalizedCommands = normalizeCommandsForUserInput(commandsInput.value);

    if (normalizedCommands.length === 0) {
        executionErrorMessage.value = 'Please enter at least one command (F, L, R).';
        return;
    }

    isExecuting.value = true;

    try {
        const response = await fetch('/api/rover/execute', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement | null)?.content ?? '',
            },
            body: JSON.stringify({
                initial: {
                    x: roverPosition.value.x,
                    y: roverPosition.value.y,
                    direction: roverDirection.value,
                },
                commands: normalizedCommands,
                obstacles: obstaclesToArray(),
            }),
        });

        if (!response.ok) {
            const responseBody = await response.json().catch(() => null);

            if (response.status === 422 && responseBody?.errors) {
                executionErrorMessage.value = Object.values(responseBody.errors).flat().join(' ');
                return;
            }

            executionErrorMessage.value = `Request failed with status ${response.status}.`;
            return;
        }

        const data = (await response.json()) as ExecuteRoverResponse;

        roverPosition.value = { x: data.position.x, y: data.position.y };
        roverDirection.value = data.direction;

        centerViewportOn(roverPosition.value);

        if (data.aborted) {
            abortedMessage.value = data.obstacle ? `Aborted: movement blocked at (${data.obstacle.x}, ${data.obstacle.y}).` : 'Aborted.';
        } else {
            abortedMessage.value = null;
        }

        commandsInput.value = '';
    } catch (error) {
        executionErrorMessage.value = 'Network error. Please try again.';
        console.error(error);
    } finally {
        isExecuting.value = false;
    }
}

onMounted(() => {
    window.addEventListener('resize', handleResize);
});

onBeforeUnmount(() => {
    window.removeEventListener('resize', handleResize);
});
</script>

<template>
    <div class="min-h-screen space-y-6 bg-linear-to-t from-[#f53b3b] to-[#f5975b] p-6 md:px-20 md:pb-20">
        <header>
            <h1 class="pt-4 text-center text-5xl font-semibold sm:text-6xl md:text-6xl">Mars Rover Mission</h1>
            <p class="text-center text-sm md:text-base">You are seeing {{ displayGridColumns }}×{{ displayGridRows }}. This world is 200×200.</p>
        </header>
        <section class="flex flex-col gap-3">
            <div class="flex flex-col items-center justify-center gap-3 md:flex-row">
                <input v-model="commandsInput" type="text" placeholder="Commands e.g. FFRFFL" class="w-full max-w-md rounded border px-3 py-2" />
                <button
                    class="rounded bg-black px-4 py-2 text-white disabled:opacity-50"
                    type="button"
                    :disabled="isExecuting"
                    @click="executeCommands"
                >
                    {{ isExecuting ? 'Executing…' : 'Execute' }}
                </button>
            </div>
            <div class="flex flex-col items-center gap-1">
                <p v-if="executionErrorMessage" class="text-sm text-red-600">
                    {{ executionErrorMessage }}
                </p>
                <p v-if="abortedMessage" class="text-sm text-amber-800">
                    {{ abortedMessage }}
                </p>
            </div>
            <div class="flex justify-center pt-2">
                <button
                    type="button"
                    class="rounded bg-white/80 px-4 py-2 text-sm font-medium text-black shadow hover:bg-white"
                    @click="openHowToModal"
                >
                    How to operate?
                </button>
            </div>
        </section>
        <section>
            <div class="text-sm text-gray-700">Rover position: ({{ roverPosition.x }}, {{ roverPosition.y }}) {{ roverDirection }}</div>
            <div class="mx-auto w-full">
                <div
                    class="grid gap-[1px]"
                    :style="{
                        gridTemplateColumns: `repeat(${displayGridColumns}, minmax(0, 1fr))`,
                    }"
                >
                    <button
                        v-for="cell in gridCells"
                        :key="`${cell.x}-${cell.y}`"
                        type="button"
                        class="flex aspect-square items-center justify-center rounded-sm border select-none"
                        :class="[
                            worldBorderClasses(cell),
                            {
                                'bg-transparent': true,
                                'outline outline-2 outline-blue-500': cell.x === roverPosition.x && cell.y === roverPosition.y,
                            },
                        ]"
                        @click="toggleObstacle(cell)"
                    >
                        <span v-if="cell.x === roverPosition.x && cell.y === roverPosition.y" class="text-lg md:text-xl">
                            {{ roverArrow }}
                        </span>
                        <img v-else-if="isObstacle(cell)" src="/images/obstacle.png" alt="Obstacle" class="pointer-events-none h-fit w-fit" />
                    </button>
                </div>
            </div>
        </section>
        <Modal :isOpen="isHowToModalOpen" @close="closeHowToModal" />
    </div>
</template>
