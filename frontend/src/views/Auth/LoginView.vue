<script setup>
import { RouterLink } from 'vue-router';
import { ref } from 'vue';
import axios from 'axios';

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

        localStorage.setItem('token', response.data.data.token);
        localStorage.setItem('role', response.data.data.user.role);

        isLoading.value = false;

        if (response.data.data.user.role === 'admin') {
            window.location.href = '/availability';
        } else {
            window.location.href = '/';
        }
    } catch (error) {
        if (error.response && error.response.data && error.response.data.message) {
            errorMessage.value = error.response.data.message;
            console.log(error.response.data.message);
        } else {
            errorMessage.value = 'An error occurred during login.';
        }
        console.error('Login error:', error);
    } finally {
        isLoading.value = false;
    }
};


</script>

<template>
    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-gray-50 via-white to-teal-50 p-6">
        <div class="w-full max-w-md">
            <!-- Card -->
            <div class="bg-white/90 backdrop-blur-2xl rounded-3xl shadow-2xl p-10 border border-gray-100">

                <!-- Logo -->
                <div class="flex justify-center mb-8">
                    <div
                        class="w-16 h-16 bg-gradient-to-br from-teal-600 to-emerald-700 rounded-2xl flex items-center justify-center shadow-xl">
                        <svg class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2" />
                        </svg>
                    </div>
                </div>

                <!-- Title -->
                <h2
                    class="text-3xl font-black text-center mb-10 bg-gradient-to-r from-teal-600 to-emerald-700 bg-clip-text text-transparent">
                    Welcome Back
                </h2>

                <!-- Form -->
                <form @submit.prevent="login" class="space-y-6">

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Email Address</label>
                        <input v-model="email" type="email" placeholder="doctor@clinic.com"
                            class="w-full px-6 py-4 bg-gray-50/50 border border-gray-200 rounded-2xl text-gray-900 placeholder:text-gray-500 focus:outline-none focus:ring-4 focus:ring-teal-500/20 focus:border-teal-500 transition-all duration-300"
                            required />
                    </div>

                    <!-- Password -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
                        <input v-model="password" type="password" placeholder="••••••••"
                            class="w-full px-6 py-4 bg-gray-50/50 border border-gray-200 rounded-2xl text-gray-900 placeholder:text-gray-500 focus:outline-none focus:ring-4 focus:ring-teal-500/20 focus:border-teal-500 transition-all duration-300"
                            required />
                    </div>

                    <!-- Error -->
                    <div v-if="errorMessage" class="text-rose-600 text-sm text-center font-medium">
                        {{ errorMessage }}
                    </div>

                    <!-- Submit -->
                    <button type="submit" :disabled="isLoading"
                        class="w-full py-5 bg-gradient-to-r from-teal-600 to-emerald-700 text-white text-lg font-bold rounded-2xl shadow-xl hover:shadow-2xl hover:scale-105 transition-all duration-300 disabled:opacity-60 disabled:cursor-not-allowed flex items-center justify-center">
                        <svg v-if="isLoading" class="animate-spin h-6 w-6 mr-3" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        {{ isLoading ? 'Signing in...' : 'Sign In' }}
                    </button>

                    <!-- Divider -->
                    <div class="relative my-8">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-200"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-4 bg-white text-gray-500 font-medium">Or continue with</span>
                        </div>
                    </div>

                    <!-- Links -->
                    <div class="mt-8 text-center space-y-3 text-sm">
                        <p>
                            <RouterLink to="/forget-password" class="text-teal-600 font-semibold hover:underline">
                                Forgot your password?
                            </RouterLink>
                        </p>
                        <p class="text-gray-600">
                            Don't have an account?
                            <RouterLink to="/signup" class="text-teal-600 font-semibold hover:underline">
                                Create one
                            </RouterLink>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
