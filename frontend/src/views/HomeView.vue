<script setup>
import axios from 'axios';
import { ref, computed, onMounted } from 'vue'
import { RouterLink } from 'vue-router';

// Navigation
const navLinks = [
    { href: '#home', label: 'Home' },
    { href: '#appointments', label: 'Appointments' },
    { href: '#products', label: 'Products' },
    { href: '#contact', label: 'Contact' },
]

// Appointments
const appointments = ref();

const fetchAppointments = async () => {
    try {
        const response = await axios.get('/time-slot');
        appointments.value = response.data.data;
        console.log('Appointments fetched:', appointments.value);
    } catch (error) {
        console.error('Error fetching appointments:', error);
    } 
};

const formatDate = (dateStr) => {
    const date = new Date(dateStr)
    return date.toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric' })
}

onMounted(() => {
    fetchAppointments();
});


// Products
const products = ref([
    { id: 1, name: 'Headache Relief', price: 15},
    { id: 2, name: 'Vitamins Pack', price: 25},
    { id: 3, name: 'Medical Kit', price: 50},
])

// Cart
const cart = ref([])
const showCart = ref(false)

const addToCart = (p) => {
    cart.value.push(p)
    alert('Added to cart!')
}
const toggleCart = () => (showCart.value = !showCart.value)

const cartTotal = computed(() => cart.value.reduce((s, i) => s + i.price, 0))


</script>


