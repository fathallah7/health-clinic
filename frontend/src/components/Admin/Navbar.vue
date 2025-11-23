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
    <header :class="[
        'sticky top-0 z-40 flex bg-white text-gray-800 transition-all duration-300',
        isSidebarOpen ? 'lg:ml-72' : 'lg:ml-0'
    ]">

        <div class="flex grow items-center justify-between px-4 lg:px-8 py-4 border-b border-gray-200">
            <!-- Left Section -->
            <div class="flex items-center gap-4">
                <!-- Toggle Button -->
                <NavbarButton :isSidebarOpen="isSidebarOpen" @toggle-sidebar="$emit('toggle-sidebar')" />

                <!-- Breadcrumb or Title -->
                <div class="hidden md:flex items-center gap-2 text-gray-500">
                    <i class="fa-solid fa-house text-sm"></i>
                    <span class="text-sm">/</span>
                    <span class="text-sm font-medium text-gray-700">Dashboard</span>
                </div>
            </div>

            <!-- Right Section -->
            <div class="flex items-center gap-4">
                <!-- Search Bar -->
                <div class="hidden lg:flex items-center relative group">
                    <i
                        class="fa-solid fa-search absolute left-4 text-gray-400 text-sm group-hover:text-blue-500 transition-colors duration-300"></i>
                    <input type="text" placeholder="Search..."
                        class="pl-11 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:border-blue-500 focus:bg-white transition-all duration-300 w-64 focus:w-72 shadow-sm focus:shadow-md" />
                </div>

                <!-- Notifications -->
                <button
                    class="relative p-2.5 rounded-xl bg-gray-50 border border-gray-200 hover:bg-white hover:border-blue-400 hover:shadow-md transition-all duration-300 group">
                    <i
                        class="fa-solid fa-bell text-gray-600 group-hover:text-blue-500 transition-colors duration-300"></i>
                    <span
                        class="absolute -top-1 -right-1 w-5 h-5 bg-gradient-to-br from-red-500 to-red-600 rounded-full text-xs flex items-center justify-center font-bold text-white shadow-lg animate-pulse">
                        3
                    </span>
                </button>

                <!-- User Menu -->
                <div
                    class="relative flex items-center gap-3 px-3 py-2 rounded-xl bg-gray-50 border border-gray-200 hover:bg-white hover:border-blue-400 hover:shadow-md transition-all duration-300 cursor-pointer group">
                    <!-- User Avatar -->
                    <div class="relative">
                        <!-- Glow effect -->
                        <div
                            class="absolute inset-0 bg-blue-400/20 blur-md rounded-full group-hover:bg-blue-500/30 transition-all duration-300">
                        </div>

                        <!-- Avatar -->
                        <div class="relative">
                            <img src="../../assets/logo.jpg" alt="Admin"
                                class="h-10 w-10 rounded-full border-2 border-gray-300 group-hover:border-blue-500 transition-all duration-300 object-cover" />
                            <!-- Online status indicator -->
                            <span
                                class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 rounded-full border-2 border-white"></span>
                        </div>
                    </div>

                    <!-- User Info -->
                    <div class="hidden md:flex flex-col">
                        <span
                            class="text-sm font-semibold text-gray-700 group-hover:text-gray-900 transition-colors duration-300">Admin</span>
                        <span class="text-xs text-gray-500">Administrator</span>
                    </div>

                    <!-- Dropdown Arrow -->
                    <i
                        class="fa-solid fa-chevron-down text-xs text-gray-500 group-hover:text-blue-500 transition-all duration-300 group-hover:translate-y-0.5"></i>
                </div>
            </div>
        </div>
    </header>
</template>

<style scoped>
/* Notification pulse animation */
@keyframes pulse {

    0%,
    100% {
        opacity: 1;
        transform: scale(1);
    }

    50% {
        opacity: 0.8;
        transform: scale(1.05);
    }
}

/* Input focus glow */
input:focus {
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}
</style>