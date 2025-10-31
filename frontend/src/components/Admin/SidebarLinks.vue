<script setup>
import { onMounted } from 'vue'
import { RouterLink } from 'vue-router'

const emit = defineEmits(['link-clicked'])

function logout() {
    localStorage.removeItem('token')
    localStorage.removeItem('role')
}

onMounted(() => {
    // Dropdown toggle
    document.querySelectorAll('.dropdown-toggle').forEach((item) => {
        item.addEventListener('click', (e) => {
            e.preventDefault()
            const dropdown = item.nextElementSibling
            if (dropdown && dropdown.hasAttribute('data-dropdown')) {
                dropdown.classList.toggle('hidden')
            }
        })
    })
})
</script>

<template>
    <nav class="flex-1 p-6 overflow-y-auto">
        <!-- Menu Group -->
        <div>
            <h3 class="mb-4 text-sm font-semibold uppercase text-blue-200 tracking-widest">Main Menu</h3>
            <ul class="space-y-2">
                <!-- Menu Item Dashboard -->
                <li v-animate="{ animation: 'fade-in', duration: 500 }">
                    <RouterLink @click="$emit('link-clicked')" to="home"
                        class="flex items-center p-3 text-white rounded-lg transition-all duration-300 group relative hover:bg-gray-700/50 hover:shadow-lg animate-pulse-on-hover"
                        :class="{ 'border-l-4 border-yellow-300 bg-gray-700/50 shadow-lg': $route.path === '/home' }">
                        <i class="fa-solid fa-chart-line w-8 mr-3 text-blue-200 group-hover:text-gray-100 group-hover:scale-110 transition-transform duration-300"></i>
                        <span class="font-medium">Dashboard</span>
                    </RouterLink>
                </li>

                <!-- Menu Item Memberships -->
                <li v-animate="{ animation: 'fade-in', duration: 500, delay: 100 }">
                    <RouterLink @click="$emit('link-clicked')" to="memberships"
                        class="flex items-center p-3 text-white rounded-lg transition-all duration-300 group relative hover:bg-gray-700/50 hover:shadow-lg animate-pulse-on-hover"
                        :class="{ 'border-l-4 border-yellow-300 bg-gray-700/50 shadow-lg': $route.path === '/memberships' }">
                        <i class="fa-solid fa-id-card w-8 mr-3 text-blue-200 group-hover:text-gray-100 group-hover:scale-110 transition-transform duration-300"></i>
                        <span class="font-medium">Memberships</span>
                    </RouterLink>
                </li>

                <!-- Menu Item Members -->
                <li v-animate="{ animation: 'fade-in', duration: 500, delay: 200 }">
                    <RouterLink @click="$emit('link-clicked')" to="members"
                        class="flex items-center p-3 text-white rounded-lg transition-all duration-300 group relative hover:bg-gray-700/50 hover:shadow-lg animate-pulse-on-hover"
                        :class="{ 'border-l-4 border-yellow-300 bg-gray-700/50 shadow-lg': $route.path === '/members' }">
                        <i class="fa-solid fa-users w-8 mr-3 text-blue-200 group-hover:text-gray-100 group-hover:scale-110 transition-transform duration-300"></i>
                        <span class="font-medium">Members</span>
                    </RouterLink>
                </li>
            </ul>
        </div>

        <!-- System Group -->
        <div class="mt-6">
            <h3 class="mb-4 text-sm font-semibold uppercase text-blue-200 tracking-widest">System</h3>
            <ul class="space-y-2">
                <!-- Item Plans -->
                <li v-animate="{ animation: 'fade-in', duration: 500, delay: 500 }">
                    <RouterLink @click="$emit('link-clicked')" to="plans"
                        class="flex items-center p-3 text-white rounded-lg transition-all duration-300 group relative hover:bg-gray-700/50 hover:shadow-lg animate-pulse-on-hover"
                        :class="{ 'border-l-4 border-yellow-300 bg-gray-700/50 shadow-lg': $route.path === '/plans' }">
                        <i class="fa-solid fa-layer-group w-8 mr-3 text-blue-200 group-hover:text-gray-100 group-hover:scale-110 transition-transform duration-300"></i>
                        <span class="font-medium">Plans</span>
                    </RouterLink>
                </li>

                <!-- Item Invoices -->
                <li v-animate="{ animation: 'fade-in', duration: 500, delay: 600 }">
                    <RouterLink @click="$emit('link-clicked')" to="invoices"
                        class="flex items-center p-3 text-white rounded-lg transition-all duration-300 group relative hover:bg-gray-700/50 hover:shadow-lg animate-pulse-on-hover"
                        :class="{ 'border-l-4 border-yellow-300 bg-gray-700/50 shadow-lg': $route.path === '/invoices' }">
                        <i class="fa-solid fa-credit-card w-8 mr-3 text-blue-200 group-hover:text-gray-100 group-hover:scale-110 transition-transform duration-300"></i>
                        <span class="font-medium">Payments & Invoices</span>
                    </RouterLink>
                </li>
            </ul>
        </div>

        <!-- Logout -->
        <div class="mt-6 border-t border-gray-600 pt-4">
            <ul>
                <li v-animate="{ animation: 'fade-in', duration: 500, delay: 700 }">
                    <RouterLink to="/login" @click.prevent="logout"
                        class="flex items-center p-3 text-red-400 rounded-lg transition-all duration-300 group relative hover:bg-red-800/50 hover:shadow-lg animate-pulse-on-hover"
                        :class="{ 'border-l-4 border-red-400 bg-red-500': $route.path === '/login' }">
                        <i class="fa-solid fa-right-from-bracket w-8 mr-3 text-red-400 group-hover:text-white group-hover:scale-110 transition-transform duration-300"></i>
                        <span class="font-medium group-hover:text-white">Logout</span>
                    </RouterLink>
                </li>
            </ul>
        </div>
    </nav>
</template>

<style>
@keyframes fade-in {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

[v-animate] {
    animation: fade-in var(--animate-duration) ease-in-out;
    animation-delay: var(--animate-delay);
}

@keyframes pulse-on-hover {
    0% { transform: scale(1); }
    50% { transform: scale(1.02); }
    100% { transform: scale(1); }
}

.animate-pulse-on-hover:hover {
    animation: pulse-on-hover 0.4s ease-in-out;
}
</style>