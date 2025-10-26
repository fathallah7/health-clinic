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
        'h-screen fixed left-0 z-50 w-70 bg-gray-900 text-white border-r border-gray-700 flex flex-col transition-transform duration-300 ease-in-out shadow-xl',
        isSidebarOpen ? 'translate-x-0' : '-translate-x-full',
        isSidebarOpen ? 'top-16 lg:top-0' : 'top-0'
    ]">
        <!-- Sidebar Header -->
        <div class="flex items-center justify-center p-6 border-b border-gray-700 ">
            <RouterLink to="/home" class="flex items-center justify-center w-full">
                <div class="relative">
                    <h1 class="text-2xl font-bold text-gray-200">Health Clinic</h1>
                </div>
            </RouterLink>
        </div>

        <!-- Sidebar Menu -->
        <SidebarLinks @link-clicked="handleLinkClick" />
    </aside>
</template>

<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap');

body {
    font-family: 'Poppins', sans-serif;
}
</style>