<script setup>
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'

const activeTab = ref('products')
const products = ref([])
const categories = ref([])
const loading = ref(true)
const error = ref(null)
const showModal = ref(false)
const modalType = ref('product') // 'product' or 'category'
const modalMode = ref('create')
const formLoading = ref(false)
const searchQuery = ref('')
const filterCategory = ref('all')

const productForm = ref({ id: null, name: '', description: '', price: '', stock: '', category_id: '', image: null })
const categoryForm = ref({ id: null, name: '', description: '' })

const filteredProducts = computed(() => {
    let filtered = products.value
    if (filterCategory.value !== 'all') filtered = filtered.filter(p => p.category_id == filterCategory.value)
    if (searchQuery.value) {
        const q = searchQuery.value.toLowerCase()
        filtered = filtered.filter(p => p.name.toLowerCase().includes(q) || p.description.toLowerCase().includes(q))
    }
    return filtered
})

const statistics = computed(() => ({
    totalProducts: products.value.length,
    totalCategories: categories.value.length,
    totalStock: products.value.reduce((sum, p) => sum + p.stock, 0),
    lowStock: products.value.filter(p => p.stock < 10).length
}))

const fetchData = async () => {
    loading.value = true
    error.value = null
    try {
        const [productsRes, categoriesRes] = await Promise.all([
            axios.get('/admin/product'),
            axios.get('/admin/category')
        ])
        if (productsRes.data.status === 'success') products.value = productsRes.data.data
        if (categoriesRes.data.status === 'success') categories.value = categoriesRes.data.data
    } catch (err) {
        error.value = err.response?.data?.message || 'Failed to fetch data'
    } finally {
        loading.value = false
    }
}

const openModal = (type, mode, item = null) => {
    modalType.value = type
    modalMode.value = mode
    if (type === 'product') {
        productForm.value = mode === 'edit' && item ? {
            id: item.id, name: item.name, description: item.description,
            price: item.price, stock: item.stock, category_id: item.category_id, image: null
        } : { id: null, name: '', description: '', price: '', stock: '', category_id: '', image: null }
    } else {
        categoryForm.value = mode === 'edit' && item ?
            { id: item.id, name: item.name, description: item.description } :
            { id: null, name: '', description: '' }
    }
    showModal.value = true
}

const handleImageUpload = (e) => productForm.value.image = e.target.files[0]

const submitForm = async () => {
    formLoading.value = true
    error.value = null
    try {
        let response
        if (modalType.value === 'product') {
            const formData = new FormData()
            Object.keys(productForm.value).forEach(key => {
                if (key !== 'id' && productForm.value[key]) formData.append(key, productForm.value[key])
            })
            if (modalMode.value === 'create') {
                response = await axios.post('/admin/product', formData, {
                    headers: { 'Content-Type': 'multipart/form-data' }
                })
            } else {
                formData.append('_method', 'PUT')
                response = await axios.post(`/admin/product/${productForm.value.id}`, formData, {
                    headers: { 'Content-Type': 'multipart/form-data' }
                })
            }
        } else {
            const payload = { name: categoryForm.value.name, description: categoryForm.value.description }
            response = modalMode.value === 'create' ?
                await axios.post('/admin/category', payload) :
                await axios.put(`/admin/category/${categoryForm.value.id}`, payload)
        }
        if (response.data.status === 'success') {
            showModal.value = false
            fetchData()
        }
    } catch (err) {
        error.value = err.response?.data?.message || 'Failed to save'
    } finally {
        formLoading.value = false
    }
}

const deleteItem = async (type, id, name) => {
    if (!confirm(`Delete "${name}"?`)) return
    try {
        await axios.delete(`/admin/${type}/${id}`)
        fetchData()
    } catch (err) {
        error.value = err.response?.data?.message || 'Failed to delete'
    }
}

onMounted(fetchData)
</script>

