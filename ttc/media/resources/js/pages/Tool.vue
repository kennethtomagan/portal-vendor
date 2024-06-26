<template>
  <div>

    <Head title="Media" />
    <Heading class="mb-6">Media</Heading>
    <div class="p-4 h-screen" style="background-color:#1e293b; overflow: auto;">
      <!-- Search Input -->
      <div class="mb-4">
        <input type="text" v-model="searchQuery" placeholder="Search media..."
          class="media-library-input px-2 py-1 border rounded w-full">
      </div>
      <!-- Media Grid -->
      <div class="media-grid">
        <div v-for="media in mediaItems" :key="media.id" class="image-container">
          <!-- Delete Button -->
          <button type="button" @click.stop="deleteMedia(media.id)"
            class="absolute top-0 right-0 p-1 bg-red-500 text-white rounded-bl-lg focus:outline-none hover:bg-red-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12">
              </path>
            </svg>
          </button>
          <img :src="media.original_url" alt="Media Thumbnail" class="cursor-pointer"
            @click="showFullMedia(media.original_url)">
          <div class="mt-2 w-full">
            <!-- File Name Input with Label -->
            <div>
              <label for="file_name" class="block text-sm font-medium text-gray-700">File Name</label>
              <input type="text" v-model="media.file_name" id="file_name"
                @blur="updateMedia(media.id, 'file_name', media.file_name)" placeholder="File Name"
                class="media-library-input mt-1 p-2 w-full border rounded-md focus:ring focus:ring-opacity-50 focus:ring-indigo-200 focus:border-indigo-300" />
            </div>

            <!-- Name Input with Label -->
            <div class="mt-4">
              <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
              <input type="text" v-model="media.name" id="name" @blur="updateMedia(media.id, 'name', media.name)"
                placeholder="Name"
                class="media-library-input mt-1 p-2 w-full border rounded-md focus:ring focus:ring-opacity-50 focus:ring-indigo-200 focus:border-indigo-300" />
            </div>
          </div>
        </div>
      </div>
      <div v-show="isModalOpen" class="fixed z-10 left-0 top-0 w-full h-full overflow-auto"
        style="background-color: rgba(0, 0, 0, 0.7); z-index: 51 !important;">
        <!-- Modal Content (The Image) -->
        <div id="modal" class="flex justify-center items-center h-full">
          <img :src="modalImageSrc" class="max-w-4xl w-full p-4" @click="closeFullMedia">
        </div>

        <!-- Close Button -->
        <button @click="closeFullMedia"
          class="absolute top-0 right-0 mt-4 mr-4 text-white text-3xl font-bold transition duration-300 hover:text-gray-400">
          &times;
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import { isProxy, toRaw } from 'vue';
import axios from 'axios';
import debounce from 'lodash.debounce'; // Import debounce from lodash

export default {
  props: ['media_items', 'model', 'modelId'],
  data() {
    return {
      selectedMediaIds: [],
      isModalOpen: false,
      modalImageSrc: '',
      searchQuery: '',
      mediaItems: this.media_items,
    }
  },
  methods: {
    showFullMedia(mediaId) {
      if (this.isSelected(mediaId)) {
        this.selectedMediaIds = this.selectedMediaIds.filter(id => id !== mediaId);
      } else {
        this.selectedMediaIds.push(mediaId);
      }
    },

    async updateMedia(mediaId, field, value) {
      try {
        await axios.put(`/ttc-media/${mediaId}`, {
          [field]: value
        });
      } catch (error) {
        console.error('Failed to update media:', error);
      }
    },

    async deleteMedia(mediaId) {
      try {
        const response = await axios.delete(`/ttc-media/${mediaId}`);

        // Handle the response or update the UI if needed
        if (response.status === 200)
          this.mediaItems = this.mediaItems.filter(media => media.id !== mediaId);

      } catch (error) {
        console.error('Failed to update media:', error);
      }
    },

    showFullMedia(src) {
      this.modalImageSrc = src; // Replace with the path to your image
      this.isModalOpen = true;
    },

    closeFullMedia() {
      this.isModalOpen = false;
    },

    outsideClick(e) {


      const modal = document.getElementById('modal');
      if (e.target == modal) {
        this.closeFullMedia(e);
      }
    },

    // Debounced method to get media
    getDebouncedMedia: debounce(function () {
      this.getMedia();
    }, 300), // 300ms delay

    async getMedia() {
      const response = await axios.get(`/ttc-media?search=${this.searchQuery}`);
      this.mediaItems = response.data;
    },
  },

  mounted() {
    document.addEventListener('click', this.outsideClick);
    this.getMedia();
  },
  beforeDestroy() {
    document.removeEventListener('click', this.outsideClick);
  },
  watch: {
    searchQuery(newValue) {
      if (newValue.length >= 3|| newValue.length === 0) {
        this.getDebouncedMedia();
      }
    },
  }
}
</script>