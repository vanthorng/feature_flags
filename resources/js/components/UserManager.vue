<template>
  <div class="bg-white rounded-2xl shadow-sm p-6 space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <h2 class="text-lg font-semibold text-slate-800">User Management</h2>
        <p class="text-xs text-slate-400">
          Create users and toggle administrator access.
        </p>
      </div>
    </div>

    <!-- Create user form -->
    <div class="grid md:grid-cols-4 gap-3 items-end">
      <div class="md:col-span-1">
        <label class="text-xs font-medium text-slate-600">Name</label>
        <input
          v-model="form.name"
          class="mt-1 w-full border border-slate-300 rounded-lg px-3 py-2 text-sm"
          placeholder="John Doe"
        />
      </div>
      <div class="md:col-span-1">
        <label class="text-xs font-medium text-slate-600">Email</label>
        <input
          v-model="form.email"
          class="mt-1 w-full border border-slate-300 rounded-lg px-3 py-2 text-sm"
          placeholder="user@example.com"
        />
      </div>
      <div class="md:col-span-1">
        <label class="text-xs font-medium text-slate-600">Password</label>
        <input
          v-model="form.password"
          type="password"
          class="mt-1 w-full border border-slate-300 rounded-lg px-3 py-2 text-sm"
          placeholder="min 6 characters"
        />
      </div>
      <div class="md:col-span-1 flex gap-2 items-center justify-end">
        <label class="flex items-center gap-2 text-xs text-slate-600">
          <input type="checkbox" v-model="form.is_admin" />
          Admin
        </label>
        <button
          @click="createUser"
          class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-4 py-2 rounded-lg"
        >
          Add User
        </button>
      </div>
    </div>

    <p v-if="error" class="text-xs text-red-500">{{ error }}</p>
    <p v-if="success" class="text-xs text-emerald-600">{{ success }}</p>

    <!-- Users table -->
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead>
        <tr class="text-left text-xs text-slate-500 border-b">
          <th class="py-2 pr-4">ID</th>
          <th class="py-2 pr-4">Name</th>
          <th class="py-2 pr-4">Email</th>
          <th class="py-2 pr-4">Role</th>
          <th class="py-2 pr-4 text-right">Actions</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="user in users" :key="user.id" class="border-b last:border-0">
          <td class="py-2 pr-4 text-slate-500">#{{ user.id }}</td>
          <td class="py-2 pr-4">{{ user.name }}</td>
          <td class="py-2 pr-4 text-slate-600">{{ user.email }}</td>
          <td class="py-2 pr-4">
            <span
              class="px-2 py-0.5 rounded-full text-xs font-semibold"
              :class="user.is_admin
                ? 'bg-emerald-100 text-emerald-700'
                : 'bg-slate-100 text-slate-600'"
            >
              {{ user.is_admin ? "Admin" : "User" }}
            </span>
          </td>
          <td class="py-2 pr-4 text-right">
            <button
              @click="toggleAdmin(user)"
              class="text-xs font-semibold px-3 py-1 rounded-full border
                     border-slate-300 hover:border-indigo-500 hover:text-indigo-600"
            >
              {{ user.is_admin ? "Remove admin" : "Make admin" }}
            </button>
          </td>
        </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      users: [],
      form: {
        name: "",
        email: "",
        password: "",
        is_admin: false,
      },
      error: "",
      success: "",
    };
  },

  async created() {
    await this.loadUsers();
  },

  methods: {
    async loadUsers() {
      const { data } = await this.$api.get("/api/admin/users");
      this.users = data;
    },

    async createUser() {
      this.error = "";
      this.success = "";
      try {
        await this.$api.post("/api/admin/users", this.form);
        this.form = { name: "", email: "", password: "", is_admin: false };
        this.success = "User created successfully.";
        await this.loadUsers();
      } catch (e) {
        this.error =
          e.response?.data?.message ||
          (e.response?.data?.errors &&
            Object.values(e.response.data.errors)[0][0]) ||
          "Failed to create user.";
      }
    },

    async toggleAdmin(user) {
      await this.$api.post(`/api/admin/users/${user.id}/toggle-admin`);
      await this.loadUsers();
    },
  },
};
</script>
