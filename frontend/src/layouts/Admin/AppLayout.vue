<script setup>
import { ref } from 'vue';
import Sidebar from '@/components/Admin/Sidebar.vue';
import Navbar from '@/components/Admin/Navbar.vue';

// Reactive state for sidebar visibility
const isSidebarOpen = ref(true);

// Function to toggle sidebar
const toggleSidebar = () => {
    isSidebarOpen.value = !isSidebarOpen.value;
};

// Function to close sidebar
const closeSidebar = () => {
    isSidebarOpen.value = false;
};
</script>

<template>
    <div class="flex h-screen">
        <!-- Sidebar -->
        <Sidebar :isSidebarOpen="isSidebarOpen" @close-sidebar="closeSidebar" />

        <!-- Main Content -->
        <div class="flex-1 flex flex-col">
            <!-- Navbar -->
            <Navbar :isSidebarOpen="isSidebarOpen" @toggle-sidebar="toggleSidebar" />

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto bg-gradient-to-b from-gray-100 to-blue-50 transition-all duration-300"
                :class="[isSidebarOpen ? 'lg:ml-70' : 'lg:ml-0']">
                <div class="min-h-screen p-6 max-w-8xl mx-auto" v-animate="{ animation: 'fade-in', duration: 500 }">
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <slot />
                    </div>
                </div>
            </main>
        </div>
    </div>
</template>

<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap');

body {
    font-family: 'Poppins', sans-serif;
}

@keyframes fade-in {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

[v-animate] {
    animation: fade-in var(--animate-duration) ease-in-out;
}

main {
    scroll-behavior: smooth;
}
</style>