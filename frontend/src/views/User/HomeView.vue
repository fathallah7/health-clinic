<script setup>
import axios from 'axios'
import { onMounted, ref } from 'vue'
import { RouterLink } from 'vue-router'
import { useToast } from 'vue-toastification'

const toast = useToast()
const isAuth = ref(false)
const slots = ref([])
const myApt = ref(null)
const loading = ref(true)
const booking = ref(false)
const showModal = ref(false)

const fetchSlots = async () => {
    try {
        slots.value = (await axios.get('/time-slots')).data.data || []
    } catch {
        toast.error('Failed to load appointments')
    } finally {
        loading.value = false
    }
}

const fetchMyApt = async () => {
    try {
        myApt.value = (await axios.get('/user/time-slots')).data.data[0] || null
    } catch { }
}

const book = async (id) => {
    if (!isAuth.value) return toast.warning('Please sign in first')
    booking.value = true
    try {
        const res = await axios.post('/appointments', { slot_id: id })
        const url = res.data.data?.checkout_url || res.data.checkout_url
        if (url) {
            toast.success('Redirecting to payment...')
            window.location.href = url
        } else {
            toast.error('Payment link not found')
            booking.value = false
        }
    } catch (e) {
        toast.error(e.response?.data?.message || 'Booking failed')
        booking.value = false
    }
}

const cancel = async () => {
    if (!confirm('Cancel this appointment?')) return
    try {
        await axios.delete(`/appointments/${myApt.value.id}`)
        await Promise.all([fetchSlots(), fetchMyApt()])
        toast.success('Appointment cancelled')
        showModal.value = false
    } catch {
        toast.error('Failed to cancel')
    }
}

const logout = () => {
    localStorage.clear()
    window.location.reload()
}

const fmt = (d) => new Date(d).toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric' })

onMounted(() => {
    isAuth.value = !!localStorage.token
    if (isAuth.value) fetchMyApt()
    fetchSlots()
})

const iconPath = {
    heart: "M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z",
    calendar: "M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z",
    arrow: "M17 8l4 4m0 0l-4 4m4-4H3",
    close: "M6 18L18 6M6 6l12 12",
    shield: "M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z",
    clock: "M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z",
    star: "M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z",
    users: "M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z",
    check: "M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
}

const features = [
    { icon: 'shield', title: 'Safe & Secure', desc: 'Complete Privacy' },
    { icon: 'clock', title: 'Flexible Hours', desc: 'Book Anytime' },
    { icon: 'star', title: 'Premium Care', desc: 'Highest Standards' },
    { icon: 'users', title: 'Expert Team', desc: 'Extensive Experience' }
]
</script>

