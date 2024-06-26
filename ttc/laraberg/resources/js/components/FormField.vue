<template>
  <DefaultField :field="field" :errors="errors" :show-help-text="showHelpText">
    <template #field>

      <input :id="field.attribute" :class="errorClasses" :placeholder="field.name" v-model="field.value"
        @change="handleChange" type="hidden">

      <MediaUpload :model="model" :modelId="modelId" :modal-open="mediaUploadModalOpen"
        @toggle-modal="toggleMediaUploadModal" @media-uploaded="handleMediaUploaded" />
      <media-library :modal-open="mediaLibraryModalOpen" @toggle-modal="toggleMediaLibraryModal"
        @media-uploaded="handleMediaUploaded" :model="model" :modelId="modelId" />
    </template>
  </DefaultField>
</template>

<script>
import { isProxy, toRaw } from "vue";

import { FormField, HandlesValidationErrors } from "laravel-nova";
import MediaUpload from "./MediaUpload.vue";
import MediaLibrary from './MediaLibrary.vue';
import axios from 'axios';
import { mediaAndText } from "@wordpress/icons";
export default {
  mixins: [FormField, HandlesValidationErrors],
  props: ["resourceName", "resourceId", "field"],
  components: { MediaUpload, MediaLibrary },
  data: () => ({
    vm: this,
    file: null,
    media: null,
    value: null,
    model: null,
    modelId: null,
    mediaItems: [],
    mediaUploadModalOpen: false,
    mediaLibraryModalOpen: false,
    currentUser: null,
    originalBlocks: {},
    blocksArray: ['core/image', 'core/gallery'],
    currentFileInput: null,
    onFileChange: () => { },
  }),
  computed: {
    id() {
      return this.getId();
    },
  },
  methods: {
    /*
     * Set the initial, internal value for the field.
     */
    setInitialValue() {

      const user = toRaw(this.currentUser);
      const field = toRaw(this.field);


      if (!user) return;

      this.model = this.resourceId ? field.resourceModel : user.model;
      this.modelId = this.resourceId ? this.resourceId : user.id;

      Laraberg.init(this.getId(), this.getSettings());
      this.initializeClickEventListener();
      this.modifyCoreBlocks();

    },
    /**
     * Fill the given FormData object with the field's internal value.
    */
    fill(formData) {
      formData.append(this.field.attribute, this.value || "");
      // formData.append(this.field.attribute + 'DraftId', this.draftId)
    },
    toggleMediaUploadModal(newValue) {
      this.mediaUploadModalOpen = newValue;
    },
    toggleMediaLibraryModal(newValue) {
      this.mediaLibraryModalOpen = newValue;
    },
    getId() {
      return this.field.attribute;
    },
    getSettings() {
      const settings = {
        sidebar: false,
        mediaUpload: this.mediaUploaded
      };

      return settings;
    },

    handleChange(event) {

      this.value = event.target.value;
    },


    initializeClickEventListener() {
      // Get the element you want to listen for click events inside of
      const element = document.querySelector('.block-editor__editor');
      // Add an event listener for the click event
      element.addEventListener('click', this.handleFileInputClick);
    },

    async getCurrentUser() {
      const response = await axios.get('/user');
      this.currentUser = response.data;
      this.setInitialValue();

    },

    modifyCoreBlocks() {
      const wp = Laraberg.wordpress;
      const { blocks } = wp;
      const { Toolbar, ToolbarButton, } = wp.components;
      const { BlockControls } = wp.blockEditor;
      const { createElement: el } = wp.element;


      this.blocksArray.forEach(block => {
        // Store the original block settings
        const originalBlock = blocks.getBlockType(block);

        if (!originalBlock) return;

        this.originalBlocks[block] = originalBlock;
        console.log('===========OriginalEditComponent===========================', originalBlock);

        const originalEdit = originalBlock.edit;
        // Unregister the original image block
        blocks.unregisterBlockType(block);
        // Create a customized edit function
        const customEditFunction = (props) => {
          // Original edit component
          const OriginalEditComponent = originalEdit(props);
       
          // Your custom toolbar
          const customToolbar = el(
            BlockControls,
            {},
            el(
              Toolbar,
              null,
              el(ToolbarButton, {
                label: 'Media Library',
                icon: mediaAndText,
                onClick: () => this.toggleMediaLibraryModal(true),
              })
            )
          );
          // Return both the original component and your custom toolbar
          return el(
            wp.element.Fragment,
            {},
            OriginalEditComponent,
            customToolbar,
          );
        }

        // Register a new image block with customized settings
        blocks.registerBlockType(block, {
          ...originalBlock,
          edit: customEditFunction,
        });

      })

    },

    async handleFileInputClick(e) {

      // Prevents the file dialog from opening
      e.preventDefault();

      // Check if the clicked element is a file input
      if (!e.target.matches('input[type="file"]')) {

        this.currentFileInput = document.querySelector('input[type="file"]');
        console.log('=================!e.target.matches(input[type="file"])=====================', !e.target.matches('input[type="file"]'), e.target, this.currentFileInput);

      } else {
        // event.target is the element that was actually clicked
        this.currentFileInput = e.target
        console.log('=================else=====================', !e.target.matches('input[type="file"]'), e.target, this.currentFileInput);

        this.toggleMediaUploadModal(true);
      }
    },

    async handleMediaUploaded(media) {
      console.log('================handleMediaUploaded======================', media);

      this.media = media;
      // Trigger a change event on the file input.
      const changeEvent = new Event('change', { 'bubbles': true, 'cancelable': true });
      this.currentFileInput.dispatchEvent(changeEvent);

    },

    async mediaUploaded({ filesList, onFileChange }) {
      try {

        let data = toRaw(this.media)
        data = data.map(file => {
          return {
            id: file.name,
            name: file.name,
            url: file.original_url
          }
        });

        onFileChange(data)

      } catch (error) {
        console.error('Error uploading the file:', error);
      }
    }
  },
  created() {
    this.getCurrentUser();
  },
  beforeUnmount() {
    const wp = Laraberg.wordpress;
    const { blocks } = wp;

    // Reset to original blocks
    Object.keys(this.originalBlocks).forEach(block => {
      blocks.unregisterBlockType(block); // Unregister custom block
      blocks.registerBlockType(block, this.originalBlocks[block]); // Re-register original block
    });

    document.removeEventListener('click', this.handleFileInputClick);

  },
  computed: {
    convertToRaw() {
      return isProxy(this.media) ? toRaw(this.media) : this.media
    }
  }
};
</script>
