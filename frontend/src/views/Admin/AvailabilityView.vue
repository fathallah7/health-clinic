<script setup>
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'

const availabilities = ref([])
const loading = ref(true)
const error = ref(null)
const expandedRows = ref([])

// Modal State
const showModal = ref(false)
const modalMode = ref('create') // 'create' or 'edit'
const formLoading = ref(false)
const formData = ref({
    id: null,
    date: '',
    start_time: '',
    end_time: '',
    slot_duration: 30
})

const modalTitle = computed(() => {
    return modalMode.value === 'create' ? 'Add New Availability' : 'Edit Availability'
})


// Fetch Availabilities
const fetchAvailabilities = async () => {
    loading.value = true
    error.value = null

    try {
        const token = localStorage.getItem('token')
        const response = await axios.get('admin/availability')

        if (response.data.status === 'success') {
            availabilities.value = response.data.data
        }
    } catch (err) {
        error.value = err.response?.data?.message || 'Failed to fetch availabilities'
        console.error('Error fetching availabilities:', err)
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
        slot_duration: 30
    }
    showModal.value = true
}

// Open Modal for Edit
const openEditModal = (availability) => {
    modalMode.value = 'edit'
    formData.value = {
        id: availability.id,
        date: availability.date,
        start_time: availability.start_time.substring(0, 5), // Convert "10:00:00" to "10:00"
        end_time: availability.end_time.substring(0, 5),
        slot_duration: availability.slot_duration
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
        slot_duration: 30
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
            end_time: formData.value.end_time,
            slot_duration: formData.value.slot_duration
        }

        let response
        if (modalMode.value === 'create') {
            // POST Request
            response = await axios.post('admin/availability', payload)
        } else {
            // PUT Request
            response = await axios.put(`admin/availability/${formData.value.id}`, payload)
        }

        if (response.data.status === 'success') {
            closeModal()
            fetchAvailabilities() // Refresh the list
        }
    } catch (err) {
        error.value = err.response?.data?.message || 'Failed to save availability'
        console.error('Error saving availability:', err)
    } finally {
        formLoading.value = false
    }
}

// Delete Availability
const deleteAvailability = async (id) => {
    if (!confirm('Are you sure you want to delete this availability?')) return

    try {
        await axios.delete(`admin/availability/${id}`)
        fetchAvailabilities() // Refresh the list
    } catch (err) {
        error.value = err.response?.data?.message || 'Failed to delete availability'
        console.error('Error deleting availability:', err)
    }
}

const toggleRow = (id) => {
    const index = expandedRows.value.indexOf(id)
    if (index > -1) {
        expandedRows.value.splice(index, 1)
    } else {
        expandedRows.value.push(id)
    }
}

