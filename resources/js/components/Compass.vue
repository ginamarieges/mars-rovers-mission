<template>
    <!-- Compass -->
    <div class="fixed bottom-4 left-1/2 z-50 -translate-x-1/2 lg:right-8 lg:bottom-6 lg:left-auto lg:translate-x-0">
        <div class="relative h-24 w-24 md:h-28 md:w-28">
            <!-- Outer circle -->
            <div class="absolute inset-0 rounded-full border-2 border-black bg-white/70 shadow backdrop-blur-sm"></div>

            <!-- Triangle pointer (rotates with rover direction) -->
            <div class="absolute inset-0 flex items-start justify-center" :style="{ transform: `rotate(${roverRotationDegrees}deg)` }">
                <div class="mt-1 h-0 w-0 border-x-[10px] border-b-[16px] border-x-transparent border-b-red-600"></div>
            </div>

            <!-- Center rover image -->
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-white shadow md:h-14 md:w-14">
                    <img src="/images/mars-rover.png" alt="Rover" class="h-9 w-9 md:h-10 md:w-10" />
                </div>
            </div>

            <!-- Optional labels -->
            <div class="absolute top-1 left-1/2 -translate-x-1/2 text-[10px] font-semibold text-black"></div>
            <div class="absolute bottom-1 left-1/2 -translate-x-1/2 text-[10px] font-semibold text-black"></div>
            <div class="absolute top-1/2 left-1 -translate-y-1/2 text-[10px] font-semibold text-black"></div>
            <div class="absolute top-1/2 right-1 -translate-y-1/2 text-[10px] font-semibold text-black"></div>

            <!-- Current direction badge (optional) -->
            <div class="absolute -bottom-3 left-1/2 -translate-x-1/2 rounded bg-black px-2 py-0.5 text-[10px] font-semibold text-white">
                {{ roverDirectionLabel }}
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
const props = defineProps<{
    roverDirection: string;
}>();
const roverRotationDegrees = computed<number>(() => {
    if (props.roverDirection === 'N') return 0;
    if (props.roverDirection === 'E') return 90;
    if (props.roverDirection === 'S') return 180;
    return 270;
});

const roverDirectionLabel = computed<string>(() => {
    if (props.roverDirection === 'N') return 'N';
    if (props.roverDirection === 'E') return 'E';
    if (props.roverDirection === 'S') return 'S';
    return 'W';
});
</script>
