import { createRouter, createWebHistory } from 'vue-router'

import LoginView from '@/views/Auth/LoginView.vue'
import ForgetPasswordView from '@/views/Auth/ForgetPasswordView.vue'
import ResetPasswordView from '@/views/Auth/ResetPasswordView.vue'
import HomeView from '@/views/HomeView.vue'
import SignupView from '@/views/Auth/SignupView.vue'
import AvailabilityView from '@/views/Admin/AvailabilityView.vue'
import TimeSlotsView from '@/views/Admin/TimeSlotsView.vue'
import AppointmentsView from '@/views/Admin/AppointmentsView.vue'
import ProductsView from '@/views/Admin/ProductsView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    // Auth Routes
    {
      path: '/login',
      name: 'login',
      component: LoginView,
      meta: { layout: 'auth', requiresAuth: false },
    },
    {
      path: '/signup',
      name: 'signup',
      component: SignupView,
      meta: { layout: 'auth', requiresAuth: false },
    },
    {
      path: '/forget-password',
      name: 'forget-password',
      component: ForgetPasswordView,
      meta: { layout: 'auth', requiresAuth: false },
    },
    {
      path: '/password-reset/:token',
      name: 'password-reset',
      component: ResetPasswordView,
      meta: { layout: 'auth', requiresAuth: false },
    },

    // User Routes
    {
      path: '/',
      name: 'home',
      component: HomeView,
      meta: { layout: 'user', requiresAuth: false, role: 'user' },
    },
    {
      path: '/availability',
      name: 'availability',
      component: AvailabilityView,
      meta: { layout: 'admin', requiresAuth: true, role: 'admin' },
    },
    // time slots route
    {
      path: '/timeslots',
      name: 'timeslots',
      component: TimeSlotsView,
      meta: { layout: 'admin', requiresAuth: true, role: 'admin' },
    },
    {
      path: '/appointments',
      name: 'appointments',
      component: AppointmentsView,
      meta: { layout: 'admin', requiresAuth: true, role: 'admin' },
    },
    {
      path: '/products',
      name: 'products',
      component: ProductsView,
      meta: { layout: 'admin', requiresAuth: true, role: 'admin' },
    },

    // Catch all - 404
    {
      path: '/:pathMatch(.*)*',
      redirect: '/',
    },
  ],
})

router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('token')
  const role = localStorage.getItem('role')

  if (to.meta.requiresAuth && !token) {
    return next('/login')
  }

  if (to.meta.layout === 'auth' && token) {
    if (role === 'admin') {
      return next('/availability')
    } else {
      return next('/')
    }
  }

  if (to.meta.role === 'admin' && role !== 'admin') {
    return next('/')
  }

  if (to.meta.role === 'user' && role === 'admin') {
    return next('/availability')
  }

  next()
})

export default router
