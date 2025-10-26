<script setup>
import { onMounted } from 'vue';

onMounted(() => {
    // Dropdown toggle
    document.querySelectorAll('.flex.items-center.p-2').forEach(item => {
        item.addEventListener('click', (e) => {
            e.preventDefault();
            const dropdown = item.nextElementSibling;
            if (dropdown && dropdown.hasAttribute('data-dropdown')) {
                dropdown.classList.toggle('hidden');
            }
        });
    });
});
</script>



<template>

    <nav class="flex-1 p-4 overflow-y-auto">
        <!-- Menu Group -->
        <div>
            <h3 class="mb-4 text-xs font-semibold uppercase text-gray-400">Menu</h3>
            <ul class="space-y-2">
                <!-- Menu Item Dashboard -->
                <li>
                    <a href="#" class="flex items-center p-2 text-gray-700 hover:bg-gray-100 rounded-lg">
                        <svg class="w-6 h-6 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                            </path>
                        </svg>
                        Dashboard
                        <svg class="ml-auto w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M4.792 7.396l5.208 5.208 5.208-5.208"></path>
                        </svg>
                    </a>
                    <ul class="pl-8 space-y-1 hidden" data-dropdown="dashboard">
                        <li><a href="#" class="block p-2 text-gray-600 hover:bg-gray-100 rounded-lg">eCommerce</a></li>
                        <li><a href="#" class="block p-2 text-gray-600 hover:bg-gray-100 rounded-lg">eCommerce</a></li>
                    </ul>
                </li>

                <!-- Menu Item Calendar -->
                <li>
                    <a href="calendar.html" class="flex items-center p-2 text-gray-700 hover:bg-gray-100 rounded-lg">
                        <svg class="w-6 h-6 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 2v2h8V2m0 0v2h2.5A2.5 2.5 0 0121 6.5v12a2.5 2.5 0 01-2.5 2.5h-13A2.5 2.5 0 013 18.5v-12A2.5 2.5 0 015.5 4h2.5V2m-5 7h15">
                            </path>
                        </svg>
                        Calendar
                    </a>
                </li>

                <!-- Menu Item Profile -->
                <li>
                    <a href="profile.html" class="flex items-center p-2 text-gray-700 hover:bg-gray-100 rounded-lg">
                        <svg class="w-6 h-6 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z">
                            </path>
                        </svg>
                        User Profile
                    </a>
                </li>

                <!-- Menu Item Forms -->
                <li>
                    <a href="#" class="flex items-center p-2 text-gray-700 hover:bg-gray-100 rounded-lg">
                        <svg class="w-6 h-6 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 5h18v14H3V5zm4 4h10m-10 4h10"></path>
                        </svg>
                        Forms
                        <svg class="ml-auto w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M4.792 7.396l5.208 5.208 5.208-5.208"></path>
                        </svg>
                    </a>
                    <ul class="pl-8 space-y-1 hidden" data-dropdown="forms">
                        <li><a href="#" class="block p-2 text-gray-600 hover:bg-gray-100 rounded-lg">Form Elements</a>
                        </li>
                    </ul>
                </li>

                <!-- Menu Item Tables -->
                <li>
                    <a href="#" class="flex items-center p-2 text-gray-700 hover:bg-gray-100 rounded-lg">
                        <svg class="w-6 h-6 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 5h18v14H3V5zm0 4h18m-18 4h18"></path>
                        </svg>
                        Tables
                        <svg class="ml-auto w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M4.792 7.396l5.208 5.208 5.208-5.208"></path>
                        </svg>
                    </a>
                    <ul class="pl-8 space-y-1 hidden" data-dropdown="tables">
                        <li><a href="#" class="block p-2 text-gray-600 hover:bg-gray-100 rounded-lg">Basic Tables</a>
                        </li>
                    </ul>
                </li>

                <!-- Menu Item Pages -->
                <li>
                    <a href="#" class="flex items-center p-2 text-gray-700 hover:bg-gray-100 rounded-lg">
                        <svg class="w-6 h-6 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z">
                            </path>
                        </svg>
                        Pages
                        <svg class="ml-auto w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M4.792 7.396l5.208 5.208 5.208-5.208"></path>
                        </svg>
                    </a>
                    <ul class="pl-8 space-y-1 hidden" data-dropdown="pages">
                        <li><a href="#" class="block p-2 text-gray-600 hover:bg-gray-100 rounded-lg">Blank
                                Page</a></li>
                        <li><a href="#" class="block p-2 text-gray-600 hover:bg-gray-100 rounded-lg">404
                                Error</a></li>
                    </ul>
                </li>
            </ul>
        </div>

        <!-- Others Group -->
        <div class="mt-6">
            <h3 class="mb-4 text-xs font-semibold uppercase text-gray-400">Others</h3>
            <ul class="space-y-2">
                <!-- Menu Item Charts -->
                <li>
                    <a href="#" class="flex items-center p-2 text-gray-700 hover:bg-gray-100 rounded-lg">
                        <svg class="w-6 h-6 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                        Charts
                        <svg class="ml-auto w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M4.792 7.396l5.208 5.208 5.208-5.208"></path>
                        </svg>
                    </a>
                    <ul class="pl-8 space-y-1 hidden" data-dropdown="charts">
                        <li><a href="#" class="block p-2 text-gray-600 hover:bg-gray-100 rounded-lg">Line
                                Chart</a></li>
                        <li><a href="#" class="block p-2 text-gray-600 hover:bg-gray-100 rounded-lg">Bar
                                Chart</a></li>
                    </ul>
                </li>

                <!-- Menu Item UI Elements -->
                <li>
                    <a href="#" class="flex items-center p-2 text-gray-700 hover:bg-gray-100 rounded-lg">
                        <svg class="w-6 h-6 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                        </svg>
                        UI Elements
                        <svg class="ml-auto w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M4.792 7.396l5.208 5.208 5.208-5.208"></path>
                        </svg>
                    </a>
                    <ul class="pl-8 space-y-1 hidden" data-dropdown="ui-elements">
                        <li><a href="#" class="block p-2 text-gray-600 hover:bg-gray-100 rounded-lg">Alerts</a></li>
                        <li><a href="#" class="block p-2 text-gray-600 hover:bg-gray-100 rounded-lg">Avatars</a></li>
                        <li><a href="#" class="block p-2 text-gray-600 hover:bg-gray-100 rounded-lg">Badges</a>
                        </li>
                        <li><a href="#" class="block p-2 text-gray-600 hover:bg-gray-100 rounded-lg">Buttons</a></li>
                        <li><a href="#" class="block p-2 text-gray-600 hover:bg-gray-100 rounded-lg">Images</a></li>
                        <li><a href="#" class="block p-2 text-gray-600 hover:bg-gray-100 rounded-lg">Videos</a></li>
                    </ul>
                </li>

                <!-- Menu Item Authentication -->
                <li>
                    <a href="#" class="flex items-center p-2 text-gray-700 hover:bg-gray-100 rounded-lg">
                        <svg class="w-6 h-6 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 11c1.66 0 3-1.34 3-3s-1.34-3-3-3-3 1.34-3 3 1.34 3 3 3zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z">
                            </path>
                        </svg>
                        Authentication
                        <svg class="ml-auto w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M4.792 7.396l5.208 5.208 5.208-5.208"></path>
                        </svg>
                    </a>
                    <ul class="pl-8 space-y-1 hidden" data-dropdown="authentication">
                        <li><a href="#" class="block p-2 text-gray-600 hover:bg-gray-100 rounded-lg">Sign
                                In</a></li>
                        <li><a href="#" class="block p-2 text-gray-600 hover:bg-gray-100 rounded-lg">Sign
                                Up</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>

</template>