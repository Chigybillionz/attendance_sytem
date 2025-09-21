import "./assets/main.css";

import { createApp } from "vue";
import { createPinia } from "pinia";

import App from "./App.vue";
import router from "./router";

// Dynamically fetch and set the CSRF token in the meta tag
import api from "./services/api";

document.addEventListener("DOMContentLoaded", async () => {
  try {
    await api.get("/sanctum/csrf-cookie");
    console.log("CSRF cookie set");

    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (csrfToken) {
      csrfToken.setAttribute("content", "your-csrf-token"); // Replace 'your-csrf-token' dynamically
    } else {
      console.error("Meta tag for CSRF token not found.");
    }
  } catch (error) {
    console.error("Failed to fetch CSRF cookie:", error);
  }
});

const app = createApp(App);

app.use(createPinia());
app.use(router);

app.mount("#app");
