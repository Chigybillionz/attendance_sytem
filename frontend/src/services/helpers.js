// Format date for display
export const formatDate = (date) => {
  return new Date(date).toLocaleDateString("en-US", {
    year: "numeric",
    month: "short",
    day: "numeric",
  });
};

// Format time for display
export const formatTime = (time) => {
  return new Date(`2000-01-01 ${time}`).toLocaleTimeString("en-US", {
    hour: "2-digit",
    minute: "2-digit",
  });
};

// Format datetime
export const formatDateTime = (datetime) => {
  return new Date(datetime).toLocaleString("en-US", {
    year: "numeric",
    month: "short",
    day: "numeric",
    hour: "2-digit",
    minute: "2-digit",
  });
};

// Calculate total hours
export const calculateHours = (clockIn, clockOut) => {
  if (!clockIn || !clockOut) return 0;

  const start = new Date(`2000-01-01 ${clockIn}`);
  const end = new Date(`2000-01-01 ${clockOut}`);
  const diff = end - start;

  return Math.round((diff / (1000 * 60 * 60)) * 100) / 100;
};

// Validate email
export const isValidEmail = (email) => {
  const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return re.test(email);
};

// Get greeting based on time
export const getGreeting = () => {
  const hour = new Date().getHours();
  if (hour < 12) return "Good morning";
  if (hour < 17) return "Good afternoon";
  return "Good evening";
};
