import './assets/main.css'

import { createApp } from 'vue'
import App from './App.vue'
import router from './router'

import axios from 'axios'

import Toast, { POSITION } from 'vue-toastification'
import 'vue-toastification/dist/index.css'

axios.defaults.baseURL = 'http://127.0.0.1:8000/api/' // Backend URL
axios.defaults.withCredentials = true

const token = localStorage.getItem('token')
if (token) {
  axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
}

const app = createApp(App)

app.use(router)

app.mount('#app')

app.use(Toast, {
  position: POSITION.TOP_RIGHT,
  timeout: 5000,
  closeOnClick: true,
  pauseOnHover: true,
})
