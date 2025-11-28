<script setup>
import Spinner from '@/components/Spinner.vue'
import axios from 'axios'
import { ref, onMounted, computed } from 'vue'
import { RouterLink } from 'vue-router'
import { useToast } from 'vue-toastification'

const toast = useToast()
const isAuth = ref(false)

// Data
const slots = ref([])
const products = ref([])
const myApt = ref(null)
const cart = ref([])
const loadingSlots = ref(true)
const loadingProducts = ref(true)
const booking = ref(false)

// Modals
const showApt = ref(false)
const showCart = ref(false)

// Computed
const cartItems = computed(() => cart.value.reduce((s, i) => s + i.quantity, 0))
const cartTotal = computed(() => cart.value.reduce((s, i) => s + i.quantity * +i.product.price, 0).toFixed(2))

// API Functions
const fetchSlots = async () => {
    try { slots.value = (await axios.get('/time-slot')).data.data || [] }
    catch { toast.error('Failed to load appointments') }
    finally { loadingSlots.value = false }
}

const fetchProducts = async () => {
    try { products.value = (await axios.get('/products')).data.data || [] }
    catch { toast.error('Failed to load products') }
    finally { loadingProducts.value = false }
}

const fetchMyApt = async () => {
    try { myApt.value = (await axios.get('/user-time-slot')).data.data[0] || null }
    catch { }
}

const book = async (id) => {
    if (!isAuth.value) return toast.warning('Please sign in first')
    try {
        booking.value = true
        await axios.post('/appointment', { slot_id: id })
        await Promise.all([fetchSlots(), fetchMyApt()])
        toast.success('Appointment booked!')
        showApt.value = true
    } catch (e) { toast.error(e.response?.data?.message || 'Booking failed') }
    finally { booking.value = false }
}

const cancel = async () => {
    if (!confirm('Cancel appointment?')) return
    try {
        await axios.delete(`/appointment/${myApt.value.id}`)
        await Promise.all([fetchSlots(), fetchMyApt()])
        toast.success('Cancelled')
        showApt.value = false
    } catch { toast.error('Failed') }
}

const addToCart = async (id) => {
    try {
        await axios.post('/cart', { product_id: id, quantity: 1 })
        toast.success('Added to cart')
        fetchCart()
    } catch { toast.error('Failed to add') }
}

const fetchCart = async () => {
    try { cart.value = (await axios.get('/cart')).data.data || [] }
    catch { }
}

const updateQty = async (id, qty) => {
    if (qty < 1) return removeFromCart(id)
    await axios.put(`/cart/${id}`, { quantity: qty })
    fetchCart()
}

const removeFromCart = async (id) => {
    await axios.delete(`/cart/${id}`)
    fetchCart()
    toast.success('Removed')
}

const checkout = async () => {
    try {
        await axios.post('/checkout')
        toast.success('Order placed! Check your email.')
        cart.value = []
        showCart.value = false
    } catch { toast.error('Checkout failed') }
}

const logout = () => {

    localStorage.removeItem('token')
    localStorage.clear()

    cart.value = []
    myApt.value = null
    slots.value = []
    products.value = []
    isAuth.value = false

    window.location.reload()

    toast.success('Logged out successfully')
}

const fmt = (d) => new Date(d).toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric' })

onMounted(() => {
    if (localStorage.token) { isAuth.value = true; fetchCart() }
    fetchSlots()
    fetchProducts()
    fetchMyApt()
})
</script>

