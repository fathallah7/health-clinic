<script setup>
import { RouterView, useRoute } from 'vue-router'
import { computed } from 'vue'

import AdminAppLayout from '@/layouts/Admin/AppLayout.vue'
import UserAppLayout from '@/layouts/User/AppLayout.vue'

import AuthLayout from '@/layouts/AuthLayout.vue'


const route = useRoute()

const layout = computed(() => {
  switch (route.meta.layout) {
    // admin + user
    case 'auth': return AuthLayout
    // admin only
    case 'landing': return LandingLayout
    // user only
    case 'user': return UserAppLayout
    // default Admin
    default: return AdminAppLayout
  }
})
</script>

<template>
  <component :is="layout">
    <RouterView />
  </component>
</template>
