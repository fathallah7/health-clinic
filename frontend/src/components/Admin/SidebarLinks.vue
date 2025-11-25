<script setup>
import { onMounted } from 'vue'
import { RouterLink } from 'vue-router'

const emit = defineEmits(['link-clicked'])

function logout() {
    localStorage.removeItem('token')
    localStorage.removeItem('role')
}

onMounted(() => {
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
    <nav class="flex-1 p-4 overflow-y-auto">
        <!-- Menu Group -->
        <div class="mb-8">
            <div class="flex items-center gap-2 mb-4 px-3">
                <div class="h-0.5 w-8 bg-gradient-to-r from-blue-500 to-transparent rounded-full"></div>
                <h3 class="text-xs font-bold uppercase text-blue-600 tracking-wider">Main Menu</h3>
            </div>

            <ul class="space-y-1.5">
                <!-- Dashboard -->
                <li class="menu-item" style="--delay: 0s">
                    <RouterLink @click="$emit('link-clicked')" to="/home" class="menu-link group"
                        :class="{ 'active': $route.path === '/home' }">
                        <div class="icon-wrapper">
                            <i class="fa-solid fa-chart-line text-lg"></i>
                        </div>
                        <span class="link-text">Dashboard</span>
                        <div class="link-indicator"></div>
                    </RouterLink>
                </li>

                <!-- Availability -->
                <li class="menu-item" style="--delay: 0.05s">
                    <RouterLink @click="$emit('link-clicked')" to="/availability" class="menu-link group"
                        :class="{ 'active': $route.path === '/availability' }">
                        <div class="icon-wrapper">
                            <i class="fa-solid fa-calendar-days text-xl"></i>
                        </div>
                        <span class="link-text">Availability</span>
                        <div class="link-indicator"></div>
                    </RouterLink>
                </li>

                <!-- Time Slots -->
                <li class="menu-item" style="--delay: 0.1s">
                    <RouterLink @click="$emit('link-clicked')" to="/timeslots" class="menu-link group"
                        :class="{ 'active': $route.path === '/timeslots' }">
                        <div class="icon-wrapper">
                            <i class="fa-solid fa-clock text-lg"></i>
                        </div>
                        <span class="link-text">Time Slots</span>
                        <div class="link-indicator"></div>
                    </RouterLink>
                </li>

                <!-- Appointments -->
                <li class="menu-item" style="--delay: 0.1s">
                    <RouterLink @click="$emit('link-clicked')" to="/appointments" class="menu-link group"
                        :class="{ 'active': $route.path === '/appointments' }">
                        <div class="icon-wrapper">
                            <i class="fa-solid fa-calendar-check text-lg"></i>
                        </div>
                        <span class="link-text">Appointments</span>
                        <div class="link-indicator"></div>
                    </RouterLink>
                </li>
            </ul>
        </div>

        <!-- System Group -->
        <div class="mb-8">
            <div class="flex items-center gap-2 mb-4 px-3">
                <div class="h-0.5 w-8 bg-gradient-to-r from-purple-500 to-transparent rounded-full"></div>
                <h3 class="text-xs font-bold uppercase text-purple-600 tracking-wider">System</h3>
            </div>

            <ul class="space-y-1.5">
                <!-- Products -->
                <li class="menu-item" style="--delay: 0.15s">
                    <RouterLink @click="$emit('link-clicked')" to="/products" class="menu-link group"
                        :class="{ 'active': $route.path === '/products' }">
                        <div class="icon-wrapper">
                            <i class="fa-solid fa-box-open text-lg"></i>
                        </div>
                        <span class="link-text">Products</span>
                        <div class="link-indicator"></div>
                    </RouterLink>
                </li>

                <!-- Orders -->
                <li class="menu-item" style="--delay: 0.2s">
                    <RouterLink @click="$emit('link-clicked')" to="/orders" class="menu-link group"
                        :class="{ 'active': $route.path === '/orders' }">
                        <div class="icon-wrapper">
                            <i class="fa-solid fa-shopping-cart text-lg"></i>
                        </div>
                        <span class="link-text">Orders</span>
                        <div class="link-indicator"></div>
                    </RouterLink>
                </li>
            </ul>
        </div>

        <!-- Logout Section -->
        <div class="mt-auto pt-4 border-t border-gray-200">
            <ul>
                <li class="menu-item" style="--delay: 0.3s">
                    <RouterLink to="/login" @click.prevent="logout" class="menu-link logout-link group">
                        <div class="icon-wrapper logout-icon">
                            <i class="fa-solid fa-right-from-bracket text-lg"></i>
                        </div>
                        <span class="link-text">Logout</span>
                        <div class="link-indicator logout-indicator"></div>
                    </RouterLink>
                </li>
            </ul>
        </div>
    </nav>
</template>

<style scoped>
/* Menu Items Animation */
.menu-item {
    opacity: 0;
    animation: slideIn 0.4s ease-out forwards;
    animation-delay: var(--delay);
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateX(-10px);
    }

    to {
        opacity: 1;
        transform: translateX(0);
    }
}

/* Menu Link Base */
.menu-link {
    position: relative;
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    border-radius: 12px;
    color: #374151;
    font-weight: 500;
    font-size: 0.9rem;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    overflow: hidden;
    background: transparent;
}

.menu-link::before {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.08) 0%, rgba(147, 51, 234, 0.05) 100%);
    opacity: 0;
    transition: opacity 0.3s ease;
    border-radius: 12px;
}

