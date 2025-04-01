<template>
    <div class="sidebar">
      <div class="logo">
        <img src="@/assets/invitext.png" alt="CRM Logo">
      </div>
      <nav class="menu">
        <ul>
          <li :class="{ active: isActive('/dashboard') }">
            <router-link to="/dashboard">
              <i class="fas fa-tachometer-alt"></i>
              <span>Dashboard</span>
            </router-link>
          </li>
          <li :class="{ active: isActive('/tickets') }">
            <router-link to="/tickets">
              <i class="fas fa-ticket-alt"></i>
              <span>Tickets</span>
            </router-link>
          </li>
          <li :class="{ active: isActive('/clients') }">
            <router-link to="/clients">
              <i class="fas fa-users"></i>
              <span>Clients</span>
            </router-link>
          </li>
          <li :class="{ active: isActive('/reports') }">
            <router-link to="/reports">
              <i class="fas fa-chart-bar"></i>
              <span>Reports</span>
            </router-link>
          </li>
          <li :class="{ active: isActive('/settings') }">
            <router-link to="/settings">
              <i class="fas fa-cog"></i>
              <span>Settings</span>
            </router-link>
          </li>
        </ul>
      </nav>
    </div>
  </template>
  
  <script>
  export default {
    name: 'AppSidebar',
    methods: {
      isActive(route) {
        return this.$route.path === route;
      }
    }
  }
  </script>
  
  <style scoped>
  .sidebar {
    width: 250px;
    background-color: #2c3e50;
    color: white;
    height: 100vh;
    overflow-y: auto;
  }
  
  .logo {
    padding: 20px;
    text-align: center;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
  }
  
  .logo h1 {
    margin: 0;
    font-size: 24px;
    font-weight: 700;
  }
  
  .menu ul {
    list-style: none;
    padding: 0;
    margin: 0;
  }
  
  .menu li {
    margin: 5px 0;
  }
  
  .menu a {
    display: flex;
    align-items: center;
    padding: 12px 20px;
    color: rgba(255, 255, 255, 0.7);
    text-decoration: none;
    transition: all 0.3s;
  }
  
  .menu a:hover {
    background-color: rgba(255, 255, 255, 0.1);
    color: white;
  }
  
  .menu li.active a {
    background-color: #42b983;
    color: white;
    border-radius: 0 30px 30px 0;
  }
  
  .menu i {
    margin-right: 10px;
    font-size: 18px;
    width: 20px;
    text-align: center;
  }
  </style>
<template>
    <div class="sidebar">
      <div class="logo">
        <img src="@/assets/invitext.png" alt="CRM Logo">
      </div>
      <nav class="menu">
        <ul>
          <li :class="{ active: isActive('/dashboard') }">
            <router-link to="/dashboard">
              <i class="fas fa-tachometer-alt"></i>
              <span>Dashboard</span>
            </router-link>
          </li>
          <li :class="{ active: isActive('/tickets') }">
            <router-link to="/tickets">
              <i class="fas fa-ticket-alt"></i>
              <span>Tickets</span>
            </router-link>
          </li>
          <li :class="{ active: isActive('/clients') }">
            <router-link to="/clients">
              <i class="fas fa-users"></i>
              <span>Clients</span>
            </router-link>
          </li>
          <li :class="{ active: isActive('/reports') }">
            <router-link to="/reports">
              <i class="fas fa-chart-bar"></i>
              <span>Reports</span>
            </router-link>
          </li>
          <li :class="{ active: isActive('/settings') }">
            <router-link to="/settings">
              <i class="fas fa-cog"></i>
              <span>Settings</span>
            </router-link>
          </li>
        </ul>
      </nav>
    </div>
  </template>
  
  <script>
  export default {
    name: 'AppSidebar',
    methods: {
      isActive(route) {
        return this.$route.path === route;
      }
    }
  }
  </script>
  
  <style scoped>
  .sidebar {
    width: 250px;
    background-color: #2c3e50;
    color: white;
    height: 100vh;
    overflow-y: auto;
  }
  
  .logo {
    padding: 20px;
    text-align: center;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
  }
  
  .logo img {
    max-width: 100%;
    height: auto;
  }
  
  .menu ul {
    list-style: none;
    padding: 0;
    margin: 0;
  }
  
  .menu li {
    margin: 5px 0;
  }
  
  .menu a {
    display: flex;
    align-items: center;
    padding: 12px 20px;
    color: rgba(255, 255, 255, 0.7);
    text-decoration: none;
    transition: all 0.3s;
  }
  
  .menu a:hover {
    background-color: rgba(255, 255, 255, 0.1);
    color: white;
  }
  
  .menu li.active a {
    background-color: #42b983;
    color: white;
    border-radius: 0 30px 30px 0;
  }
  
  .menu i {
    margin-right: 10px;
    font-size: 18px;
    width: 20px;
    text-align: center;
  }
  </style>