<script setup lang="ts">
import { ref, computed } from 'vue';
import { useRoute, RouterLink, useRouter } from 'vue-router';
import axios from 'axios';
import { useToast } from 'vue-toastification';

const toast = useToast();
const route = useRoute();
const router = useRouter();
const isLoading = ref(false);

const password = ref('');
const confirmPassword = ref('');


const token = route.params.token as string;
const email = route.query.email as string;


const passwordError = computed(() => {
    if (!password.value) return 'Password is required';
    if (password.value.length < 8) return 'Password must be at least 8 characters';
    return '';
});

const confirmPasswordError = computed(() => {
    if (!confirmPassword.value) return 'Password confirmation is required';
    if (confirmPassword.value !== password.value) return 'Passwords do not match the password entered above';
    return '';
});

const isFormValid = computed(() => !passwordError.value && !confirmPasswordError.value);


const handleSubmit = async () => {

    if (!isFormValid.value) {
        toast.error('Please fix the errors in the form');
        return;
    }

    isLoading.value = true;

    try {
        const response = await axios.post('/reset-password', {
            email,
            token,
            password: password.value,
            password_confirmation: confirmPassword.value,
        });
        toast.success('Password has been reset successfully!');
        password.value = '';
        confirmPassword.value = '';
        router.push('/login');
    }
    catch (error) {
        if (axios.isAxiosError(error) && error.response) {
            const errorMessage = error.response.data.message || 'Unknown error';
            toast.error(errorMessage);
        }
        else {
            toast.error('Error resetting password');
        }
        console.error(error);
    } finally {
        isLoading.value = false;
    }
};
</script>

<template>
    <div class="w-full min-h-screen bg-gray-50 flex items-center justify-center p-6 ">
        <div class="bg-white border border-gray-200 shadow-md rounded-lg p-6 max-w-sm sm:max-w-md w-full">
            <!-- Logo -->
            <div class="flex justify-center mb-6">
                <div class="p-2 bg-blue-600 rounded-xl">
                    <i class="fas fa-stethoscope text-white text-3xl"></i>
                </div>
            </div>

            <!-- Title -->
            <h2 class="text-2xl font-semibold text-gray-800 mb-6 text-center">Reset Password</h2>

            <!-- Form -->
            <form @submit.prevent="handleSubmit" class="space-y-4">
                <!-- Password field -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">New Password</label>
                    <input v-model="password" type="password" id="password" placeholder="Enter new password"
                        class="mt-1 w-full px-4 py-2.5 rounded-lg border border-gray-300 bg-transparent text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:ring-blue-500/10 focus:outline-none transition-all duration-300 hover:border-gray-400"
                        :class="{ 'border-red-500': passwordError }" required />
                    <p v-if="passwordError" class="mt-1 text-sm text-red-500">{{ passwordError }}</p>
                </div>

                <!-- Confirm password field -->
                <div>
                    <label for="confirm-password" class="block text-sm font-medium text-gray-700">Confirm
                        Password</label>
                    <input v-model="confirmPassword" type="password" id="confirm-password"
                        placeholder="Confirm password"
                        class="mt-1 w-full px-4 py-2.5 rounded-lg border border-gray-300 bg-transparent text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:ring-blue-500/10 focus:outline-none transition-all duration-300 hover:border-gray-400"
                        :class="{ 'border-red-500': confirmPasswordError }" required />
                    <p v-if="confirmPasswordError" class="mt-1 text-sm text-red-500">{{ confirmPasswordError }}</p>
                </div>

                <!-- Submit button -->
                <div>
                    <button type="submit"
                        class="w-full px-4 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 focus:ring-3 focus:ring-blue-500/10 focus:outline-none transition-all duration-300 flex items-center justify-center"
                        :disabled="isLoading || !isFormValid"
                        :class="{ 'opacity-50 cursor-not-allowed': isLoading || !isFormValid }">
                        <svg v-if="isLoading" class="animate-spin h-5 w-5 mr-2 text-white"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        <span>{{ isLoading ? 'Sending...' : 'Reset Password' }}</span>
                    </button>
                </div>

                <!-- Back to login link -->
                <div class="text-center text-sm text-gray-600">
                    <p>
                        Back to
                        <RouterLink to="/login" class="text-indigo-600 hover:underline">Login</RouterLink>
                    </p>
                </div>
            </form>
        </div>
    </div>
</template>
