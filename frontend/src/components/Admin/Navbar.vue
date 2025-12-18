<script setup lang="ts">
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
            <!-- Toggle Button -->
            <NavbarButton :isSidebarOpen="isSidebarOpen" @toggle-sidebar="$emit('toggle-sidebar')" />

            <!-- Right Section -->
            <div class="flex items-center gap-4">
                <!-- Search Bar -->
                <div class="hidden lg:flex items-center relative group">
                    <i
                        class="fa-solid fa-search absolute left-4 text-gray-400 text-sm group-hover:text-blue-500 transition-colors duration-300"></i>
                    <input type="text" placeholder="Search..."
                        class="pl-11 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:border-blue-500 focus:bg-white transition-all duration-300 w-64 focus:w-72 shadow-sm focus:shadow-md" />
                </div>

                <!-- User Menu -->
                <div
                    class="relative flex items-center gap-3 px-3 py-2 rounded-xl bg-gray-50 border border-gray-200 hover:bg-white hover:border-blue-400 hover:shadow-md transition-all duration-300 cursor-pointer group">
                    <!-- User Avatar -->
                    <div class="relative">
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
                </div>
            </div>
        </div>
    </header>
</template>

<style scoped>
/* Input focus glow */
input:focus {
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}
</style>