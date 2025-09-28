<template>
  <div class="max-w-2xl mx-auto">
    <div class="bg-white overflow-hidden shadow rounded-lg">
      <div class="px-4 py-5 sm:p-6">
        <div class="mb-6">
          <h2 class="text-lg font-medium text-gray-900 mb-2">Processing Import</h2>
          <p class="text-sm text-gray-600">{{ filename }}</p>
        </div>

        <div class="mb-6">
          <div class="flex justify-between text-sm font-medium text-gray-900 mb-1">
            <span>Progress</span>
            <span>{{ progressPercentage }}%</span>
          </div>
          <div class="w-full bg-gray-200 rounded-full h-2">
            <div
              class="h-2 rounded-full transition-all duration-300"
              :class="progressBarClass"
              :style="{ width: progressPercentage + '%' }"
            ></div>
          </div>
        </div>

        <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
          <div class="bg-blue-50 px-4 py-3 rounded-lg">
            <div class="text-sm font-medium text-blue-900">Total Rows</div>
            <div class="text-2xl font-bold text-blue-600">{{ formatNumber(progress.total_rows) }}</div>
          </div>
          <div class="bg-green-50 px-4 py-3 rounded-lg">
            <div class="text-sm font-medium text-green-900">Imported</div>
            <div class="text-2xl font-bold text-green-600">{{ formatNumber(progress.imported_count) }}</div>
          </div>
          <div class="bg-yellow-50 px-4 py-3 rounded-lg">
            <div class="text-sm font-medium text-yellow-900">Duplicates</div>
            <div class="text-2xl font-bold text-yellow-600">{{ formatNumber(progress.duplicate_count) }}</div>
          </div>
          <div class="bg-red-50 px-4 py-3 rounded-lg">
            <div class="text-sm font-medium text-red-900">Errors</div>
            <div class="text-2xl font-bold text-red-600">{{ formatNumber(progress.error_count) }}</div>
          </div>
        </div>

        <div v-if="status === 'completed'" class="mt-6 flex space-x-3">
          <button
            type="button"
            class="flex-1 bg-green-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500"
            @click="$emit('view-summary')"
          >
            View Summary
          </button>
          <button
            type="button"
            class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
            @click="$emit('view-contacts')"
          >
            View Contacts
          </button>
        </div>

        <div v-if="status === 'failed'" class="mt-6">
          <div class="bg-red-50 border border-red-200 rounded-md p-4">
            <div class="flex">
              <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                  <path
                    fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                    clip-rule="evenodd"
                  />
                </svg>
              </div>
              <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">Processing Failed</h3>
                <p class="mt-1 text-sm text-red-700">{{ errorMessage }}</p>
              </div>
            </div>
          </div>
          <button
            type="button"
            class="mt-4 w-full bg-gray-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500"
            @click="$emit('start-over')"
          >
            Start Over
          </button>
        </div>

        <div v-if="status === 'processing'" class="mt-6 flex items-center text-sm text-gray-600">
          <svg
            class="animate-spin -ml-1 mr-3 h-5 w-5 text-blue-500"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
          >
            <circle
              class="opacity-25"
              cx="12"
              cy="12"
              r="10"
              stroke="currentColor"
              stroke-width="4"
            ></circle>
            <path
              class="opacity-75"
              fill="currentColor"
              d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
            ></path>
          </svg>
          Processing your CSV file...
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import api from '../services/api.js';

export default {
  name: 'ProgressComponent',
  props: {
    importId: {
      type: Number,
      required: true,
    },
    filename: {
      type: String,
      default: 'Unknown file',
    },
  },
  emits: ['view-summary', 'view-contacts', 'start-over'],
  data() {
    return {
      status: 'pending',
      progress: {
        total_rows: 0,
        imported_count: 0,
        duplicate_count: 0,
        error_count: 0,
      },
      errorMessage: '',
      pollInterval: null,
    };
  },
  computed: {
    progressPercentage() {
      if (this.progress.total_rows === 0) return 0;
      const processed = this.progress.imported_count + this.progress.duplicate_count + this.progress.error_count;
      return Math.round((processed / this.progress.total_rows) * 100);
    },
    progressBarClass() {
      if (this.status === 'failed') return 'bg-red-500';
      if (this.status === 'completed') return 'bg-green-500';
      return 'bg-blue-500';
    },
  },
  mounted() {
    this.startPolling();
  },
  unmounted() {
    this.stopPolling();
  },
  methods: {
    async fetchStatus() {
      try {
        const response = await api.getImportStatus(this.importId);
        this.status = response.data.status;
        this.progress = response.data.progress;

        if (this.status === 'completed' || this.status === 'failed') {
          this.stopPolling();

          if (this.status === 'failed') {
            const summaryResponse = await api.getImportSummary(this.importId);
            this.errorMessage = summaryResponse.data.error_details || 'Unknown error occurred';
          }
        }
      } catch (error) {
        console.error('Failed to fetch status:', error);
        this.errorMessage = 'Failed to get import status';
        this.status = 'failed';
        this.stopPolling();
      }
    },
    startPolling() {
      this.fetchStatus();
      this.pollInterval = setInterval(() => {
        if (this.status === 'pending' || this.status === 'processing') {
          this.fetchStatus();
        }
      }, 2000);
    },
    stopPolling() {
      if (this.pollInterval) {
        clearInterval(this.pollInterval);
        this.pollInterval = null;
      }
    },
    formatNumber(num) {
      return new Intl.NumberFormat().format(num);
    },
  },
};
</script>