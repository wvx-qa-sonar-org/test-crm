<template>
    <div class="client-detail-container">
      <sidebar />
      <div class="main-content">
        <header-component />
        <div class="client-detail-content">
          <div class="client-detail-header">
            <div class="back-button" @click="$router.push('/clients')">
              <i class="fas fa-arrow-left"></i> Back to Clients
            </div>
            <div class="client-actions">
              <button class="btn-edit" @click="$router.push(`/clients/${client.id}/edit`)">
                <i class="fas fa-edit"></i> Edit
              </button>
              <button class="btn-delete" @click="confirmDelete">
                <i class="fas fa-trash"></i> Delete
              </button>
            </div>
          </div>
          
          <div v-if="loading" class="loading">
            <p>Loading client data...</p>
          </div>
          
          <div v-else class="client-info">
            <h1>{{ client.name }}</h1>
            
            <div class="info-section">
              <div class="info-item">
                <div class="label">Email</div>
                <div class="value">{{ client.email }}</div>
              </div>
              
              <div class="info-item">
                <div class="label">Phone</div>
                <div class="value">{{ client.phone || 'Not provided' }}</div>
              </div>
              
              <div class="info-item">
                <div class="label">Address</div>
                <div class="value">{{ client.address || 'Not provided' }}</div>
              </div>
              
              <div class="info-item">
                <div class="label">Created</div>
                <div class="value">{{ client.created }}</div>
              </div>
            </div>
            
            <div class="info-section">
              <div class="label">Notes</div>
              <div class="value notes">{{ client.notes || 'No notes available' }}</div>
            </div>
            
            <div class="info-section">
              <h2>Client Tickets</h2>
              <div v-if="tickets.length === 0" class="no-data">
                No tickets found for this client.
              </div>
              <table v-else class="tickets-table">
                <thead>
                  <tr>
                    <th>Subject</th>
                    <th>Status</th>
                    <th>Priority</th>
                    <th>Created</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="ticket in tickets" :key="ticket.id">
                    <td>{{ ticket.subject }}</td>
                    <td>
                      <span class="status-badge" :class="'status-' + ticket.status.toLowerCase()">
                        {{ ticket.status }}
                      </span>
                    </td>
                    <td>{{ ticket.priority }}</td>
                    <td>{{ ticket.created }}</td>
                    <td>
                      <button class="btn-icon" @click="viewTicket(ticket.id)">
                        <i class="fas fa-eye"></i>
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
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
    name: 'ClientDetail',
    components: {
      Sidebar,
      HeaderComponent
    },
    data() {
      return {
        client: {
          id: '',
          name: '',
          email: '',
          phone: '',
          address: '',
          notes: '',
          created: ''
        },
        tickets: [],
        loading: true
      }
    },
    created() {
      this.fetchClientData();
    },
    methods: {
      async fetchClientData() {
        this.loading = true;
        try {
          const clientId = this.$route.params.id;
          
          // Fetch client details
          const clientResponse = await axios.get(`${process.env.VUE_APP_API_URL}/clients/${clientId}`, {
            headers: {
              'Authorization': `Bearer ${localStorage.getItem('token')}`
            }
          });
          
          this.client = clientResponse.data;
          
          // Fetch client tickets
          const ticketsResponse = await axios.get(`${process.env.VUE_APP_API_URL}/tickets`, {
            headers: {
              'Authorization': `Bearer ${localStorage.getItem('token')}`
            },
            params: {
              clientId: clientId
            }
          });
          
          this.tickets = ticketsResponse.data.tickets || [];
        } catch (error) {
          console.error('Error fetching client data:', error);
        } finally {
          this.loading = false;
        }
      },
      confirmDelete() {
        if (confirm(`Are you sure you want to delete ${this.client.name}?`)) {
          this.deleteClient();
        }
      },
      async deleteClient() {
        try {
          await axios.delete(`${process.env.VUE_APP_API_URL}/clients/${this.client.id}`, {
            headers: {
              'Authorization': `Bearer ${localStorage.getItem('token')}`
            }
          });
          
          this.$router.push('/clients');
        } catch (error) {
          if (error.response && error.response.status === 400) {
            alert(error.response.data.message);
          } else {
            console.error('Error deleting client:', error);
            alert('Failed to delete client. Please try again.');
          }
        }
      },
      viewTicket(id) {
        this.$router.push(`/tickets/${id}`);
      }
    }
  }
  </script>
  
  <style scoped>
  .client-detail-container {
    display: flex;
    height: 100vh;
  }
  
  .main-content {
    flex: 1;
    overflow-y: auto;
    background-color: #f5f7fb;
  }
  
  .client-detail-content {
    padding: 20px;
  }
  
  .client-detail-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
  }
  
  .back-button {
    color: #6c757d;
    cursor: pointer;
  }
  
  .back-button i {
    margin-right: 5px;
  }
  
  .client-actions {
    display: flex;
    gap: 10px;
  }
  
  .btn-edit, .btn-delete {
    padding: 8px 16px;
    border-radius: 4px;
    cursor: pointer;
    display: flex;
    align-items: center;
  }
  
  .btn-edit {
    background-color: #42b983;
    color: white;
    border: none;
  }
  
  .btn-delete {
    background-color: #f8f9fa;
    color: #dc3545;
    border: 1px solid #dc3545;
  }
  
  .btn-edit i, .btn-delete i {
    margin-right: 5px;
  }
  
  .client-info {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    padding: 20px;
  }
  
  .client-info h1 {
    margin-top: 0;
    margin-bottom: 20px;
    color: #2c3e50;
  }
  
  .info-section {
    margin-bottom: 20px;
    padding-bottom: 20px;
    border-bottom: 1px solid #f2f2f2;
  }
  
  .info-section:last-child {
    border-bottom: none;
  }
  
  .info-item {
    margin-bottom: 10px;
  }
  
  .label {
    font-weight: 600;
    color: #6c757d;
    margin-bottom: 5px;
  }
  
  .value {
    color: #2c3e50;
  }
  
  .value.notes {
    white-space: pre-line;
  }
  
  .tickets-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
  }
  
  .tickets-table th, 
  .tickets-table td {
    padding: 12px 15px;
    text-align: left;
  }
  
  .tickets-table th {
    background-color: #f8f9fa;
    font-weight: 600;
    color: #333;
    font-size: 14px;
  }
  
  .tickets-table tr:not(:last-child) {
    border-bottom: 1px solid #f2f2f2;
  }
  
  .status-badge {
    display: inline-block;
    padding: 5px 10px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
  }
  
  .status-open {
    background-color: #fff0c2;
    color: #856404;
  }
  
  .status-pending {
    background-color: #cce5ff;
    color: #004085;
  }
  
  .status-closed {
    background-color: #d4edda;
    color: #155724;
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
  
  .loading, .no-data {
    text-align: center;
    padding: 40px;
    color: #666;
  }
  </style>