<template>
  <div class="bg-white rounded-2xl shadow-sm p-6 space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <h2 class="text-lg font-semibold text-slate-800">My Invoices</h2>
        <p class="text-xs text-slate-400">View and print your billing history.</p>
      </div>
    </div>

    <!-- List -->
    <div class="overflow-x-auto">
      <table class="min-w-full text-xs">
        <thead>
        <tr class="text-left text-slate-500 border-b">
          <th class="py-2 pr-4">Number</th>
          <th class="py-2 pr-4">Date</th>
          <th class="py-2 pr-4">Total</th>
          <th class="py-2 pr-4 text-right">Actions</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="inv in invoices" :key="inv.id" class="border-b last:border-0">
          <td class="py-2 pr-4 font-mono">{{ inv.number }}</td>
          <td class="py-2 pr-4">{{ inv.date }}</td>
          <td class="py-2 pr-4">{{ Number(inv.total).toFixed(2) }}</td>
          <td class="py-2 pr-4 text-right">
            <button
              class="text-xs text-indigo-600 hover:text-indigo-700 font-semibold"
              @click="selectInvoice(inv)"
            >
              View
            </button>
          </td>
        </tr>
        </tbody>
      </table>
    </div>

    <!-- Selected invoice -->
    <div v-if="selected" class="border border-slate-200 rounded-xl p-6">
      <div ref="printArea" class="space-y-4">
        <div class="flex justify-between items-start">
          <div>
            <h3 class="text-xl font-bold text-slate-800">
              Invoice {{ selected.number }}
            </h3>
            <p class="text-xs text-slate-500">Date: {{ selected.date }}</p>
            <p class="text-xs text-slate-500" v-if="selected.due_date">
              Due: {{ selected.due_date }}
            </p>
          </div>
          <div class="text-right text-xs text-slate-500">
            <p>Your Company Name</p>
            <p>Address line 1</p>
            <p>support@example.com</p>
          </div>
        </div>

        <table class="w-full text-xs mt-4 border-t border-b border-slate-200">
          <thead>
          <tr class="text-left text-slate-500">
            <th class="py-2 pr-2">Description</th>
            <th class="py-2 pr-2 text-right">Qty</th>
            <th class="py-2 pr-2 text-right">Unit</th>
            <th class="py-2 pl-2 text-right">Total</th>
          </tr>
          </thead>
          <tbody>
          <tr
            v-for="item in selected.items"
            :key="item.id"
            class="border-t border-slate-100"
          >
            <td class="py-1 pr-2">{{ item.description }}</td>
            <td class="py-1 pr-2 text-right">{{ item.quantity }}</td>
            <td class="py-1 pr-2 text-right">
              {{ Number(item.unit_price).toFixed(2) }}
            </td>
            <td class="py-1 pl-2 text-right">
              {{ Number(item.total).toFixed(2) }}
            </td>
          </tr>
          </tbody>
        </table>

        <div class="flex justify-end mt-2 text-xs">
          <div class="w-48 space-y-1">
            <div class="flex justify-between">
              <span class="text-slate-500">Subtotal</span>
              <span>{{ Number(selected.subtotal).toFixed(2) }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-slate-500">Tax</span>
              <span>{{ Number(selected.tax).toFixed(2) }}</span>
            </div>
            <div class="flex justify-between font-semibold">
              <span>Total</span>
              <span>{{ Number(selected.total).toFixed(2) }}</span>
            </div>
          </div>
        </div>

        <p v-if="selected.notes" class="text-xs text-slate-500 mt-2">
          Notes: {{ selected.notes }}
        </p>
      </div>

      <div class="mt-4 flex justify-end">
        <button
          v-if="canPrint"
          class="bg-slate-800 text-white text-xs font-semibold px-4 py-2 rounded-lg hover:bg-slate-900"
          @click="printInvoice"
        >
          Print invoice
        </button>
        <p v-else class="text-xs text-slate-400 italic">
          Printing disabled. Ask admin to enable <code>print_invoice</code>.
        </p>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    canPrint: {
      type: Boolean,
      default: false,
    },
  },

  data() {
    return {
      invoices: [],
      selected: null,
    };
  },

  async created() {
    await this.loadInvoices();
  },

  methods: {
    async loadInvoices() {
      const { data } = await this.$api.get("/api/invoices");
      this.invoices = data;
    },
    selectInvoice(inv) {
      this.selected = inv;
    },
    printInvoice() {
      window.print();
    },
  },
};
</script>

<style scoped>
@media print {
  :global(body) {
    background: white !important;
  }
}
</style>