<template>
    <nav class="fixed top-0 inset-x-0 z-50 bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex items-center justify-between h-24">
                <div class="flex items-center gap-4">
                    <div
                        class="w-16 h-16 bg-gradient-to-br from-emerald-600 via-teal-600 to-emerald-700 rounded-full flex items-center justify-center shadow-xl shadow-emerald-500/30 ring-4 ring-emerald-50">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                :d="iconPath.heart" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Dr. Fathallah</h1>
                        <p class="text-sm text-emerald-600 font-medium">Premium Medical Care</p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <button @click="showModal = true" class="p-3 hover:bg-gray-50 rounded-xl transition group">
                        <svg class="w-6 h-6 text-gray-600 group-hover:text-emerald-600 transition" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                :d="iconPath.calendar" />
                        </svg>
                    </button>
                    <RouterLink v-if="!isAuth" to="/login"
                        class="px-7 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-semibold rounded-xl shadow-lg shadow-emerald-500/30 hover:shadow-emerald-500/50 hover:scale-105 transition-all">
                        Sign In</RouterLink>
                    <button v-else @click="logout"
                        class="px-6 py-3 text-gray-700 font-semibold hover:bg-gray-50 rounded-xl transition">Logout</button>
                </div>
            </div>
        </div>
    </nav>

    <section
        class="min-h-screen bg-gradient-to-b from-gray-50 via-white to-emerald-50/30 relative overflow-hidden pt-24">
        <div class="absolute inset-0 opacity-[0.03]">
            <div class="absolute inset-0"
                style="background-image: radial-gradient(circle at 1px 1px, rgb(16 185 129) 1px, transparent 0); background-size: 40px 40px;">
            </div>
        </div>
        <div class="absolute top-32 right-20 w-96 h-96 bg-emerald-200/40 rounded-full blur-3xl"></div>
        <div class="absolute bottom-32 left-20 w-80 h-80 bg-teal-200/40 rounded-full blur-3xl"></div>

        <div class="relative max-w-7xl mx-auto px-6 py-16 grid lg:grid-cols-2 gap-20 items-center">
            <div class="space-y-10">
                <div
                    class="inline-flex items-center gap-3 px-5 py-2.5 bg-emerald-50 border border-emerald-200 rounded-full">
                    <span class="relative flex h-3 w-3">
                        <span class="animate-ping absolute h-full w-full rounded-full bg-emerald-500 opacity-75"></span>
                        <span class="relative rounded-full h-3 w-3 bg-emerald-600"></span>
                    </span>
                    <span class="text-sm font-semibold text-emerald-700">Accepting New Patients</span>
                </div>
                <div class="space-y-6">
                    <h1 class="text-6xl lg:text-7xl font-bold leading-[1.1] text-gray-900">Your Health in<br /><span
                            class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-700">Safe
                            Hands</span></h1>
                    <p class="text-xl text-gray-600 leading-relaxed max-w-xl font-light">Experience exceptional medical
                        care combining extensive expertise with modern techniques. Your health deserves the best.</p>
                </div>
                <div class="flex flex-wrap gap-4">
                    <a href="#appointments"
                        class="group px-8 py-4 bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-semibold text-lg rounded-xl shadow-xl shadow-emerald-500/30 hover:shadow-emerald-500/50 hover:scale-105 transition-all flex items-center gap-3">
                        Book Appointment
                        <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                :d="iconPath.arrow" />
                        </svg>
                    </a>
                    <button
                        class="px-8 py-4 bg-white text-gray-700 font-semibold text-lg rounded-xl border-2 border-gray-200 hover:border-emerald-300 hover:bg-emerald-50/50 transition-all">Contact
                        Us</button>
                </div>
                <div class="grid grid-cols-3 gap-8 pt-8 border-t border-gray-200">
                    <div class="text-center">
                        <div
                            class="text-5xl font-bold bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent mb-2">
                            5K+</div>
                        <div class="text-sm text-gray-600 font-medium">Happy Patients</div>
                    </div>
                    <div class="text-center">
                        <div
                            class="text-5xl font-bold bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent mb-2">
                            4.9★</div>
                        <div class="text-sm text-gray-600 font-medium">Excellence Rating</div>
                    </div>
                    <div class="text-center">
                        <div
                            class="text-5xl font-bold bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent mb-2">
                            15+</div>
                        <div class="text-sm text-gray-600 font-medium">Years Experience</div>
                    </div>
                </div>
            </div>

            <div class="relative">
                <div
                    class="absolute -inset-4 bg-gradient-to-br from-emerald-400 via-teal-400 to-emerald-500 rounded-3xl opacity-20 blur-2xl">
                </div>
                <div class="relative rounded-3xl overflow-hidden shadow-2xl ring-8 ring-white">
                    <img src="../../assets/logo.jpg" alt="Dr. Fathallah" class="w-full" />
                </div>
                <div
                    class="absolute -bottom-8 -left-8 bg-white rounded-2xl shadow-2xl p-6 border border-gray-100 animate-float">
                    <div class="flex items-center gap-4">
                        <div
                            class="w-16 h-16 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl flex items-center justify-center shadow-lg">
                            <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-lg font-bold text-gray-900">Certified Doctor</div>
                            <div class="text-sm text-gray-500">Licensed Professional</div>
                        </div>
                    </div>
                </div>
                <div
                    class="absolute -top-6 -right-6 bg-gradient-to-br from-emerald-600 to-teal-600 rounded-2xl shadow-2xl p-4 text-center min-w-[120px]">
                    <div class="text-3xl font-black text-white">15+</div>
                    <div class="text-xs text-emerald-100 font-semibold">Years Experience</div>
                </div>
            </div>
        </div>

        <div class="relative max-w-7xl mx-auto px-6 py-20">
            <div class="grid grid-cols-4 gap-8 text-center">
                <div v-for="f in features" :key="f.title" class="group">
                    <div
                        class="w-16 h-16 bg-emerald-100 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition">
                        <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                :d="iconPath[f.icon]" />
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-1">{{ f.title }}</h3>
                    <p class="text-sm text-gray-600">{{ f.desc }}</p>
                </div>
            </div>
        </div>
    </section>

    <section id="appointments" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16">
                <div
                    class="inline-flex items-center gap-2 px-5 py-2 bg-emerald-50 border border-emerald-200 rounded-full mb-6">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="iconPath.calendar" />
                    </svg>
                    <span class="text-sm font-semibold text-emerald-700">Available Appointments</span>
                </div>
                <h2 class="text-5xl font-bold text-gray-900 mb-4">Book Your Appointment</h2>
                <p class="text-xl text-gray-600 font-light">Choose a convenient time slot from available options</p>
            </div>

            <div v-if="loading" class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <div v-for="i in 8" :key="i" class="h-48 bg-gray-50 rounded-2xl animate-pulse"></div>
            </div>

            <div v-else class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <div v-for="s in slots" :key="s.id"
                    class="group relative bg-white rounded-2xl transition-all duration-300"
                    :class="s.status === 'available' ? 'border-2 border-gray-200 hover:border-emerald-400 hover:shadow-xl shadow-lg cursor-pointer' : 'border-2 border-gray-100 opacity-50'">
                    <div v-if="s.status === 'available'"
                        class="absolute inset-0 bg-gradient-to-br from-emerald-50 to-teal-50 opacity-0 group-hover:opacity-100 transition-opacity rounded-2xl">
                    </div>
                    <div class="relative p-6 space-y-4">
                        <div class="flex justify-between items-start">
                            <div v-if="s.status === 'available'"
                                class="flex items-center gap-1.5 px-3 py-1 bg-emerald-100 border border-emerald-300 rounded-full">
                                <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                                <span class="text-xs font-bold text-emerald-700">Available</span>
                            </div>
                            <div v-else class="px-3 py-1 bg-gray-100 rounded-full">
                                <span class="text-xs font-bold text-gray-500">Booked</span>
                            </div>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 font-medium mb-2">{{ fmt(s.date) }}</p>
                            <p class="text-3xl font-bold text-gray-900">{{ s.start_time }}</p>
                        </div>
                        <button v-if="s.status === 'available'" @click="book(s.id)" :disabled="booking"
                            class="w-full py-3.5 bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-semibold rounded-xl hover:from-emerald-700 hover:to-teal-700 active:scale-95 transition-all disabled:opacity-50 shadow-lg shadow-emerald-500/30 group-hover:shadow-emerald-500/50">
                            {{ booking ? 'Booking...' : 'Book Now' }}
                        </button>
                        <div v-else
                            class="w-full py-3.5 text-center text-sm font-semibold text-gray-400 border-2 border-gray-200 rounded-xl">
                            Unavailable</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <teleport to="body">
        <transition name="modal">
            <div v-if="showModal"
                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-md"
                @click.self="showModal = false">
                <div class="bg-white rounded-3xl w-full max-w-lg p-8 shadow-2xl">
                    <div class="flex justify-between items-start mb-8">
                        <div>
                            <h3 class="text-3xl font-bold text-gray-900">My Appointment</h3>
                            <p class="text-gray-500 mt-1">Manage your booking</p>
                        </div>
                        <button @click="showModal = false"
                            class="w-12 h-12 hover:bg-gray-100 rounded-xl flex items-center justify-center transition group">
                            <svg class="w-6 h-6 text-gray-400 group-hover:text-gray-600 transition" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    :d="iconPath.close" />
                            </svg>
                        </button>
                    </div>

                    <div v-if="!myApt" class="py-16 text-center">
                        <div class="w-24 h-24 bg-gray-100 rounded-3xl flex items-center justify-center mx-auto mb-6">
                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    :d="iconPath.calendar" />
                            </svg>
                        </div>
                        <p class="text-gray-600 font-semibold text-lg mb-6">No appointment scheduled</p>
                        <a href="#appointments" @click="showModal = false"
                            class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-semibold rounded-xl hover:from-emerald-700 hover:to-teal-700 transition-all shadow-lg shadow-emerald-500/30">
                            Book Now
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    :d="iconPath.arrow" />
                            </svg>
                        </a>
                    </div>

                    <div v-else class="space-y-6">
                        <div
                            class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-2xl p-8 border-2 border-emerald-200">
                            <div class="flex items-center gap-6 mb-6">
                                <div
                                    class="w-20 h-20 bg-gradient-to-br from-emerald-600 to-teal-600 rounded-2xl flex items-center justify-center shadow-xl shadow-emerald-500/30">
                                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            :d="iconPath.calendar" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-4xl font-bold text-gray-900">{{ myApt.slot.start_time }}</p>
                                    <p class="text-gray-600 font-medium mt-1">{{ fmt(myApt.slot.date) }}</p>
                                </div>
                            </div>
                            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-xl font-semibold"
                                :class="myApt.status === 'confirmed' ? 'bg-emerald-200 text-emerald-800' : 'bg-red-200 text-red-800'">
                                <svg v-if="myApt.status === 'confirmed'" class="w-5 h-5" fill="currentColor"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" :d="iconPath.check" clip-rule="evenodd" />
                                </svg>
                                {{ myApt.status === 'confirmed' ? 'Confirmed' : myApt.status }}
                            </div>
                        </div>
                        <button @click="cancel"
                            class="w-full py-4 border-2 border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-red-50 hover:border-red-300 hover:text-red-700 transition-all">Cancel
                            Appointment</button>
                    </div>
                </div>
            </div>
        </transition>
    </teleport>

    <footer class="py-16 bg-gradient-to-b from-gray-50 to-white border-t border-gray-200">
        <div class="max-w-7xl mx-auto px-6 text-center space-y-6">
            <div class="inline-flex items-center gap-4">
                <div
                    class="w-16 h-16 bg-gradient-to-br from-emerald-600 via-teal-600 to-emerald-700 rounded-full flex items-center justify-center shadow-xl shadow-emerald-500/30">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="iconPath.heart" />
                    </svg>
                </div>
                <span class="text-2xl font-bold text-gray-900">Dr. Fathallah</span>
            </div>
            <p class="text-gray-600 font-medium">Excellence in healthcare. Compassion in every visit.</p>
            <p class="text-sm text-gray-500">© 2025 Dr. Fathallah Clinic. All rights reserved.</p>
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
        transform: translateY(0);
    }

    50% {
        transform: translateY(-20px);
    }
}

.animate-float {
    animation: float 6s ease-in-out infinite;
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
