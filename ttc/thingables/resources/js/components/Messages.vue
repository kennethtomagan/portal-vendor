<template>
    <div>
        <article 
            :id="[message.type+'_'+message.id]"
            class="p-6 mb-1 text-base"
            :class="index > 0 ? 'bg-white border-gray-200 border-gray-700 ': ''"
        >
            <footer class="flex justify-between items-center mb-2">
                <div class="flex items-center">
                    <p class="inline-flex items-center mr-3 text-sm  font-semibold"><img
                        class="mr-2 w-6 h-6 rounded-full"
                        src="https://t4.ftcdn.net/jpg/05/34/22/33/360_F_534223391_83Z5G3eCWFkutie7csol6BNb00dGZbad.jpg"
                        alt="Bonnie Green"
                    >
                    <span v-if="message.owner_name">{{ message.owner_name }}</span>
                    <span v-else-if="message.reporter_name">{{ message.reporter_name }} </span>
                        
                    </p>
                    <p class="text-sm ">
                        <time pubdate datetime="2022-03-12" title="March 12th, 2022">
                        {{ message.human_created_at ?? message.created_at }}
                        </time>
                    </p>
                </div>
                <button
                    v-if="message.owner_email == authEmail"
                    id="dropdownMessageActionButton" 
                    data-dropdown-toggle="dropdownMessageAction"
                    @click="showMessageAction"
                    type="button"
                >
                    <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 3">
                        <path d="M2 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3Zm6.041 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM14 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3Z"/>
                    </svg>
                    <span class="sr-only">Message settings</span>
                </button>
                <!-- Dropdown menu -->
                <div :id="'dropdownMessageAction' + message.id"
                        v-show="showAction"
                        class="block absolute right-0 mt-28 w-36 bg-white rounded divide-y divide-gray-100 shadow divide-gray-600"
                    >
                    <ul class="py-1 text-sm text-gray-700"
                        aria-labelledby="dropdownMenuIconHorizontalButton">
                        <li>
                            <a @click.prevent="showEditModal = true"
                                class="block py-2 px-4  cursor-pointer hover:bg-light-500">
                                Edit
                            </a>
                        </li>
                        <li>
                            <a @click.prevent="showDeleteModal = true"
                                class="block py-2 px-4  cursor-pointer">
                                Delete
                            </a>
                        </li>
                    </ul>
                </div>
            </footer>
            <p  v-html="message.content"></p>
            
            <!-- Attachments -->
            <div class="gallery mt-3">
                <div class="gallery-list clearfix"  v-for="(attachment, index) in message.attachments" :key="index">
                    <div class="float-r mr-1" style="float: left; ">
                        <div class="gallery-item gallery-item-file mb-3 p-3 mr-3" field="[object Object]" editable="false">
                            <div class="gallery-item-info">
                            <a class="download mr-2" :href="attachment.url" target="_blank">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16" height="16" class="inline-block" role="presentation" view-box="0 0 20 20">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </a>
                            <a class="download mr-2" href="/nova-vendor/ebess/advanced-nova-media-library/download/1?uuid=a0f36bce-930a-477b-8086-a585010c90fe">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="16" height="16" class="inline-block" role="presentation" view-box="0 0 20 22">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                </svg>
                            </a>
                            <span class="label">{{ attachment.file_name }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <br style="clear: both;"><!---->
                <p class="help-text mt-2"></p>
                <!---->
                </div>


            <div class="flex items-center mt-3 space-x-3"></div>
            <hr class="mt-4">
        </article>

        <!-- Action Modals -->
        <DeleteModal 
            @confirm="deleteMessage"
            @close="showDeleteModal = false"
            :show="showDeleteModal"
            :working.sync="loading"
        />
        <EditModal 
            :show="showEditModal"
            :working.sync="loading"
            :fields="fields"
            :resource-name="resourceName"
            :resource-id="resourceId"
            :message="message"
            @close="showEditModal = false"
            @updated="updateMessage"
            >
        </EditModal>
    </div>
</template>
<script>

import DeleteModal from "./../../../../../vendor/laravel/nova/resources/js/components/Modals/DeleteResourceModal"
import EditModal from "./EditModal.vue"
export default {
    name: 'thing',
    components: {
        DeleteModal,
        EditModal,
    },
    props: {
        message: {
            type: Array,
            default: [],
        },
        index: {
            type: Number,
            default: 0,
        },
        authEmail: {
            type: String,
            default: ''
        },
        authEmail: {
            type: String,
            default: ''
        },
        fields: { 
            type: Array,
            required: true
        },
        resourceName: {
            type: String,
            required: true
        },
        resourceId: {
            type: Number,
            required: true
        }
    },
    data() {
        return {
            showAction: false,
            showDeleteModal: false,
            showEditModal: false,
            loading: false
        }
    },
    methods: {
        
        showMessageAction() {
            this.showAction = true;
            setTimeout(() => {
                document.addEventListener('click', this.onClickOutsideAction);
            }, 500);
        },

        onClickOutsideAction(event) {
            const dropdown = document.getElementById('dropdownMessageAction' + this.message.id);

            if (!dropdown.contains(event.target) && event.target.id !== 'dropdownMessageActionButton') {
                this.showAction = false;
                // Remove the click event listener when the dropdown is closed
                document.removeEventListener('click', this.onClickOutsideAction);
            }
        },

        deleteMessage() {
            this.loading = true;
            Nova.request().delete('/nova-api/ticket/messages/' + this.message.id + '/delete-message')
            .then(response => {
                Nova.success('Message deleted successfully');
                this.showDeleteModal = false;
                this.loading = false;
                this.$emit('removeMessage', this.message)
            })
            .catch(error => {
                console.error('Error:', error);
            });
        },

        updateMessage(response) {
            this.loading = false;
            this.showEditModal = false;
            this.$emit('updateMessageOnList', response)
        }
    },
}
</script>