<template>
    <div class="p-6 bg-gray-50 min-h-screen">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Products & Categories</h1>
            <p class="text-gray-600">Manage your inventory</p>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div v-for="(stat, idx) in [
                { label: 'Products', value: statistics.totalProducts, color: 'blue', icon: 'box' },
                { label: 'Categories', value: statistics.totalCategories, color: 'purple', icon: 'tags' },
                { label: 'Total Stock', value: statistics.totalStock, color: 'green', icon: 'warehouse' },
                { label: 'Low Stock', value: statistics.lowStock, color: 'red', icon: 'exclamation-triangle' }
            ]" :key="idx"
                :class="`bg-gradient-to-br from-${stat.color}-500 to-${stat.color}-600 rounded-xl p-5 shadow-lg text-white`">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm opacity-90 mb-1">{{ stat.label }}</p>
                        <p class="text-3xl font-bold">{{ stat.value }}</p>
                    </div>
                    <i :class="`fa-solid fa-${stat.icon} text-2xl opacity-80`"></i>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="bg-white rounded-xl shadow mb-6">
            <div class="flex border-b">
                <button v-for="tab in ['products', 'categories']" :key="tab" @click="activeTab = tab" :class="[
                    'flex-1 px-6 py-3 font-semibold transition',
                    activeTab === tab ? 'text-blue-600 border-b-2 border-blue-600 bg-blue-50' : 'text-gray-600 hover:bg-gray-50'
                ]">
                    <i :class="`fa-solid fa-${tab === 'products' ? 'box' : 'tags'} mr-2`"></i>
                    {{ tab.charAt(0).toUpperCase() + tab.slice(1) }} ({{ tab === 'products' ? products.length :
                        categories.length }})
                </button>
            </div>
        </div>

        <!-- Error -->
        <div v-if="error"
            class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded flex justify-between items-center">
            <div class="flex items-center">
                <i class="fa-solid fa-exclamation-circle text-red-500 mr-3"></i>
                <p class="text-red-700 font-medium">{{ error }}</p>
            </div>
            <button @click="error = null" class="text-red-500"><i class="fa-solid fa-times"></i></button>
        </div>

        <!-- PRODUCTS TAB -->
        <div v-if="activeTab === 'products'">
            <div class="bg-white rounded-xl p-4 shadow mb-6">
                <div class="flex flex-wrap gap-3">
                    <input v-model="searchQuery" placeholder="Search..."
                        class="flex-1 min-w-[200px] px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" />
                    <select v-model="filterCategory"
                        class="px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="all">All Categories</option>
                        <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                    </select>
                    <button @click="fetchData" class="px-5 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                        <i class="fa-solid fa-rotate-right"></i>
                    </button>
                    <button @click="openModal('product', 'create')"
                        class="px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <i class="fa-solid fa-plus mr-1"></i> Add
                    </button>
                </div>
            </div>

            <div v-if="loading" class="flex justify-center py-20">
                <div class="animate-spin rounded-full h-12 w-12 border-4 border-blue-500 border-t-transparent"></div>
            </div>

            <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div v-for="p in filteredProducts" :key="p.id"
                    class="bg-white rounded-xl shadow border hover:shadow-lg transition">
                    <div class="relative h-40 bg-gray-100 rounded-t-xl overflow-hidden">
                        <img :src="p.image ? `http://127.0.0.1:8000/storage/${p.image}` : 'https://via.placeholder.com/300'"
                            class="w-full h-full object-cover" />
                        <span
                            :class="['absolute top-2 right-2 px-2 py-1 rounded text-xs font-bold', p.stock > 10 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700']">
                            Stock: {{ p.stock }}
                        </span>
                    </div>
                    <div class="p-4">
                        <h3 class="font-bold text-gray-800 mb-1">{{ p.name }}</h3>
                        <p class="text-sm text-gray-600 line-clamp-2 mb-3">{{ p.description }}</p>
                        <div class="flex justify-between items-center mb-3">
                            <span class="text-xl font-bold text-blue-600">${{ p.price }}</span>
                            <span class="px-2 py-1 bg-purple-100 text-purple-700 rounded text-xs">{{ p.category?.name
                            }}</span>
                        </div>
                        <div class="flex gap-2">
                            <button @click="openModal('product', 'edit', p)"
                                class="flex-1 px-3 py-2 bg-yellow-50 text-yellow-600 rounded hover:bg-yellow-100 text-sm">
                                <i class="fa-solid fa-edit"></i>
                            </button>
                            <button @click="deleteItem('product', p.id, p.name)"
                                class="flex-1 px-3 py-2 bg-red-50 text-red-600 rounded hover:bg-red-100 text-sm">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CATEGORIES TAB -->
        <div v-if="activeTab === 'categories'">
            <div class="bg-white rounded-xl p-4 shadow mb-6 flex justify-between">
                <h2 class="text-xl font-bold text-gray-800"><i
                        class="fa-solid fa-tags mr-2 text-purple-600"></i>Categories</h2>
                <div class="flex gap-3">
                    <button @click="fetchData" class="px-5 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                        <i class="fa-solid fa-rotate-right"></i>
                    </button>
                    <button @click="openModal('category', 'create')"
                        class="px-5 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                        <i class="fa-solid fa-plus mr-1"></i> Add
                    </button>
                </div>
            </div>

            <div v-if="loading" class="flex justify-center py-20">
                <div class="animate-spin rounded-full h-12 w-12 border-4 border-purple-500 border-t-transparent"></div>
            </div>

            <div v-else class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div v-for="c in categories" :key="c.id"
                    class="bg-white rounded-xl p-5 shadow border-2 hover:border-purple-400 transition">
                    <div class="flex justify-between items-start mb-3">
                        <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center">
                            <i class="fa-solid fa-tag text-purple-600"></i>
                        </div>
                        <span class="text-xs bg-gray-100 px-2 py-1 rounded">ID: {{ c.id }}</span>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800 mb-2">{{ c.name }}</h3>
                    <p class="text-sm text-gray-600 mb-4">{{ c.description }}</p>
                    <div class="flex gap-2 pt-3 border-t">
                        <button @click="openModal('category', 'edit', c)"
                            class="flex-1 px-3 py-2 bg-yellow-50 text-yellow-600 rounded hover:bg-yellow-100 text-sm">
                            <i class="fa-solid fa-edit"></i> Edit
                        </button>
                        <button @click="deleteItem('category', c.id, c.name)"
                            class="flex-1 px-3 py-2 bg-red-50 text-red-600 rounded hover:bg-red-100 text-sm">
                            <i class="fa-solid fa-trash"></i> Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- MODAL -->
        <div v-if="showModal"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                <div
                    :class="`bg-gradient-to-r px-6 py-4 rounded-t-xl ${modalType === 'product' ? 'from-blue-500 to-blue-600' : 'from-purple-500 to-purple-600'}`">
                    <div class="flex justify-between items-center">
                        <h3 class="text-xl font-bold text-white">
                            <i :class="`fa-solid fa-${modalMode === 'create' ? 'plus' : 'edit'} mr-2`"></i>
                            {{ modalMode === 'create' ? 'Add' : 'Edit' }} {{ modalType === 'product' ? 'Product' :
                                'Category' }}
                        </h3>
                        <button @click="showModal = false" class="text-white hover:text-gray-200">
                            <i class="fa-solid fa-times text-xl"></i>
                        </button>
                    </div>
                </div>

                <div class="p-6 space-y-4">
                    <template v-if="modalType === 'product'">
                        <div class="grid md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Name</label>
                                <input v-model="productForm.name"
                                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" />
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Category</label>
                                <select v-model="productForm.category_id"
                                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                                    <option value="">Select</option>
                                    <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
                            <textarea v-model="productForm.description" rows="3"
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
                        </div>
                        <div class="grid md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Price</label>
                                <input v-model="productForm.price" type="number" step="0.01"
                                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" />
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Stock</label>
                                <input v-model="productForm.stock" type="number"
                                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" />
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Image</label>
                            <input @change="handleImageUpload" type="file" accept="image/*"
                                class="w-full px-4 py-2 border rounded-lg" />
                        </div>
                    </template>

                    <template v-else>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Name</label>
                            <input v-model="categoryForm.name"
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500" />
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
                            <textarea v-model="categoryForm.description" rows="4"
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500"></textarea>
                        </div>
                    </template>
                </div>

                <div class="px-6 py-4 bg-gray-50 rounded-b-xl flex gap-3 justify-end">
                    <button @click="showModal = false" :disabled="formLoading"
                        class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                        Cancel
                    </button>
                    <button @click="submitForm" :disabled="formLoading"
                        :class="`px-6 py-2 text-white rounded-lg flex items-center gap-2 ${modalType === 'product' ? 'bg-blue-600 hover:bg-blue-700' : 'bg-purple-600 hover:bg-purple-700'}`">
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

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>