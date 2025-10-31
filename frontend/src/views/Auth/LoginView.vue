<script setup>
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
        localStorage.setItem('token', response.data.data.token);
        localStorage.setItem('role', response.data.data.user.role);

        if (response.data.data.user.role === 'admin') {
            router.push('/admin');
        } else {
            router.push('/');
        }
    } catch (error) {
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
                <div class="p-2 bg-blue-600 rounded-xl">
                    <i class="fas fa-stethoscope text-white text-3xl"></i>
                </div>
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
                    <!-- Sign up by google -->
                    <div class="mt-4">
                        <button
                            class="w-full px-4 py-2.5 bg-gray-400 text-white rounded-lg hover:bg-red-600 focus:ring-3 focus:ring-red-500/10 focus:outline-none transition-all duration-300 flex items-center justify-center">
                            <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48">
                                <path fill="#FFC107"
                                    d="M43.611 20.083H42V20H24v8h11.303c-1.59 4.657-6.08 8-11.303 8-6.627 0-12-5.373-12-12s5.373-12 12-12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.268 4 24 4 12.954 4 4 12.954 4 24s8.954 20 20 20c11.598 0 19.945-8.168 19.945-19.635 0-1.33-.134-2.597-.334-3.782z" />
                                <path fill="#FF3D00"
                                    d="M6.306 14.691l6.571 4.819C14.655 16.108 19.002 14 24 14c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.268 4 24 4c-7.843 0-14.455 4.522-17.694 10.691z" />
                                <path fill="#4CAF50"
                                    d="M24 44c5.166 0 9.86-1.977 13.409-5.192l-6.19-5.238C29.211 35.845 26.705 36 24 36c-5.202 0-9.631-3.317-11.283-8.065l-6.522 5.025C10.286 39.556 16.799 44 24 44z" />
                                <path fill="#1976D2"
                                    d="M43.611 20.083H42V20H24v8h11.303c-1.087 3.185-3.025 5.877-5.571 7.651l.005-.003 6.19 5.238C39.556 36.963 44 30.761 44 24c0-1.33-.134-2.597-.334-3.782z" />
                            </svg>
                            <span>Sign in with Google</span>
                        </button>
                    </div>
                </div>

                <!-- Additional Links -->
                <div class="text-center text-sm text-gray-600 space-y-2">
                    <p>
                        Forgot your password?
                        <RouterLink to="forget-password" class="text-indigo-600 hover:underline">Reset it</RouterLink>
                    </p>
                    <p>
                        Don't have an account?
                        <RouterLink to="/signup" class="text-indigo-600 hover:underline">Sign Up</RouterLink>
                    </p>
                </div>
            </form>
        </div>
        </div>
</template>
