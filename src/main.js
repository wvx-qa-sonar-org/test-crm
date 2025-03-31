import Vue from 'vue'
import App from './App.vue'
import router from './router'
import store from './store'
import ApiService from './services/api'
import axios from 'axios'

// Install API service
Vue.use(ApiService);

// Add Font Awesome (you'll need to install this separately)
// npm install @fortawesome/fontawesome-free
import '@fortawesome/fontawesome-free/css/all.css'

Vue.config.productionTip = false

// Add axios to Vue prototype for global access
Vue.prototype.$axios = axios

// Check authentication status before mounting the app
const token = localStorage.getItem('token')
const user = JSON.parse(localStorage.getItem('user'))

if (token && user) {
  store.commit('setUser', user)
  store.commit('setAuthenticated', true)
}

new Vue({
  router,
  store,
  render: h => h(App),
}).$mount('#app')