<template>
    <div v-if="modalOpen" id="modal" class="fixed inset-0 flex items-center justify-center z-50"
        style="background-color: rgba(0, 0, 0, 0.7); z-index: 51 !important;">

        <!-- Modal Content -->
        <div style="background-color: rgb(30, 41, 59);" class="rounded-lg w-1/2">

            <!-- Modal Header -->
            <div class="border-b px-4 py-2 flex justify-between items-right">
                <h3 class="font-semibold text-lg">Media Library</h3>
                <button @click="handleToggle" class="text-black close-modal" aria-label="Close modal">&times;</button>
            </div>

            <!-- Modal Body -->
            <div class="p-4" style="height: 500px; overflow: auto;">
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
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12">
                                </path>
                            </svg>
                        </button>
                        <img :src="media.original_url" alt="Media Thumbnail" :class="{ 'selected': isSelected(media.id) }"
                            @click="toggleSelection(media.id)">
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
                                <input type="text" v-model="media.name" id="name"
                                    @blur="updateMedia(media.id, 'name', media.name)" placeholder="Name"
                                    class="media-library-input mt-1 p-2 w-full border rounded-md focus:ring focus:ring-opacity-50 focus:ring-indigo-200 focus:border-indigo-300" />
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Modal Footer -->
            <div class="border-t px-4 py-2 flex justify-end">
                <button @click="handleToggle" type="button"
                    class="px-4 py-2 bg-red-500 text-white hover:bg-red-400 rounded-md mr-2">Cancel</button>
                <button @click="handleSave" type="button"
                    class="px-4 py-2 bg-green-500 text-white hover:bg-green-400 rounded-md">Save</button>
            </div>
        </div>

    </div>
</template>
  
<script>
import { isProxy, toRaw } from 'vue';
import axios from 'axios';
import debounce from 'lodash.debounce'; // Import debounce from lodash

export default {
    props: ['modalOpen', 'model', 'modelId'],
    data() {
        return {
            selectedMediaIds: [],
            mediaItems: [],
            searchQuery: ''
        };
    },
    methods: {
        handleToggle(e) {
            e.preventDefault()
            this.$emit('toggle-modal', !this.modalOpen);
        },
        isSelected(mediaId) {
            return this.selectedMediaIds.includes(mediaId);
        },
        toggleSelection(mediaId) {
            if (this.isSelected(mediaId)) {
                this.selectedMediaIds = this.selectedMediaIds.filter(id => id !== mediaId);
            } else {
                this.selectedMediaIds.push(mediaId);
            }
        },
        handleSave() {
            const selectedMedia = this.mediaItems.filter(media => this.isSelected(media.id));
            this.$emit('media-uploaded', isProxy ? toRaw(selectedMedia) : selectedMedia);
            this.$emit('toggle-modal', !this.modalOpen);
        },

        // Debounced method to get media
        getDebouncedMedia: debounce(function () {
            this.getMedia();
        }, 300), // 300ms delay

        async getMedia() {
            const response = await axios.get(`/ttc-media?model=${this.model}&modelId=${this.modelId}&search=${this.searchQuery}`);
            this.mediaItems = response.data;
        },

        async updateMedia(mediaId, field, value) {

            try {
                const response = await axios.put(`/ttc-media/${mediaId}`, {
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

        outsideClick(e) {
            const modal = document.getElementById('modal');
            if (e.target == modal) {
                this.handleToggle(e);
            }
        },
    },
    mounted() {
        document.addEventListener('click', this.outsideClick);
    },
    beforeDestroy() {
        document.removeEventListener('click', this.outsideClick);
        this.selectedMediaIds = []
    },
    watch: {
        modalOpen(newValue, oldVal) {
            this.getMedia();
        },
        searchQuery(newValue) {
            if (newValue.length >= 3 || newValue.length === 0) {
                this.getDebouncedMedia();
            }
        },
    }
}
</script>