<template>
    <!-- Navbar - Glassmorphism -->
    <nav class="fixed inset-x-0 top-0 z-50">
        <div class="mx-4 mt-4">
            <div
                class="max-w-6xl mx-auto bg-white/70 backdrop-blur-2xl rounded-2xl border border-white/50 shadow-lg shadow-black/5">
                <div class="px-6 h-16 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="relative group">
                            <div
                                class="absolute -inset-1 bg-gradient-to-r from-teal-500 to-emerald-500 rounded-xl blur opacity-40 group-hover:opacity-70 transition duration-300">
                            </div>
                            <div
                                class="relative w-10 h-10 bg-gradient-to-br from-teal-500 to-emerald-600 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                            </div>
                        </div>
                        <div>
                            <span class="font-bold text-gray-900">Dr.Fathallah</span>
                            <span class="hidden sm:inline text-xs text-gray-400 ml-2">Premium Healthcare</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <button @click="showCart = true"
                            class="relative p-2.5 rounded-xl bg-gray-100/80 hover:bg-gray-200/80 transition-all duration-300 group">
                            <svg class="w-5 h-5 text-gray-600 group-hover:scale-110 transition-transform" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                            <span v-if="cartItems"
                                class="absolute -top-1 -right-1 w-5 h-5 bg-gradient-to-r from-rose-500 to-pink-500 text-white text-[10px] rounded-full flex items-center justify-center font-bold shadow-lg animate-pulse">
                                {{ cartItems }}
                            </span>
                        </button>

                        <button @click="showApt = true"
                            class="p-2.5 rounded-xl bg-gray-100/80 hover:bg-gray-200/80 transition-all duration-300 group">
                            <svg class="w-5 h-5 text-gray-600 group-hover:scale-110 transition-transform" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </button>

                        <template v-if="!isAuth">
                            <RouterLink to="/login"
                                class="ml-2 px-5 py-2.5 bg-gradient-to-r from-teal-500 to-emerald-600 text-white text-sm font-semibold rounded-xl hover:shadow-lg hover:shadow-teal-500/30 hover:-translate-y-0.5 transition-all duration-300">
                                Sign In
                            </RouterLink>
                        </template>
                        <button v-else @click="logout"
                            class="ml-2 px-4 py-2.5 text-gray-600 text-sm font-medium rounded-xl hover:bg-gray-100 transition">
                            Logout
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero - Spectacular -->
    <section class="min-h-screen relative overflow-hidden bg-[#fafbfc]">
        <div class="absolute inset-0">
            <div
                class="absolute top-0 left-0 w-full h-full bg-[radial-gradient(ellipse_at_top_left,_var(--tw-gradient-stops))] from-teal-100/60 via-transparent to-transparent">
            </div>
            <div
                class="absolute bottom-0 right-0 w-full h-full bg-[radial-gradient(ellipse_at_bottom_right,_var(--tw-gradient-stops))] from-emerald-100/60 via-transparent to-transparent">
            </div>

            <div class="absolute top-20 left-10 w-72 h-72 bg-teal-300/30 rounded-full blur-3xl animate-float"></div>
            <div
                class="absolute bottom-20 right-10 w-96 h-96 bg-emerald-300/30 rounded-full blur-3xl animate-float-delayed">
            </div>
            <div
                class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[40rem] h-[40rem] bg-cyan-200/20 rounded-full blur-3xl">
            </div>

            <div
                class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg%20width%3D%2260%22%20height%3D%2260%22%20viewBox%3D%220%200%2060%2060%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3Cg%20fill%3D%22none%22%20fill-rule%3D%22evenodd%22%3E%3Cg%20fill%3D%22%239CA3AF%22%20fill-opacity%3D%220.05%22%3E%3Cpath%20d%3D%22M36%2034v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6%2034v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6%204V0H4v4H0v2h4v4h2V6h4V4H6z%22%2F%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E')] opacity-50">
            </div>
        </div>

        <div class="relative max-w-6xl mx-auto px-5 pt-32 pb-20 grid lg:grid-cols-2 gap-12 items-center min-h-screen">
            <div class="space-y-8 text-center lg:text-left">
                <div
                    class="inline-flex items-center gap-2 px-4 py-2 bg-white/80 backdrop-blur-sm rounded-full border border-gray-200/50 shadow-sm">
                    <span class="relative flex h-2 w-2">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    <span class="text-sm font-medium text-gray-700">Now accepting new patients</span>
                </div>

                <h1 class="text-5xl sm:text-6xl lg:text-7xl font-black leading-[1.1] tracking-tight">
                    <span class="text-gray-900">Exceptional</span><br>
                    <span class="relative">
                        <span
                            class="bg-gradient-to-r from-teal-600 via-emerald-600 to-cyan-600 bg-clip-text text-transparent">
                            Healthcare
                        </span>
                        <svg class="absolute -bottom-2 left-0 w-full h-3 text-teal-500/30" viewBox="0 0 200 9"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill="currentColor" d="M0,7 C50,9 100,4 150,6 L200,5 L200,9 L0,9 Z"></path>
                        </svg>
                    </span><br>
                    <span class="text-gray-900">Experience</span>
                </h1>

                <p class="text-xl text-gray-600 max-w-lg mx-auto lg:mx-0 leading-relaxed">
                    Where <span class="text-teal-600 font-semibold">medical excellence</span> meets compassionate care.
                    Your journey to better health starts here.
                </p>

                <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                    <a href="#appointments" class="group relative inline-flex items-center justify-center">
                        <div
                            class="absolute -inset-0.5 bg-gradient-to-r from-teal-500 to-emerald-500 rounded-2xl blur opacity-60 group-hover:opacity-100 transition duration-300">
                        </div>
                        <span
                            class="relative flex items-center gap-2 px-8 py-4 bg-gradient-to-r from-teal-500 to-emerald-600 text-white font-bold rounded-xl">
                            Book Consultation
                            <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </span>
                    </a>
                    <a href="#products"
                        class="px-8 py-4 bg-white text-gray-800 font-bold rounded-xl border-2 border-gray-200 hover:border-teal-300 hover:shadow-lg transition-all duration-300">
                        Browse Products
                    </a>
                </div>

                <div class="grid grid-cols-3 gap-4 pt-8">
                    <div
                        class="text-center lg:text-left p-4 rounded-2xl bg-white/50 backdrop-blur-sm border border-white">
                        <div
                            class="text-3xl sm:text-4xl font-black bg-gradient-to-r from-teal-600 to-emerald-600 bg-clip-text text-transparent">
                            5K+</div>
                        <div class="text-sm text-gray-500 mt-1">Happy Patients</div>
                    </div>
                    <div
                        class="text-center lg:text-left p-4 rounded-2xl bg-white/50 backdrop-blur-sm border border-white">
                        <div
                            class="text-3xl sm:text-4xl font-black bg-gradient-to-r from-teal-600 to-emerald-600 bg-clip-text text-transparent">
                            4.9</div>
                        <div class="text-sm text-gray-500 mt-1">Star Rating</div>
                    </div>
                    <div
                        class="text-center lg:text-left p-4 rounded-2xl bg-white/50 backdrop-blur-sm border border-white">
                        <div
                            class="text-3xl sm:text-4xl font-black bg-gradient-to-r from-teal-600 to-emerald-600 bg-clip-text text-transparent">
                            15+</div>
                        <div class="text-sm text-gray-500 mt-1">Years Exp.</div>
                    </div>
                </div>
            </div>

            <div class="flex justify-center lg:justify-end">
                <div class="relative">
                    <div
                        class="absolute -top-8 -left-8 w-24 h-24 bg-gradient-to-br from-teal-400 to-emerald-400 rounded-2xl rotate-12 opacity-20 blur-sm">
                    </div>
                    <div
                        class="absolute -bottom-6 -right-6 w-32 h-32 bg-gradient-to-br from-cyan-400 to-teal-400 rounded-full opacity-20 blur-sm">
                    </div>

                    <div
                        class="absolute -left-12 top-1/4 p-3 bg-white rounded-xl shadow-xl border border-gray-100 animate-float z-10">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-gray-700">Verified Doctor</span>
                        </div>
                    </div>

                    <div
                        class="absolute -right-8 bottom-1/4 p-3 bg-white rounded-xl shadow-xl border border-gray-100 animate-float-delayed z-10">
                        <div class="flex items-center gap-2">
                            <div class="flex -space-x-2">
                                <div
                                    class="w-6 h-6 bg-gradient-to-r from-yellow-400 to-orange-400 rounded-full border-2 border-white">
                                </div>
                                <div
                                    class="w-6 h-6 bg-gradient-to-r from-pink-400 to-rose-400 rounded-full border-2 border-white">
                                </div>
                                <div
                                    class="w-6 h-6 bg-gradient-to-r from-blue-400 to-cyan-400 rounded-full border-2 border-white">
                                </div>
                            </div>
                            <span class="text-sm font-medium text-gray-700">+5k patients</span>
                        </div>
                    </div>

                    <div class="relative">
                        <div
                            class="absolute -inset-4 bg-gradient-to-r from-teal-500 to-emerald-500 rounded-3xl blur-2xl opacity-30">
                        </div>
                        <div class="relative bg-gradient-to-br from-teal-100 to-emerald-100 p-3 rounded-3xl">
                            <img src="../assets/logo.jpg" alt="Dr. Fathallah"
                                class="w-full max-w-md rounded-2xl shadow-2xl" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="absolute bottom-8 left-1/2 -translate-x-1/2">
            <div class="flex flex-col items-center gap-2 text-gray-400">
                <span class="text-xs font-medium uppercase tracking-widest">Scroll</span>
                <div class="w-6 h-10 rounded-full border-2 border-gray-300 flex justify-center pt-2">
                    <div class="w-1.5 h-3 bg-gray-400 rounded-full animate-bounce"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Appointments - Premium Cards -->
    <section id="appointments" class="py-24 bg-white relative overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-px bg-gradient-to-r from-transparent via-gray-200 to-transparent">
        </div>

        <div class="max-w-6xl mx-auto px-5">
            <div class="text-center mb-16">
                <span
                    class="inline-block px-4 py-1.5 bg-teal-100 text-teal-700 text-sm font-semibold rounded-full mb-4">
                    Appointments
                </span>
                <h2 class="text-4xl sm:text-5xl font-black text-gray-900 mb-4">
                    Choose Your <span class="text-teal-600">Time</span>
                </h2>
                <p class="text-gray-500 text-lg max-w-xl mx-auto">
                    Select a convenient slot and we'll take care of the rest
                </p>
            </div>

            <div v-if="loadingSlots" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                <div v-for="i in 8" :key="i"
                    class="h-44 bg-gradient-to-br from-gray-100 to-gray-50 rounded-2xl animate-pulse"></div>
            </div>

            <div v-else class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                <div v-for="s in slots" :key="s.id" class="group relative p-5 rounded-2xl transition-all duration-500"
                    :class="s.status === 'available'
                        ? 'bg-white border-2 border-gray-100 hover:border-teal-300 hover:shadow-xl hover:shadow-teal-100/50 hover:-translate-y-1'
                        : 'bg-gray-50 border-2 border-gray-100 opacity-60'">

                    <div v-if="s.status === 'available'"
                        class="absolute -top-2 -right-2 px-2 py-0.5 bg-gradient-to-r from-green-400 to-emerald-500 text-white text-[10px] font-bold rounded-full shadow-lg">
                        OPEN
                    </div>

                    <div class="text-center">
                        <p class="text-sm text-gray-500 font-medium">{{ fmt(s.date) }}</p>
                        <p class="text-2xl font-black text-gray-900 mt-2 group-hover:text-teal-600 transition-colors">
                            {{ s.start_time }}
                        </p>

                        <button v-if="s.status === 'available'" @click="book(s.id)" :disabled="booking"
                            class="mt-4 w-full py-2.5 bg-gradient-to-r from-teal-500 to-emerald-600 text-white text-sm font-bold rounded-xl hover:shadow-lg hover:shadow-teal-500/30 active:scale-95 transition-all duration-300 disabled:opacity-50">
                            {{ booking ? 'Booking...' : 'Book Now' }}
                        </button>

                        <div v-else class="mt-4 flex items-center justify-center gap-1 text-gray-400">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span class="text-sm font-medium">Reserved</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Products - Magazine Style -->
    <section id="products" class="py-24 bg-gradient-to-b from-gray-50 to-white relative">
        <div class="max-w-6xl mx-auto px-5">
            <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-12">
                <div>
                    <span
                        class="inline-block px-4 py-1.5 bg-gradient-to-r from-teal-100 to-emerald-100 text-teal-700 text-sm font-semibold rounded-full mb-4">
                        Shop Now
                    </span>
                    <h2 class="text-4xl sm:text-5xl font-black text-gray-900">
                        Premium <span class="text-teal-600">Products</span>
                    </h2>
                </div>
            </div>

            <div v-if="loadingProducts" class="grid grid-cols-2 lg:grid-cols-4 gap-6">
                <div v-for="i in 8" :key="i" class="bg-white rounded-3xl p-4 animate-pulse">
                    <div class="h-48 bg-gray-100 rounded-2xl mb-4"></div>
                    <div class="h-4 bg-gray-100 rounded w-3/4 mb-2"></div>
                    <div class="h-6 bg-gray-100 rounded w-1/2 mb-4"></div>
                    <div class="h-10 bg-gray-100 rounded-xl"></div>
                </div>
            </div>

            <div v-else class="grid grid-cols-2 lg:grid-cols-4 gap-6">
                <div v-for="p in products" :key="p.id"
                    class="group bg-white rounded-3xl overflow-hidden border border-gray-100 hover:border-gray-200 shadow-sm hover:shadow-2xl transition-all duration-500 hover:-translate-y-2">

                    <!-- Image -->
                    <div class="relative overflow-hidden h-48">
                        <img :src="p.image ? `http://127.0.0.1:8000/storage/${p.image}` : '/placeholder.jpg'"
                            class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                    </div>

                    <!-- Content -->
                    <div class="p-5">
                        <h3
                            class="font-bold text-gray-900 line-clamp-2 group-hover:text-teal-600 transition-colors text-base">
                            {{ p.name }}
                        </h3>

                        <div class="flex items-center justify-between mt-3">
                            <p class="text-2xl font-black text-teal-600">
                                {{ p.price }}
                                <span class="text-sm font-medium text-gray-400">EGP</span>
                            </p>
                            <div class="flex items-center gap-1 text-amber-400">
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                    <path
                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                                <span class="text-xs text-gray-500">4.9</span>
                            </div>
                        </div>

                        <button @click="addToCart(p.id)"
                            class="mt-4 w-full py-3 bg-gradient-to-r from-gray-900 to-gray-800 text-white font-bold rounded-xl hover:from-teal-500 hover:to-emerald-600 hover:shadow-lg hover:shadow-teal-500/30 active:scale-95 transition-all duration-300">
                            <span class="flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                </svg>
                                Add to Cart
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Appointment Modal -->
    <teleport to="body">
        <transition name="modal">
            <div v-if="showApt"
                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
                @click.self="showApt = false">
                <div
                    class="bg-white/95 backdrop-blur-xl rounded-3xl w-full max-w-md p-8 shadow-2xl border border-white/50 transform transition-all">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h3 class="text-2xl font-black text-gray-900">My Appointment</h3>
                            <p class="text-sm text-gray-500">Manage your booking</p>
                        </div>
                        <button @click="showApt = false"
                            class="w-10 h-10 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition-colors">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div v-if="!myApt" class="py-12 text-center">
                        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <p class="text-gray-500 font-medium">No appointment booked yet</p>
                        <a href="#appointments" @click="showApt = false"
                            class="inline-block mt-4 text-teal-600 font-semibold hover:underline">
                            Book one now →
                        </a>
                    </div>

                    <div v-else class="space-y-4">
                        <div
                            class="bg-gradient-to-br from-teal-50 to-emerald-50 rounded-2xl p-6 border border-teal-100">
                            <div class="flex items-center gap-4 mb-4">
                                <div
                                    class="w-14 h-14 bg-gradient-to-br from-teal-500 to-emerald-600 rounded-2xl flex items-center justify-center">
                                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-2xl font-black text-gray-900">{{ myApt.slot.start_time }}</p>
                                    <p class="text-gray-600">{{ fmt(myApt.slot.date) }}</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 text-sm text-teal-700 bg-white/60 rounded-xl px-4 py-2">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span class="font-medium">Confirmed</span>
                            </div>
                        </div>

                        <button @click="cancel"
                            class="w-full py-3.5 border-2 border-red-200 text-red-600 font-bold rounded-xl hover:bg-red-50 transition-colors">
                            Cancel Appointment
                        </button>
                    </div>
                </div>
            </div>
        </transition>
    </teleport>

    <!-- Cart Modal -->
    <teleport to="body">
        <transition name="modal">
            <div v-if="showCart"
                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
                @click.self="showCart = false">
                <div class="bg-white rounded-3xl w-full max-w-lg max-h-[85vh] flex flex-col shadow-2xl overflow-hidden">
                    <div
                        class="p-6 border-b border-gray-100 flex justify-between items-center bg-gradient-to-r from-gray-50 to-white">
                        <div>
                            <h3 class="text-2xl font-black text-gray-900">Shopping Cart</h3>
                            <p class="text-sm text-gray-500">{{ cartItems }} items</p>
                        </div>
                        <button @click="showCart = false"
                            class="w-10 h-10 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition-colors">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div v-if="!cart.length" class="flex-1 flex flex-col items-center justify-center py-16 px-6">
                        <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                        </div>
                        <p class="text-gray-500 font-medium mb-2">Your cart is empty</p>
                        <a href="#products" @click="showCart = false"
                            class="text-teal-600 font-semibold hover:underline">
                            Start shopping →
                        </a>
                    </div>

                    <div v-else class="flex-1 overflow-y-auto p-6 space-y-4">
                        <div v-for="item in cart" :key="item.id"
                            class="flex gap-4 p-4 bg-gray-50 rounded-2xl hover:bg-gray-100 transition-colors">
                            <img :src="item.product.image ? `http://127.0.0.1:8000/storage/${item.product.image}` : '/placeholder.jpg'"
                                class="w-20 h-20 rounded-xl object-cover shadow-sm">
                            <div class="flex-1 min-w-0">
                                <h4 class="font-bold text-gray-900 truncate">{{ item.product.name }}</h4>
                                <p class="text-lg font-black text-teal-600">{{ item.product.price }} EGP</p>
                            </div>
                            <div class="flex flex-col items-center gap-2">
                                <div class="flex items-center gap-1 bg-white rounded-full p-1 shadow-sm">
                                    <button @click="updateQty(item.id, item.quantity - 1)"
                                        class="w-8 h-8 rounded-full hover:bg-gray-100 flex items-center justify-center text-gray-600 font-bold transition-colors">
                                        −
                                    </button>
                                    <span class="w-8 text-center font-bold text-gray-900">{{ item.quantity }}</span>
                                    <button @click="updateQty(item.id, item.quantity + 1)"
                                        class="w-8 h-8 rounded-full bg-teal-500 hover:bg-teal-600 text-white flex items-center justify-center font-bold transition-colors">
                                        +
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-if="cart.length" class="p-6 border-t border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-gray-600 font-medium">Total</span>
                            <span class="text-3xl font-black text-gray-900">{{ cartTotal }} <span
                                    class="text-base font-medium text-gray-500">EGP</span></span>
                        </div>
                        <button @click="checkout" class="relative w-full group">
                            <div
                                class="absolute -inset-0.5 bg-gradient-to-r from-teal-500 to-emerald-500 rounded-xl blur opacity-60 group-hover:opacity-100 transition duration-300">
                            </div>
                            <span
                                class="relative block py-4 bg-gradient-to-r from-teal-500 to-emerald-600 text-white font-bold rounded-xl text-center">
                                Complete Purchase
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </transition>
    </teleport>

    <!-- Footer -->
    <footer class="py-16 bg-gray-900 relative overflow-hidden">
        <div
            class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg%20width%3D%2260%22%20height%3D%2260%22%20viewBox%3D%220%200%2060%2060%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3Cg%20fill%3D%22none%22%20fill-rule%3D%22evenodd%22%3E%3Cg%20fill%3D%22%23ffffff%22%20fill-opacity%3D%220.03%22%3E%3Cpath%20d%3D%22M36%2034v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6%2034v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6%204V0H4v4H0v2h4v4h2V6h4V4H6z%22%2F%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E')] opacity-50">
        </div>

        <div class="max-w-6xl mx-auto px-5 relative">
            <div class="text-center">
                <div class="inline-flex items-center gap-3 mb-6">
                    <div
                        class="w-12 h-12 bg-gradient-to-br from-teal-400 to-emerald-500 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                    </div>
                    <span class="text-2xl font-bold text-white">Dr.Fathallah</span>
                </div>

                <p class="text-gray-400 mb-8 max-w-md mx-auto">
                    Providing exceptional healthcare services in Alexandria, Egypt. Your health is our priority.
                </p>

                <div class="pt-8 border-t border-white/10">
                    <p class="text-gray-500 text-sm">© 2025 Dr.Fathallah Clinic. All rights reserved.</p>
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