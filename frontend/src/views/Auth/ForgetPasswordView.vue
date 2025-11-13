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
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-gray-50 via-white to-teal-50 p-6">
    <div class="w-full max-w-md">
      <!-- Card -->
      <div class="bg-white/90 backdrop-blur-2xl rounded-3xl shadow-2xl p-10 border border-gray-100">
        
        <!-- Logo -->
        <div class="flex justify-center mb-8">
          <div class="w-16 h-16 bg-gradient-to-br from-teal-600 to-emerald-700 rounded-2xl flex items-center justify-center shadow-xl">
            <svg class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" 
                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2"/>
            </svg>
          </div>
        </div>

        <!-- Title -->
        <h2 class="text-3xl font-black text-center mb-10 bg-gradient-to-r from-teal-600 to-emerald-700 bg-clip-text text-transparent">
          Reset Password
        </h2>

        <!-- Description -->
        <p class="text-center text-gray-600 mb-8 text-sm leading-relaxed">
          Enter your email address and we'll send you a link to reset your password.
        </p>

        <!-- Form -->
        <form @submit.prevent="sendResetLink" class="space-y-6">
          
          <!-- Email -->
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Email Address</label>
            <input 
              v-model="email" 
              type="email" 
              placeholder="doctor@clinic.com"
              class="w-full px-6 py-4 bg-gray-50/50 border border-gray-200 rounded-2xl text-gray-900 placeholder:text-gray-500 focus:outline-none focus:ring-4 focus:ring-teal-500/20 focus:border-teal-500 transition-all duration-300"
              required
            />
          </div>

          <!-- Success Message -->
          <div v-if="successMessage" class="text-emerald-600 text-sm text-center font-medium bg-emerald-50/50 py-3 px-4 rounded-xl">
            {{ successMessage }}
          </div>

          <!-- Error Message -->
          <div v-if="errorMessage" class="text-rose-600 text-sm text-center font-medium bg-rose-50/50 py-3 px-4 rounded-xl">
            {{ errorMessage }}
          </div>

          <!-- Submit -->
          <button 
            type="submit"
            :disabled="isLoading"
            class="w-full py-5 bg-gradient-to-r from-teal-600 to-emerald-700 text-white text-lg font-bold rounded-2xl shadow-xl hover:shadow-2xl hover:scale-105 transition-all duration-300 disabled:opacity-60 disabled:cursor-not-allowed flex items-center justify-center"
          >
            <svg v-if="isLoading" class="animate-spin h-6 w-6 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ isLoading ? 'Sending...' : 'Send Reset Link' }}
          </button>

          <!-- Back to Login -->
          <div class="mt-8 text-center">
            <p class="text-sm text-gray-600">
              Remember your password? 
              <RouterLink to="/login" class="text-teal-600 font-semibold hover:underline">
                Back to Sign In
              </RouterLink>
            </p>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>
