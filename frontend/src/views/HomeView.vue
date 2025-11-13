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
    <!-- Navbar -->
    <nav class="fixed inset-x-0 top-0 z-50 bg-white/90 backdrop-blur-xl border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
            <!-- Logo -->
            <div class="flex items-center gap-3">
                <div
                    class="w-10 h-10 bg-gradient-to-br from-teal-700 to-emerald-800 rounded-xl flex items-center justify-center shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2" />
                    </svg>
                </div>
                <h1 class="text-lg font-bold text-gray-900">Dr.Fathallah</h1>
            </div>

            <!-- Desktop Nav -->
            <div class=" md:flex items-center gap-6">
                <button @click="showCart = true"
                    class="relative p-2.5 rounded-xl bg-gray-50 hover:bg-gray-100 transition">
                    <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2-8m4 8v6m4-6v6m4-6v6" />
                    </svg>
                    <span v-if="cartItems"
                        class="absolute -top-1 -right-1 w-5 h-5 bg-rose-500 text-white text-xs rounded-full flex items-center justify-center font-bold">
                        {{ cartItems }}
                    </span>
                </button>

                <button @click="showApt = true" class="p-2.5 rounded-xl bg-gray-50 hover:bg-gray-100 transition">
                    <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </button>

                <template v-if="!isAuth">
                    <RouterLink to="/login"
                        class="px-5 py-2.5 bg-gradient-to-r from-teal-700 to-emerald-800 text-white rounded-xl font-medium shadow-md hover:shadow-lg transition">
                        Sign In
                    </RouterLink>
                </template>
                <button v-else @click="logout"
                    class="px-5 py-2.5 bg-rose-600 text-white rounded-xl font-medium shadow-md hover:shadow-lg transition">
                    Logout
                </button>
            </div>
        </div>
    </nav>

    <!-- HERO -->
    <section class="relative min-h-screen overflow-hidden bg-gradient-to-br from-teal-50 via-white to-emerald-50 pt-20">
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-40 -left-40 w-96 h-96 bg-teal-200/30 rounded-full blur-3xl animate-pulse"></div>
            <div
                class="absolute top-20 right-0 w-80 h-80 bg-emerald-200/30 rounded-full blur-3xl animate-pulse delay-1000">
            </div>
            <div
                class="absolute bottom-0 left-1/3 w-72 h-72 bg-cyan-200/20 rounded-full blur-3xl animate-pulse delay-500">
            </div>
        </div>

        <div
            class="relative grid grid-cols-1 lg:grid-cols-2 items-center min-h-screen max-w-7xl mx-auto px-6 gap-8 lg:gap-12">
            <!-- Content -->
            <div class="space-y-6 text-center lg:text-left">
                <div
                    class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-emerald-100 to-teal-100 rounded-full shadow-md">
                    <div class="w-2 h-2 bg-emerald-600 rounded-full animate-pulse"></div>
                    <span class="text-sm font-bold text-emerald-800">Premium Healthcare in Alexandria</span>
                </div>

                <h1 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-black leading-tight">
                    <span class="block text-gray-900">Excellence in</span>
                    <span
                        class="block bg-gradient-to-r from-teal-600 via-emerald-700 to-cyan-700 bg-clip-text text-transparent">
                        Modern Medicine
                    </span>
                </h1>

                <p class="text-base sm:text-lg md:text-xl text-gray-700 max-w-2xl mx-auto lg:mx-0">
                    Personalized consultations, cutting-edge treatments, and premium medical products —
                    <span class="text-emerald-700 font-bold">delivered with precision, care, and trust.</span>
                </p>

                <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                    <a href="#appointments"
                        class="group inline-flex items-center justify-center gap-3 px-8 py-4 bg-gradient-to-r from-teal-600 to-emerald-700 text-white text-base sm:text-lg font-bold rounded-2xl shadow-xl hover:shadow-2xl hover:-translate-y-1 transition-all duration-300">
                        Book Consultation
                        <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>

                    <a href="#products"
                        class="inline-flex items-center justify-center gap-2 px-8 py-4 border-4 border-teal-600 text-teal-700 text-base sm:text-lg font-bold rounded-2xl hover:bg-teal-600 hover:text-white transition-all duration-300">
                        Explore Products
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2" />
                        </svg>
                    </a>
                </div>

                <!-- Trust Indicators -->
                <div class="flex flex-wrap justify-center lg:justify-start gap-6 pt-8 text-sm">
                    <div
                        class="flex items-center gap-3 bg-white/60 backdrop-blur-sm px-4 py-3 rounded-2xl shadow-md border border-gray-100">
                        <div class="w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-emerald-700" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div>
                            <div class="font-bold text-gray-900">5000+</div>
                            <div class="text-gray-600">Happy Patients</div>
                        </div>
                    </div>

                    <div
                        class="flex items-center gap-3 bg-white/60 backdrop-blur-sm px-4 py-3 rounded-2xl shadow-md border border-gray-100">
                        <div class="w-10 h-10 bg-teal-100 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-teal-700" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.953a1 1 0 00.95.69h4.16c.969 0 1.371 1.24.588 1.81l-3.37 2.448a1 1 0 00-.364 1.118l1.287 3.953c.3.921-.755 1.688-1.54 1.118l-3.37-2.448a1 1 0 00-1.175 0l-3.37 2.448c-.784.57-1.838-.197-1.54-1.118l1.287-3.953a1 1 0 00-.364-1.118L2.316 9.38c-.784-.57-.38-1.81.588-1.81h4.16a1 1 0 00.95-.69l1.286-3.953z" />
                            </svg>
                        </div>
                        <div>
                            <div class="font-bold text-gray-900">4.9/5</div>
                            <div class="text-gray-600">Patient Rating</div>
                        </div>
                    </div>

                    <div
                        class="flex items-center gap-3 bg-white/60 backdrop-blur-sm px-4 py-3 rounded-2xl shadow-md border border-gray-100">
                        <div class="w-10 h-10 bg-amber-100 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-amber-700" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h.01a1 1 0 100-2H6zm2 0a1 1 0 000 2h.01a1 1 0 100-2H8zm2 0a1 1 0 000 2h.01a1 1 0 100-2H10zm2 0a1 1 0 000 2h.01a1 1 0 100-2H12zm-6 3a1 1 0 000 2h.01a1 1 0 100-2H6zm2 0a1 1 0 000 2h.01a1 1 0 100-2H8zm2 0a1 1 0 000 2h.01a1 1 0 100-2H10zm2 0a1 1 0 000 2h.01a1 1 0 100-2H12z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div>
                            <div class="font-bold text-gray-900">24/7</div>
                            <div class="text-gray-600">Support Available</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Doctor Image -->
            <div class="flex justify-center lg:justify-end">
                <div class="relative w-full max-w-xs sm:max-w-sm md:max-w-md lg:max-w-lg">
                    <div
                        class="absolute -inset-6 bg-white/30 backdrop-blur-2xl rounded-3xl shadow-2xl border border-white/50 transform rotate-3 scale-90 opacity-60 hidden lg:block">
                    </div>
                    <div class="relative bg-white rounded-3xl shadow-3xl overflow-hidden border-8 border-white">
                        <img src="../assets/logo.jpg" alt="Dr. Ahmed Hassan" class="w-full h-auto object-cover"
                            loading="eager" />
                    </div>
                </div>
            </div>
        </div>

        <div class="absolute bottom-6 left-1/2 transform -translate-x-1/2 animate-bounce">
            <svg class="w-7 h-7 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
            </svg>
        </div>
    </section>

    <!-- Appointments -->
    <section id="appointments" class="py-16 sm:py-24 bg-white">
        <div class="max-w-7xl mx-auto px-6">
            <h2 class="text-3xl sm:text-4xl font-bold text-center mb-12 sm:mb-16 text-gray-900">Available Appointments
            </h2>

            <div v-if="loadingSlots" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-6">
                <div v-for="i in 8" :key="i" class="bg-gray-100 rounded-2xl p-6 animate-pulse">
                    <div class="h-4 bg-gray-200 rounded mb-3"></div>
                    <div class="h-8 bg-gray-300 rounded-xl"></div>
                </div>
            </div>

            <div v-else class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-6">
                <div v-for="s in slots" :key="s.id"
                    class="bg-white rounded-2xl p-6 shadow-lg hover:shadow-xl border border-gray-100 transition text-center">
                    <p class="text-xs sm:text-sm text-gray-600">{{ fmt(s.date) }}</p>
                    <p class="text-lg sm:text-xl font-bold text-gray-900 mt-2">{{ s.start_time }}</p>

                    <button v-if="s.status === 'available'" @click="book(s.id)" :disabled="booking"
                        class="mt-4 w-full py-3 bg-gradient-to-r from-teal-700 to-emerald-800 text-white rounded-xl text-sm font-medium disabled:opacity-60">
                        {{ booking ? 'Booking...' : 'Book Now' }}
                    </button>

                    <p v-else class="mt-4 text-rose-600 font-semibold text-sm">Reserved</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Products -->
    <section id="products" class="py-16 sm:py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-6">
            <h2 class="text-3xl sm:text-4xl font-bold text-center mb-12 sm:mb-16 text-gray-900">Premium Medical Products
            </h2>

            <div v-if="loadingProducts" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div v-for="i in 8" :key="i" class="bg-white rounded-2xl p-5 animate-pulse">
                    <div class="h-40 bg-gray-200 rounded-xl mb-3"></div>
                    <div class="h-5 bg-gray-200 rounded mb-2"></div>
                    <div class="h-4 bg-gray-300 rounded w-20"></div>
                </div>
            </div>

            <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div v-for="p in products" :key="p.id"
                    class="bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-xl transition">
                    <img :src="p.image ? `http://127.0.0.1:8000/storage/${p.image}` : '/placeholder.jpg'"
                        class="w-full h-48 sm:h-56 object-cover">
                    <div class="p-5">
                        <h3 class="text-base sm:text-lg font-semibold text-gray-900 line-clamp-2">{{ p.name }}</h3>
                        <p class="text-xl sm:text-2xl font-bold text-teal-700 mt-2">{{ p.price }} EGP</p>
                        <button @click="addToCart(p.id)"
                            class="mt-4 w-full py-3 bg-gradient-to-r from-teal-700 to-emerald-800 text-white rounded-xl text-sm font-medium hover:shadow-lg transition">
                            Add to Cart
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modals -->
    <!-- Appointment Modal -->
    <teleport to="body">
        <div v-if="showApt" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
            @click.self="showApt = false">
            <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 sm:p-8">
                <h3 class="text-xl sm:text-2xl font-bold mb-6">Your Appointment</h3>
                <div v-if="!myApt" class="text-center py-12 text-gray-600 text-sm sm:text-base">
                    No appointment booked
                </div>
                <div v-else class="bg-gray-50 rounded-xl p-5 space-y-3 text-sm sm:text-base">
                    <p><span class="font-medium">Date:</span> {{ fmt(myApt.slot.date) }}</p>
                    <p><span class="font-medium">Time:</span> {{ myApt.slot.start_time }}</p>
                </div>
                <button v-if="myApt" @click="cancel"
                    class="mt-5 w-full py-3 bg-rose-600 text-white rounded-xl font-medium text-sm">
                    Cancel Appointment
                </button>
                <button @click="showApt = false" class="mt-3 text-gray-500 underline text-sm">Close</button>
            </div>
        </div>
    </teleport>

    <!-- Cart Modal -->
    <teleport to="body">
        <div v-if="showCart"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
            @click.self="showCart = false">
            <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[85vh] overflow-y-auto">
                <div class="p-6 sm:p-8 border-b">
                    <h3 class="text-xl sm:text-2xl font-bold">Shopping Cart ({{ cartItems }})</h3>
                </div>
                <div class="p-6 sm:p-8 space-y-5">
                    <div v-if="!cart.length" class="text-center py-16 text-gray-600 text-sm sm:text-base">Your cart is
                        empty
                    </div>
                    <div v-for="item in cart" :key="item.id" class="flex items-center gap-4 bg-gray-50 rounded-xl p-4">
                        <img :src="item.product.image ? `http://127.0.0.1:8000/storage/${item.product.image}` : '/placeholder.jpg'"
                            class="w-16 h-16 rounded-xl object-cover">
                        <div class="flex-1">
                            <p class="font-semibold text-sm sm:text-base">{{ item.product.name }}</p>
                            <p class="text-xs sm:text-sm text-gray-600">{{ item.product.price }} EGP × {{ item.quantity
                                }}
                            </p>
                        </div>
                        <div class="flex items-center gap-2">
                            <button @click="updateQty(item.id, item.quantity - 1)"
                                class="w-8 h-8 rounded-full bg-gray-300 hover:bg-gray-400 text-sm">-</button>
                            <span class="w-10 text-center font-medium text-sm">{{ item.quantity }}</span>
                            <button @click="updateQty(item.id, item.quantity + 1)"
                                class="w-8 h-8 rounded-full bg-teal-700 text-white hover:bg-teal-800 text-sm">+</button>
                        </div>
                    </div>
                </div>
                <div class="p-6 sm:p-8 bg-gradient-to-r from-teal-50 to-emerald-50 border-t">
                    <div class="flex justify-between text-lg sm:text-2xl font-bold mb-5">
                        <span>Total</span>
                        <span class="text-teal-800">{{ cartTotal }} EGP</span>
                    </div>
                    <button @click="checkout"
                        class="w-full py-3 sm:py-4 bg-gradient-to-r from-teal-700 to-emerald-800 text-white rounded-xl font-medium shadow-lg hover:shadow-xl text-sm sm:text-base">
                        Complete Purchase
                    </button>
                </div>
            </div>
        </div>
    </teleport>

    <!-- Footer -->
    <footer class="py-12 sm:py-16 bg-gradient-to-r from-teal-900 to-emerald-900 text-white text-center">
        <div class="max-w-7xl mx-auto px-6">
            <h3 class="text-xl sm:text-2xl font-bold mb-1">Dr.Fathallah Clinic</h3>
            <p class="text-teal-200 text-sm sm:text-base">Alexandria • Egypt</p>
            <p class="mt-6 sm:mt-8 text-xs sm:text-sm opacity-70">© 2025 All rights reserved.</p>
        </div>
    </footer>
</template>

<style>
html {
    scroll-behavior: smooth;
}
</style>