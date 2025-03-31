import Vue from 'vue';
import VueRouter from 'vue-router';
import store from '../store';

// Import components
import Login from '../components/auth/Login.vue';
import Register from '../components/auth/Register.vue';
import Dashboard from '../components/dashboard/Dashboard.vue';
import TicketsList from '../components/tickets/TicketsList.vue';
import TicketDetail from '../components/tickets/TicketDetail.vue';
import CreateTicket from '../components/tickets/CreateTicket.vue';

Vue.use(VueRouter);

const routes = [
  {
    path: '/',
    redirect: '/dashboard'
  },
  {
    path: '/login',
    name: 'Login',
    component: Login,
    meta: { requiresAuth: false }
  },
  {
    path: '/register',
    name: 'Register',
    component: Register,
    meta: { requiresAuth: false }
  },
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: Dashboard,
    meta: { requiresAuth: true }
  },
  {
    path: '/tickets',
    name: 'Tickets',
    component: TicketsList,
    meta: { requiresAuth: true }
  },
  {
    path: '/tickets/create',
    name: 'CreateTicket',
    component: CreateTicket,
    meta: { requiresAuth: true }
  },
  {
    path: '/tickets/:id',
    name: 'TicketDetail',
    component: TicketDetail,
    meta: { requiresAuth: true }
  },
  {
    path: '*',
    redirect: '/dashboard'
  }
];

const router = new VueRouter({
  mode: 'history',
  base: process.env.BASE_URL,
  routes
});

// Navigation guard
router.beforeEach((to, from, next) => {
  const isAuthenticated = store.getters.isAuthenticated;
  
  // Check if route requires authentication
  if (to.matched.some(record => record.meta.requiresAuth)) {
    // If not authenticated, redirect to login
    if (!isAuthenticated) {
      next({
        path: '/login',
        query: { redirect: to.fullPath }
      });
    } else {
      next();
    }
  } else {
    // If authenticated and trying to access login/register, redirect to dashboard
    if (isAuthenticated && (to.path === '/login' || to.path === '/register')) {
      next({ path: '/dashboard' });
    } else {
      next();
    }
  }
});

export default router;