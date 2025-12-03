<template>
  <div class="min-h-screen bg-slate-100 flex flex-col">
    <!-- Top bar -->
    <header class="bg-white shadow-sm">
      <div class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="h-9 w-9 bg-indigo-600 text-white flex items-center justify-center text-lg font-bold rounded-lg">
            FF
          </div>
          <div>
            <h1 class="text-xl font-bold text-slate-800">Feature Flags System</h1>
            <p class="text-xs text-slate-500">Laravel · Vue 3 · Sanctum · Pennant</p>
          </div>
        </div>

        <div v-if="user" class="flex items-center gap-3">
          <div class="text-right leading-tight">
            <p class="text-sm font-semibold text-slate-800">{{ user.name }}</p>
            <p class="text-xs text-slate-500">{{ user.is_admin ? "Administrator" : "User" }}</p>
          </div>
          <button
            @click="logout"
            class="text-xs font-semibold text-red-500 hover:text-red-600"
          >
            Logout
          </button>
        </div>
      </div>
    </header>

    <!-- MAIN -->
    <main class="flex-1">
      <div class="max-w-6xl mx-auto px-6 py-8">

        <!-- LOGIN -->
        <div v-if="!user" class="max-w-md mx-auto">
          <div class="bg-white shadow-xl rounded-2xl p-8 space-y-6">
            <h2 class="text-2xl font-bold text-center text-slate-800">Login</h2>
            <p class="text-xs text-slate-500 text-center">
              Sign in to manage feature flags and invoices.
            </p>

            <div class="space-y-1">
              <label class="text-sm font-medium text-slate-700">Email</label>
              <input
                v-model="form.email"
                type="email"
                placeholder="admin@example.com"
                class="w-full border border-slate-300 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
              />
            </div>

            <div class="space-y-1">
              <label class="text-sm font-medium text-slate-700">Password</label>
              <input
                v-model="form.password"
                type="password"
                placeholder="••••••••"
                class="w-full border border-slate-300 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
              />
            </div>

            <p v-if="error" class="text-red-500 text-sm font-medium">{{ error }}</p>

            <button
              @click="login"
              class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 rounded-lg text-sm"
            >
              Login
            </button>

            <p class="text-center text-xs text-slate-400">
              Default admin: <b>admin@example.com / password</b>
            </p>
          </div>
        </div>

        <!-- AUTHENTICATED -->
        <div v-else class="grid md:grid-cols-3 gap-6">
          <!-- LEFT COLUMN: user info, features, dashboard, invoices -->
          <section class="md:col-span-1 space-y-6">
            <div class="bg-white rounded-2xl shadow-md p-6">
              <h2 class="text-lg font-semibold text-slate-800">Account Info</h2>
              <p class="text-sm text-slate-500">{{ user.email }}</p>
              <span
                class="inline-flex mt-2 px-2 py-1 rounded-full text-xs font-semibold"
                :class="user.is_admin ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-600'"
              >
                {{ user.is_admin ? "Administrator" : "User" }}
              </span>
            </div>

            <div class="bg-white rounded-2xl shadow-md p-6">
              <h2 class="text-lg font-semibold text-slate-800">Feature Flags</h2>
              <div v-if="Object.keys(features).length" class="mt-3 space-y-2">
                <div
                  v-for="(value, key) in features"
                  :key="key"
                  class="flex justify-between border rounded-lg px-3 py-2 text-sm"
                >
                  <span class="text-slate-700 font-medium">{{ key }}</span>
                  <span
                    :class="value ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700'"
                    class="px-2 py-0.5 rounded-full text-xs font-semibold"
                  >
                    {{ value ? "ON" : "OFF" }}
                  </span>
                </div>
              </div>
              <p v-else class="text-sm text-slate-400">No flags assigned.</p>
            </div>

            <div class="bg-white rounded-2xl shadow-md p-6 space-y-3">
              <h2 class="text-lg font-semibold text-slate-800">Dashboard</h2>

              <template v-if="hasFeature('new-dashboard')">
                <button
                  @click="loadNewDashboard"
                  class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold px-4 py-2 rounded-lg"
                >
                  Load Dashboard Data
                </button>

                <pre
                  v-if="dashboardData"
                  class="bg-black text-emerald-300 text-xs p-3 rounded-lg overflow-x-auto"
                >
{{ dashboardData }}
                </pre>
              </template>

              <p v-else class="text-sm text-slate-400 italic">
                new-dashboard is not enabled for you.
              </p>
            </div>

            <!-- USER INVOICES: can-print controlled by feature -->
            <UserInvoices :can-print="hasFeature('print_invoice')" />
          </section>

          <!-- RIGHT COLUMN: admin tools -->
          <section class="md:col-span-2 space-y-6" v-if="user.is_admin">
            <FeatureManager />
            <InvoiceManager />
            <UserManager />
          </section>

          <section
            v-else
            class="md:col-span-2 bg-white rounded-2xl shadow p-6 text-center text-slate-400"
          >
            Administrator access required to manage system settings.
          </section>
        </div>
      </div>
    </main>
  </div>
</template>

<script>
import FeatureManager from "./components/FeatureManager.vue";
import UserManager from "./components/UserManager.vue";      // if you already built it
import InvoiceManager from "./components/InvoiceManager.vue"; // if you already built it
import UserInvoices from "./components/UserInvoices.vue";

export default {
  components: { FeatureManager, UserManager, InvoiceManager, UserInvoices },

  data() {
    return {
      user: null,
      features: {},
      error: null,
      form: { email: "", password: "" },
      dashboardData: null,
    };
  },

  async created() {
    await this.safeFetchUser();
  },

  methods: {
    async safeFetchUser() {
      try {
        const { data } = await this.$api.get("/api/user");
        this.user = data.user;
        this.features = data.features || {};
      } catch (e) {
        if (e.response?.status === 401) {
          this.user = null;
          this.features = {};
        }
      }
    },

    async login() {
      this.error = null;
      try {
        await this.$api.get("/sanctum/csrf-cookie");
        await this.$api.post("/api/login", this.form);
        await this.safeFetchUser();
      } catch {
        this.error = "Invalid login credentials.";
      }
    },

    async logout() {
      await this.$api.post("/api/logout");
      this.user = null;
      this.features = {};
      this.form.password = "";
      this.dashboardData = null;
    },

    hasFeature(name) {
      return !!this.features[name];
    },

    async loadNewDashboard() {
      this.dashboardData = "Loading...";
      try {
        const { data } = await this.$api.get("/api/new-dashboard-data");
        this.dashboardData = JSON.stringify(data, null, 2);
      } catch {
        this.dashboardData = "Failed to load dashboard information.";
      }
    },
  },
};
</script>
