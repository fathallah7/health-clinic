import { createRouter, createWebHistory } from 'vue-router'

import LoginView from '@/views/Auth/LoginView.vue'
import ForgetPasswordView from '@/views/Auth/ForgetPasswordView.vue'
import ResetPasswordView from '@/views/Auth/ResetPasswordView.vue'
import Tese from '@/views/Admin/Tese.vue'
import Tes from '@/views/User/Tes.vue'
import HomeView from '@/views/HomeView.vue'

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
      redirect: '/home',
    },
  ],
})

router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('token')

  if (to.meta.requiresAuth && !token) {
    next('/login')
  } else if (
    (to.name === 'login' || to.name === 'forget-password' || to.name === 'password-reset') &&
    token
  ) {
    next('/dashboard')
  } else {
    next()
  }
})

export default router
