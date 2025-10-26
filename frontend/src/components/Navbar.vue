<script setup lang="ts">
import { RouterLink } from 'vue-router';
import NavbarButton from './NavbarButton.vue';
defineProps<{
    isSidebarOpen: boolean;
}>();
defineEmits<{
    (e: 'toggle-sidebar'): void;
}>();
</script>

<template>
    <header
        :class="['sticky top-0 z-50 flex border-b border-gray-200 bg-white', isSidebarOpen ? 'lg:ml-80' : 'lg:ml-0']">
        <div class="flex grow flex-col items-center justify-between lg:flex-row lg:px-6">

            <NavbarButton :isSidebarOpen="isSidebarOpen" @toggle-sidebar="$emit('toggle-sidebar')" />

            <!-- Right Section (User Menu) -->
            <div id="app-menu"
                class="hidden w-full items-center justify-between gap-4 px-5 py-4 lg:flex lg:justify-end lg:px-0">
                <div class="flex items-center gap-3">
                    <!-- User Menu -->
                    <div class="relative flex justify-center items-center">
                        <img src="../assets/logo3.webp" alt="User" class="h-12" />
                        <p class="text-sm mr-1 font-medium">Admin</p>
                    </div>
                </div>
            </div>
        </div>
    </header>
</template>