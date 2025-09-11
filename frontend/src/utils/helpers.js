

// Format date for display
export const formatDate = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

// Format time for display
export const formatTime = (time) => {
  if (!time) return '-'
  return new Date(`2000-01-01 ${time}`).toLocaleTimeString('en-US', {
    hour: '2-digit',
    minute: '2-digit'
  })
}

// Format datetime
export const formatDateTime = (datetime) => {
  if (!datetime) return '-'
  return new Date(datetime).toLocaleString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

// Calculate total hours
export const calculateHours = (clockIn, clockOut) => {
  if (!clockIn || !clockOut) return 0
  
  const start = new Date(`2000-01-01 ${clockIn}`)
  const end = new Date(`2000-01-01 ${clockOut}`)
  const diff = end - start
  
  return Math.round((diff / (1000 * 60 * 60)) * 100) / 100
}

// Validate email
export const isValidEmail = (email) => {
  const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  return re.test(email)
}

// Get greeting based on time
export const getGreeting = () => {
  const hour = new Date().getHours()
  if (hour < 12) return 'Good morning'
  if (hour < 17) return 'Good afternoon'
  return 'Good evening'
}

// Format status with colors
export const getStatusColor = (status) => {
  const colors = {
    present: 'text-green-600 bg-green-100',
    late: 'text-yellow-600 bg-yellow-100',
    absent: 'text-red-600 bg-red-100',
    early_departure: 'text-orange-600 bg-orange-100',
    active: 'text-green-600 bg-green-100',
    inactive: 'text-red-600 bg-red-100'
  }
  return colors[status] || 'text-gray-600 bg-gray-100'
}

// Capitalize first letter
export const capitalize = (str) => {
  if (!str) return ''
  return str.charAt(0).toUpperCase() + str.slice(1)
}

// Get current time formatted
export const getCurrentTime = () => {
  return new Date().toLocaleTimeString('en-US', {
    hour: '2-digit',
    minute: '2-digit'
  })
}

// Check if date is today
export const isToday = (date) => {
  if (!date) return false
  const today = new Date().toDateString()
  const checkDate = new Date(date).toDateString()
  return today === checkDate
}

