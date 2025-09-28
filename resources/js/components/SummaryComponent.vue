<template>
  <div class="max-w-4xl mx-auto">
    <div class="bg-white overflow-hidden shadow rounded-lg">
      <div class="px-4 py-5 sm:p-6">
        <div class="mb-6">
          <h2 class="text-2xl font-bold text-gray-900 mb-2">Import Summary</h2>
          <div class="flex items-center text-sm text-gray-600">
            <span>{{ summary.filename }}</span>
            <span class="mx-2">•</span>
            <span>{{ formatDate(summary.created_at) }}</span>
            <span
              class="ml-3 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
              :class="statusClass"
            >
              {{ summary.status }}
            </span>
          </div>
        </div>

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
          <div class="bg-gradient-to-r from-blue-400 to-blue-600 rounded-lg shadow">
            <div class="p-5">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <svg class="h-6 w-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-blue-100 truncate">Total Rows Read</dt>
                    <dd class="text-3xl font-bold text-white">{{ formatNumber(summary.total_rows) }}</dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-gradient-to-r from-green-400 to-green-600 rounded-lg shadow">
            <div class="p-5">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <svg class="h-6 w-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path
                      fill-rule="evenodd"
                      d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                      clip-rule="evenodd"
                    />
                  </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-green-100 truncate">Successfully Imported</dt>
                    <dd class="text-3xl font-bold text-white">{{ formatNumber(summary.imported_count) }}</dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-gradient-to-r from-yellow-400 to-yellow-600 rounded-lg shadow">
            <div class="p-5">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <svg class="h-6 w-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path
                      fill-rule="evenodd"
                      d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                      clip-rule="evenodd"
                    />
                  </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-yellow-100 truncate">Ignored (Duplicates)</dt>
                    <dd class="text-3xl font-bold text-white">{{ formatNumber(summary.duplicate_count) }}</dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-gradient-to-r from-red-400 to-red-600 rounded-lg shadow">
            <div class="p-5">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <svg class="h-6 w-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path
                      fill-rule="evenodd"
                      d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                      clip-rule="evenodd"
                    />
                  </svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-red-100 truncate">Validation Errors</dt>
                    <dd class="text-3xl font-bold text-white">{{ formatNumber(summary.error_count) }}</dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div v-if="summary.error_details" class="mt-8">
          <h3 class="text-lg font-medium text-gray-900 mb-3">Error Details</h3>
          <div class="bg-red-50 border border-red-200 rounded-md p-4">
            <p class="text-sm text-red-700">{{ summary.error_details }}</p>
          </div>
        </div>

        <div class="mt-8 flex space-x-4">
          <button
            type="button"
            class="bg-blue-600 text-white px-6 py-3 rounded-md text-sm font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
            @click="$emit('view-contacts')"
          >
            View Imported Contacts
          </button>
          <button
            type="button"
            class="bg-gray-600 text-white px-6 py-3 rounded-md text-sm font-medium hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500"
            @click="$emit('start-over')"
          >
            Import Another File
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import api from '../services/api.js';

export default {
  name: 'SummaryComponent',
  props: {
    importId: {
      type: Number,
      required: true,
    },
  },
  emits: ['view-contacts', 'start-over'],
  data() {
    return {
      summary: {
        filename: '',
        status: '',
        total_rows: 0,
        imported_count: 0,
        duplicate_count: 0,
        error_count: 0,
        error_details: null,
        created_at: null,
      },
      loading: true,
      error: null,
    };
  },
  computed: {
    statusClass() {
      switch (this.summary.status) {
        case 'completed':
          return 'bg-green-100 text-green-800';
        case 'failed':
          return 'bg-red-100 text-red-800';
        case 'processing':
          return 'bg-blue-100 text-blue-800';
        default:
          return 'bg-gray-100 text-gray-800';
      }
    },
  },
  async mounted() {
    await this.fetchSummary();
  },
  methods: {
    async fetchSummary() {
      try {
        const response = await api.getImportSummary(this.importId);
        this.summary = response.data;
      } catch (error) {
        console.error('Failed to fetch summary:', error);
        this.error = 'Failed to load import summary';
      } finally {
        this.loading = false;
      }
    },
    formatNumber(num) {
      return new Intl.NumberFormat().format(num);
    },
    formatDate(date) {
      if (!date) return '';
      return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
      });
    },
  },
};
</script>