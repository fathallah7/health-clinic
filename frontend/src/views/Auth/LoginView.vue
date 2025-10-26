<script setup lang="ts">
import { RouterLink } from 'vue-router';
import { useRouter } from 'vue-router';
import { ref } from 'vue';
import axios from 'axios';

const router = useRouter();
const isLoading = ref(false);

const email = ref('');
const password = ref('');
const errorMessage = ref('');

const login = async () => {
    try {
        isLoading.value = true;
        const response = await axios.post('/login', {
            email: email.value,
            password: password.value
        });
        console.log(response.data);
        localStorage.setItem('token', response.data.token);
        router.push('/home');
    } catch (error: any) {
        isLoading.value = false;
        if (error.response && error.response.data && error.response.data.message) {
            errorMessage.value = error.response.data.message;
            console.log(error.response.data.message);
        } else {
            errorMessage.value = 'An error occurred during login.';
        }
        console.error('Login error:', error);
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
            <h2 class="text-2xl font-semibold text-gray-800 mb-6 text-center">Login to Dashboard</h2>

            <!-- Form -->
            <form @submit.prevent="login" class="space-y-4">
                <!-- Email Input -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input v-model="email" type="email" id="email" placeholder="Enter your email"
                        class="mt-1 w-full px-4 py-2.5 rounded-lg border border-gray-300 bg-transparent text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:ring-blue-500/10 focus:outline-none transition-all duration-300 hover:border-gray-400"
                        required />
                </div>

                <!-- Password Input -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input v-model="password" type="password" id="password" placeholder="Enter your password"
                        class="mt-1 w-full px-4 py-2.5 rounded-lg border border-gray-300 bg-transparent text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:ring-blue-500/10 focus:outline-none transition-all duration-300 hover:border-gray-400"
                        required />
                </div>


                <!-- Error Message -->
                <div v-if="errorMessage" class="text-red-500 text-sm mt-2">{{ errorMessage }}</div>

                <!-- Submit Button -->
                <div>
                    <button type="submit"
                        class="w-full px-4 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 focus:ring-3 focus:ring-blue-500/10 focus:outline-none transition-all duration-300 flex items-center justify-center"
                        :class="{ 'opacity-50 cursor-not-allowed': isLoading }">
                        <svg v-if="isLoading" class="animate-spin h-5 w-5 mr-2 text-white"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        <span>{{ isLoading ? 'Logging in...' : 'Log In' }}</span>
                    </button>
                </div>

                <!-- Additional Links -->
                <div class="text-center text-sm text-gray-600 space-y-2">
                    <p>
                        Forgot your password?
                        <RouterLink to="forget-password" class="text-indigo-600 hover:underline">Reset it</RouterLink>
                    </p>
                </div>
            </form>
        </div>
    </div>
</template>
