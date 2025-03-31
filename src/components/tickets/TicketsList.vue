<template>
    <div class="tickets-container">
      <Sidebar />
      <div class="main-content">
        <Header />
        <div class="tickets-content">
          <div class="tickets-header">
            <h1>Tickets</h1>
            <button class="btn-create" @click="$router.push('/tickets/create')">
              <i class="fas fa-plus"></i> Create Ticket
            </button>
          </div>
          
          <div class="filters">
            <div class="filter-group">
              <label>Status</label>
              <select v-model="statusFilter">
                <option value="">All</option>
                <option value="Open">Open</option>
                <option value="Pending">Pending</option>
                <option value="Resolved">Resolved</option>
                <option value="Closed">Closed</option>
              </select>
            </div>
            <div class="filter-group">
              <label>Priority</label>
              <select v-model="priorityFilter">
                <option value="">All</option>
                <option value="Low">Low</option>
                <option value="Medium">Medium</option>
                <option value="High">High</option>
                <option value="Urgent">Urgent</option>
              </select>
            </div>
            <div class="filter-group search">
              <input type="text" v-model="searchQuery" placeholder="Search tickets...">
            </div>
          </div>
          
          <div class="tickets-table">
            <table>
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Subject</th>
                  <th>Client</th>
                  <th>Status</th>
                  <th>Priority</th>
                  <th>Created</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="ticket in filteredTickets" :key="ticket.id">
                  <td>#{{ ticket.id }}</td>
                  <td>{{ ticket.subject }}</td>
                  <td>{{ ticket.client }}</td>
                  <td>
                    <span :class="'status-badge ' + ticket.status.toLowerCase()">
                      {{ ticket.status }}
                    </span>
                  </td>
                  <td>
                    <span :class="'priority-badge ' + ticket.priority.toLowerCase()">
                      {{ ticket.priority }}
                    </span>
                  </td>
                  <td>{{ ticket.created }}</td>
                  <td>
                    <button class="btn-view" @click="viewTicket(ticket.id)">View</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          
          <div class="pagination">
            <button :disabled="currentPage === 1" @click="currentPage--">Previous</button>
            <span>Page {{ currentPage }} of {{ totalPages }}</span>
            <button :disabled="currentPage === totalPages" @click="currentPage++">Next</button>
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
    name: 'TicketsList',
    components: {
      Sidebar,
      Header
    },
    data() {
      return {
        tickets: [],
        statusFilter: '',
        priorityFilter: '',
        searchQuery: '',
        currentPage: 1,
        totalPages: 1,
        itemsPerPage: 10
      }
    },
    computed: {
      filteredTickets() {
        let filtered = [...this.tickets];
        
        if (this.statusFilter) {
          filtered = filtered.filter(ticket => ticket.status === this.statusFilter);
        }
        
        if (this.priorityFilter) {
          filtered = filtered.filter(ticket => ticket.priority === this.priorityFilter);
        }
        
        if (this.searchQuery) {
          const query = this.searchQuery.toLowerCase();
          filtered = filtered.filter(ticket => 
            ticket.subject.toLowerCase().includes(query) || 
            ticket.client.toLowerCase().includes(query)
          );
        }
        
        return filtered;
      }
    },
    created() {
      this.fetchTickets();
    },
    methods: {
      async fetchTickets() {
        try {
          const response = await axios.get('/api/tickets', {
            headers: {
              'Authorization': `Bearer ${localStorage.getItem('token')}`
            },
            params: {
              page: this.currentPage,
              limit: this.itemsPerPage
            }
          });
          
          this.tickets = response.data.tickets;
          this.totalPages = response.data.totalPages;
        } catch (error) {
          console.error('Error fetching tickets:', error);
        }
      },
      viewTicket(id) {
        this.$router.push(`/tickets/${id}`);
      }
    },
    watch: {
      currentPage() {
        this.fetchTickets();
      }
    }
  }
  </script>
  
  <style scoped>
  .tickets-container {
    display: flex;
    height: 100vh;
  }
  
  .main-content {
    flex: 1;
    overflow-y: auto;
    background-color: #f5f7fb;
  }
  
  .tickets-content {
    padding: 20px;
  }
  
  .tickets-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
  }
  
  .tickets-header h1 {
    margin: 0;
    color: #2c3e50;
  }
  
  .btn-create {
    background-color: #42b983;
    color: white;
    border: none;
    padding: 10px 15px;
    border-radius: 4px;
    cursor: pointer;
    display: flex;
    align-items: center;
  }
  
  .btn-create i {
    margin-right: 5px;
  }
  
  .filters {
    display: flex;
    margin-bottom: 20px;
    background: white;
    padding: 15px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
  }
  
  .filter-group {
    margin-right: 20px;
  }
  
  .filter-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 500;
    color: #6c757d;
  }
  
  .filter-group select, .filter-group input {
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    min-width: 150px;
  }
  
  .filter-group.search {
    flex-grow: 1;
  }
  
  .filter-group.search input {
    width: 100%;
  }
  
  .tickets-table {
    background: white;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    margin-bottom: 20px;
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
  
  .status-badge, .priority-badge {
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
  
  .low {
    background-color: #e6f7f0;
    color: #42b983;
  }
  
  .medium {
    background-color: #e7f5ff;
    color: #0d6efd;
  }
  
  .high {
    background-color: #fff3cd;
    color: #856404;
  }
  
  .urgent {
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
  
  .pagination {
    display: flex;
    justify-content: center;
    align-items: center;
  }
  
  .pagination button {
    background-color: #42b983;
    color: white;
    border: none;
    padding: 8px 15px;
    border-radius: 4px;
    cursor: pointer;
    margin: 0 10px;
  }
  
  .pagination button:disabled {
    background-color: #ccc;
    cursor: not-allowed;
  }
  
  .pagination span {
    color: #6c757d;
  }
  </style>