<template>
    <div class="register-container">
      <div class="register-form">
        <h2>Sign Up</h2>
        <div class="form-group">
          <label for="name">Full Name</label>
          <input 
            type="text" 
            id="name" 
            v-model="name" 
            placeholder="Enter your full name"
            required
          >
        </div>
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
        <div class="form-group">
          <label for="confirmPassword">Confirm Password</label>
          <input 
            type="password" 
            id="confirmPassword" 
            v-model="confirmPassword" 
            placeholder="Confirm your password"
            required
          >
        </div>
        <button @click="register" class="btn-register">Sign Up</button>
        <p class="login-link">
          Already have an account? <router-link to="/login">Sign In</router-link>
        </p>
        <p v-if="error" class="error-message">{{ error }}</p>
      </div>
    </div>
  </template>
  
  <script>
  import axios from 'axios';
  
  export default {
    name: 'RegisterForm',
    data() {
      return {
        name: '',
        email: '',
        password: '',
        confirmPassword: '',
        error: null
      }
    },
    methods: {
      async register() {
        // Validate passwords match
        if (this.password !== this.confirmPassword) {
          this.error = 'Passwords do not match';
          return;
        }
        
        try {
          await axios.post('/api/register', {
            name: this.name,
            email: this.email,
            password: this.password
          });
          
          // Redirect to login page after successful registration
          this.$router.push('/login');
        } catch (err) {
          this.error = err.response?.data?.message || 'Registration failed. Please try again.';
        }
      }
    }
  }
  </script>
  
  <style scoped>
  .register-container {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    background-color: #f5f5f5;
  }
  
  .register-form {
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
  
  .btn-register {
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
  
  .btn-register:hover {
    background-color: #3aa876;
  }
  
  .login-link {
    text-align: center;
    margin-top: 15px;
    font-size: 14px;
  }
  
  .error-message {
    color: #e74c3c;
    text-align: center;
    margin-top: 15px;
  }
  </style>