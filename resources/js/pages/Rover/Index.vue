<script setup lang="ts">
import Modal from '@/components/Modal.vue';
import { Direction, ExecuteRoverResponse, Obstacle, Position, ViewportOrigin } from '@/types/Rover/types';
import { computed, nextTick, ref } from 'vue';

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
const viewportOrigin = ref<ViewportOrigin>({ x: 0, y: 0 });
const isHowToModalOpen = ref<boolean>(false);
const howToModalContainerElement = ref<HTMLDivElement | null>(null);

function centerViewportOn(position: Position): void {
    const halfViewport = Math.floor(props.gridSize / 2);

    const maxOriginX = props.worldSize - props.gridSize;
    const maxOriginY = props.worldSize - props.gridSize;

    viewportOrigin.value = {
        x: clamp(position.x - halfViewport, 0, maxOriginX),
        y: clamp(position.y - halfViewport, 0, maxOriginY),
    };
}

centerViewportOn(roverPosition.value);

async function openHowToModal(): Promise<void> {
    isHowToModalOpen.value = true;
    await nextTick();
    howToModalContainerElement.value?.focus();
}

function closeHowToModal(): void {
    isHowToModalOpen.value = false;
    document.body.classList.remove('overflow-hidden');
}

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

const roverArrow = computed(() => {
    if (roverDirection.value === 'N') return '↑';
    if (roverDirection.value === 'E') return '→';
    if (roverDirection.value === 'S') return '↓';
    return '←';
});

const gridCells = computed(() => {
    const cells: Position[] = [];

    for (let localY = props.gridSize - 1; localY >= 0; localY--) {
        for (let localX = 0; localX < props.gridSize; localX++) {
            cells.push({
                x: viewportOrigin.value.x + localX,
                y: viewportOrigin.value.y + localY,
            });
        }
    }

    return cells;
});

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

        commandsInput.value = normalizedCommands;
    } catch (error) {
        executionErrorMessage.value = 'Network error. Please try again.';
        console.error(error);
    } finally {
        isExecuting.value = false;
    }
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

function clamp(value: number, min: number, max: number): number {
    return Math.min(Math.max(value, min), max);
}
</script>

<template>
    <div class="space-y-6 bg-linear-to-t from-[#f53b3b] to-[#f5975b] p-6">
        <header class="space-y-1">
            <h1 class="pt-10 text-center text-7xl font-semibold">Mars Rover Mission</h1>
            <p class="text-center">You are seeing {{ props.gridSize }}×{{ props.gridSize }}...This world is 200x200</p>
        </header>

        <section class="flex flex-col gap-2">
            <div class="flex items-center justify-center gap-3">
                <input v-model="commandsInput" type="text" placeholder="Commands e.g. FFRFFL" class="w-full max-w-md rounded border px-3 py-2" />
                <p v-if="executionErrorMessage" class="text-sm text-red-600">
                    {{ executionErrorMessage }}
                </p>

                <p v-if="abortedMessage" class="text-sm text-amber-700">
                    {{ abortedMessage }}
                </p>
                <button class="rounded bg-black px-4 py-2 text-white" type="button" :disabled="isExecuting" @click="executeCommands">Execute</button>
            </div>
            <div class="flex justify-center pt-4">
                <button
                    type="button"
                    class="rounded bg-white/80 px-4 py-2 text-sm font-medium text-black shadow hover:bg-white"
                    @click="openHowToModal"
                >
                    How to opperate?
                </button>
            </div>
        </section>
        <section class="space-y-2">
            <div class="text-sm text-gray-600">Rover position: ({{ roverPosition.x }}, {{ roverPosition.y }}) {{ roverDirection }}</div>

            <div class="grid gap-0.5" :style="{ gridTemplateColumns: `repeat(${props.gridSize}, minmax(0, 1fr))` }">
                <button
                    v-for="cell in gridCells"
                    :key="`${cell.x}-${cell.y}`"
                    type="button"
                    class="flex aspect-square items-center justify-center rounded-sm border text-xs select-none"
                    :class="{
                        'bg-transparent': true,
                        'outline outline-2 outline-blue-500': cell.x === roverPosition.x && cell.y === roverPosition.y,
                    }"
                    @click="toggleObstacle(cell)"
                >
                    <span v-if="cell.x === roverPosition.x && cell.y === roverPosition.y" class="md:text-xl">
                        {{ roverArrow }}
                    </span>
                    <img v-else-if="isObstacle(cell)" src="/images/obstacle.png" alt="Obstacle" class="pointer-events-none h-fit w-fit" />
                </button>
            </div>

            <p class="text-xs text-gray-500">Click cells to add obstacles</p>
        </section>
        <Modal :isOpen="isHowToModalOpen" @close="closeHowToModal" />
    </div>
</template>
