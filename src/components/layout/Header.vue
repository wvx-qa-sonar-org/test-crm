<template>
    <header class="header">
      <div class="search-bar">
        <i class="fas fa-search"></i>
        <input type="text" placeholder="Search...">
      </div>
      <div class="user-menu">
        <div class="notifications">
          <i class="fas fa-bell"></i>
          <span class="badge">3</span>
        </div>
        <div class="user-profile" @click="toggleDropdown">
          <img :src="userAvatar" alt="User Avatar">
          <span>{{ userName }}</span>
          <i class="fas fa-chevron-down"></i>
          <div class="dropdown-menu" v-if="showDropdown">
            <ul>
              <li><i class="fas fa-user"></i> Profile</li>
              <li><i class="fas fa-cog"></i> Settings</li>
              <li @click="logout"><i class="fas fa-sign-out-alt"></i> Logout</li>
            </ul>
          </div>
        </div>
      </div>
    </header>
  </template>
  
  <script>
  export default {
    name: 'AppHeader',
    data() {
      return {
        showDropdown: false,
        userAvatar: 'https://via.placeholder.com/40',
        userName: 'John Doe'
      }
    },
    created() {
      // Get user data from store or localStorage
      const user = JSON.parse(localStorage.getItem('user'));
      if (user) {
        this.userName = user.name;
        if (user.avatar) {
          this.userAvatar = user.avatar;
        }
      }
    },
    methods: {
      toggleDropdown() {
        this.showDropdown = !this.showDropdown;
      },
      logout() {
        // Clear localStorage
        localStorage.removeItem('token');
        localStorage.removeItem('user');
        
        // Update store
        this.$store.commit('setUser', null);
        this.$store.commit('setAuthenticated', false);
        
        // Redirect to login
        this.$router.push('/login');
      }
    }
  }
  </script>
  
  <style scoped>
  .header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 20px;
    background-color: white;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
  }
  
  .search-bar {
    display: flex;
    align-items: center;
    background-color: #f5f7fb;
    border-radius: 20px;
    padding: 8px 15px;
    width: 300px;
  }
  
  .search-bar i {
    color: #6c757d;
    margin-right: 10px;
  }
  
  .search-bar input {
    border: none;
    background: transparent;
    outline: none;
    width: 100%;
  }
  
  .user-menu {
    display: flex;
    align-items: center;
  }
  
  .notifications {
    position: relative;
    margin-right: 20px;
    cursor: pointer;
  }
  
  .notifications i {
    font-size: 18px;
    color: #6c757d;
  }
  
  .badge {
    position: absolute;
    top: -5px;
    right: -5px;
    background-color: #e74c3c;
    color: white;
    font-size: 10px;
    width: 15px;
    height: 15px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  
  .user-profile {
    display: flex;
    align-items: center;
    cursor: pointer;
    position: relative;
  }
  
  .user-profile img {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    margin-right: 10px;
  }
  
  .user-profile span {
    margin-right: 10px;
    font-weight: 500;
  }
  
  .dropdown-menu {
    position: absolute;
    top: 50px;
    right: 0;
    background: white;
    border-radius: 8px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    width: 200px;
    z-index: 100;
  }
  
  .dropdown-menu ul {
    list-style: none;
    padding: 0;
    margin: 0;
  }
  
  .dropdown-menu li {
    padding: 12px 15px;
    display: flex;
    align-items: center;
    transition: background-color 0.2s;
  }
  
  .dropdown-menu li:hover {
    background-color: #f5f7fb;
  }
  
  .dropdown-menu i {
    margin-right: 10px;
    color: #6c757d;
  }
  </style>