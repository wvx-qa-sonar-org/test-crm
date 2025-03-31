<template>
    <div class="create-ticket-container">
      <Sidebar />
      <div class="main-content">
        <Header />
        <div class="create-ticket-content">
          <div class="create-ticket-header">
            <div class="back-button" @click="$router.go(-1)">
              <i class="fas fa-arrow-left"></i> Back to Tickets
            </div>
            <h1>Create New Ticket</h1>
          </div>
          
          <div class="create-ticket-form">
            <div class="form-group">
              <label for="subject">Subject *</label>
              <input 
                type="text" 
                id="subject" 
                v-model="ticket.subject" 
                placeholder="Enter ticket subject"
                required
              >
            </div>
            
            <div class="form-row">
              <div class="form-group">
                <label for="client">Client *</label>
                <select id="client" v-model="ticket.clientId" required>
                  <option value="">Select a client</option>
                  <option v-for="client in clients" :key="client.id" :value="client.id">
                    {{ client.name }}
                  </option>
                </select>
              </div>
              
              <div class="form-group">
                <label for="priority">Priority *</label>
                <select id="priority" v-model="ticket.priority" required>
                  <option value="">Select priority</option>
                  <option value="Low">Low</option>
                  <option value="Medium">Medium</option>
                  <option value="High">High</option>
                  <option value="Urgent">Urgent</option>
                </select>
              </div>
            </div>
            
            <div class="form-group">
              <label for="description">Description *</label>
              <textarea 
                id="description" 
                v-model="ticket.description" 
                rows="6" 
                placeholder="Describe the issue in detail"
                required
              ></textarea>
            </div>
            
            <div class="form-group">
              <label for="attachments">Attachments</label>
              <div class="file-upload">
                <input 
                  type="file" 
                  id="attachments" 
                  @change="handleFileUpload" 
                  multiple
                >
                <div class="upload-placeholder">
                  <i class="fas fa-cloud-upload-alt"></i>
                  <p>Drag files here or click to browse</p>
                </div>
              </div>
              <div class="file-list" v-if="ticket.attachments.length > 0">
                <div class="file-item" v-for="(file, index) in ticket.attachments" :key="index">
                  <i class="fas fa-file"></i>
                  <span class="file-name">{{ file.name }}</span>
                  <button class="btn-remove-file" @click="removeFile(index)">
                    <i class="fas fa-times"></i>
                  </button>
                </div>
              </div>
            </div>
            
            <div class="form-actions">
              <button class="btn-cancel" @click="$router.go(-1)">Cancel</button>
              <button class="btn-create" @click="createTicket">Create Ticket</button>
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
    name: 'CreateTicket',
    components: {
      Sidebar,
      Header
    },
    data() {
      return {
        ticket: {
          subject: '',
          clientId: '',
          priority: '',
          description: '',
          attachments: []
        },
        clients: [],
        error: null
      }
    },
    created() {
      this.fetchClients();
    },
    methods: {
      async fetchClients() {
        try {
          const response = await axios.get('/api/clients', {
            headers: {
              'Authorization': `Bearer ${localStorage.getItem('token')}`
            }
          });
          
          this.clients = response.data.clients;
        } catch (error) {
          console.error('Error fetching clients:', error);
        }
      },
      handleFileUpload(event) {
        const files = event.target.files;
        for (let i = 0; i < files.length; i++) {
          this.ticket.attachments.push(files[i]);
        }
      },
      removeFile(index) {
        this.ticket.attachments.splice(index, 1);
      },
      async createTicket() {
        // Validate required fields
        if (!this.ticket.subject || !this.ticket.clientId || !this.ticket.priority || !this.ticket.description) {
          this.error = 'Please fill in all required fields';
          return;
        }
        
        try {
          // Create FormData for file uploads
          const formData = new FormData();
          formData.append('subject', this.ticket.subject);
          formData.append('clientId', this.ticket.clientId);
          formData.append('priority', this.ticket.priority);
          formData.append('description', this.ticket.description);
          
          // Append attachments
          for (let i = 0; i < this.ticket.attachments.length; i++) {
            formData.append('attachments', this.ticket.attachments[i]);
          }
          
          const response = await axios.post('/api/tickets', formData, {
            headers: {
              'Authorization': `Bearer ${localStorage.getItem('token')}`,
              'Content-Type': 'multipart/form-data'
            }
          });
          
          // Redirect to the newly created ticket
          this.$router.push(`/tickets/${response.data.ticket.id}`);
        } catch (error) {
          console.error('Error creating ticket:', error);
          this.error = error.response?.data?.message || 'Failed to create ticket. Please try again.';
        }
      }
    }
  }
  </script>
  
  <style scoped>
  .create-ticket-container {
    display: flex;
    height: 100vh;
  }
  
  .main-content {
    flex: 1;
    overflow-y: auto;
    background-color: #f5f7fb;
  }
  
  .create-ticket-content {
    padding: 20px;
  }
  
  .create-ticket-header {
    display: flex;
    align-items: center;
    margin-bottom: 20px;
  }
  
  .back-button {
    color: #6c757d;
    cursor: pointer;
    display: flex;
    align-items: center;
    margin-right: 20px;
  }
  
  .back-button i {
    margin-right: 5px;
  }
  
  .create-ticket-header h1 {
    margin: 0;
    color: #2c3e50;
  }
  
  .create-ticket-form {
    background: white;
    border-radius: 8px;
    padding: 30px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
  }
  
  .form-group {
    margin-bottom: 20px;
  }
  
  .form-row {
    display: flex;
    gap: 20px;
  }
  
  .form-row .form-group {
    flex: 1;
  }
  
  label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    color: #2c3e50;
  }
  
  input, select, textarea {
    width: 100%;
    padding: 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
  }
  
  select {
    height: 45px;
  }
  
  .file-upload {
    position: relative;
    border: 2px dashed #ddd;
    border-radius: 4px;
    padding: 20px;
    text-align: center;
    cursor: pointer;
  }
  
  .file-upload input[type="file"] {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    cursor: pointer;
  }
  
  .upload-placeholder {
    color: #6c757d;
  }
  
  .upload-placeholder i {
    font-size: 30px;
    margin-bottom: 10px;
  }
  
  .file-list {
    margin-top: 15px;
  }
  
  .file-item {
    display: flex;
    align-items: center;
    background-color: #f8f9fa;
    padding: 8px 12px;
    border-radius: 4px;
    margin-bottom: 8px;
  }
  
  .file-item i {
    color: #6c757d;
    margin-right: 10px;
  }
  
  .file-name {
    flex: 1;
  }
  
  .btn-remove-file {
    background: none;
    border: none;
    color: #e74c3c;
    cursor: pointer;
  }
  
  .form-actions {
    display: flex;
    justify-content: flex-end;
    margin-top: 30px;
  }
  
  .btn-cancel, .btn-create {
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
  }
  
  .btn-cancel {
    background-color: #f8f9fa;
    color: #6c757d;
    margin-right: 15px;
  }
  
  .btn-create {
    background-color: #42b983;
    color: white;
  }
  
  .btn-create:hover {
    background-color: #3aa876;
  }
  </style>