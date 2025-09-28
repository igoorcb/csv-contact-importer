<template>
  <div id="app" class="min-h-screen bg-gray-50">
    <header class="bg-white shadow">
      <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-900">CSV Contact Importer</h1>
      </div>
    </header>

    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      <div class="px-4 py-6 sm:px-0">
        <upload-component
          v-if="currentView === 'upload'"
          :key="uploadKey"
          @upload-complete="handleUploadComplete"
        />

        <progress-component
          v-else-if="currentView === 'progress'"
          :import-id="currentImportId"
          :filename="currentFilename"
          @view-summary="showSummary"
          @view-contacts="showContacts"
          @start-over="startOver"
        />

        <summary-component
          v-else-if="currentView === 'summary'"
          :import-id="currentImportId"
          @view-contacts="showContacts"
          @start-over="startOver"
        />

        <contacts-component
          v-else-if="currentView === 'contacts'"
          @start-over="startOver"
        />
      </div>
    </main>
  </div>
</template>

<script>
import UploadComponent from './UploadComponent.vue';
import ProgressComponent from './ProgressComponent.vue';
import SummaryComponent from './SummaryComponent.vue';
import ContactsComponent from './ContactsComponent.vue';

export default {
  name: 'App',
  components: {
    UploadComponent,
    ProgressComponent,
    SummaryComponent,
    ContactsComponent,
  },
  data() {
    return {
      currentView: 'upload',
      currentImportId: null,
      currentFilename: '',
      uploadKey: 0,
    };
  },
  methods: {
    handleUploadComplete(data) {
      this.currentImportId = data.import_id;
      this.currentFilename = data.filename || 'Unknown file';
      this.currentView = 'progress';
    },
    showSummary() {
      this.currentView = 'summary';
    },
    showContacts() {
      this.currentView = 'contacts';
    },
    startOver() {
      this.currentView = 'upload';
      this.currentImportId = null;
      this.currentFilename = '';
      this.uploadKey += 1; // Force component recreation
    },
  },
};
</script>
