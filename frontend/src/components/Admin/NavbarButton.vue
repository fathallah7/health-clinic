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
        class="p-4 text-white hover:bg-blue-800 rounded-lg transition-all duration-300 lg:p-2 animate-pulse-on-hover">
        <i :class="['fa-solid', isSidebarOpen ? 'fa-times' : 'fa-bars', 'text-xl']"></i>
    </button>
</template>

<style>
@keyframes pulse-on-hover {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}

.animate-pulse-on-hover:hover {
    animation: pulse-on-hover 0.4s ease-in-out;
}
</style>