<template>
  <div class="max-w-7xl mx-auto">
    <div class="bg-white shadow rounded-lg">
      <div class="px-4 py-5 sm:p-6">
        <div class="mb-6 flex justify-between items-center">
          <div>
            <h2 class="text-2xl font-bold text-gray-900">Imported Contacts</h2>
            <p class="mt-1 text-sm text-gray-600" v-if="meta.total">
              Showing {{ meta.total }} contacts
            </p>
          </div>
          <button
            type="button"
            class="bg-gray-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500"
            @click="$emit('start-over')"
          >
            Import Another File
          </button>
        </div>

        <div v-if="loading" class="flex justify-center items-center py-12">
          <svg
            class="animate-spin h-8 w-8 text-blue-500"
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
        </div>

        <div v-else-if="error" class="text-center py-12">
          <div class="bg-red-50 border border-red-200 rounded-md p-4">
            <p class="text-sm text-red-600">{{ error }}</p>
          </div>
        </div>

        <div v-else-if="contacts.length === 0" class="text-center py-12">
          <svg
            class="mx-auto h-12 w-12 text-gray-400"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"
            />
          </svg>
          <h3 class="mt-2 text-sm font-medium text-gray-900">No contacts found</h3>
          <p class="mt-1 text-sm text-gray-500">No contacts have been imported yet.</p>
        </div>

        <div v-else>
          <div class="hidden sm:block">
            <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
              <table class="min-w-full divide-y divide-gray-300">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Contact
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Email
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Phone
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Birthdate
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Imported
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="contact in contacts" :key="contact.id" class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10">
                          <img
                            class="h-10 w-10 rounded-full"
                            :src="contact.gravatar_url"
                            :alt="contact.name"
                          />
                        </div>
                        <div class="ml-4">
                          <div class="text-sm font-medium text-gray-900">
                            {{ contact.name }}
                          </div>
                        </div>
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm text-gray-900">{{ contact.email }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm text-gray-900">
                        {{ contact.phone || '-' }}
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm text-gray-900">
                        {{ formatDate(contact.birthdate) }}
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      {{ formatDateTime(contact.created_at) }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <div class="sm:hidden">
            <div class="space-y-4">
              <div
                v-for="contact in contacts"
                :key="contact.id"
                class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm"
              >
                <div class="flex items-center space-x-3 mb-3">
                  <img
                    class="h-12 w-12 rounded-full"
                    :src="contact.gravatar_url"
                    :alt="contact.name"
                  />
                  <div>
                    <h3 class="text-lg font-medium text-gray-900">{{ contact.name }}</h3>
                    <p class="text-sm text-gray-600">{{ contact.email }}</p>
                  </div>
                </div>
                <div class="space-y-1">
                  <p v-if="contact.phone" class="text-sm text-gray-600">
                    <span class="font-medium">Phone:</span> {{ contact.phone }}
                  </p>
                  <p v-if="contact.birthdate" class="text-sm text-gray-600">
                    <span class="font-medium">Birthdate:</span> {{ formatDate(contact.birthdate) }}
                  </p>
                  <p class="text-sm text-gray-500">
                    <span class="font-medium">Imported:</span> {{ formatDateTime(contact.created_at) }}
                  </p>
                </div>
              </div>
            </div>
          </div>

          <div v-if="meta.last_page > 1" class="mt-6 flex items-center justify-between">
            <div class="flex-1 flex justify-between sm:hidden">
              <button
                :disabled="meta.current_page === 1"
                @click="changePage(meta.current_page - 1)"
                class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                Previous
              </button>
              <button
                :disabled="meta.current_page === meta.last_page"
                @click="changePage(meta.current_page + 1)"
                class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
              >
                Next
              </button>
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
              <div>
                <p class="text-sm text-gray-700">
                  Showing
                  <span class="font-medium">{{ (meta.current_page - 1) * meta.per_page + 1 }}</span>
                  to
                  <span class="font-medium">{{ Math.min(meta.current_page * meta.per_page, meta.total) }}</span>
                  of
                  <span class="font-medium">{{ meta.total }}</span>
                  results
                </p>
              </div>
              <div>
                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                  <button
                    :disabled="meta.current_page === 1"
                    @click="changePage(meta.current_page - 1)"
                    class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    Previous
                  </button>
                  <button
                    v-for="page in visiblePages"
                    :key="page"
                    @click="changePage(page)"
                    class="relative inline-flex items-center px-4 py-2 border text-sm font-medium"
                    :class="page === meta.current_page
                      ? 'z-10 bg-blue-50 border-blue-500 text-blue-600'
                      : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'"
                  >
                    {{ page }}
                  </button>
                  <button
                    :disabled="meta.current_page === meta.last_page"
                    @click="changePage(meta.current_page + 1)"
                    class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    Next
                  </button>
                </nav>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import api from '../services/api.js';

export default {
  name: 'ContactsComponent',
  emits: ['start-over'],
  data() {
    return {
      contacts: [],
      meta: {
        current_page: 1,
        per_page: 10,
        total: 0,
        last_page: 1,
      },
      loading: false,
      error: null,
    };
  },
  computed: {
    visiblePages() {
      const pages = [];
      const current = this.meta.current_page;
      const last = this.meta.last_page;

      let start = Math.max(1, current - 2);
      let end = Math.min(last, current + 2);

      if (end - start < 4) {
        if (start === 1) {
          end = Math.min(last, start + 4);
        } else {
          start = Math.max(1, end - 4);
        }
      }

      for (let i = start; i <= end; i++) {
        pages.push(i);
      }

      return pages;
    },
  },
  async mounted() {
    await this.fetchContacts();
  },
  methods: {
    async fetchContacts(page = 1) {
      this.loading = true;
      this.error = null;

      try {
        const response = await api.getContacts(page, 15);
        this.contacts = response.data.data;
        this.meta = response.data.meta;
      } catch (error) {
        console.error('Failed to fetch contacts:', error);
        this.error = 'Failed to load contacts';
      } finally {
        this.loading = false;
      }
    },
    async changePage(page) {
      if (page >= 1 && page <= this.meta.last_page && page !== this.meta.current_page) {
        await this.fetchContacts(page);
      }
    },
    formatDate(date) {
      if (!date) return '-';
      return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
      });
    },
    formatDateTime(date) {
      if (!date) return '';
      return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
      });
    },
  },
};
</script>