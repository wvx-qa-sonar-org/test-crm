import axios from 'axios';
import router from '../router';
import store from '../store';

// Create axios instance
const api = axios.create({
  baseURL: process.env.VUE_APP_API_URL || '/api',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
});

// Request interceptor
api.interceptors.request.use(
  config => {
    const token = localStorage.getItem('token');
    if (token) {
      config.headers['Authorization'] = `Bearer ${token}`;
    }
    return config;
  },
  error => {
    return Promise.reject(error);
  }
);

// Response interceptor
api.interceptors.response.use(
  response => {
    return response;
  },
  error => {
    // Handle 401 Unauthorized responses
    if (error.response && error.response.status === 401) {
      // Clear user data and redirect to login
      store.dispatch('logout');
      router.push('/login');
    }
    
    // Handle 403 Forbidden responses
    if (error.response && error.response.status === 403) {
      // Redirect to dashboard or show permission error
      router.push('/dashboard');
    }
    
    // Handle 500 Server Error
    if (error.response && error.response.status >= 500) {
      console.error('Server Error:', error.response.data);
      // You could show a global error notification here
    }
    
    return Promise.reject(error);
  }
);

// Install the API as a Vue plugin
export default {
  install(Vue) {
    Vue.prototype.$api = api;
  }
};

// Export the API instance for direct use
export { api };