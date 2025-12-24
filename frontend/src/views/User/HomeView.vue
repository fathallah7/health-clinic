<script setup>
import axios from 'axios'
import { ref, onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import { useToast } from 'vue-toastification'

const toast = useToast()
const isAuth = ref(false)

// Data
const slots = ref([])
const myApt = ref(null)
const loadingSlots = ref(true)
const booking = ref(false)

// Modals
const showApt = ref(false)

// API Functions
const fetchSlots = async () => {
    try { slots.value = (await axios.get('/time-slots')).data.data || [] }
    catch { toast.error('Failed to load appointments') }
    finally { loadingSlots.value = false }
}

const fetchMyApt = async () => {
    try { myApt.value = (await axios.get('/user/time-slots')).data.data[0] || null }
    catch { }
}

const book = async (id) => {
    if (!isAuth.value) return toast.warning('Please sign in first')
    try {
        booking.value = true
        await axios.post('/appointments', { slot_id: id })
        await Promise.all([fetchSlots(), fetchMyApt()])
        toast.success('Appointment booked!')
        showApt.value = true
    } catch (e) { toast.error(e.response?.data?.message || 'Booking failed') }
    finally { booking.value = false }
}

const cancel = async () => {
    if (!confirm('Cancel appointment?')) return
    try {
        await axios.delete(`/appointments/${myApt.value.id}`)
        await Promise.all([fetchSlots(), fetchMyApt()])
        toast.success('Cancelled')
        showApt.value = false
    } catch { toast.error('Failed') }
}

const logout = () => {
    localStorage.removeItem('token')
    localStorage.clear()
    myApt.value = null
    slots.value = []
    isAuth.value = false
    window.location.reload()
    toast.success('Logged out successfully')
}

const fmt = (d) => new Date(d).toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric' })

onMounted(() => {
    if (localStorage.token) { isAuth.value = true; fetchMyApt() }
    fetchSlots()
})
</script>

<template>
    <!-- Navbar - Clean & Modern -->
    <nav class="fixed inset-x-0 top-0 z-50 bg-white/80 backdrop-blur-xl border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="relative">
                    <div
                        class="w-11 h-11 bg-gradient-to-br from-teal-500 to-emerald-500 rounded-2xl flex items-center justify-center shadow-lg shadow-teal-500/20">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                    </div>
                </div>
                <div>
                    <span class="font-bold text-gray-900 text-lg">Dr.Fathallah</span>
                    <span class="hidden sm:inline text-xs text-gray-400 ml-2">Premium Care</span>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <button @click="showApt = true"
                    class="p-2.5 rounded-xl hover:bg-gray-50 transition-all duration-300 group">
                    <svg class="w-5 h-5 text-gray-600 group-hover:text-teal-600 transition-colors" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </button>

                <template v-if="!isAuth">
                    <RouterLink to="/login"
                        class="px-6 py-2.5 bg-gray-900 text-white text-sm font-medium rounded-xl hover:bg-gray-800 transition-all duration-300">
                        Sign In
                    </RouterLink>
                </template>
                <button v-else @click="logout"
                    class="px-5 py-2.5 text-gray-600 text-sm font-medium rounded-xl hover:bg-gray-50 transition-all">
                    Logout
                </button>
            </div>
        </div>
    </nav>

    <!-- Hero - Clean & Elegant -->
    <section class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-teal-50/30 relative overflow-hidden">
        <!-- Subtle Background Elements -->
        <div class="absolute inset-0 opacity-40">
            <div class="absolute top-20 right-20 w-96 h-96 bg-teal-100/40 rounded-full blur-3xl"></div>
            <div class="absolute bottom-32 left-20 w-80 h-80 bg-emerald-100/40 rounded-full blur-3xl"></div>
        </div>

        <div class="relative max-w-7xl mx-auto px-6 pt-32 pb-20 grid lg:grid-cols-2 gap-16 items-center min-h-screen">
            <!-- Left Content -->
            <div class="space-y-8">
                <div class="inline-flex items-center gap-2 px-4 py-2 bg-teal-50 rounded-full border border-teal-100">
                    <span class="relative flex h-2 w-2">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-teal-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-teal-500"></span>
                    </span>
                    <span class="text-sm font-medium text-teal-700">Accepting New Patients</span>
                </div>

                <div class="space-y-4">
                    <h1 class="text-6xl lg:text-7xl font-bold leading-tight text-gray-900">
                        Your Health,<br>
                        <span class="text-teal-600">Our Priority</span>
                    </h1>
                    <p class="text-xl text-gray-600 leading-relaxed max-w-xl">
                        Experience world-class medical care with compassion and dedication.
                        Book your consultation today.
                    </p>
                </div>

                <div class="flex flex-wrap gap-4 pt-4">
                    <a href="#appointments"
                        class="px-8 py-4 bg-gray-900 text-white font-medium rounded-xl hover:bg-gray-800 transition-all duration-300 inline-flex items-center gap-2">
                        Book Appointment
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </a>
                </div>

                <!-- Stats -->
                <div class="grid grid-cols-3 gap-6 pt-8">
                    <div class="space-y-1">
                        <div class="text-4xl font-bold text-gray-900">5K+</div>
                        <div class="text-sm text-gray-500">Happy Patients</div>
                    </div>
                    <div class="space-y-1">
                        <div class="text-4xl font-bold text-gray-900">4.9★</div>
                        <div class="text-sm text-gray-500">Rating</div>
                    </div>
                    <div class="space-y-1">
                        <div class="text-4xl font-bold text-gray-900">15+</div>
                        <div class="text-sm text-gray-500">Years Exp.</div>
                    </div>
                </div>
            </div>

            <!-- Right Image -->
            <div class="relative">
                <div class="relative">
                    <img src="../../assets/logo.jpg" alt="Dr. Fathallah"
                        class="relative rounded-3xl shadow-2xl w-full" />

                    <!-- Floating Card 1 -->
                    <div
                        class="absolute -left-8 top-1/4 bg-white p-4 rounded-2xl shadow-xl border border-gray-100 animate-float">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div>
                                <div class="text-sm font-semibold text-gray-900">Verified</div>
                                <div class="text-xs text-gray-500">Licensed Doctor</div>
                            </div>
                        </div>
                    </div>

                    <!-- Floating Card 2 -->
                    <div
                        class="absolute -right-8 bottom-1/4 bg-white p-4 rounded-2xl shadow-xl border border-gray-100 animate-float-delayed">
                        <div class="flex items-center gap-3">
                            <div class="flex -space-x-2">
                                <div
                                    class="w-8 h-8 bg-gradient-to-br from-teal-400 to-teal-500 rounded-full border-2 border-white">
                                </div>
                                <div
                                    class="w-8 h-8 bg-gradient-to-br from-emerald-400 to-emerald-500 rounded-full border-2 border-white">
                                </div>
                                <div
                                    class="w-8 h-8 bg-gradient-to-br from-cyan-400 to-cyan-500 rounded-full border-2 border-white">
                                </div>
                            </div>
                            <div>
                                <div class="text-sm font-semibold text-gray-900">5,000+</div>
                                <div class="text-xs text-gray-500">Patients Treated</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Appointments - Minimalist & Clean -->
    <section id="appointments" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16 space-y-4">
                <div class="inline-flex items-center gap-2 px-4 py-2 bg-gray-50 rounded-full">
                    <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span class="text-sm font-medium text-gray-700">Appointments</span>
                </div>
                <h2 class="text-4xl sm:text-5xl font-bold text-gray-900">
                    Book Your Visit
                </h2>
                <p class="text-gray-600 text-lg max-w-2xl mx-auto">
                    Choose a convenient time slot for your consultation
                </p>
            </div>

            <div v-if="loadingSlots" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                <div v-for="i in 8" :key="i" class="h-36 bg-gray-50 rounded-2xl animate-pulse"></div>
            </div>

            <div v-else class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                <div v-for="s in slots" :key="s.id" class="group relative p-6 rounded-2xl transition-all duration-300"
                    :class="s.status === 'available'
                        ? 'bg-white border-2 border-gray-100 hover:border-teal-500 hover:shadow-lg cursor-pointer'
                        : 'bg-gray-50 border-2 border-gray-100 opacity-60'">

                    <div v-if="s.status === 'available'"
                        class="absolute top-3 right-3 w-2 h-2 bg-green-500 rounded-full">
                    </div>

                    <div class="space-y-3">
                        <div>
                            <p class="text-sm text-gray-500 font-medium">{{ fmt(s.date) }}</p>
                            <p class="text-2xl font-bold text-gray-900 mt-1">{{ s.start_time }}</p>
                        </div>

                        <button v-if="s.status === 'available'" @click="book(s.id)" :disabled="booking"
                            class="w-full py-2.5 bg-gray-900 text-white text-sm font-medium rounded-xl hover:bg-gray-800 active:scale-95 transition-all duration-200 disabled:opacity-50">
                            {{ booking ? 'Booking...' : 'Book' }}
                        </button>

                        <div v-else
                            class="w-full py-2.5 text-center text-sm font-medium text-gray-400 border-2 border-gray-100 rounded-xl">
                            Unavailable
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Appointment Modal - Clean Design -->
    <teleport to="body">
        <transition name="modal">
            <div v-if="showApt"
                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm"
                @click.self="showApt = false">
                <div class="bg-white rounded-3xl w-full max-w-md p-8 shadow-2xl transform transition-all">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900">My Appointment</h3>
                            <p class="text-sm text-gray-500 mt-1">Manage your booking</p>
                        </div>
                        <button @click="showApt = false"
                            class="w-10 h-10 rounded-xl hover:bg-gray-100 flex items-center justify-center transition-colors">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div v-if="!myApt" class="py-12 text-center">
                        <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <p class="text-gray-600 font-medium mb-4">No appointment scheduled</p>
                        <a href="#appointments" @click="showApt = false"
                            class="inline-flex items-center gap-2 text-teal-600 font-medium hover:gap-3 transition-all">
                            Book an appointment
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </a>
                    </div>

                    <div v-else class="space-y-6">
                        <div
                            class="bg-gradient-to-br from-teal-50 to-emerald-50 rounded-2xl p-6 border border-teal-100">
                            <div class="flex items-center gap-4 mb-4">
                                <div class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center shadow-sm">
                                    <svg class="w-7 h-7 text-teal-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-2xl font-bold text-gray-900">{{ myApt.slot.start_time }}</p>
                                    <p class="text-gray-600">{{ fmt(myApt.slot.date) }}</p>
                                </div>
                            </div>

                            <div
                                class="flex items-center gap-2 text-sm font-medium text-teal-700 bg-white/70 rounded-xl px-4 py-2.5">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span>Confirmed</span>
                            </div>
                        </div>

                        <button @click="cancel"
                            class="w-full py-3.5 border-2 border-gray-200 text-gray-700 font-medium rounded-xl hover:bg-gray-50 hover:border-gray-300 transition-colors">
                            Cancel Appointment
                        </button>
                    </div>
                </div>
            </div>
        </transition>
    </teleport>

    <!-- Footer - Minimal & Clean -->
    <footer class="py-16 bg-gray-50 border-t border-gray-100">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center space-y-6">
                <div class="inline-flex items-center gap-3">
                    <div
                        class="w-11 h-11 bg-gradient-to-br from-teal-500 to-emerald-500 rounded-2xl flex items-center justify-center shadow-lg shadow-teal-500/20">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                    </div>
                    <span class="text-xl font-bold text-gray-900">Dr.Fathallah</span>
                </div>

                <p class="text-gray-600 max-w-md mx-auto">
                    Excellence in healthcare. Compassion in every visit.
                </p>

                <div class="pt-6 text-sm text-gray-500">
                    © 2025 Dr.Fathallah Clinic. All rights reserved.
                </div>
            </div>
        </div>
    </footer>
</template>

<style>
html {
    scroll-behavior: smooth;
}

@keyframes float {

    0%,
    100% {
        transform: translateY(0px);
    }

    50% {
        transform: translateY(-20px);
    }
}

@keyframes float-delayed {

    0%,
    100% {
        transform: translateY(0px);
    }

    50% {
        transform: translateY(-15px);
    }
}

.animate-float {
    animation: float 6s ease-in-out infinite;
}

.animate-float-delayed {
    animation: float-delayed 6s ease-in-out infinite;
    animation-delay: 2s;
}

.modal-enter-active,
.modal-leave-active {
    transition: all 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
    opacity: 0;
}

.modal-enter-from>div,
.modal-leave-to>div {
    transform: scale(0.95) translateY(20px);
}
</style>