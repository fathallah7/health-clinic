<script setup>
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'

const availabilities = ref([])
const loading = ref(true)
const error = ref(null)
const expandedRows = ref([])
const showModal = ref(false)
const modalMode = ref('create')
const formLoading = ref(false)
const formData = ref({ id: null, date: '', start_time: '', end_time: '', slot_duration: 30 })

const fetchAvailabilities = async () => {
    loading.value = true
    error.value = null
    try {
        const res = await axios.get('admin/availabilities')
        if (res.data.status === 'success') availabilities.value = res.data.data
    } catch (err) {
        error.value = err.response?.data?.message || 'Failed to fetch availabilities'
    } finally {
        loading.value = false
    }
}

const openModal = (mode, item = null) => {
    modalMode.value = mode
    formData.value = mode === 'edit' && item ? {
        id: item.id,
        date: item.date,
        start_time: item.start_time.substring(0, 5),
        end_time: item.end_time.substring(0, 5),
        slot_duration: item.slot_duration
    } : { id: null, date: '', start_time: '', end_time: '', slot_duration: 30 }
    showModal.value = true
}

const submitForm = async () => {
    formLoading.value = true
    error.value = null
    try {
        const { id, ...payload } = formData.value
        const res = modalMode.value === 'create' ?
            await axios.post('admin/availabilities', payload) :
            await axios.put(`admin/availabilities/${id}`, payload)
        if (res.data.status === 'success') {
            showModal.value = false
            fetchAvailabilities()
        }
    } catch (err) {
        error.value = err.response?.data?.message || 'Failed to save'
    } finally {
        formLoading.value = false
    }
}

const deleteItem = async (id) => {
    if (!confirm('Delete this availability?')) return
    try {
        await axios.delete(`admin/availabilities/${id}`)
        fetchAvailabilities()
    } catch (err) {
        error.value = err.response?.data?.message || 'Failed to delete'
    }
}

const toggleRow = (id) => {
    const idx = expandedRows.value.indexOf(id)
    idx > -1 ? expandedRows.value.splice(idx, 1) : expandedRows.value.push(id)
}

const formatDate = (d) => new Date(d).toLocaleDateString('en-US', { weekday: 'short', year: 'numeric', month: 'short', day: 'numeric' })
const formatTime = (t) => new Date('2000-01-01 ' + t).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true })
const countSlots = (slots, status) => slots.filter(s => s.status === status).length

onMounted(fetchAvailabilities)
</script>

