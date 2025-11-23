<script setup>
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'

const timeSlots = ref([])
const loading = ref(true)
const error = ref(null)

// Modal State
const showModal = ref(false)
const modalMode = ref('create') // 'create' or 'edit'
const formLoading = ref(false)
const formData = ref({
    id: null,
    date: '',
    start_time: '',
    end_time: '',
    status: 'available'
})

// Filter State
const filterStatus = ref('all') // 'all', 'available', 'booked'
const searchQuery = ref('')


const modalTitle = computed(() => {
    return modalMode.value === 'create' ? 'Add New Time Slot' : 'Edit Time Slot'
})

// Filtered Time Slots
const filteredTimeSlots = computed(() => {
    let filtered = timeSlots.value

    // Filter by status
    if (filterStatus.value !== 'all') {
        filtered = filtered.filter(slot => slot.status === filterStatus.value)
    }

    // Filter by search query (date or time)
    if (searchQuery.value) {
        filtered = filtered.filter(slot =>
            slot.date.includes(searchQuery.value) ||
            slot.start_time.includes(searchQuery.value) ||
            slot.end_time.includes(searchQuery.value)
        )
    }

    return filtered
})

// Statistics
const statistics = computed(() => {
    return {
        total: timeSlots.value.length,
        available: timeSlots.value.filter(s => s.status === 'available').length,
        booked: timeSlots.value.filter(s => s.status === 'booked').length
    }
})

// Fetch Time Slots
const fetchTimeSlots = async () => {
    loading.value = true
    error.value = null

    try {
        const response = await axios.get('admin/time-slot')

        if (response.data.status === 'success') {
            timeSlots.value = response.data.data
        }
    } catch (err) {
        error.value = err.response?.data?.message || 'Failed to fetch time slots'
        console.error('Error fetching time slots:', err)
    } finally {
        loading.value = false
    }
}

// Open Modal for Create
const openCreateModal = () => {
    modalMode.value = 'create'
    formData.value = {
        id: null,
        date: '',
        start_time: '',
        end_time: '',
        status: 'available'
    }
    showModal.value = true
}

// Open Modal for Edit
const openEditModal = (slot) => {
    modalMode.value = 'edit'
    formData.value = {
        id: slot.id,
        date: slot.date,
        start_time: slot.start_time.substring(0, 5),
        end_time: slot.end_time.substring(0, 5),
        status: slot.status
    }
    showModal.value = true
}

// Close Modal
const closeModal = () => {
    showModal.value = false
    formData.value = {
        id: null,
        date: '',
        start_time: '',
        end_time: '',
        status: 'available'
    }
}

// Submit Form (Create or Edit)
const submitForm = async () => {
    formLoading.value = true
    error.value = null

    try {
        const payload = {
            date: formData.value.date,
            start_time: formData.value.start_time,
            end_time: formData.value.end_time
        }

        let response
        if (modalMode.value === 'create') {
            response = await axios.post('admin/time-slot', payload)
        } else {
            response = await axios.put(`admin/time-slot/${formData.value.id}`, payload)
        }

        if (response.data.status === 'success') {
            closeModal()
            fetchTimeSlots()
        }
    } catch (err) {
        error.value = err.response?.data?.message || 'Failed to save time slot'
        console.error('Error saving time slot:', err)
    } finally {
        formLoading.value = false
    }
}

// Delete Time Slot
const deleteTimeSlot = async (id) => {
    if (!confirm('Are you sure you want to delete this time slot?')) return

    try {
        await axios.delete(`admin/time-slot/${id}`)
        fetchTimeSlots()
    } catch (err) {
        error.value = err.response?.data?.message || 'Failed to delete time slot'
        console.error('Error deleting time slot:', err)
    }
}

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', {
        weekday: 'short',
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    })
}

const formatTime = (time) => {
    return new Date('2000-01-01 ' + time).toLocaleTimeString('en-US', {
        hour: '2-digit',
        minute: '2-digit',
        hour12: true
    })
}

const getStatusClass = (status) => {
    return status === 'available'
        ? 'bg-green-100 text-green-700 border-green-300'
        : 'bg-red-100 text-red-700 border-red-300'
}

const getStatusIcon = (status) => {
    return status === 'available' ? 'fa-check-circle' : 'fa-times-circle'
}

onMounted(() => {
    fetchTimeSlots()
})
</script>

