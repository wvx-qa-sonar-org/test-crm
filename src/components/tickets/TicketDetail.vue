<template>
    <div class="ticket-detail-container">
      <Sidebar />
      <div class="main-content">
        <Header />
        <div class="ticket-content">
          <div class="ticket-header">
            <div class="back-button" @click="$router.go(-1)">
              <i class="fas fa-arrow-left"></i> Back to Tickets
            </div>
            <h1>Ticket #{{ ticket.id }}</h1>
            <div class="ticket-actions">
              <button class="btn-edit" @click="editMode = !editMode">
                <i class="fas fa-edit"></i> {{ editMode ? 'Cancel' : 'Edit' }}
              </button>
              <button v-if="!editMode" class="btn-delete" @click="confirmDelete">
                <i class="fas fa-trash"></i> Delete
              </button>
            </div>
          </div>
          
          <div class="ticket-info">
            <div class="ticket-main">
              <div class="ticket-subject" v-if="!editMode">
                <h2>{{ ticket.subject }}</h2>
                <div class="ticket-meta">
                  <span class="ticket-date">Created: {{ ticket.created }}</span>
                  <span :class="'status-badge ' + ticket.status.toLowerCase()">
                    {{ ticket.status }}
                  </span>
                  <span :class="'priority-badge ' + ticket.priority.toLowerCase()">
                    {{ ticket.priority }}
                  </span>
                </div>
              </div>
              
              <div class="ticket-edit-form" v-else>
                <div class="form-group">
                  <label for="subject">Subject</label>
                  <input type="text" id="subject" v-model="editedTicket.subject">
                </div>
                <div class="form-row">
                  <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" v-model="editedTicket.status">
                      <option value="Open">Open</option>
                      <option value="Pending">Pending</option>
                      <option value="Resolved">Resolved</option>
                      <option value="Closed">Closed</option>
                    </select>
                  </div>
                  <div class="form-group">
                    <label for="priority">Priority</label>
                    <select id="priority" v-model="editedTicket.priority">
                      <option value="Low">Low</option>
                      <option value="Medium">Medium</option>
                      <option value="High">High</option>
                      <option value="Urgent">Urgent</option>
                    </select>
                  </div>
                </div>
                <div class="form-group">
                  <label for="description">Description</label>
                  <textarea id="description" v-model="editedTicket.description" rows="5"></textarea>
                </div>
                <button class="btn-save" @click="saveTicket">
                  <i class="fas fa-save"></i> Save Changes
                </button>
              </div>
              
              <div class="ticket-description" v-if="!editMode">
                <h3>Description</h3>
                <p>{{ ticket.description }}</p>
              </div>
              
              <div class="ticket-comments">
                <h3>Comments ({{ comments.length }})</h3>
                <div class="comment-list">
                  <div class="comment" v-for="comment in comments" :key="comment.id">
                    <div class="comment-avatar">
                      <img :src="comment.avatar" alt="User Avatar">
                    </div>
                    <div class="comment-content">
                      <div class="comment-header">
                        <span class="comment-author">{{ comment.author }}</span>
                        <span class="comment-date">{{ comment.date }}</span>
                      </div>
                      <div class="comment-text">
                        {{ comment.text }}
                      </div>
                    </div>
                  </div>
                </div>
                
                <div class="add-comment">
                  <h4>Add Comment</h4>
                  <textarea v-model="newComment" placeholder="Type your comment here..."></textarea>
                  <button class="btn-comment" @click="addComment">
                    <i class="fas fa-paper-plane"></i> Post Comment
                  </button>
                </div>
              </div>
            </div>
            
            <div class="ticket-sidebar">
              <div class="ticket-client">
                <h3>Client Information</h3>
                <div class="client-info">
                  <div class="client-avatar">
                    <img :src="ticket.clientAvatar" alt="Client Avatar">
                  </div>
                  <div class="client-details">
                    <h4>{{ ticket.client }}</h4>
                    <p><i class="fas fa-envelope"></i> {{ ticket.clientEmail }}</p>
                    <p><i class="fas fa-phone"></i> {{ ticket.clientPhone }}</p>
                  </div>
                </div>
              </div>
              
              <div class="ticket-activity">
                <h3>Activity Log</h3>
                <div class="activity-list">
                  <div class="activity-item" v-for="activity in activities" :key="activity.id">
                    <div class="activity-icon">
                      <i :class="activity.icon"></i>
                    </div>
                    <div class="activity-details">
                      <p class="activity-text">{{ activity.text }}</p>
                      <span class="activity-date">{{ activity.date }}</span>
                    </div>
                  </div>
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
    name: 'TicketDetail',
    components: {
      Sidebar,
      Header
    },
    data() {
      return {
        ticket: {
          id: '',
          subject: '',
          description: '',
          status: '',
          priority: '',
          created: '',
          client: '',
          clientEmail: '',
          clientPhone: '',
          clientAvatar: 'https://via.placeholder.com/60'
        },
        editedTicket: {
          subject: '',
          description: '',
          status: '',
          priority: ''
        },
        comments: [],
        activities: [],
        newComment: '',
        editMode: false
      }
    },
    created() {
      this.fetchTicketDetails();
    },
    methods: {
      async fetchTicketDetails() {
        try {
          const ticketId = this.$route.params.id;
          const response = await axios.get(`/api/tickets/${ticketId}`, {
            headers: {
              'Authorization': `Bearer ${localStorage.getItem('token')}`
            }
          });
          
          this.ticket = response.data.ticket;
          this.comments = response.data.comments;
          this.activities = response.data.activities;
          
          // Initialize edited ticket with current values
          this.editedTicket = {
            subject: this.ticket.subject,
            description: this.ticket.description,
            status: this.ticket.status,
            priority: this.ticket.priority
          };
        } catch (error) {
          console.error('Error fetching ticket details:', error);
        }
      },
      async saveTicket() {
        try {
          const ticketId = this.$route.params.id;
          await axios.put(`/api/tickets/${ticketId}`, this.editedTicket, {
            headers: {
              'Authorization': `Bearer ${localStorage.getItem('token')}`
            }
          });
          
          // Update the ticket with edited values
          this.ticket.subject = this.editedTicket.subject;
          this.ticket.description = this.editedTicket.description;
          this.ticket.status = this.editedTicket.status;
          this.ticket.priority = this.editedTicket.priority;
          
          this.editMode = false;
          
          // Refresh activities to show the update
          this.fetchTicketDetails();
        } catch (error) {
          console.error('Error updating ticket:', error);
        }
      },
      async addComment() {
        if (!this.newComment.trim()) return;
        
        try {
          const ticketId = this.$route.params.id;
          const response = await axios.post(`/api/tickets/${ticketId}/comments`, {
            text: this.newComment
          }, {
            headers: {
              'Authorization': `Bearer ${localStorage.getItem('token')}`
            }
          });
          
          // Add the new comment to the list
          this.comments.push(response.data.comment);
          this.newComment = '';
          
          // Refresh activities to show the comment
          this.fetchTicketDetails();
        } catch (error) {
          console.error('Error adding comment:', error);
        }
      },
      confirmDelete() {
        if (confirm('Are you sure you want to delete this ticket?')) {
          this.deleteTicket();
        }
      },
      async deleteTicket() {
        try {
          const ticketId = this.$route.params.id;
          await axios.delete(`/api/tickets/${ticketId}`, {
            headers: {
              'Authorization': `Bearer ${localStorage.getItem('token')}`
            }
          });
          
          // Redirect back to tickets list
          this.$router.push('/tickets');
        } catch (error) {
          console.error('Error deleting ticket:', error);
        }
      }
    }
  }
  </script>
  
  <style scoped>
  .ticket-detail-container {
    display: flex;
    height: 100vh;
  }
  
  .main-content {
    flex: 1;
    overflow-y: auto;
    background-color: #f5f7fb;
  }
  
  .ticket-content {
    padding: 20px;
  }
  
  .ticket-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
  }
  
  .back-button {
    color: #6c757d;
    cursor: pointer;
    display: flex;
    align-items: center;
  }
  
  .back-button i {
    margin-right: 5px;
  }
  
  .ticket-header h1 {
    margin: 0;
    color: #2c3e50;
  }
  
  .ticket-actions {
    display: flex;
  }
  
  .btn-edit, .btn-delete, .btn-save, .btn-comment {
    padding: 8px 15px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    display: flex;
    align-items: center;
    margin-left: 10px;
  }
  
  .btn-edit, .btn-save, .btn-comment {
    background-color: #42b983;
    color: white;
  }
  
  .btn-delete {
    background-color: #e74c3c;
    color: white;
  }
  
  .btn-edit i, .btn-delete i, .btn-save i, .btn-comment i {
    margin-right: 5px;
  }
  
  .ticket-info {
    display: flex;
  }
  
  .ticket-main {
    flex: 3;
    margin-right: 20px;
  }
  
  .ticket-sidebar {
    flex: 1;
  }
  
  .ticket-subject, .ticket-description, .ticket-comments,
  .ticket-client, .ticket-activity, .ticket-edit-form {
    background: white;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    margin-bottom: 20px;
  }
  
  .ticket-subject h2 {
    margin-top: 0;
    color: #2c3e50;
  }
  
  .ticket-meta {
    display: flex;
    align-items: center;
    margin-top: 10px;
  }
  
  .ticket-date {
    color: #6c757d;
    margin-right: 15px;
  }
  
  .status-badge, .priority-badge {
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
    margin-right: 10px;
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
  
  .ticket-description h3, .ticket-comments h3,
  .ticket-client h3, .ticket-activity h3 {
    margin-top: 0;
    color: #2c3e50;
  }
  
  .comment-list {
    margin-bottom: 20px;
  }
  
  .comment {
    display: flex;
    margin-bottom: 15px;
    padding-bottom: 15px;
    border-bottom: 1px solid #eee;
  }
  
  .comment:last-child {
    border-bottom: none;
  }
  
  .comment-avatar img {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    margin-right: 15px;
  }
  
  .comment-header {
    display: flex;
    justify-content: space-between;
    margin-bottom: 5px;
  }
  
  .comment-author {
    font-weight: 600;
    color: #2c3e50;
  }
  
  .comment-date {
    color: #6c757d;
    font-size: 12px;
  }
  
  .add-comment h4 {
    margin-top: 0;
    color: #2c3e50;
  }
  
  .add-comment textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    margin-bottom: 10px;
    resize: vertical;
    min-height: 80px;
  }
  
  .client-info {
    display: flex;
    align-items: center;
  }
  
  .client-avatar img {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    margin-right: 15px;
  }
  
  .client-details h4 {
    margin: 0 0 5px 0;
    color: #2c3e50;
  }
  
  .client-details p {
    margin: 5px 0;
    color: #6c757d;
  }
  
  .client-details i {
    margin-right: 5px;
    width: 15px;
  }
  
  .activity-item {
    display: flex;
    margin-bottom: 15px;
  }
  
  .activity-icon {
    width: 30px;
    height: 30px;
    background-color: #e6f7f0;
    color: #42b983;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 10px;
  }
  
  .activity-text {
    margin: 0 0 5px 0;
    color: #2c3e50;
  }
  
  .activity-date {
    color: #6c757d;
    font-size: 12px;
  }
  
  .form-group {
    margin-bottom: 15px;
  }
  
  .form-row {
    display: flex;
    gap: 15px;
  }
  
  .form-row .form-group {
    flex: 1;
  }
  
  label {
    display: block;
    margin-bottom: 5px;
    font-weight: 500;
    color: #2c3e50;
  }
  
  input, select, textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
  }
  
  select {
    height: 40px;
  }
  </style>