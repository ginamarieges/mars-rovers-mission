<script setup lang="ts">
import { Direction, Position } from '@/types/Rover/types';
import { computed, ref } from 'vue';

const props = defineProps<{
    gridSize: number;
    worldSize: number;
}>();

const roverPosition = ref<Position>({ x: 0, y: 0 });
const roverDirection = ref<Direction>('N');
const commandsInput = ref<string>('');
const obstacles = ref<Map<string, true>>(new Map());

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
    for (let y = props.gridSize - 1; y >= 0; y--) {
        for (let x = 0; x < props.gridSize; x++) {
            cells.push({ x, y });
        }
    }
    return cells;
});
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
                <button class="rounded bg-black px-4 py-2 text-white" type="button">Execute</button>
            </div>
            <div class="flex justify-center gap-4 text-gray-500">
                <span>F : Forward</span>
                <span>L : Left</span>
                <span>R : Right</span>
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
                    <span v-if="cell.x === roverPosition.x && cell.y === roverPosition.y" class="text-xl">
                        {{ roverArrow }}
                    </span>
                    <img v-else-if="isObstacle(cell)" src="/images/obstacle.png" alt="Obstacle" class="pointer-events-none h-15 w-15" />
                </button>
            </div>

            <p class="text-xs text-gray-500">Click cells to add obstacles</p>
        </section>
    </div>
</template>