<template>
    <!-- Navigation -->
    <nav class="fixed inset-x-0 top-0 z-50 bg-white/90 backdrop-blur-md shadow-xl border-b border-gray-100">
        <div class="container mx-auto flex items-center justify-between px-6 py-4">
            <!-- Logo -->
            <div class="flex items-center space-x-3">
                <div class="p-2 bg-blue-600 rounded-xl">
                    <i class="fas fa-stethoscope text-white text-xl"></i>
                </div>
                <h1
                    class="text-2xl font-black bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
                    Dr. Clinic
                </h1>
            </div>

            <!-- Nav Links -->
            <ul class="hidden md:flex space-x-8">
                <li v-for="link in navLinks" :key="link.href">
                    <a :href="link.href"
                        class="text-gray-700 font-semibold hover:text-blue-600 transition-colors duration-200 relative group">
                        {{ link.label }}
                        <span
                            class="absolute -bottom-1 left-0 w-0 h-0.5 bg-blue-600 transition-all duration-300 group-hover:w-full"></span>
                    </a>
                </li>
            </ul>

            <!-- Actions -->
            <div class="flex items-center space-x-4">
                <button @click="toggleCart"
                    class="relative p-2 text-gray-700 hover:text-blue-600 transition-colors duration-200">
                    <i class="fas fa-shopping-cart text-xl"></i>
                    <span v-if="cart.length"
                        class="absolute -top-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-xs font-bold text-white animate-pulse">
                        {{ cart.length }}
                    </span>
                </button>

                <RouterLink to="/login"
                    class="rounded-full bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-2.5 font-bold text-white shadow-lg transition-all duration-300 hover:shadow-xl hover:scale-105">
                    Login
                </RouterLink>
            </div>
        </div>
    </nav>

    <!-- Hero -->
    <section id="home"
        class="min-h-screen flex items-center justify-center bg-gradient-to-br from-white via-blue-50 to-indigo-50 pt-20">
        <div class="text-center px-6 max-w-4xl mx-auto">
            <h1 class="text-5xl md:text-7xl font-black text-gray-900 leading-tight">
                Healthcare<br>
                <span
                    class="bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">Reimagined</span>
            </h1>
            <p class="mt-6 text-lg md:text-xl text-gray-600 max-w-2xl mx-auto">
                Book appointments instantly. Shop medical essentials. All in one modern platform.
            </p>
            <div class="mt-10 flex flex-col sm:flex-row gap-4 justify-center">
                <a href="#appointments"
                    class="inline-block rounded-full bg-gradient-to-r from-blue-600 to-indigo-600 px-9 py-4 font-bold text-white shadow-xl transition-all duration-300 hover:shadow-2xl hover:scale-105">
                    Book Appointment
                </a>
                <a href="#products"
                    class="inline-block rounded-full border-2 border-blue-600 px-9 py-4 font-bold text-blue-600 transition-all duration-300 hover:bg-blue-600 hover:text-white">
                    View Products
                </a>
            </div>
        </div>
    </section>

    <!-- Appointments -->
    <section id="appointments" class="py-24 bg-white">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-black text-gray-800">Available Appointments</h2>
                <p class="mt-3 text-gray-600">Choose a time that works for you</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 max-w-7xl mx-auto">
                <div v-for="(apt, i) in appointments" :key="i"
                    class="group bg-gradient-to-br from-white to-blue-50/30 p-1 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300">
                    <div
                        class="bg-white rounded-2xl p-6 h-full flex flex-col items-center text-center border border-gray-100">
                        <div class="mb-4">
                            <p class="text-xl font-bold text-gray-800">{{  formatDate(apt.date) }}</p>
                            <p class="text-3xl font-black text-blue-600 mt-1">{{ apt.start_time }}</p>
                        </div>

                        <div class="mb-5">
                            <span
                                :class="apt.status === 'booked' ? 'bg-red-100 text-red-700' : 'bg-emerald-100 text-emerald-700'"
                                class="px-5 py-2 rounded-full text-sm font-bold tracking-wide">
                                {{ apt.status === 'booked' ? 'Booked' : 'Available' }}
                            </span>
                        </div>

                        <button v-if="apt.status === 'available'" @click="bookAppointment(i)"
                            class="w-full mt-auto rounded-full bg-gradient-to-r from-blue-600 to-indigo-600 py-3 font-bold text-white shadow-md transition-all duration-300 hover:shadow-lg hover:scale-105">
                            Book Now
                        </button>
                        <p v-else class="mt-auto text-gray-500 italic font-medium">Slot Taken</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Products -->
    <section id="products" class="py-24 bg-gray-50">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-black text-gray-800">Medical Products</h2>
                <p class="mt-3 text-gray-600">Trusted health essentials delivered fast</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-6xl mx-auto">
                <div v-for="p in products" :key="p.id"
                    class="group bg-white rounded-2xl shadow-lg overflow-hidden transition-all duration-300 hover:shadow-2xl hover:-translate-y-2">
                    <div class="h-56 bg-gradient-to-br from-blue-100 to-indigo-100 p-4">
                        <img :src="p.image" :alt="p.name" class="w-full h-full object-contain rounded-xl">
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-800">{{ p.name }}</h3>
                        <p class="mt-2 text-2xl font-black text-blue-600">${{ p.price }}</p>
                        <button @click="addToCart(p)"
                            class="mt-5 w-full rounded-full bg-gradient-to-r from-blue-600 to-indigo-600 py-3 font-bold text-white shadow-md transition-all duration-300 hover:shadow-lg hover:scale-105">
                            Add to Cart
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer id="contact" class="bg-gradient-to-r from-blue-600 to-indigo-600 py-12 text-white">
        <div class="container mx-auto text-center px-6">
            <p class="text-sm opacity-90">© {{ new Date().getFullYear() }} Dr. Clinic. All rights reserved.</p>
            <div class="flex justify-center space-x-6 mt-4">
                <a href="#" class="hover:opacity-70 transition-opacity"><i class="fab fa-facebook-f text-xl"></i></a>
                <a href="#" class="hover:opacity-70 transition-opacity"><i class="fab fa-twitter text-xl"></i></a>
                <a href="#" class="hover:opacity-70 transition-opacity"><i class="fab fa-instagram text-xl"></i></a>
            </div>
        </div>
    </footer>

    <!-- Cart Modal -->
    <teleport to="body">
        <div v-if="showCart"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 backdrop-blur-sm">
            <div
                class="w-full max-w-md bg-white rounded-2xl shadow-2xl p-6 animate-in fade-in slide-in-from-top duration-300">
                <div class="flex justify-between items-center mb-5">
                    <h3 class="text-2xl font-black text-gray-900">Your Cart</h3>
                    <button @click="toggleCart" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <ul class="space-y-3 mb-5 max-h-64 overflow-y-auto">
                    <li v-for="it in cart" :key="it.id"
                        class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <span class="font-medium text-gray-800">{{ it.name }}</span>
                        <span class="font-bold text-blue-600">${{ it.price }}</span>
                    </li>
                </ul>

                <div class="border-t pt-4">
                    <p class="text-right font-black text-xl text-gray-800">Total: <span class="text-blue-600">${{
                        cartTotal }}</span></p>
                </div>

                <button @click="toggleCart"
                    class="mt-5 w-full rounded-full bg-gradient-to-r from-blue-600 to-indigo-600 py-3 font-bold text-white shadow-lg transition-all duration-300 hover:shadow-xl hover:scale-105">
                    Continue Shopping
                </button>
            </div>
        </div>
    </teleport>
</template>

<style scoped>
/* Smooth scroll */
html {
    scroll-behavior: smooth;
}

/* Custom animations */
@keyframes slide-in-from-top {
    from {
        transform: translateY(-20px);
        opacity: 0;
    }

    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.animate-in {
    animation: slide-in-from-top 0.3s ease-out;
}
</style>