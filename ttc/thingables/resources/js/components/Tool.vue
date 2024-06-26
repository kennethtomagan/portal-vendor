<template>
    <section class="ticket-messages">
        <div class="mb-8">Messages</div>
        <card class="p-6 flex flex-col">
            <div class="flex justify-center mb-8">
                <div class="w-5/6 mx-auto">
                    <component
                        v-for="field in fields"
                        :key="field.uniqueKey"
                        :is="'form-' + field.component"
                        :id="field.uniqueKey"
                        :fieldAttribute="field.attribute"
                        :resource-name="resourceName"
                        :resource-id="resourceId"
                        :field="field"
                        :showHelpText="'field.showHelpText'"
                        :errors="validationErrors"
                        value="public"
                    >
                    </component>
                    <div class="flex justify-center space-x-2 pl-5">
                        <a size="md"
                        class="flex-shrink-0 shadow rounded focus:outline-none ring-primary-200 dark:ring-gray-600 focus:ring bg-primary-500 hover:bg-primary-400 active:bg-primary-600 text-white dark:text-gray-800 inline-flex items-center font-bold px-4 h-9 text-sm flex-shrink-0"
                        dusk="attach-button"
                        @click="addMessage"
                        >
                            <span class="md:inline-block">Add New Message</span>
                        </a>
                    </div>
                </div> 
            </div> 
            <!-- Sorting Message -->
            <div class="flex mb-2 justify-between pr-8">
                <div class="relative inline-block ml-auto">
                    <select 
                        v-model="sortBy"
                        class="block appearance-none w-full cursor-pointer bg-white border border-gray-400 hover:border-gray-500 px-4 py-2 pr-8 rounded shadow leading-tight focus:outline-none focus:shadow-outline">
                        <option :value="null" disabled selected>Sort By</option>
                        <option value="desc">Newest</option>
                        <option value="asc">Oldest</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9 11a1 1 0 0 0 1.41 0l3-3a1 1 0 0 0-1.41-1.41L10 8.59l-2.29-2.3a1 1 0 0 0-1.42 1.42l3 3z"/></svg>
                    </div>
                </div>
            </div>

            <!-- List of Messages -->
            <Messages
                v-if="messages.length > 0"
                v-for="(message, index) in messages"
                class="flex flex-col flex-no-wrap p-2 mb-2 basis-4/4 w-4/4"
                :class="'meta-row-'+index"
                :message="message"
                :index="index"
                :key="index"
                :authEmail="auth.email"
                :resourceId="resourceId"
                :fields="fields"
                :resource-name="resourceName"
                :resource-id="resourceId"
                @removeMessage="removeMessage"
                @updateMessageOnList="updateMessageOnList"
            />
            <div v-else-if="loading" class="flex justify-center "> <span>Loading please wait..</span></div>
            <div v-else class="flex justify-center "> <span>No records to display</span></div>
            
            <div class="flex justify-center ">
                <button 
                    v-if="showViewMoreMessage && messages.length != 0"
                    @click="viewMoreMessage"
                    class="bg-transparent hover:bg-blue-500 text-blue-700 font-semibold hover:text-white py-2 px-4 border border-blue-500 hover:border-transparent rounded mt-5">
                    View {{ countUnloadMessages }} remaining older messages
                </button>
            </div>
        </card>
    </section>
    
</template>

<script>
import Thing from "./Thing";
import Messages from "./Messages";
import Card from "./../../../../../vendor/laravel/nova/resources/js/components/Card"
import { Errors } from 'form-backend-validation'

export default {
    components: {
        Thing,
        Card,
        Messages
    },
    props: [
      'resourceName',
      'resourceId',
      'panel',
      'tenantId'
    ],
    data() {
        return {
            fields: this.panel.fields[0].fields,
            component: this.panel.fields[0].component ?? 'thingables',
            parentResource: this.panel.fields[0].parentResource,
            auth: this.panel.fields[0].auth,
            thingsModel: this.panel.fields[0].thingsModel,
            value: new FormData(),
            messages: [],
            loading: false,
            page: 1,
            perPage: 10,
            showViewMoreMessage: true,
            countUnloadMessages: 0,
            sortBy: null,
            validationErrors: new Errors(),
        };
    },

    mounted() {
        this.value.set("parentResource", this.parentResource);
        this.value.set("thingsModel", this.thingsModel);
        this.getMessages();
    },

    methods: {
        setFieldValue: function(evt, field){
            const fieldElement = document.querySelector("#"+field.uniqueKey);
            let valueToAdd = "";
            if(typeof field.checked !== "undefined"){
                valueToAdd = field.checked;
            }else{
                const fieldTrix = fieldElement.querySelector("trix-editor");
                if(fieldTrix){
                    valueToAdd = fieldTrix.value;
                } else {
                    const fieldElement = document.getElementById(field["attribute"]);
                    valueToAdd = fieldElement.value;
                }
            }
            this.value.set(field.attribute, valueToAdd);
        },
        fill() {
            for (let i = 0; i < this.fields.length; i++) {
                const field = this.fields[i];
                this.setFieldValue(null, field)
            }
        },
        resetFields() {
            for (let i = 0; i < this.fields.length; i++) {
                let field = this.fields[i];
                const fieldElement = document.querySelector("#"+field.uniqueKey);
                if(typeof field.checked !== "undefined"){
                    field.checked = false;
                } else {
                    const fieldTrix = fieldElement.querySelector("trix-editor");
                    if(fieldTrix){
                        field = fieldTrix;
                    }
                    field.value = "";

                }
                this.value.delete(field.attribute);
            }
        },
        addMessage() {
            this.fill();
            if (this.tenantId) {
                this.value.set('tenant_id', this.tenantId);
            }

            Nova.request().post('/nova-api/ticket/'+ this.resourceId +'/add-message',  this.value)
            .then(response => {
                this.messages.unshift(response.data);
                this.resetFields();
                Nova.success('Message added successfully')
                this.validationErrors = new Errors()
            })
            .catch(error => {
                if (error.response.status == 422) {
                    this.validationErrors = new Errors(error.response.data.errors)
                }
            });
        },
        getMessages() {
            this.loading = true;
            Nova.request().get('/nova-api/ticket/'+ this.resourceId +'/messages',  {
                params: {
                    page: this.page,
                    per_page: this.perPage,
                    order_by: this.sortBy ?? 'desc',
                    tenant_id: this.tenantId ?? null,
                }
            })
            .then(response => {
                const results = response.data;
                if (results.data.length > 0) {
                    this.messages = [...this.messages, ...results.data];
                }
                this.showViewMoreMessage =  this.page == results.meta.last_page ? false : true;
                this.countUnloadMessages =  results.meta.total - this.messages.length;
                this.loading = false;
            })
            .catch(error => {
                console.error('Error:', error);
                this.loading = false;
            });
        },
        viewMoreMessage() {
            this.page = this.page + 1;
            this.getMessages();
        },
        removeMessage(message) {
            this.messages = this.messages.filter(obj => obj.id !== message.id);
        },
        updateMessageOnList(message){
            this.messages = this.messages.map(element => {
                if (element.id === message.id && element.connection === message.connection) {
                    return {
                        ...element,
                        status: message.status,
                        content: message.content
                    };
                }
                return element;
            });
        }
    },
    watch: {
        sortBy() {
            this.messages = [];
            this.getMessages()
        }
    }
}
</script>