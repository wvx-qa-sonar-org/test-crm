<template>
    <div class="login-container">
      <div class="login-form">
        <h2>Sign In</h2>
        <div class="form-group">
          <label for="email">Email</label>
          <input 
            type="email" 
            id="email" 
            v-model="email" 
            placeholder="Enter your email"
            required
          >
        </div>
        <div class="form-group">
          <label for="password">Password</label>
          <input 
            type="password" 
            id="password" 
            v-model="password" 
            placeholder="Enter your password"
            required
          >
        </div>
        <button @click="login" class="btn-login">Sign In</button>
        <p class="register-link">
          Don't have an account? <router-link to="/register">Sign Up</router-link>
        </p>
        <p v-if="error" class="error-message">{{ error }}</p>
        <p v-if="status" class="status-message">{{ status }}</p>
      </div>
    </div>
  </template>
  
  <script>
  import axios from 'axios';
  
  export default {
    name: 'LoginForm',
    data() {
      return {
        email: '',
        password: '',
        error: null,
        status: null
      }
    },
    methods: {
      async login() {
        this.status = "Attempting to log in...";
        this.error = null;
        
        try {
          this.status = "Connecting to API...";
          
          const response = await axios.post(`${process.env.VUE_APP_API_URL}/login`, {
            email: this.email,
            password: this.password
          });
          
          this.status = "Login successful, storing data...";
          
          // Store token in localStorage
          localStorage.setItem('token', response.data.token);
          localStorage.setItem('user', JSON.stringify(response.data.user));
          
          // Update Vuex store
          this.$store.commit('setUser', response.data.user);
          this.$store.commit('setAuthenticated', true);
          
          // Set axios default authorization header
          axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.token}`;
          
          this.status = "Redirecting to dashboard...";
          
          // Redirect to dashboard
          this.$router.push('/dashboard');
        } catch (err) {
          console.error('Login error:', err);
          this.error = err.response?.data?.message || 'Login failed. Please try again.';
          this.status = null;
        }
      }
    }
  }
  </script>
  
  <style scoped>
  .login-container {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    background-color: #f5f5f5;
  }
  
  .login-form {
    background: white;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    width: 350px;
  }
  
  h2 {
    text-align: center;
    margin-bottom: 20px;
    color: #2c3e50;
  }
  
  .form-group {
    margin-bottom: 15px;
  }
  
  label {
    display: block;
    margin-bottom: 5px;
    font-weight: 500;
  }
  
  input {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
  }
  
  .btn-login {
    width: 100%;
    padding: 10px;
    background-color: #42b983;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
    margin-top: 10px;
  }
  
  .btn-login:hover {
    background-color: #3aa876;
  }
  
  .register-link {
    text-align: center;
    margin-top: 15px;
    font-size: 14px;
  }
  
  .error-message {
    color: #e74c3c;
    text-align: center;
    margin-top: 15px;
  }
  
  .status-message {
    color: #3498db;
    text-align: center;
    margin-top: 15px;
  }
  </style>