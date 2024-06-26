<template>
    <div>
        <Head title="Flag Tickets" />
        <div class="md:flex items-center mb-3">
            <div class="flex flex-auto truncate items-center">
                <h1 class="font-normal text-xl md:text-xl">Ticket Details: </h1>
                <!---->
            </div>
            <div class="ml-auto flex items-center">
                <TicketStatusActions 
                    v-if="ticket"
                    :ticket="ticket"
                    @reload="getTicketDetails"
                />
            </div>
        </div>

        <Card 
            v-if="ticket"
            class="p-3 dark:bg-gray-800 rounded-lg shadow mt-3 py-2 px-6 divide-y divide-gray-100 dark:divide-gray-700"
        >
            <div 
                v-for="(field, key) in fields"
                class="flex flex-col md:flex-row -mx-6 px-6 py-2 md:py-0 space-y-2 md:space-y-0"
            >
                <div class="md:w-1/4 md:py-3">
                    <h4 class="font-normal"><span>{{ field }}</span></h4>
                </div>
                <div class="md:w-3/4 md:py-3 break-all lg:break-words">
                    <excerpt
                        v-if="key == 'content' || key == 'excerpt'"
                        :plainText="false" 
                        :shouldShow="false"
                        :content="ticket[key]"
                    />
                    <p v-else class="flex items-center">{{ ticket[key] ?? '' }}</p>
                </div>
            </div>
            <div 
                class="flex flex-col md:flex-row -mx-6 px-6 py-2 md:py-0 space-y-2 md:space-y-0"
            >
                <div class="md:w-1/4 md:py-3">
                    <h4 class="font-normal"><span>File Attachments</span></h4>
                </div>
                <div class="md:w-3/4 md:py-3 break-all lg:break-words">
                <!-- Attachments -->
                <div class="gallery">
                    <div class="gallery-list clearfix"  v-for="(attachment, index) in ticket.attachments" :key="index">
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
                </div>
            </div>
        </Card>
        <MessageSection 
            resourceName="tickets"
            :resourceId="ticketId"
            :panel="messageProps"
            :tenantId="tenantId"
        />
    </div>
</template>
<script>
import TicketStatusActions from '../components/TicketStatusActions'
import MessageSection from '../../../../Thingables/resources/js/components/Tool';

export default {
    props: {
        ticketId: {
            type: Number,
            required: true
        },
        tenantId: {
            type: String,
            required: true
        },
        messageFields: {
            type: Array,
        },
        auth: {
            type: Object,
        }
    },
    components: {
        TicketStatusActions,
        MessageSection
    },
    data() {
        return {
            fields: {
                'id': 'ID', 
                'title': 'Title', 
                'type': 'Type', 
                'status': 'Status', 
                'reporter_email': 'Reporter Email',
                'reporter_name': 'Reporter Name',
                'content': 'Content',
                'excerpt': 'Excerpt',
            },
            loading: false,
            ticket: null,
            messageProps: {
                fields: [
                    {
                        auth: this.auth,
                        fields: this.messageFields,
                        parentResource: "App\\Nova\\Shared\\Ticket",
                        thingsModel: "App\\Models\\Shared\\Ticket",
                    }
                ]
            }
        }
    },
    mounted() {
        console.log('fields', this.messageFields);
        this.getTicketDetails();
    },
    methods: {
        getTicketDetails() {
            this.loading = true;
            Nova.request().get('/api/flag-tickets/' + this.tenantId + '/' + this.ticketId )
            .then(response => {
                this.ticket = response.data;
                this.loading = false;
            })
            .catch(error => {
                console.error('Error:', error);
                this.loading = false;
            });
        },
    }
}
</script>