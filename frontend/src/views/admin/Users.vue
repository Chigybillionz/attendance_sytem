<!-- File: frontend/src/views/admin/Users.vue -->
<!-- Location: frontend/src/views/admin/Users.vue -->

<template>
  <div class="space-y-6">
    <!-- Page Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">User Management</h1>
          <p class="text-gray-600 mt-1">Manage employees and their accounts</p>
        </div>
        <button @click="showAddModal = true" class="btn-primary">
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M12 6v6m0 0v6m0-6h6m-6 0H6"
            />
          </svg>
          Add Employee
        </button>
      </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
          <input
            v-model="filters.search"
            type="text"
            placeholder="Search by name, email, or ID"
            class="input-field"
            @input="debouncedSearch"
          />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
          <select v-model="filters.role" class="input-field" @change="fetchUsers">
            <option value="">All Roles</option>
            <option value="admin">Admin</option>
            <option value="worker">Worker</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
          <select v-model="filters.status" class="input-field" @change="fetchUsers">
            <option value="">All Status</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
          <select v-model="filters.department" class="input-field" @change="fetchUsers">
            <option value="">All Departments</option>
            <option v-for="dept in departments" :key="dept" :value="dept">{{ dept }}</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
      <div class="p-6 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-900">Employees</h2>
      </div>

      <div v-if="loading" class="p-6 text-center">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
        <p class="mt-2 text-gray-500">Loading...</p>
      </div>

      <div v-else-if="users.length" class="table-container">
        <table class="table">
          <thead>
            <tr>
              <th>Employee</th>
              <th>Employee ID</th>
              <th>Department</th>
              <th>Role</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="user in users" :key="user.id">
              <td>
                <div>
                  <p class="font-medium text-gray-900">{{ user.name }}</p>
                  <p class="text-sm text-gray-500">{{ user.email }}</p>
                </div>
              </td>
              <td class="font-medium">{{ user.employee_id }}</td>
              <td>{{ user.department || "-" }}</td>
              <td>
                <span
                  class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                  :class="
                    user.role === 'admin'
                      ? 'bg-purple-100 text-purple-800'
                      : 'bg-blue-100 text-blue-800'
                  "
                >
                  {{ user.role }}
                </span>
              </td>
              <td>
                <span
                  class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                  :class="getStatusColor(user.status)"
                >
                  {{ user.status }}
                </span>
              </td>
              <td>
                <div class="flex space-x-2">
                  <button @click="editUser(user)" class="text-blue-600 hover:text-blue-800 text-sm">
                    Edit
                  </button>
                  <button
                    @click="toggleStatus(user)"
                    class="text-yellow-600 hover:text-yellow-800 text-sm"
                  >
                    {{ user.status === "active" ? "Deactivate" : "Activate" }}
                  </button>
                  <button @click="deleteUser(user)" class="text-red-600 hover:text-red-800 text-sm">
                    Delete
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div v-else class="p-6 text-center">
        <svg
          class="w-12 h-12 text-gray-400 mx-auto mb-4"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
          />
        </svg>
        <p class="text-gray-500">No employees found</p>
      </div>

      <!-- Pagination -->
      <div v-if="pagination.last_page > 1" class="p-6 border-t border-gray-200">
        <div class="flex items-center justify-between">
          <div class="text-sm text-gray-700">
            Showing {{ (pagination.current_page - 1) * pagination.per_page + 1 }} to
            {{ Math.min(pagination.current_page * pagination.per_page, pagination.total) }} of
            {{ pagination.total }} results
          </div>
          <div class="flex space-x-2">
            <button
              @click="changePage(pagination.current_page - 1)"
              :disabled="pagination.current_page <= 1"
              class="btn-secondary text-sm"
              :class="{ 'opacity-50 cursor-not-allowed': pagination.current_page <= 1 }"
            >
              Previous
            </button>
            <button
              @click="changePage(pagination.current_page + 1)"
              :disabled="pagination.current_page >= pagination.last_page"
              class="btn-secondary text-sm"
              :class="{
                'opacity-50 cursor-not-allowed': pagination.current_page >= pagination.last_page,
              }"
            >
              Next
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Add/Edit User Modal -->
    <Modal
      :show="showAddModal || showEditModal"
      @close="closeModal"
      :title="editingUser ? 'Edit Employee' : 'Add New Employee'"
    >
      <form @submit.prevent="saveUser">
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
            <input
              v-model="form.name"
              type="text"
              required
              class="input-field"
              :class="{ 'border-red-300': errors.name }"
            />
            <p v-if="errors.name" class="mt-1 text-sm text-red-600">{{ errors.name }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input
              v-model="form.email"
              type="email"
              required
              class="input-field"
              :class="{ 'border-red-300': errors.email }"
            />
            <p v-if="errors.email" class="mt-1 text-sm text-red-600">{{ errors.email }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Employee ID</label>
            <input
              v-model="form.employee_id"
              type="text"
              required
              class="input-field"
              :class="{ 'border-red-300': errors.employee_id }"
            />
            <p v-if="errors.employee_id" class="mt-1 text-sm text-red-600">
              {{ errors.employee_id }}
            </p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
            <input v-model="form.department" type="text" class="input-field" />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
            <input v-model="form.phone" type="tel" class="input-field" />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
            <select v-model="form.role" class="input-field" required>
              <option value="worker">Worker</option>
              <option value="admin">Admin</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select v-model="form.status" class="input-field" required>
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
          </div>

          <div v-if="!editingUser">
            <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
            <input
              v-model="form.password"
              type="password"
              :required="!editingUser"
              class="input-field"
              :class="{ 'border-red-300': errors.password }"
            />
            <p v-if="errors.password" class="mt-1 text-sm text-red-600">{{ errors.password }}</p>
          </div>
        </div>

        <div v-if="modalError" class="mt-4 bg-red-50 border border-red-200 rounded-lg p-4">
          <p class="text-sm text-red-600">{{ modalError }}</p>
        </div>

        <template #footer>
          <button type="button" @click="closeModal" class="btn-secondary mr-3">Cancel</button>
          <button type="submit" :disabled="modalLoading" class="btn-primary">
            {{ modalLoading ? "Saving..." : "Save Employee" }}
          </button>
        </template>
      </form>
    </Modal>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from "vue";
import { useUsersStore } from "@/stores/users";
import { getStatusColor } from "@/utils/helpers";
import Modal from "@/components/common/Modal.vue";

const usersStore = useUsersStore();

const loading = ref(false);
const modalLoading = ref(false);
const showAddModal = ref(false);
const showEditModal = ref(false);
const editingUser = ref(null);
const modalError = ref("");

const filters = reactive({
  search: "",
  role: "",
  status: "",
  department: "",
});

const form = reactive({
  name: "",
  email: "",
  employee_id: "",
  department: "",
  phone: "",
  role: "worker",
  status: "active",
  password: "",
});

const errors = reactive({
  name: "",
  email: "",
  employee_id: "",
  password: "",
});

// Computed properties
const users = computed(() => usersStore.users);
const departments = computed(() => usersStore.departments);
const pagination = computed(() => usersStore.pagination);

// Debounced search
let searchTimeout;
const debouncedSearch = () => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    fetchUsers();
  }, 500);
};

