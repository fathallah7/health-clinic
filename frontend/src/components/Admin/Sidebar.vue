<script setup>
import { RouterLink } from 'vue-router'
import SidebarLinks from './SidebarLinks.vue'

const props = defineProps({
    isSidebarOpen: Boolean
})

const emit = defineEmits(['close-sidebar'])

function handleLinkClick() {
    if (window.innerWidth < 1024) {
        emit('close-sidebar')
    }
}
</script>

<template>
    <aside :class="[
        'h-screen fixed left-0 z-50 w-72 bg-gradient-to-b from-white via-gray-50 to-gray-100 text-gray-800 border-r border-gray-200 flex flex-col transition-all duration-300 ease-in-out',
        isSidebarOpen ? 'translate-x-0 shadow-xl' : '-translate-x-full shadow-none',
        isSidebarOpen ? 'top-16 lg:top-0' : 'top-0'
    ]">
        <!-- Sidebar Header -->
        <div
            class="relative flex items-center justify-center p-6 border-b  border-gray-200 bg-white/80 backdrop-blur-sm">
            <a href="#" class="flex items-center justify-center w-full group">
                <div class="relative flex items-center gap-3">
                    <!-- Icon with glow effect -->
                    <div class="relative">
                        <div
                            class="absolute inset-0 bg-blue-400/20 blur-xl rounded-full group-hover:bg-blue-500/30 transition-all duration-300">
                        </div>
                        <div
                            class="relative bg-gradient-to-br from-blue-500 to-blue-600 p-2.5 rounded-xl shadow-lg group-hover:scale-110 group-hover:rotate-3 transition-all duration-300">
                            <i class="fa-solid fa-hospital text-white text-xl"></i>
                        </div>
                    </div>

                    <!-- Title -->
                    <div class="flex flex-col">
                        <h1
                            class="text-xl font-bold bg-gradient-to-r from-gray-800 to-gray-600 bg-clip-text text-transparent group-hover:from-blue-600 group-hover:to-purple-600 transition-all duration-300">
                            Health Clinic
                        </h1>
                        <span class="text-xs text-gray-500 font-normal">Management System</span>
                    </div>
                </div>
            </a>
        </div>

        <!-- Sidebar Menu with custom scrollbar -->
        <div class="flex-1 overflow-hidden relative">
            <SidebarLinks @link-clicked="handleLinkClick" class="h-full overflow-y-auto custom-scrollbar" />
        </div>

        <!-- Decorative bottom gradient -->
        <div
            class="absolute bottom-0 left-0 right-0 h-32 bg-gradient-to-t from-gray-100 to-transparent pointer-events-none">
        </div>
    </aside>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');

/* Custom Scrollbar */
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: rgba(243, 244, 246, 0.5);
    border-radius: 10px;
    margin: 8px 0;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: linear-gradient(180deg, #3b82f6 0%, #2563eb 100%);
    border-radius: 10px;
    transition: background 0.3s;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(180deg, #60a5fa 0%, #3b82f6 100%);
}

/* Firefox */
.custom-scrollbar {
    scrollbar-width: thin;
    scrollbar-color: #3b82f6 rgba(243, 244, 246, 0.5);
}
</style>