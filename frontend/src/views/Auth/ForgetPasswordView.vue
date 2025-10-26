<script setup>
import { RouterLink } from 'vue-router';
import { ref } from 'vue';
import axios from 'axios';
import { useToast } from 'vue-toastification';

const isLoading = ref(false);
const toast = useToast();
const email = ref('');

const sendResetLink = async () => {
    try {
        isLoading.value = true;
        const response = await axios.post('/forgot-password', { email: email.value });
        toast.success('Reset link sent!');
        console.log(response.data);
        email.value = '';
        isLoading.value = false;
    } catch (error) {
        isLoading.value = false;
        if (error.response && error.response.data) {
            const errorMessage = error.response.data.message || 'Unknown error';
            toast.error(errorMessage);
        } else {
            console.error(error);
            toast.error('Error sending reset link');
        }
    }
};

</script>


<template>
    <div class="w-full min-h-screen bg-gray-50 flex items-center justify-center p-6">
        <div class="bg-white border border-gray-200 shadow-md rounded-lg p-6 max-w-sm sm:max-w-md w-full">
            <!-- Logo -->
            <div class="flex justify-center mb-6">
                <img src="../../assets/logo3.webp" alt="Logo" class="h-16" />
            </div>

            <!-- Heading -->
            <h2 class="text-2xl font-semibold text-gray-800 mb-6 text-center">Reset Your Password</h2>

            <!-- Form -->
            <form @submit.prevent="sendResetLink" class="space-y-4">
                <!-- Email Input -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input v-model="email" type="email" id="email" placeholder="Enter your email"
                        class="mt-1 w-full px-4 py-2.5 rounded-lg border border-gray-300 bg-transparent text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:ring-blue-500/10 focus:outline-none transition-all duration-300 hover:border-gray-400"
                        required />
                </div>

                <!-- Submit Button -->
                <div>
                    <button type="submit" :class="{ 'opacity-50 cursor-not-allowed': isLoading }" :disabled="isLoading"
                        class="w-full px-4 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 focus:ring-3 focus:ring-blue-500/10 focus:outline-none transition-all duration-300 flex items-center justify-center">
                        Send Reset Link
                    </button>
                </div>

                <!-- Back to Login Link -->
                <div class="text-center text-sm text-gray-600">
                    <p>
                        Back to
                        <RouterLink to="login" class="text-indigo-600 hover:underline">Login</RouterLink>
                    </p>
                </div>
            </form>
        </div>
    </div>
</template>