const fetchUsers = async () => {
  loading.value = true;
  try {
    const params = {};
    if (filters.search) params.search = filters.search;
    if (filters.role) params.role = filters.role;
    if (filters.status) params.status = filters.status;
    if (filters.department) params.department = filters.department;

    await usersStore.fetchUsers(params);
  } catch (error) {
    console.error("Failed to fetch users:", error);
  } finally {
    loading.value = false;
  }
};

const editUser = (user) => {
  editingUser.value = user;
  form.name = user.name;
  form.email = user.email;
  form.employee_id = user.employee_id;
  form.department = user.department || "";
  form.phone = user.phone || "";
  form.role = user.role;
  form.status = user.status;
  form.password = "";
  showEditModal.value = true;
};

const saveUser = async () => {
  clearErrors();
  if (!validateForm()) return;

  modalLoading.value = true;
  try {
    if (editingUser.value) {
      await usersStore.updateUser(editingUser.value.id, form);
    } else {
      // For new users, use the auth store to register
      // This would need to be implemented
    }

    closeModal();
    await fetchUsers();
    window.showNotification?.(
      `Employee ${editingUser.value ? "updated" : "added"} successfully!`,
      "success"
    );
  } catch (error) {
    modalError.value = error.response?.data?.message || "Failed to save employee";
  } finally {
    modalLoading.value = false;
  }
};

const toggleStatus = async (user) => {
  const newStatus = user.status === "active" ? "inactive" : "active";
  try {
    await usersStore.updateUserStatus(user.id, newStatus);
    window.showNotification?.("User status updated successfully!", "success");
  } catch (error) {
    window.showNotification?.("Failed to update user status", "error");
  }
};

const deleteUser = async (user) => {
  if (!confirm(`Are you sure you want to delete ${user.name}? This action cannot be undone.`)) {
    return;
  }

  try {
    await usersStore.deleteUser(user.id);
    window.showNotification?.("Employee deleted successfully!", "success");
  } catch (error) {
    window.showNotification?.("Failed to delete employee", "error");
  }
};

const closeModal = () => {
  showAddModal.value = false;
  showEditModal.value = false;
  editingUser.value = null;
  modalError.value = "";
  clearErrors();
  Object.keys(form).forEach((key) => {
    form[key] = key === "role" ? "worker" : key === "status" ? "active" : "";
  });
};

const clearErrors = () => {
  Object.keys(errors).forEach((key) => {
    errors[key] = "";
  });
};

const validateForm = () => {
  let isValid = true;

  if (!form.name.trim()) {
    errors.name = "Name is required";
    isValid = false;
  }

  if (!form.email.trim()) {
    errors.email = "Email is required";
    isValid = false;
  }

  if (!form.employee_id.trim()) {
    errors.employee_id = "Employee ID is required";
    isValid = false;
  }

  if (!editingUser.value && !form.password) {
    errors.password = "Password is required for new employees";
    isValid = false;
  }

  return isValid;
};

const changePage = (page) => {
  // Implementation for pagination
  fetchUsers();
};

onMounted(async () => {
  await fetchUsers();
  await usersStore.fetchDepartments();
});
</script>
