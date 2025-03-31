import Vue from 'vue'
import App from './App.vue'
import router from './router'
import store from './store'
import ApiService from './services/api'

// Install API service
Vue.use(ApiService);

// Add Font Awesome (you'll need to install this separately)
// npm install @fortawesome/fontawesome-free
import '@fortawesome/fontawesome-free/css/all.css'

Vue.config.productionTip = false

// Check authentication status before mounting the app
store.dispatch('checkAuth');

new Vue({
  router,
  store,
  render: h => h(App),
}).$mount('#app')