<script setup>
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'

const appointments = ref([])
const loading = ref(true)
const error = ref(null)

// Filter State
const searchQuery = ref('')

// Filtered Appointments
const filteredAppointments = computed(() => {
    let filtered = appointments.value

    // Filter by search query (patient name, email, phone)
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase()
        filtered = filtered.filter(app =>
            app.patient.name.toLowerCase().includes(query) ||
            app.patient.email.toLowerCase().includes(query) ||
            app.patient.phone.toLowerCase().includes(query)
        )
    }

    return filtered
})

// Statistics
const statistics = computed(() => {
    return {
        total: appointments.value.length,
    }
})

// Fetch Appointments
const fetchAppointments = async () => {
    loading.value = true
    error.value = null

    try {
        const response = await axios.get('admin/appointments')

        if (response.data.status === 'success') {
            appointments.value = response.data.data
        }
    } catch (err) {
        error.value = err.response?.data?.message || 'Failed to fetch appointments'
        console.error('Error fetching appointments:', err)
    } finally {
        loading.value = false
    }
}

// Delete Appointment
const deleteAppointment = async (id, patientName) => {
    if (!confirm(`Are you sure you want to cancel the appointment for ${patientName}?`)) return

    try {
        await axios.delete(`admin/appointments/${id}`)
        fetchAppointments() // Refresh the list
    } catch (err) {
        error.value = err.response?.data?.message || 'Failed to delete appointment'
        console.error('Error deleting appointment:', err)
    }
}

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        weekday: 'short',
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })
}

const getGenderIcon = (gender) => {
    if (gender === 'male') return 'fa-mars text-blue-500'
    if (gender === 'female') return 'fa-venus text-pink-500'
    return 'fa-user text-gray-500'
}

onMounted(() => {
    fetchAppointments()
})
</script>

<template>
    <div class="p-6 bg-gray-50 min-h-screen">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Appointments Management</h1>
            <p class="text-gray-600">View and manage patient appointments</p>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <!-- Total -->
            <div
                class="bg-white rounded-2xl p-6 shadow-lg border border-gray-200 hover:shadow-xl transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium mb-1">Total</p>
                        <p class="text-3xl font-bold text-gray-800">{{ statistics.total }}</p>
                    </div>
                    <div class="w-14 h-14 rounded-xl bg-blue-100 flex items-center justify-center">
                        <i class="fa-solid fa-calendar-check text-blue-600 text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-200 mb-6">
            <div class="flex flex-wrap gap-4 items-center justify-between">
                <!-- Search -->
                <div class="flex-1 min-w-[250px]">
                    <div class="relative">
                        <i
                            class="fa-solid fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input v-model="searchQuery" type="text"
                            placeholder="Search by patient name, email, or phone..."
                            class="w-full pl-11 pr-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all" />
                    </div>
                </div>

                <!-- Refresh Button -->
                <button @click="fetchAppointments"
                    class="px-6 py-3 bg-gray-600 text-white rounded-xl hover:bg-gray-700 transition-all duration-300 shadow-md hover:shadow-lg flex items-center gap-2">
                    <i class="fa-solid fa-rotate-right"></i>
                    Refresh
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
            <p class="text-gray-600 font-medium">Loading appointments...</p>
        </div>

        <!-- Empty State -->
        <div v-else-if="!loading && filteredAppointments.length === 0"
            class="bg-white rounded-2xl shadow-lg p-12 text-center">
            <i class="fa-solid fa-calendar-xmark text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">No Appointments Found</h3>
            <p class="text-gray-500">
                {{ searchQuery || filterStatus !== 'all' ? 'Try adjusting your filters' : 'There are no appointments at the moment.' }}
            </p>
        </div>

        <!-- Appointments Table -->
        <div v-else class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-200">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <!-- Table Header -->
                    <thead class="bg-gradient-to-r from-blue-50 to-purple-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                ID
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <i class="fa-solid fa-user mr-2"></i>Patient
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <i class="fa-solid fa-envelope mr-2"></i>Contact
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <i class="fa-solid fa-clock mr-2"></i>Slot ID
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                <i class="fa-solid fa-calendar mr-2"></i>Created At
                            </th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>

                    <!-- Table Body -->
                    <tbody class="divide-y divide-gray-200">
                        <tr v-for="appointment in filteredAppointments" :key="appointment.id"
                            class="hover:bg-gray-50 transition-colors duration-200">
                            <!-- ID -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-lg text-sm font-semibold">
                                    #{{ appointment.id }}
                                </span>
                            </td>

                            <!-- Patient Info -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div
                                        class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center mr-3 shadow-md">
                                        <i
                                            :class="['fa-solid', getGenderIcon(appointment.patient.gender), 'text-white text-lg']"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900">
                                            {{ appointment.patient.name }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            Patient ID: #{{ appointment.patient.id }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Contact -->
                            <td class="px-6 py-4">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2 text-sm text-gray-700">
                                        <i class="fa-solid fa-envelope text-blue-500 text-xs"></i>
                                        <span>{{ appointment.patient.email }}</span>
                                    </div>
                                    <div class="flex items-center gap-2 text-sm text-gray-700">
                                        <i class="fa-solid fa-phone text-green-500 text-xs"></i>
                                        <span>{{ appointment.patient.phone }}</span>
                                    </div>
                                </div>
                            </td>

                            <!-- Slot ID -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-lg text-sm font-semibold">
                                    Slot #{{ appointment.slot_id }}
                                </span>
                            </td>

                            <!-- Created At -->
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-700">
                                    {{ formatDate(appointment.created_at) }}
                                </div>
                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <button @click="deleteAppointment(appointment.id, appointment.patient.name)"
                                    class="px-4 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-all duration-300 font-medium text-sm flex items-center gap-2 mx-auto">
                                    <i class="fa-solid fa-trash"></i>
                                    Cancel
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
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