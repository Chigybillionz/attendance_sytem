

import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

// Import views
const Login = () => import('@/views/auth/Login.vue')
const Register = () => import('@/views/auth/Register.vue')
const AdminDashboard = () => import('@/views/admin/Dashboard.vue')
const AdminUsers = () => import('@/views/admin/Users.vue')
const AdminReports = () => import('@/views/admin/Reports.vue')
const WorkerDashboard = () => import('@/views/worker/Dashboard.vue')
const WorkerAttendance = () => import('@/views/worker/Attendance.vue')
const WorkerHistory = () => import('@/views/worker/History.vue')
const Profile = () => import('@/views/Profile.vue')
const NotFound = () => import('@/views/NotFound.vue')

const routes = [
  {
    path: '/',
    redirect: '/dashboard'
  },
  {
    path: '/login',
    name: 'Login',
    component: Login,
    meta: { 
      requiresGuest: true,
      title: 'Login',
      layout: 'auth'
    }
  },
  {
    path: '/register',
    name: 'Register',
    component: Register,
    meta: { 
      requiresGuest: true,
      title: 'Register',
      layout: 'auth'
    }
  },
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: () => {
      const authStore = useAuthStore()
      if (authStore.isAdmin) {
        return AdminDashboard
      } else {
        return WorkerDashboard
      }
    },
    meta: { 
      requiresAuth: true,
      title: 'Dashboard'
    }
  },
  
  // Admin routes
  {
    path: '/admin',
    redirect: '/admin/dashboard',
    meta: { 
      requiresAuth: true, 
      requiresAdmin: true 
    }
  },
  {
    path: '/admin/dashboard',
    name: 'AdminDashboard',
    component: AdminDashboard,
    meta: { 
      requiresAuth: true, 
      requiresAdmin: true,
      title: 'Admin Dashboard'
    }
  },
  {
    path: '/admin/users',
    name: 'AdminUsers',
    component: AdminUsers,
    meta: { 
      requiresAuth: true, 
      requiresAdmin: true,
      title: 'Manage Users'
    }
  },
  {
    path: '/admin/reports',
    name: 'AdminReports',
    component: AdminReports,
    meta: { 
      requiresAuth: true, 
      requiresAdmin: true,
      title: 'Attendance Reports'
    }
  },

  // Worker routes
  {
    path: '/worker',
    redirect: '/worker/dashboard',
    meta: { 
      requiresAuth: true, 
      requiresWorker: true 
    }
  },
  {
    path: '/worker/dashboard',
    name: 'WorkerDashboard',
    component: WorkerDashboard,
    meta: { 
      requiresAuth: true, 
      requiresWorker: true,
      title: 'My Dashboard'
    }
  },
  {
    path: '/worker/attendance',
    name: 'WorkerAttendance',
    component: WorkerAttendance,
    meta: { 
      requiresAuth: true, 
      requiresWorker: true,
      title: 'Clock In/Out'
    }
  },
  {
    path: '/worker/history',
    name: 'WorkerHistory',
    component: WorkerHistory,
    meta: { 
      requiresAuth: true, 
      requiresWorker: true,
      title: 'My Attendance History'
    }
  },

  // Shared routes
  {
    path: '/profile',
    name: 'Profile',
    component: Profile,
    meta: { 
      requiresAuth: true,
      title: 'My Profile'
    }
  },

  // 404 page
  {
    path: '/:pathMatch(.*)*',
    name: 'NotFound',
    component: NotFound,
    meta: { title: 'Page Not Found' }
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) {
      return savedPosition
    }
    return { top: 0 }
  }
})

// Navigation guards
router.beforeEach(async (to, from, next) => {
  // Set page title
  document.title = to.meta.title ? `${to.meta.title} - Attendance System` : 'Attendance System'

  // Initialize auth store
  const authStore = useAuthStore()

  // Check if route requires authentication
  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next({ name: 'Login', query: { redirect: to.fullPath } })
    return
  }

  // Check if route requires guest (login/register pages)
  if (to.meta.requiresGuest && authStore.isAuthenticated) {
    next({ name: 'Dashboard' })
    return
  }

  // Check if route requires admin
  if (to.meta.requiresAdmin && !authStore.isAdmin) {
    next({ name: 'Dashboard' })
    return
  }

  // Check if route requires worker
  if (to.meta.requiresWorker && !authStore.isWorker) {
    next({ name: 'Dashboard' })
    return
  }

  // Handle redirect query parameter
  if (to.name === 'Login' && authStore.isAuthenticated) {
    const redirect = to.query.redirect
    if (redirect && typeof redirect === 'string') {
      next(redirect)
      return
    }
  }

  next()
})

export default router