const isExpanded = (id) => {
    return expandedRows.value.includes(id)
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

const getStatusIcon = (status) => {
    return status === 'available' ? 'fa-check-circle' : 'fa-times-circle'
}

const countAvailableSlots = (slots) => {
    return slots.filter(slot => slot.status === 'available').length
}

const countBookedSlots = (slots) => {
    return slots.filter(slot => slot.status === 'booked').length
}

onMounted(() => {
    fetchAvailabilities()
})
</script>

<template>
    <div class="p-6 bg-gray-50 min-h-screen">
        <!-- Header -->
        <div class="mb-6 flex items-center justify-between flex-wrap gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Availability Management</h1>
                <p class="text-gray-600">Manage your time slots and appointments</p>
            </div>
            <div class="flex gap-3">
                <button @click="fetchAvailabilities"
                    class="px-6 py-3 bg-gray-600 text-white rounded-xl hover:bg-gray-700 transition-all duration-300 shadow-md hover:shadow-lg flex items-center gap-2">
                    <i class="fa-solid fa-rotate-right"></i>
                    Refresh
                </button>
                <button @click="openCreateModal"
                    class="px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl hover:from-blue-600 hover:to-blue-700 transition-all duration-300 shadow-md hover:shadow-lg flex items-center gap-2">
                    <i class="fa-solid fa-plus"></i>
                    Add Availability
                </button>
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
            <p class="text-gray-600 font-medium">Loading availabilities...</p>
        </div>

        <!-- Empty State -->
        <div v-else-if="!loading && availabilities.length === 0"
            class="bg-white rounded-2xl shadow-lg p-12 text-center">
            <i class="fa-solid fa-calendar-xmark text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">No Availabilities Found</h3>
            <p class="text-gray-500 mb-6">There are no availability schedules at the moment.</p>
            <button @click="openCreateModal"
                class="px-6 py-3 bg-blue-500 text-white rounded-xl hover:bg-blue-600 transition-all duration-300 shadow-md">
                <i class="fa-solid fa-plus mr-2"></i>
                Create First Availability
            </button>
        </div>

        <!-- Table -->
        <div v-else class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-200">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <!-- Table Header -->
                    <thead class="bg-gradient-to-r from-blue-50 to-purple-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <i class="fa-solid fa-calendar mr-2"></i>Date
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <i class="fa-solid fa-clock mr-2"></i>Time Range
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <i class="fa-solid fa-hourglass-half mr-2"></i>Duration
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <i class="fa-solid fa-chart-pie mr-2"></i>Status
                            </th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>

                    <!-- Table Body -->
                    <tbody class="divide-y divide-gray-200">
                        <template v-for="availability in availabilities" :key="availability.id">
                            <!-- Main Row -->
                            <tr class="hover:bg-gray-50 transition-colors duration-200">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div
                                            class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center mr-3">
                                            <i class="fa-solid fa-calendar-day text-blue-600 text-lg"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-semibold text-gray-900">
                                                {{ formatDate(availability.date) }}
                                            </div>
                                            <div class="text-xs text-gray-500">ID: #{{ availability.id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="px-3 py-1 bg-purple-100 text-purple-700 rounded-lg text-sm font-medium">
                                            {{ formatTime(availability.start_time) }}
                                        </span>
                                        <i class="fa-solid fa-arrow-right text-gray-400"></i>
                                        <span
                                            class="px-3 py-1 bg-purple-100 text-purple-700 rounded-lg text-sm font-medium">
                                            {{ formatTime(availability.end_time) }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-4 py-2 bg-orange-100 text-orange-700 rounded-lg text-sm font-semibold">
                                        {{ availability.slot_duration }} min
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex gap-2">
                                        <span
                                            class="px-3 py-1 bg-green-100 text-green-700 rounded-lg text-xs font-semibold flex items-center gap-1">
                                            <i class="fa-solid fa-check-circle"></i>
                                            {{ countAvailableSlots(availability.time_slots) }}
                                        </span>
                                        <span
                                            class="px-3 py-1 bg-red-100 text-red-700 rounded-lg text-xs font-semibold flex items-center gap-1">
                                            <i class="fa-solid fa-times-circle"></i>
                                            {{ countBookedSlots(availability.time_slots) }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button @click="toggleRow(availability.id)"
                                            class="px-3 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-all duration-300 text-sm">
                                            <i
                                                :class="['fa-solid', isExpanded(availability.id) ? 'fa-chevron-up' : 'fa-chevron-down']"></i>
                                        </button>
                                        <button @click="openEditModal(availability)"
                                            class="px-3 py-2 bg-yellow-50 text-yellow-600 rounded-lg hover:bg-yellow-100 transition-all duration-300 text-sm">
                                            <i class="fa-solid fa-edit"></i>
                                        </button>
                                        <button @click="deleteAvailability(availability.id)"
                                            class="px-3 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-all duration-300 text-sm">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Expanded Row - Time Slots -->
                            <tr v-if="isExpanded(availability.id)" class="bg-gray-50">
                                <td colspan="5" class="px-6 py-6">
                                    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                                        <h4 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                                            <i class="fa-solid fa-clock text-blue-500"></i>
                                            Time Slots Details ({{ availability.time_slots.length }} slots)
                                        </h4>

                                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                            <div v-for="slot in availability.time_slots" :key="slot.id" :class="[
                                                'p-4 rounded-xl border-2 transition-all duration-300 hover:shadow-md',
                                                slot.status === 'available'
                                                    ? 'bg-green-50 border-green-200 hover:border-green-400'
                                                    : 'bg-red-50 border-red-200 hover:border-red-400'
                                            ]">
                                                <div class="flex items-start justify-between mb-3">
                                                    <div class="flex items-center gap-2">
                                                        <i
                                                            :class="['fa-solid', getStatusIcon(slot.status), slot.status === 'available' ? 'text-green-600' : 'text-red-600']"></i>
                                                        <span
                                                            :class="['text-xs font-bold uppercase', slot.status === 'available' ? 'text-green-700' : 'text-red-700']">
                                                            {{ slot.status }}
                                                        </span>
                                                    </div>
                                                    <span class="text-xs text-gray-500 font-medium">ID: {{ slot.id
                                                        }}</span>
                                                </div>

                                                <div class="space-y-2">
                                                    <div class="flex items-center gap-2 text-sm">
                                                        <i class="fa-solid fa-clock text-gray-600"></i>
                                                        <span class="font-semibold text-gray-800">{{
                                                            formatTime(slot.start_time) }}</span>
                                                        <i class="fa-solid fa-arrow-right text-gray-400 text-xs"></i>
                                                        <span class="font-semibold text-gray-800">{{
                                                            formatTime(slot.end_time) }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
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

                    <!-- Slot Duration -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fa-solid fa-hourglass-half mr-2 text-orange-500"></i>Slot Duration (minutes)
                        </label>
                        <input v-model.number="formData.slot_duration" type="number" min="15" step="15"
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
/* Animation for modal */
@keyframes modalFadeIn {
    from {
        opacity: 0;
        transform: scale(0.9);
    }

    to {
        opacity: 1;
        transform: scale(1);
    }
}

/* Loading spinner animation */
@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

.animate-spin {
    animation: spin 1s linear infinite;
}
</style>