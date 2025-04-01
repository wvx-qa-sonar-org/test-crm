<template>
    <div class="clients-container">
      <sidebar />
      <div class="main-content">
        <header-component />
        <div class="clients-content">
          <div class="clients-header">
            <h1>Clients</h1>
            <button class="btn-primary" @click="$router.push('/clients/new')">
              <i class="fas fa-plus"></i> New Client
            </button>
          </div>
          
          <div class="search-filters">
            <div class="search-box">
              <input 
                type="text" 
                v-model="searchQuery" 
                placeholder="Search clients..." 
                @input="debounceSearch"
              />
              <i class="fas fa-search"></i>
            </div>
          </div>
          
          <div v-if="loading" class="loading">
            <p>Loading clients...</p>
          </div>
          
          <div v-else-if="clients.length === 0" class="no-clients">
            <p>No clients found.</p>
          </div>
          
          <table v-else class="clients-table">
            <thead>
              <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="client in clients" :key="client.id">
                <td>{{ client.name }}</td>
                <td>{{ client.email }}</td>
                <td>{{ client.phone }}</td>
                <td class="actions">
                  <button class="btn-icon" @click="viewClient(client.id)">
                    <i class="fas fa-eye"></i>
                  </button>
                  <button class="btn-icon" @click="editClient(client.id)">
                    <i class="fas fa-edit"></i>
                  </button>
                  <button class="btn-icon delete" @click="confirmDelete(client)">
                    <i class="fas fa-trash"></i>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
          
          <div class="pagination" v-if="totalPages > 1">
            <button 
              :disabled="currentPage === 1" 
              @click="changePage(currentPage - 1)"
            >
              <i class="fas fa-chevron-left"></i>
            </button>
            
            <span>Page {{ currentPage }} of {{ totalPages }}</span>
            
            <button 
              :disabled="currentPage === totalPages" 
              @click="changePage(currentPage + 1)"
            >
              <i class="fas fa-chevron-right"></i>
            </button>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import Sidebar from '../layout/Sidebar.vue';
  import HeaderComponent from '../layout/Header.vue';
  import axios from 'axios';
  
  export default {
    name: 'ClientsList',
    components: {
      Sidebar,
      HeaderComponent
    },
    data() {
      return {
        clients: [],
        searchQuery: '',
        currentPage: 1,
        totalPages: 1,
        totalClients: 0,
        loading: true,
        searchTimeout: null
      }
    },
    created() {
      this.fetchClients();
    },
    methods: {
      async fetchClients() {
        this.loading = true;
        try {
          const response = await axios.get(`${process.env.VUE_APP_API_URL}/clients`, {
            headers: {
              'Authorization': `Bearer ${localStorage.getItem('token')}`
            },
            params: {
              page: this.currentPage,
              limit: 10,
              search: this.searchQuery
            }
          });
          
          this.clients = response.data.clients;
          this.currentPage = response.data.currentPage;
          this.totalPages = response.data.totalPages;
          this.totalClients = response.data.totalClients;
        } catch (error) {
          console.error('Error fetching clients:', error);
        } finally {
          this.loading = false;
        }
      },
      viewClient(id) {
        this.$router.push(`/clients/${id}`);
      },
      editClient(id) {
        this.$router.push(`/clients/${id}/edit`);
      },
      confirmDelete(client) {
        if (confirm(`Are you sure you want to delete ${client.name}?`)) {
          this.deleteClient(client.id);
        }
      },
      async deleteClient(id) {
        try {
          await axios.delete(`${process.env.VUE_APP_API_URL}/clients/${id}`, {
            headers: {
              'Authorization': `Bearer ${localStorage.getItem('token')}`
            }
          });
          
          // Remove from list
          this.clients = this.clients.filter(client => client.id !== id);
          
          // Reload if page is now empty
          if (this.clients.length === 0 && this.currentPage > 1) {
            this.currentPage--;
            this.fetchClients();
          }
        } catch (error) {
          if (error.response && error.response.status === 400) {
            alert(error.response.data.message);
          } else {
            console.error('Error deleting client:', error);
            alert('Failed to delete client. Please try again.');
          }
        }
      },
      changePage(page) {
        this.currentPage = page;
        this.fetchClients();
      },
      debounceSearch() {
        clearTimeout(this.searchTimeout);
        this.searchTimeout = setTimeout(() => {
          this.currentPage = 1;
          this.fetchClients();
        }, 300);
      }
    }
  }
  </script>
  
  <style scoped>
  .clients-container {
    display: flex;
    height: 100vh;
  }
  
  .main-content {
    flex: 1;
    overflow-y: auto;
    background-color: #f5f7fb;
  }
  
  .clients-content {
    padding: 20px;
  }
  
  .clients-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
  }
  
  .btn-primary {
    background-color: #42b983;
    color: white;
    border: none;
    border-radius: 4px;
    padding: 8px 16px;
    font-size: 14px;
    cursor: pointer;
    display: flex;
    align-items: center;
  }
  
  .btn-primary i {
    margin-right: 5px;
  }
  
  .btn-primary:hover {
    background-color: #3aa876;
  }
  
  .search-filters {
    display: flex;
    margin-bottom: 20px;
  }
  
  .search-box {
    position: relative;
    flex: 1;
    max-width: 300px;
  }
  
  .search-box input {
    width: 100%;
    padding: 8px 30px 8px 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
  }
  
  .search-box i {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    color: #999;
  }
  
  .clients-table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
  }
  
  .clients-table th,
  .clients-table td {
    padding: 12px 15px;
    text-align: left;
  }
  
  .clients-table th {
    background-color: #f8f9fa;
    font-weight: 600;
    color: #333;
    font-size: 14px;
  }
  
  .clients-table tr:not(:last-child) {
    border-bottom: 1px solid #f2f2f2;
  }
  
  .actions {
    display: flex;
    gap: 5px;
  }
  
  .btn-icon {
    background: none;
    border: none;
    cursor: pointer;
    color: #555;
    padding: 5px;
    border-radius: 4px;
  }
  
  .btn-icon:hover {
    background-color: #f1f1f1;
  }
  
  .btn-icon.delete:hover {
    color: #e74c3c;
  }
  
  .pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-top: 20px;
    gap: 10px;
  }
  
  .pagination button {
    background: white;
    border: 1px solid #ddd;
    padding: 5px 10px;
    border-radius: 4px;
    cursor: pointer;
  }
  
  .pagination button:disabled {
    opacity: 0.5;
    cursor: not-allowed;
  }
  
  .loading, .no-clients {
    text-align: center;
    padding: 40px;
    color: #666;
  }
  </style>