<template>
    <div class="p-6 bg-gray-50 min-h-screen">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Time Slots Management</h1>
            <p class="text-gray-600">Manage individual time slots and their availability</p>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <!-- Total Slots -->
            <div
                class="bg-white rounded-2xl p-6 shadow-lg border border-gray-200 hover:shadow-xl transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium mb-1">Total Slots</p>
                        <p class="text-3xl font-bold text-gray-800">{{ statistics.total }}</p>
                    </div>
                    <div class="w-14 h-14 rounded-xl bg-blue-100 flex items-center justify-center">
                        <i class="fa-solid fa-calendar-days text-blue-600 text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Available Slots -->
            <div
                class="bg-white rounded-2xl p-6 shadow-lg border border-gray-200 hover:shadow-xl transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium mb-1">Available</p>
                        <p class="text-3xl font-bold text-green-600">{{ statistics.available }}</p>
                    </div>
                    <div class="w-14 h-14 rounded-xl bg-green-100 flex items-center justify-center">
                        <i class="fa-solid fa-check-circle text-green-600 text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Booked Slots -->
            <div
                class="bg-white rounded-2xl p-6 shadow-lg border border-gray-200 hover:shadow-xl transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium mb-1">Booked</p>
                        <p class="text-3xl font-bold text-red-600">{{ statistics.booked }}</p>
                    </div>
                    <div class="w-14 h-14 rounded-xl bg-red-100 flex items-center justify-center">
                        <i class="fa-solid fa-times-circle text-red-600 text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters and Actions -->
        <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-200 mb-6">
            <div class="flex flex-wrap gap-4 items-center justify-between">
                <!-- Search -->
                <div class="flex-1 min-w-[250px]">
                    <div class="relative">
                        <i
                            class="fa-solid fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input v-model="searchQuery" type="text" placeholder="Search by date or time..."
                            class="w-full pl-11 pr-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" />
                    </div>
                </div>

                <!-- Status Filter -->
                <div class="flex gap-2">
                    <button @click="filterStatus = 'all'" :class="[
                        'px-4 py-2 rounded-xl font-medium transition-all duration-300',
                        filterStatus === 'all'
                            ? 'bg-blue-500 text-white shadow-lg'
                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                    ]">
                        All
                    </button>
                    <button @click="filterStatus = 'available'" :class="[
                        'px-4 py-2 rounded-xl font-medium transition-all duration-300',
                        filterStatus === 'available'
                            ? 'bg-green-500 text-white shadow-lg'
                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                    ]">
                        Available
                    </button>
                    <button @click="filterStatus = 'booked'" :class="[
                        'px-4 py-2 rounded-xl font-medium transition-all duration-300',
                        filterStatus === 'booked'
                            ? 'bg-red-500 text-white shadow-lg'
                            : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                    ]">
                        Booked
                    </button>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-3">
                    <button @click="fetchTimeSlots"
                        class="px-6 py-3 bg-gray-600 text-white rounded-xl hover:bg-gray-700 transition-all duration-300 shadow-md hover:shadow-lg flex items-center gap-2">
                        <i class="fa-solid fa-rotate-right"></i>
                        Refresh
                    </button>
                    <button @click="openCreateModal"
                        class="px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl hover:from-blue-600 hover:to-blue-700 transition-all duration-300 shadow-md hover:shadow-lg flex items-center gap-2">
                        <i class="fa-solid fa-plus"></i>
                        Add Slot
                    </button>
                </div>
            </div>
        </div>

        <!-- Error State -->
        <div v-if="error" class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-lg">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fa-solid fa-exclamation-circle text-red-500 text-xl mr-3"></i>
                    <p class="text-red-700 font-medium">{{ error }}</p>
                </div>
                <button @click="error = null" class="text-red-500 hover:text-red-700">
                    <i class="fa-solid fa-times"></i>
                </button>
            </div>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="flex flex-col items-center justify-center py-20">
            <div class="animate-spin rounded-full h-16 w-16 border-4 border-blue-500 border-t-transparent mb-4"></div>
            <p class="text-gray-600 font-medium">Loading time slots...</p>
        </div>

        <!-- Empty State -->
        <div v-else-if="!loading && filteredTimeSlots.length === 0"
            class="bg-white rounded-2xl shadow-lg p-12 text-center">
            <i class="fa-solid fa-clock text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">No Time Slots Found</h3>
            <p class="text-gray-500 mb-6">
                {{ searchQuery || filterStatus !== 'all' ? 'Try adjusting your filters' : 'There are no time slots at the moment.' }}
            </p>
            <button v-if="!searchQuery && filterStatus === 'all'" @click="openCreateModal"
                class="px-6 py-3 bg-blue-500 text-white rounded-xl hover:bg-blue-600 transition-all duration-300 shadow-md">
                <i class="fa-solid fa-plus mr-2"></i>
                Create First Time Slot
            </button>
        </div>

        <!-- Time Slots Grid -->
        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <div v-for="slot in filteredTimeSlots" :key="slot.id" :class="[
                'bg-white rounded-2xl p-6 shadow-lg border-2 transition-all duration-300 hover:shadow-xl hover:-translate-y-1',
                slot.status === 'available' ? 'border-green-200 hover:border-green-400' : 'border-red-200 hover:border-red-400'
            ]">
                <!-- Header -->
                <div class="flex items-center justify-between mb-4">
                    <span
                        :class="['px-3 py-1 rounded-lg text-xs font-bold uppercase flex items-center gap-2', getStatusClass(slot.status)]">
                        <i :class="['fa-solid', getStatusIcon(slot.status)]"></i>
                        {{ slot.status }}
                    </span>
                    <span class="text-xs text-gray-500 font-medium">ID: #{{ slot.id }}</span>
                </div>

                <!-- Date -->
                <div class="mb-4">
                    <div class="flex items-center gap-2 mb-2">
                        <i class="fa-solid fa-calendar text-blue-500"></i>
                        <span class="text-xs text-gray-500 font-medium">Date</span>
                    </div>
                    <p class="text-sm font-semibold text-gray-800">{{ formatDate(slot.date) }}</p>
                </div>

                <!-- Time Range -->
                <div class="mb-6">
                    <div class="flex items-center gap-2 mb-2">
                        <i class="fa-solid fa-clock text-purple-500"></i>
                        <span class="text-xs text-gray-500 font-medium">Time Range</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-lg text-sm font-semibold">
                            {{ formatTime(slot.start_time) }}
                        </span>
                        <i class="fa-solid fa-arrow-right text-gray-400 text-xs"></i>
                        <span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-lg text-sm font-semibold">
                            {{ formatTime(slot.end_time) }}
                        </span>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex gap-2 pt-4 border-t border-gray-200">
                    <button @click="openEditModal(slot)"
                        class="flex-1 px-4 py-2 bg-yellow-50 text-yellow-600 rounded-lg hover:bg-yellow-100 transition-all duration-300 font-medium text-sm flex items-center justify-center gap-2">
                        <i class="fa-solid fa-edit"></i>
                        Edit
                    </button>
                    <button @click="deleteTimeSlot(slot.id)"
                        class="flex-1 px-4 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-all duration-300 font-medium text-sm flex items-center justify-center gap-2">
                        <i class="fa-solid fa-trash"></i>
                        Delete
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div v-if="showModal"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-50 backdrop-blur-sm">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md transform transition-all">
                <!-- Modal Header -->
                <div class="bg-gradient-to-r from-blue-500 to-purple-500 px-6 py-4 rounded-t-2xl">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-bold text-white flex items-center gap-2">
                            <i :class="['fa-solid', modalMode === 'create' ? 'fa-plus-circle' : 'fa-edit']"></i>
                            {{ modalTitle }}
                        </h3>
                        <button @click="closeModal" class="text-white hover:text-gray-200 transition-colors">
                            <i class="fa-solid fa-times text-xl"></i>
                        </button>
                    </div>
                </div>

                <!-- Modal Body -->
                <div class="p-6 space-y-4">
                    <!-- Date -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fa-solid fa-calendar mr-2 text-blue-500"></i>Date
                        </label>
                        <input v-model="formData.date" type="date"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                            required />
                    </div>

                    <!-- Start Time -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fa-solid fa-clock mr-2 text-green-500"></i>Start Time
                        </label>
                        <input v-model="formData.start_time" type="time"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                            required />
                    </div>

                    <!-- End Time -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fa-solid fa-clock mr-2 text-red-500"></i>End Time
                        </label>
                        <input v-model="formData.end_time" type="time"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                            required />
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="px-6 py-4 bg-gray-50 rounded-b-2xl flex gap-3 justify-end">
                    <button @click="closeModal" :disabled="formLoading"
                        class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition-all duration-300 font-medium">
                        Cancel
                    </button>
                    <button @click="submitForm" :disabled="formLoading"
                        class="px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl hover:from-blue-600 hover:to-blue-700 transition-all duration-300 font-medium flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                        <i v-if="formLoading" class="fa-solid fa-spinner fa-spin"></i>
                        <i v-else :class="['fa-solid', modalMode === 'create' ? 'fa-plus' : 'fa-save']"></i>
                        {{ formLoading ? 'Saving...' : (modalMode === 'create' ? 'Create' : 'Update') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

.animate-spin {
    animation: spin 1s linear infinite;
}
</style>