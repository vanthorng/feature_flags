<template>
  <div class="mt-8 bg-indigo-50 p-6 rounded-xl border border-indigo-200 space-y-4">
    <h2 class="text-xl font-bold text-indigo-700">Admin: Feature Manager</h2>

    <!-- New Feature -->
    <div class="space-y-2">
      <h3 class="font-semibold text-sm text-slate-800">New Feature</h3>
      <div class="flex gap-2">
        <input
          v-model="newFeature"
          placeholder="Feature name (e.g. print_invoice)"
          class="flex-1 border rounded px-3 py-2 text-sm"
        />
        <button
          class="bg-indigo-600 text-white px-4 py-2 rounded text-sm font-semibold hover:bg-indigo-700"
          @click="createFeature"
        >
          Add
        </button>
      </div>
    </div>

    <!-- Existing Features -->
    <div>
      <h3 class="font-semibold text-sm text-slate-800 border-b pb-1">Existing Features</h3>
      <ul class="mt-2 text-sm text-slate-700">
        <li v-for="name in features" :key="name">• {{ name }}</li>
        <li v-if="!features.length" class="text-slate-400">
          No features yet. Create one above.
        </li>
      </ul>
    </div>

    <!-- Assign Feature -->
    <div class="space-y-2">
      <h3 class="font-semibold text-sm text-slate-800">Assign Feature to User</h3>

      <input
        v-model="userId"
        placeholder="User ID"
        class="w-full border rounded px-3 py-2 text-sm"
      />

      <select
        v-model="selectedFeature"
        class="w-full border rounded px-3 py-2 text-sm"
      >
        <option disabled value="">Select feature</option>
        <option v-for="name in features" :key="name" :value="name">
          {{ name }}
        </option>
      </select>

      <div class="flex gap-2 mt-2">
        <button
          class="flex-1 bg-emerald-600 text-white py-2 rounded text-sm font-semibold hover:bg-emerald-700"
          @click="activate"
        >
          Activate
        </button>
        <button
          class="flex-1 bg-rose-600 text-white py-2 rounded text-sm font-semibold hover:bg-rose-700"
          @click="deactivate"
        >
          Deactivate
        </button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "FeatureManager",

  data() {
    return {
      features: [],        // ["print_invoice","new-dashboard",...]
      newFeature: "",
      userId: "",
      selectedFeature: "",
    };
  },

  async created() {
    await this.loadFeatures();
  },

  methods: {
    async loadFeatures() {
      const { data } = await this.$api.get("/api/features");
      this.features = data;
    },

    async createFeature() {
      if (!this.newFeature) return;
      await this.$api.post("/api/features/create", { name: this.newFeature });
      this.newFeature = "";
      await this.loadFeatures();
    },

    async activate() {
      if (!this.userId || !this.selectedFeature) return;
      await this.$api.post("/api/features/activate", {
        feature: this.selectedFeature,
        user_id: this.userId,
      });
      alert(
        `Feature "${this.selectedFeature}" activated for user ${this.userId}`
      );
    },

    async deactivate() {
      if (!this.userId || !this.selectedFeature) return;
      await this.$api.post("/api/features/deactivate", {
        feature: this.selectedFeature,
        user_id: this.userId,
      });
      alert(
        `Feature "${this.selectedFeature}" deactivated for user ${this.userId}`
      );
    },
  },
};
</script>
