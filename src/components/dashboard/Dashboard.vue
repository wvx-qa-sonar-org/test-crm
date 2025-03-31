<template>
    <div class="dashboard">
      <Sidebar />
      <div class="main-content">
        <Header />
        <div class="dashboard-content">
          <div class="stats-cards">
            <div class="stat-card">
              <div class="stat-icon">
                <i class="fas fa-users"></i>
              </div>
              <div class="stat-details">
                <h3>Total Clients</h3>
                <p class="stat-number">{{ stats.totalClients }}</p>
              </div>
            </div>
            <div class="stat-card">
              <div class="stat-icon">
                <i class="fas fa-ticket-alt"></i>
              </div>
              <div class="stat-details">
                <h3>Total Tickets</h3>
                <p class="stat-number">{{ stats.totalTickets }}</p>
              </div>
            </div>
            <div class="stat-card">
              <div class="stat-icon">
                <i class="fas fa-clock"></i>
              </div>
              <div class="stat-details">
                <h3>Open Tickets</h3>
                <p class="stat-number">{{ stats.ticketsByStatus.open }}</p>
              </div>
            </div>
            <div class="stat-card">
              <div class="stat-icon">
                <i class="fas fa-check-circle"></i>
              </div>
              <div class="stat-details">
                <h3>Closed Tickets</h3>
                <p class="stat-number">{{ stats.ticketsByStatus.closed }}</p>
              </div>
            </div>
          </div>
          
          <div class="recent-tickets">
            <h2>Recent Activities</h2>
            <div class="activities-list">
              <div v-for="ticket in stats.recentActivities" :key="ticket._id" class="activity-item">
                <div class="activity-icon" :class="getStatusClass(ticket.status)">
                  <i class="fas fa-ticket-alt"></i>
                </div>
                <div class="activity-content">
                  <h4>{{ ticket.subject }}</h4>
                  <p>{{ ticket.client }} - {{ ticket.status }}</p>
                  <small>{{ formatDate(ticket.created) }}</small>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import Sidebar from '../layout/Sidebar.vue';
  import Header from '../layout/Header.vue';
  import axios from 'axios';
  
  export default {
    name: 'DashboardView',
    components: {
      Sidebar,
      Header
    },
    data() {
      return {
        stats: {
          totalClients: 0,
          totalTickets: 0,
          ticketsByStatus: {
            open: 0,
            pending: 0,
            closed: 0
          },
          recentActivities: []
        }
      }
    },
    created() {
      this.fetchDashboardData();
    },
    methods: {
      async fetchDashboardData() {
        try {
          const response = await axios.get(`${process.env.VUE_APP_API_URL}/dashboard`, {
            headers: {
              'Authorization': `Bearer ${localStorage.getItem('token')}`
            }
          });
          
          // Check the response format and adapt if needed
          if (response.data.status && response.data.data) {
            this.stats = response.data.data;
          } else {
            // Handle the existing format from index.php
            this.stats = {
              totalClients: response.data.totalClients || 0,
              totalTickets: response.data.totalTickets || 0,
              ticketsByStatus: {
                open: response.data.totalTickets - (response.data.pendingTickets + response.data.resolvedTickets) || 0,
                pending: response.data.pendingTickets || 0,
                closed: response.data.resolvedTickets || 0
              },
              recentActivities: response.data.recentTickets || []
            };
          }
        } catch (error) {
          console.error('Error fetching dashboard data:', error);
        }
      },
      getStatusClass(status) {
        return {
          'status-open': status === 'Open',
          'status-pending': status === 'Pending',
          'status-closed': status === 'Closed'
        }
      },
      formatDate(date) {
        return new Date(date).toLocaleDateString();
      }
    }
  }
  </script>
  
  <style scoped>
  .dashboard {
    display: flex;
    height: 100vh;
  }
  
  .main-content {
    flex: 1;
    overflow-y: auto;
    background-color: #f5f7fb;
  }
  
  .dashboard-content {
    padding: 20px;
  }
  
  .stats-cards {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
    margin-bottom: 30px;
  }
  
  .stat-card {
    background: white;
    border-radius: 8px;
    padding: 20px;
    display: flex;
    align-items: center;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
  }
  
  .stat-icon {
    background-color: #e6f7f0;
    color: #42b983;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
    font-size: 20px;
  }
  
  .stat-details h3 {
    font-size: 14px;
    color: #6c757d;
    margin: 0 0 5px 0;
  }
  
  .stat-number {
    font-size: 24px;
    font-weight: 600;
    color: #2c3e50;
    margin: 0;
  }
  
  .recent-tickets {
    background: white;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
  }
  
  .recent-tickets h2 {
    margin-top: 0;
    margin-bottom: 20px;
    color: #2c3e50;
  }
  
  table {
    width: 100%;
    border-collapse: collapse;
  }
  
  th, td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid #eee;
  }
  
  th {
    font-weight: 600;
    color: #6c757d;
  }
  
  .status-badge {
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
  }
  
  .open {
    background-color: #e6f7f0;
    color: #42b983;
  }
  
  .pending {
    background-color: #fff3cd;
    color: #856404;
  }
  
  .resolved {
    background-color: #d4edda;
    color: #155724;
  }
  
  .closed {
    background-color: #f8d7da;
    color: #721c24;
  }
  
  .btn-view {
    background-color: #42b983;
    color: white;
    border: none;
    padding: 5px 10px;
    border-radius: 4px;
    cursor: pointer;
  }
  
  .btn-view:hover {
    background-color: #3aa876;
  }
  </style>