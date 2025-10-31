<script setup>
import Spinner from '@/components/Spinner.vue';
import axios from 'axios';
import { ref, onMounted } from 'vue';
import { RouterLink } from 'vue-router';
import { useToast } from 'vue-toastification';

// === Auth & Toast ===
const isAuth = ref(false);
const toast = useToast();

// === Loading States ===
const isLoadingAppointments = ref(false);
const isLoadingUserAppointment = ref(false);
const isBooking = ref(false);
const isCancelling = ref(false);

// === Data ===
const appointments = ref([]);
const user_appointment = ref(null);

// === Navigation ===
const navLinks = [
    { href: '#home', label: 'Home' },
    { href: '#appointments', label: 'Appointments' },
    { href: '#products', label: 'Products' },
    { href: '#contact', label: 'Contact' },
];

// === Toggle Modal ===
const showUserAppointment = ref(false);
const toggleUserAppointment = () => {
    showUserAppointment.value = !showUserAppointment.value;
};

// === Logout ===
const logout = () => {
    localStorage.removeItem('token');
    localStorage.removeItem('role');
    isAuth.value = false;
    toast.success('Logged out successfully');
};

// === Fetch Data ===
const fetchAppointments = async () => {
    try {
        isLoadingAppointments.value = true;
        const response = await axios.get('/time-slot');
        appointments.value = response.data.data || [];
    } catch (error) {
        toast.error('Failed to load appointments. Try again.');
    } finally {
        isLoadingAppointments.value = false;
    }
};

const fetchUserAppointment = async () => {
    try {
        isLoadingUserAppointment.value = true;
        const response = await axios.get('/user-time-slot');
        user_appointment.value = response.data.data[0] || null;
    } catch (error) {
        // Normal if no appointment
    } finally {
        isLoadingUserAppointment.value = false;
    }
};

// === Book & Cancel ===
const BookAppointment = async (id) => {
    if (!isAuth.value) {
        toast.warning('Please log in to book an appointment');
        return;
    }
    try {
        isBooking.value = true;
        await axios.post('/appointment', { slot_id: id });
        await Promise.all([fetchAppointments(), fetchUserAppointment()]);
        toast.success('Appointment booked successfully!');
        showUserAppointment.value = true;
    } catch (error) {
        toast.error(error.response?.data?.message || 'Booking failed. Try again.');
    } finally {
        isBooking.value = false;
    }
};

const cancelAppointment = async (id) => {
    if (!confirm('Are you sure you want to cancel?')) return;
    try {
        isCancelling.value = true;
        await axios.delete(`/appointment/${id}`);
        await Promise.all([fetchAppointments(), fetchUserAppointment()]);
        toast.success('Appointment cancelled');
        showUserAppointment.value = false;
    } catch (error) {
        toast.error('Cancellation failed');
    } finally {
        isCancelling.value = false;
    }
};

// === Format Date ===
const formatDate = (dateStr) => {
    return new Date(dateStr).toLocaleDateString('en-US', {
        weekday: 'short',
        month: 'short',
        day: 'numeric'
    });
};

onMounted(() => {
    const token = localStorage.getItem('token');
    if (token) isAuth.value = true;

    fetchAppointments();
    fetchUserAppointment();
});
</script>