.menu-link:hover::before {
    opacity: 1;
}

.menu-link:hover {
    color: #1f2937;
    transform: translateX(4px);
    background: rgba(249, 250, 251, 0.8);
}

/* Active State */
.menu-link.active {
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.12) 0%, rgba(147, 51, 234, 0.08) 100%);
    color: #1f2937;
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
}

.menu-link.active .link-indicator {
    opacity: 1;
    transform: scaleY(1);
}

.menu-link.active .icon-wrapper {
    background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
    color: white;
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

/* Icon Wrapper */
.icon-wrapper {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: rgba(249, 250, 251, 0.8);
    color: #3b82f6;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    flex-shrink: 0;
    border: 1px solid rgba(229, 231, 235, 0.5);
}

.menu-link:hover .icon-wrapper {
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.15) 0%, rgba(147, 51, 234, 0.1) 100%);
    color: #2563eb;
    transform: scale(1.05) rotate(5deg);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);
    border-color: rgba(59, 130, 246, 0.3);
}

/* Link Text */
.link-text {
    flex: 1;
    font-weight: 500;
}

/* Link Indicator */
.link-indicator {
    position: absolute;
    left: 0;
    top: 50%;
    transform: translateY(-50%) scaleY(0);
    width: 4px;
    height: 60%;
    background: linear-gradient(180deg, #3b82f6 0%, #8b5cf6 100%);
    border-radius: 0 4px 4px 0;
    opacity: 0;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Logout Specific Styles */
.logout-link {
    color: #dc2626;
}

.logout-link:hover {
    background: rgba(254, 226, 226, 0.5);
    color: #b91c1c;
}

.logout-link.active {
    background: rgba(254, 226, 226, 0.7);
}

.logout-icon {
    background: rgba(254, 226, 226, 0.5);
    color: #dc2626;
    border-color: rgba(254, 202, 202, 0.5);
}

.logout-link:hover .logout-icon {
    background: linear-gradient(135deg, rgba(220, 38, 38, 0.15) 0%, rgba(185, 28, 28, 0.1) 100%);
    color: #b91c1c;
    box-shadow: 0 4px 12px rgba(220, 38, 38, 0.2);
    border-color: rgba(220, 38, 38, 0.3);
}

.logout-link.active .logout-icon {
    background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
    color: white;
    box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
}

.logout-indicator {
    background: linear-gradient(180deg, #dc2626 0%, #b91c1c 100%);
}

/* Responsive adjustments */
@media (max-width: 1024px) {
    .menu-link {
        padding: 10px 14px;
    }

    .icon-wrapper {
        width: 36px;
        height: 36px;
    }
}
</style>