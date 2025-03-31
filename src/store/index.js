import Vue from 'vue';
import Vuex from 'vuex';

Vue.use(Vuex);

export default new Vuex.Store({
  state: {
    user: null,
    isAuthenticated: false,
    tickets: [],
    clients: []
  },
  mutations: {
    setUser(state, user) {
      state.user = user;
    },
    setAuthenticated(state, isAuthenticated) {
      state.isAuthenticated = isAuthenticated;
    },
    setTickets(state, tickets) {
      state.tickets = tickets;
    },
    setClients(state, clients) {
      state.clients = clients;
    },
    addTicket(state, ticket) {
      state.tickets.unshift(ticket);
    },
    updateTicket(state, updatedTicket) {
      const index = state.tickets.findIndex(ticket => ticket.id === updatedTicket.id);
      if (index !== -1) {
        state.tickets.splice(index, 1, updatedTicket);
      }
    },
    removeTicket(state, ticketId) {
      state.tickets = state.tickets.filter(ticket => ticket.id !== ticketId);
    }
  },
  actions: {
    checkAuth({ commit }) {
      const token = localStorage.getItem('token');
      const user = JSON.parse(localStorage.getItem('user'));
      
      if (token && user) {
        commit('setUser', user);
        commit('setAuthenticated', true);
        return true;
      } else {
        commit('setUser', null);
        commit('setAuthenticated', false);
        return false;
      }
    },
    logout({ commit }) {
      localStorage.removeItem('token');
      localStorage.removeItem('user');
      commit('setUser', null);
      commit('setAuthenticated', false);
    },
    async fetchTickets({ commit }) {
      try {
        const response = await this._vm.$api.get('/tickets');
        commit('setTickets', response.data.tickets);
        return response.data;
      } catch (error) {
        console.error('Error fetching tickets:', error);
        throw error;
      }
    },
    async fetchClients({ commit }) {
      try {
        const response = await this._vm.$api.get('/clients');
        commit('setClients', response.data.clients);
        return response.data;
      } catch (error) {
        console.error('Error fetching clients:', error);
        throw error;
      }
    },
    async createTicket({ commit }, ticketData) {
      try {
        const response = await this._vm.$api.post('/tickets', ticketData);
        commit('addTicket', response.data.ticket);
        return response.data.ticket;
      } catch (error) {
        console.error('Error creating ticket:', error);
        throw error;
      }
    },
    async updateTicket({ commit }, { id, data }) {
      try {
        const response = await this._vm.$api.put(`/tickets/${id}`, data);
        commit('updateTicket', response.data.ticket);
        return response.data.ticket;
      } catch (error) {
        console.error('Error updating ticket:', error);
        throw error;
      }
    },
    async deleteTicket({ commit }, id) {
      try {
        await this._vm.$api.delete(`/tickets/${id}`);
        commit('removeTicket', id);
      } catch (error) {
        console.error('Error deleting ticket:', error);
        throw error;
      }
    }
  },
  getters: {
    isAuthenticated: state => state.isAuthenticated,
    currentUser: state => state.user,
    allTickets: state => state.tickets,
    allClients: state => state.clients,
    ticketById: state => id => {
      return state.tickets.find(ticket => ticket.id === id);
    },
    ticketsByStatus: state => status => {
      return state.tickets.filter(ticket => ticket.status === status);
    },
    ticketsByPriority: state => priority => {
      return state.tickets.filter(ticket => ticket.priority === priority);
    }
  }
});