<template>

    <!-- Navigation -->
    <nav class="fixed inset-x-0 top-0 z-50 bg-white/95 backdrop-blur-xl shadow-lg border-b border-gray-100">
        <div class="container mx-auto flex items-center justify-between px-5 py-4">
            <!-- Logo -->
            <div class="flex items-center space-x-3">
                <div
                    class="w-11 h-11 bg-gradient-to-br from-blue-600 to-indigo-700 rounded-2xl flex items-center justify-center shadow-md">
                    <i class="fas fa-stethoscope text-white text-lg"></i>
                </div>
                <h1
                    class="text-2xl font-black bg-gradient-to-r from-blue-600 to-indigo-700 bg-clip-text text-transparent">
                    Dr. Clinic
                </h1>
            </div>

            <!-- Desktop Links -->
            <ul class="hidden md:flex items-center space-x-10">
                <li v-for="link in navLinks" :key="link.href">
                    <a :href="link.href"
                        class="text-gray-700 font-semibold hover:text-blue-600 transition-all duration-300 relative group">
                        {{ link.label }}
                        <span
                            class="absolute -bottom-1 left-0 w-0 h-0.5 bg-gradient-to-r from-blue-600 to-indigo-700 transition-all duration-300 group-hover:w-full rounded-full"></span>
                    </a>
                </li>
            </ul>

            <!-- Actions -->
            <div class="flex items-center space-x-4">
                <!-- User Appointment -->
                <button @click="toggleUserAppointment"
                    class="relative p-2.5 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl hover:from-blue-100 hover:to-indigo-100 transition-all duration-300 group">
                    <i class="fas fa-calendar-check text-blue-600 text-lg"></i>
                    <span v-if="user_appointment"
                        class="absolute -top-1 -right-1 w-3 h-3 bg-emerald-500 rounded-full animate-ping"></span>
                    <span v-if="user_appointment"
                        class="absolute -top-1 -right-1 w-3 h-3 bg-emerald-500 rounded-full"></span>
                </button>

                <!-- Auth Buttons -->
                <template v-if="!isAuth">
                    <RouterLink to="/signup"
                        class="px-6 py-2.5 bg-white border-2 border-blue-600 text-blue-600 font-bold rounded-full hover:bg-blue-50 transition-all duration-300 shadow-md">
                        Register
                    </RouterLink>
                    <RouterLink to="/login"
                        class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-700 text-white font-bold rounded-full shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-300">
                        Login
                    </RouterLink>
                </template>
                <button v-else @click="logout"
                    class="px-6 py-2.5 bg-gradient-to-r from-red-600 to-pink-600 text-white font-bold rounded-full shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-300 flex items-center gap-2">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </div>
        </div>
    </nav>

    <!-- Hero -->
    <section id="home"
        class="min-h-screen flex items-center justify-center pt-20 bg-gradient-to-br from-blue-50 via-white to-indigo-50">
        <div class="text-center px-6 max-w-5xl mx-auto">
            <h1 class="text-5xl md:text-7xl font-black text-gray-900 leading-tight">
                Healthcare<br>
                <span class="bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 bg-clip-text text-transparent">
                    Reimagined
                </span>
            </h1>
            <p class="mt-6 text-lg md:text-xl text-gray-600 max-w-2xl mx-auto">
                Book instantly. Track easily. All in one modern platform.
            </p>
            <div class="mt-12 flex flex-col sm:flex-row gap-5 justify-center">
                <a href="#appointments"
                    class="px-10 py-4 bg-gradient-to-r from-blue-600 to-indigo-700 text-white font-bold rounded-full shadow-xl hover:shadow-2xl hover:scale-105 transition-all duration-300 text-lg">
                    Book Now
                </a>
                <a href="#products"
                    class="px-10 py-4 bg-white border-2 border-blue-600 text-blue-600 font-bold rounded-full shadow-lg hover:bg-blue-50 hover:scale-105 transition-all duration-300 text-lg">
                    View Products
                </a>
            </div>
        </div>
    </section>

    <!-- Appointments -->
    <section id="appointments" class="py-24 bg-gray-50">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-black text-gray-800">Available Appointments</h2>
                <p class="mt-3 text-gray-600 text-lg">Choose your preferred time</p>
            </div>

            <!-- Skeleton Loading -->
            <div v-if="isLoadingAppointments"
                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <div v-for="n in 8" :key="n"
                    class="bg-white/80 backdrop-blur-sm rounded-2xl p-6 shadow-lg animate-pulse border border-gray-100">
                    <div class="h-6 bg-gray-200 rounded w-24 mx-auto mb-3"></div>
                    <div class="h-9 bg-gray-300 rounded w-20 mx-auto mb-6"></div>
                    <div class="h-11 bg-gray-200 rounded-full"></div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else-if="!appointments.length" class="text-center py-20">
                <div
                    class="w-28 h-28 mx-auto mb-6 rounded-full bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center shadow-inner">
                    <i class="fas fa-calendar-times text-5xl text-gray-400"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-700">No appointments available</h3>
                <p class="text-gray-500 mt-3">Check back later for new slots</p>
            </div>

            <!-- Appointments Cards -->
            <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <div v-for="apt in appointments" :key="apt.id"
                    class="group bg-white rounded-2xl p-1 shadow-lg hover:shadow-2xl transition-all duration-500 border border-gray-100 overflow-hidden">
                    <div
                        class="bg-gradient-to-br from-blue-50/50 to-indigo-50/50 p-5 rounded-2xl h-full flex flex-col items-center text-center relative">
                        <!-- Decorative Corner -->
                        <div
                            class="absolute top-0 right-0 w-16 h-16 bg-gradient-to-bl from-blue-400 to-transparent opacity-20 rounded-bl-3xl">
                        </div>

                        <p class="text-xl font-bold text-gray-800 relative z-10">{{ formatDate(apt.date) }}</p>
                        <p
                            class="text-3xl font-black text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-700 mt-2 relative z-10">
                            {{ apt.start_time }}
                        </p>

                        <div class="my-6">
                            <span
                                :class="apt.status === 'booked' ? 'bg-red-100 text-red-700' : 'bg-emerald-100 text-emerald-700'"
                                class="px-6 py-2.5 rounded-full text-sm font-bold tracking-wide shadow-sm">
                                {{ apt.status === 'booked' ? 'Booked' : 'Available' }}
                            </span>
                        </div>

                        <!-- Book Button -->
                        <button v-if="apt.status === 'available'" @click="BookAppointment(apt.id)"
                            :disabled="isBooking || !isAuth"
                            class="w-full mt-auto py-3 bg-gradient-to-r from-blue-600 to-indigo-700 text-white font-bold rounded-full shadow-md hover:shadow-lg hover:scale-105 transition-all duration-300 disabled:opacity-60 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                            <span v-if="isBooking">
                                <i class="fas fa-spinner fa-spin"></i> Booking...
                            </span>
                            <span v-else>Book Now</span>
                        </button>

                        <p v-else class="mt-auto text-red-600 font-medium flex items-center gap-2">
                            <i class="fas fa-lock"></i> Taken
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- User Appointment Modal -->
    <div v-if="showUserAppointment"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm"
        @click.self="toggleUserAppointment">
        <div class="w-full max-w-md bg-white rounded-3xl shadow-2xl p-7 border border-gray-100">

            <!-- Loading -->
            <div v-if="isLoadingUserAppointment" class="text-center py-12">
                <Spinner />
                <p class="mt-4 text-gray-600">Loading your appointment...</p>
            </div>

            <!-- No Appointment -->
            <div v-else-if="!user_appointment" class="text-center py-10">
                <div
                    class="w-24 h-24 mx-auto mb-5 rounded-full bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center shadow-inner">
                    <i class="fas fa-calendar-slash text-4xl text-gray-400"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-700">No Appointment</h3>
                <p class="text-gray-500 mt-2">You haven't booked any appointment yet</p>
                <button @click="toggleUserAppointment"
                    class="mt-6 px-8 py-3 bg-blue-600 text-white rounded-full font-bold hover:bg-blue-700 transition">
                    Close
                </button>
            </div>

            <!-- Has Appointment -->
            <div v-else>
                <div class="flex justify-between items-start mb-6">
                    <div class="flex items-center gap-4">
                        <div
                            class="w-14 h-14 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-check text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">Your Appointment</h3>
                            <p class="text-sm text-emerald-600 font-medium">Confirmed & Ready</p>
                        </div>
                    </div>
                    <button @click="toggleUserAppointment" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>

                <div
                    class="p-5 bg-gradient-to-br from-emerald-50 to-teal-50 rounded-2xl border border-emerald-200 space-y-4">
                    <div class="flex justify-between">
                        <span class="font-medium text-gray-600">Date</span>
                        <span class="font-bold text-gray-800">{{ formatDate(user_appointment.slot.date) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium text-gray-600">Time</span>
                        <span class="font-bold text-gray-800">{{ user_appointment.slot.start_time }} – {{
                            user_appointment.slot.end_time }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-medium text-gray-600">Status</span>
                        <span class="px-4 py-1.5 bg-emerald-100 text-emerald-700 rounded-full text-sm font-bold">
                            {{ user_appointment.status }}
                        </span>
                    </div>
                </div>

                <button @click="cancelAppointment(user_appointment.id)" :disabled="isCancelling"
                    class="mt-6 w-full py-3 bg-gradient-to-r from-red-600 to-pink-600 text-white font-bold rounded-full shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-300 disabled:opacity-70 flex items-center justify-center gap-2">
                    <span v-if="isCancelling">
                        <i class="fas fa-spinner fa-spin"></i> Cancelling...
                    </span>
                    <span v-else>Cancel Appointment</span>
                </button>

                <p class="mt-5 text-center text-xs text-gray-400">
                    ID: #{{ String(user_appointment.id).padStart(3, '0') }} •
                    Booked on {{ new Date(user_appointment.created_at).toLocaleDateString('en-US') }}
                </p>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer id="contact" class="py-16 bg-gradient-to-r from-blue-700 via-indigo-700 to-purple-700 text-white">
        <div class="container mx-auto px-6 text-center">
            <div class="flex items-center justify-center gap-3 mb-4">
                <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm">
                    <i class="fas fa-stethoscope text-xl"></i>
                </div>
                <h3 class="text-2xl font-bold">Dr. Clinic</h3>
            </div>
            <p class="text-sm opacity-90 mb-8">Modern healthcare. Simple. Secure.</p>

            <div class="flex justify-center gap-5 mb-8">
                <a href="#"
                    class="w-12 h-12 bg-white/10 rounded-full flex items-center justify-center hover:bg-white/20 transition-all backdrop-blur-sm">
                    <i class="fab fa-facebook-f"></i>
                </a>
                <a href="#"
                    class="w-12 h-12 bg-white/10 rounded-full flex items-center justify-center hover:bg-white/20 transition-all backdrop-blur-sm">
                    <i class="fab fa-twitter"></i>
                </a>
                <a href="#"
                    class="w-12 h-12 bg-white/10 rounded-full flex items-center justify-center hover:bg-white/20 transition-all backdrop-blur-sm">
                    <i class="fab fa-instagram"></i>
                </a>
            </div>

            <p class="text-xs opacity-70">
                © {{ new Date().getFullYear() }} Dr. Clinic. All rights reserved.
            </p>
        </div>
    </footer>

</template>

<style scoped>
/* Smooth Scroll */
html {
    scroll-behavior: smooth;
}

/* Card Hover Lift */
.group:hover {
    transform: translateY(-6px);
}

/* Button Focus Ring */
button:focus-visible,
a:focus-visible {
    outline: 2px solid #3b82f6;
    outline-offset: 3px;
}
</style>