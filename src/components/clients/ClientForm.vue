<template>
    <div class="client-form-container">
      <sidebar />
      <div class="main-content">
        <header-component />
        <div class="client-form-content">
          <div class="client-form-header">
            <div class="back-button" @click="$router.push('/clients')">
              <i class="fas fa-arrow-left"></i> Back to Clients
            </div>
            <h1>{{ isEditing ? 'Edit Client' : 'New Client' }}</h1>
          </div>
          
          <div v-if="loading" class="loading">
            <p>Loading client data...</p>
          </div>
          
          <form v-else @submit.prevent="saveClient" class="client-form">
            <div v-if="error" class="error-message">{{ error }}</div>
            
            <div class="form-group">
              <label for="name">Name *</label>
              <input 
                type="text" 
                id="name" 
                v-model="client.name" 
                required
                placeholder="Enter client name"
              />
            </div>
            
            <div class="form-group">
              <label for="email">Email *</label>
              <input 
                type="email" 
                id="email" 
                v-model="client.email" 
                required
                placeholder="Enter client email"
              />
            </div>
            
            <div class="form-group">
              <label for="phone">Phone</label>
              <input 
                type="tel" 
                id="phone" 
                v-model="client.phone" 
                placeholder="Enter client phone"
              />
            </div>
            
            <div class="form-group">
              <label for="address">Address</label>
              <textarea 
                id="address" 
                v-model="client.address" 
                rows="3"
                placeholder="Enter client address"
              ></textarea>
            </div>
            
            <div class="form-group">
              <label for="notes">Notes</label>
              <textarea 
                id="notes" 
                v-model="client.notes" 
                rows="5"
                placeholder="Enter any additional notes"
              ></textarea>
            </div>
            
            <div class="form-actions">
              <button type="button" class="btn-cancel" @click="$router.push('/clients')">
                Cancel
              </button>
              <button type="submit" class="btn-save">
                {{ isEditing ? 'Update Client' : 'Create Client' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import Sidebar from '../layout/Sidebar.vue';
  import HeaderComponent from '../layout/Header.vue';
  import axios from 'axios';
  
  export default {
    name: 'ClientForm',
    components: {
      Sidebar,
      HeaderComponent
    },
    data() {
      return {
        client: {
          name: '',
          email: '',
          phone: '',
          address: '',
          notes: ''
        },
        isEditing: false,
        loading: false,
        error: null
      }
    },
    created() {
      // Check if editing or creating
      const id = this.$route.params.id;
      if (id) {
        this.isEditing = true;
        this.fetchClient(id);
      }
    },
    methods: {
      async fetchClient(id) {
        this.loading = true;
        try {
          const response = await axios.get(`${process.env.VUE_APP_API_URL}/clients/${id}`, {
            headers: {
              'Authorization': `Bearer ${localStorage.getItem('token')}`
            }
          });
          
          this.client = response.data;
        } catch (error) {
          console.error('Error fetching client:', error);
          this.error = 'Failed to load client data. Please try again.';
        } finally {
          this.loading = false;
        }
      },
      async saveClient() {
        try {
          if (this.isEditing) {
            // Update existing client
            await axios.put(
              `${process.env.VUE_APP_API_URL}/clients/${this.$route.params.id}`, 
              this.client,
              {
                headers: {
                  'Authorization': `Bearer ${localStorage.getItem('token')}`
                }
              }
            );
          } else {
            // Create new client
            await axios.post(
              `${process.env.VUE_APP_API_URL}/clients`, 
              this.client,
              {
                headers: {
                  'Authorization': `Bearer ${localStorage.getItem('token')}`
                }
              }
            );
          }
          
          // Redirect to clients list
          this.$router.push('/clients');
          
        } catch (error) {
          console.error('Error saving client:', error);
          if (error.response && error.response.data && error.response.data.message) {
            this.error = error.response.data.message;
          } else {
            this.error = 'Failed to save client. Please try again.';
          }
        }
      }
    }
  }
  </script>
  
  <style scoped>
  .client-form-container {
    display: flex;
    height: 100vh;
  }
  
  .main-content {
    flex: 1;
    overflow-y: auto;
    background-color: #f5f7fb;
  }
  
  .client-form-content {
    padding: 20px;
  }
  
  .client-form-header {
    margin-bottom: 20px;
  }
  
  .back-button {
    color: #6c757d;
    cursor: pointer;
    display: inline-block;
    margin-bottom: 10px;
  }
  
  .back-button i {
    margin-right: 5px;
  }
  
  .client-form {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
  }
  
  .error-message {
    background-color: #f8d7da;
    color: #721c24;
    padding: 10px;
    border-radius: 4px;
    margin-bottom: 15px;
  }
  
  .form-group {
    margin-bottom: 15px;
  }
  
  label {
    display: block;
    margin-bottom: 5px;
    font-weight: 500;
  }
  
  input, textarea {
    width: 100%;
    padding: 8px 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
  }
  
  textarea {
    resize: vertical;
  }
  
  .form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 20px;
  }
  
  .btn-cancel {
    background-color: #f8f9fa;
    color: #333;
    border: 1px solid #ddd;
    padding: 8px 16px;
    border-radius: 4px;
    cursor: pointer;
  }
  
  .btn-save {
    background-color: #42b983;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 4px;
    cursor: pointer;
  }
  
  .btn-save:hover {
    background-color: #3aa876;
  }
  
  .loading {
    text-align: center;
    padding: 40px;
    color: #666;
  }
  </style>