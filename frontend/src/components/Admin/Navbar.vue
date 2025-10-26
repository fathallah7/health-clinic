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
        :class="['sticky top-0 z-50 flex border-b border-gray-300 bg-gray-900 text-white', isSidebarOpen ? 'lg:ml-70' : 'lg:ml-0']">
        <div class="flex grow items-center justify-between lg:px-6">


            <!-- Toggle Button -->
            <NavbarButton :isSidebarOpen="isSidebarOpen" @toggle-sidebar="$emit('toggle-sidebar')" />

            <!-- Right Section (User Menu) -->
            <div id="app-menu" class="gap-4 px-5 py-4 lg:flex lg:justify-end lg:px-0">
                <!-- User Menu -->
                <div class="relative flex justify-center items-center group"
                    v-animate="{ animation: 'fade-in', duration: 500 }">
                    <div
                        class="absolute inset-0 rounded-full border-2 border-gray-300 transition-all duration-300">
                    </div>
                    <img src="../../assets/logo.jpg" alt="User"
                        class="h-12 rounded-full transition-transform duration-300 group-hover:scale-105" />
                    <p class="text-sm mr-2 font-medium">Admin</p>
                </div>
            </div>
        </div>
    </header>
</template>