<template>
    <div class="p-6 bg-gray-50 min-h-screen">
        <div class="mb-6 flex items-center justify-between flex-wrap gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Availability Management</h1>
                <p class="text-gray-600">Manage your time slots and appointments</p>
            </div>
            <div class="flex gap-3">
                <button @click="fetchAvailabilities"
                    class="px-6 py-3 bg-gray-600 text-white rounded-xl hover:bg-gray-700 transition shadow-md flex items-center gap-2">
                    <i class="fa-solid fa-rotate-right"></i> Refresh
                </button>
                <button @click="openModal('create')"
                    class="px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl hover:from-blue-600 hover:to-blue-700 transition shadow-md flex items-center gap-2">
                    <i class="fa-solid fa-plus"></i> Add Availability
                </button>
            </div>
        </div>

        <div v-if="error" class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-lg flex justify-between">
            <div class="flex items-center">
                <i class="fa-solid fa-exclamation-circle text-red-500 text-xl mr-3"></i>
                <p class="text-red-700 font-medium">{{ error }}</p>
            </div>
            <button @click="error = null" class="text-red-500"><i class="fa-solid fa-times"></i></button>
        </div>

        <div v-if="loading" class="flex flex-col items-center justify-center py-20">
            <div class="animate-spin rounded-full h-16 w-16 border-4 border-blue-500 border-t-transparent mb-4"></div>
            <p class="text-gray-600 font-medium">Loading...</p>
        </div>

        <div v-else-if="!availabilities.length" class="bg-white rounded-2xl shadow-lg p-12 text-center">
            <i class="fa-solid fa-calendar-xmark text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">No Availabilities Found</h3>
            <p class="text-gray-500 mb-6">There are no availability schedules at the moment.</p>
            <button @click="openModal('create')"
                class="px-6 py-3 bg-blue-500 text-white rounded-xl hover:bg-blue-600 transition shadow-md">
                <i class="fa-solid fa-plus mr-2"></i>Create First Availability
            </button>
        </div>

        <div v-else class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-200">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gradient-to-r from-blue-50 to-purple-50 border-b border-gray-200">
                        <tr>
                            <th v-for="h in ['Date', 'Time Range', 'Duration', 'Status', 'Actions']" :key="h"
                                :class="['px-6 py-4 text-xs font-bold text-gray-700 uppercase', h === 'Actions' ? 'text-center' : 'text-left']">
                                <i v-if="h !== 'Actions'"
                                    :class="`fa-solid fa-${h === 'Date' ? 'calendar' : h === 'Time Range' ? 'clock' : h === 'Duration' ? 'hourglass-half' : 'chart-pie'} mr-2`"></i>
                                {{ h }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <template v-for="av in availabilities" :key="av.id">
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div
                                            class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center mr-3">
                                            <i class="fa-solid fa-calendar-day text-blue-600 text-lg"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-semibold text-gray-900">{{ formatDate(av.date) }}
                                            </div>
                                            <div class="text-xs text-gray-500">ID: #{{ av.id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="px-3 py-1 bg-purple-100 text-purple-700 rounded-lg text-sm font-medium">
                                            {{ formatTime(av.start_time) }}
                                        </span>
                                        <i class="fa-solid fa-arrow-right text-gray-400"></i>
                                        <span
                                            class="px-3 py-1 bg-purple-100 text-purple-700 rounded-lg text-sm font-medium">
                                            {{ formatTime(av.end_time) }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-4 py-2 bg-orange-100 text-orange-700 rounded-lg text-sm font-semibold">
                                        {{ av.slot_duration }} min
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex gap-2">
                                        <span
                                            class="px-3 py-1 bg-green-100 text-green-700 rounded-lg text-xs font-semibold">
                                            <i class="fa-solid fa-check-circle mr-1"></i>{{ countSlots(av.time_slots,
                                                'available') }}
                                        </span>
                                        <span
                                            class="px-3 py-1 bg-red-100 text-red-700 rounded-lg text-xs font-semibold">
                                            <i class="fa-solid fa-times-circle mr-1"></i>{{ countSlots(av.time_slots,
                                                'booked') }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button @click="toggleRow(av.id)"
                                            class="px-3 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition text-sm">
                                            <i
                                                :class="`fa-solid fa-chevron-${expandedRows.includes(av.id) ? 'up' : 'down'}`"></i>
                                        </button>
                                        <button @click="openModal('edit', av)"
                                            class="px-3 py-2 bg-yellow-50 text-yellow-600 rounded-lg hover:bg-yellow-100 transition text-sm">
                                            <i class="fa-solid fa-edit"></i>
                                        </button>
                                        <button @click="deleteItem(av.id)"
                                            class="px-3 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition text-sm">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr v-if="expandedRows.includes(av.id)" class="bg-gray-50">
                                <td colspan="5" class="px-6 py-6">
                                    <div class="bg-white rounded-xl p-6 shadow-sm border">
                                        <h4 class="text-lg font-bold text-gray-800 mb-4">
                                            <i class="fa-solid fa-clock text-blue-500 mr-2"></i>
                                            Time Slots Details ({{ av.time_slots.length }})
                                        </h4>
                                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                            <div v-for="slot in av.time_slots" :key="slot.id" :class="[
                                                'p-4 rounded-xl border-2 transition hover:shadow-md',
                                                slot.status === 'available' ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200'
                                            ]">
                                                <div class="flex justify-between items-start mb-3">
                                                    <div class="flex items-center gap-2">
                                                        <i
                                                            :class="`fa-solid fa-${slot.status === 'available' ? 'check' : 'times'}-circle text-${slot.status === 'available' ? 'green' : 'red'}-600`"></i>
                                                        <span
                                                            :class="`text-xs font-bold uppercase text-${slot.status === 'available' ? 'green' : 'red'}-700`">
                                                            {{ slot.status }}
                                                        </span>
                                                    </div>
                                                    <span class="text-xs text-gray-500">ID: {{ slot.id }}</span>
                                                </div>
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
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        <div v-if="showModal"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
                <div class="bg-gradient-to-r from-blue-500 to-purple-500 px-6 py-4 rounded-t-2xl">
                    <div class="flex justify-between items-center">
                        <h3 class="text-xl font-bold text-white">
                            <i :class="`fa-solid fa-${modalMode === 'create' ? 'plus' : 'edit'} mr-2`"></i>
                            {{ modalMode === 'create' ? 'Add New' : 'Edit' }} Availability
                        </h3>
                        <button @click="showModal = false" class="text-white hover:text-gray-200">
                            <i class="fa-solid fa-times text-xl"></i>
                        </button>
                    </div>
                </div>

                <div class="p-6 space-y-4">
                    <div v-for="(field, idx) in [
                        { key: 'date', label: 'Date', type: 'date', icon: 'calendar', color: 'blue' },
                        { key: 'start_time', label: 'Start Time', type: 'time', icon: 'clock', color: 'green' },
                        { key: 'end_time', label: 'End Time', type: 'time', icon: 'clock', color: 'red' },
                        { key: 'slot_duration', label: 'Slot Duration (min)', type: 'number', icon: 'hourglass-half', color: 'orange', attrs: { min: 15, step: 15 } }
                    ]" :key="idx">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i :class="`fa-solid fa-${field.icon} mr-2 text-${field.color}-500`"></i>{{ field.label
                                }}
                            </label>
                            <input v-model="formData[field.key]" :type="field.type" v-bind="field.attrs"
                                class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-blue-500" required />
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50 rounded-b-2xl flex gap-3 justify-end">
                    <button @click="showModal = false" :disabled="formLoading"
                        class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition font-medium">
                        Cancel
                    </button>
                    <button @click="submitForm" :disabled="formLoading"
                        class="px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl hover:from-blue-600 hover:to-blue-700 transition font-medium flex items-center gap-2 disabled:opacity-50">
                        <i v-if="formLoading" class="fa-solid fa-spinner fa-spin"></i>
                        <i v-else :class="`fa-solid fa-${modalMode === 'create' ? 'plus' : 'save'}`"></i>
                        {{ formLoading ? 'Saving...' : modalMode === 'create' ? 'Create' : 'Update' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.animate-spin {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}
</style>