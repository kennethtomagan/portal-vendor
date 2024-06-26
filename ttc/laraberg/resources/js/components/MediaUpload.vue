<template>
    <div v-if="modalOpen" class="fixed inset-0 flex items-center justify-center z-50"
        style="background-color: rgba(0, 0, 0, 0.7);" id="myModal">

        <!-- Modal Content -->
        <div class="bg-white rounded-lg w-1/2">

            <!-- Modal Header -->
            <div class="border-b px-4 py-2 flex justify-between items-center">
                <h3 class="font-semibold text-lg">Media Library</h3>
                <button @click="handleToggle" class="text-black close-modal" aria-label="Close modal">&times;</button>
            </div>

            <!-- Modal Body -->
            <div class="p-4">
                <media-library-attachment name="media" multiple rule="mimes:jpg,png" @change="onChange"
                    @is-ready-to-submit-change="isReadyToSubmit = $event" />
            </div>

            <!-- Modal Footer -->
            <div class="border-t px-4 py-2 flex justify-end">
                <button @click="handleToggle" type="button"
                    class="px-4 py-2 bg-red-500 text-white hover:bg-red-400 rounded-md mr-2">Cancel</button>
                <button @click="handleSave" :disabled="!isReadyToSubmit" type="button"
                    class="px-4 py-2 bg-green-500 text-white hover:bg-green-400 rounded-md">Save</button>
            </div>
        </div>

    </div>
</template>
<script>
import { isProxy, toRaw } from 'vue'
import axios from 'axios';
import { MediaLibraryAttachment } from "@spatie/media-library-pro-vue3-attachment";

export default {
    props: ['modalOpen', 'model', 'modelId'],
    components: { MediaLibraryAttachment },
    data: () => {
        return {
            media: [],
            uuid: '',
            currentFile: null,
            isReadyToSubmit: true,
        }
    },
    methods: {
        handleToggle(e) {
            e.preventDefault()
            this.$emit('toggle-modal', !this.modalOpen);
        },
        
        onChange(files) {
            const rawFiles = Object.keys(files).map(key => {
                return isProxy ? toRaw(files[key]) : files[key]

            })

            this.media = rawFiles
        },

        async handleSave(e) {
            e.preventDefault()
            const formData = new FormData();
            formData.append('media', isProxy ? JSON.stringify(toRaw(this.media)) : this.media);
            formData.append('modelId', this.modelId);
            formData.append('model', this.model);

            try {
                const response = await axios.post(`/ttc-media`, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                });

                this.$emit('media-uploaded', response.data);
                this.$emit('toggle-modal', !this.modalOpen);

            } catch (error) {
                console.error('Error uploading files:', error);
            }

        },
    }
}
</script>
