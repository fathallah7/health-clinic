<script setup lang="ts">
defineProps<{
    isSidebarOpen: boolean;
}>();
defineEmits<{
    (e: 'toggle-sidebar'): void;
}>();
</script>

<template>
    <button @click="$emit('toggle-sidebar')"
        class="relative p-3 rounded-xl bg-gray-50 border border-gray-200 hover:bg-gradient-to-br hover:from-blue-50 hover:to-purple-50 hover:border-blue-400 hover:shadow-md transition-all duration-300 group overflow-hidden">
        <!-- Animated background on hover -->
        <div
            class="absolute inset-0 bg-gradient-to-r from-blue-500/0 via-blue-500/10 to-blue-500/0 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-700">
        </div>

        <!-- Icon Container -->
        <div class="relative flex items-center justify-center w-6 h-6">
            <!-- Hamburger Icon -->
            <div :class="[
                'absolute transition-all duration-300 ease-in-out',
                isSidebarOpen ? 'opacity-0 rotate-90 scale-0' : 'opacity-100 rotate-0 scale-100'
            ]">
                <div class="flex flex-col gap-1.5">
                    <span
                        class="block w-6 h-0.5 bg-gray-600 rounded-full group-hover:bg-blue-500 transition-all duration-300 group-hover:w-7"></span>
                    <span
                        class="block w-5 h-0.5 bg-gray-600 rounded-full group-hover:bg-blue-500 transition-all duration-300 group-hover:w-7"></span>
                    <span
                        class="block w-6 h-0.5 bg-gray-600 rounded-full group-hover:bg-blue-500 transition-all duration-300 group-hover:w-7"></span>
                </div>
            </div>

            <!-- Close Icon (X) -->
            <div :class="[
                'absolute transition-all duration-300 ease-in-out',
                isSidebarOpen ? 'opacity-100 rotate-0 scale-100' : 'opacity-0 rotate-90 scale-0'
            ]">
                <div class="relative w-6 h-6">
                    <span
                        class="absolute top-1/2 left-0 w-6 h-0.5 bg-gray-600 rounded-full rotate-45 group-hover:bg-red-500 transition-all duration-300 group-hover:w-7"></span>
                    <span
                        class="absolute top-1/2 left-0 w-6 h-0.5 bg-gray-600 rounded-full -rotate-45 group-hover:bg-red-500 transition-all duration-300 group-hover:w-7"></span>
                </div>
            </div>
        </div>

        <!-- Ripple effect on click -->
        <span
            class="absolute inset-0 rounded-xl opacity-0 group-active:opacity-100 bg-blue-500/10 transition-opacity duration-150"></span>
    </button>
</template>

<style scoped>
/* Button press animation */
button:active {
    transform: scale(0.95);
}

/* Smooth icon transitions */
button div {
    backface-visibility: hidden;
    -webkit-backface-visibility: hidden;
}
</style>