import { createRouter, createWebHistory } from 'vue-router'

import LoginView from '@/views/Auth/LoginView.vue'
import ForgetPasswordView from '@/views/Auth/ForgetPasswordView.vue'
import ResetPasswordView from '@/views/Auth/ResetPasswordView.vue'
import Tese from '@/views/Admin/Tese.vue'
import HomeView from '@/views/HomeView.vue'
import SignupView from '@/views/Auth/SignupView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/login',
      name: 'login',
      component: LoginView,
      meta: { layout: 'auth' },
    },
    {
      path: '/signup',
      name: 'signup',
      component: SignupView,
      meta: { layout: 'auth' },
    },
    {
      path: '/forget-password',
      name: 'forget-password',
      component: ForgetPasswordView,
      meta: { layout: 'auth' },
    },
    {
      path: '/password-reset/:token',
      name: 'password-reset',
      component: ResetPasswordView,
      meta: { layout: 'auth' },
    },
    {
      path: '/',
      name: 'home',
      component: HomeView,
      meta: { layout: 'user', requiresAuth: false },
    },
    {
      path: '/admin',
      name: 'test',
      component: Tese,
      meta: { layout: '', requiresAuth: false },
    },
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

  if (['login', 'signup', 'forget-password', 'password-reset'].includes(to.name) && token) {
    if (role === 'admin') return next('/admin')
    else return next('/')
  }

  if (to.path.startsWith('/admin') && role !== 'admin') {
    return next('/') 
  }

  if (to.path.startsWith('/') && role === 'admin') {
    return next('/admin')
  }

  next()
})

export default router
