<template>
    <div class="relative">
        <!---->
        <section v-if="!loading">
            <div class="overflow-hidden overflow-x-auto relative" v-if="tickets.length > 0">
                <table class="w-full divide-y divide-gray-100 dark:divide-gray-700" data-testid="resource-table">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                        <th
                            class="text-left px-2 whitespace-nowrap uppercase text-gray-500 text-xxs tracking-wide py-2"
                            v-for="(header, index) in headers " 
                            :key="index"
                        >
                            <span>{{ header }}</span>
                        </th>
                        <th class="uppercase text-xxs tracking-wide px-2 py-2"><span class="sr-only"></span></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        <tr 
                            v-for="ticket in tickets"
                            :key="ticket.id"
                            class="group"
                        >
                            <td class="px-2 py-2 whitespace-nowrap cursor-pointer dark:bg-gray-800 group-hover:bg-gray-50 dark:group-hover:bg-gray-900">
                                <div class="text-left"><span class="whitespace-nowrap">{{ ticket.tenant_id }}</span></div>
                            </td>
                            <td class="px-2 py-2 whitespace-nowrap cursor-pointer dark:bg-gray-800 group-hover:bg-gray-50 dark:group-hover:bg-gray-900">
                                <div class="text-left"><span class="whitespace-nowrap">{{ ticket.title }}</span></div>
                            </td>
                            <td class="px-2 py-2 whitespace-nowrap cursor-pointer dark:bg-gray-800 group-hover:bg-gray-50 dark:group-hover:bg-gray-900">
                                <div class="text-left uppercase"><span class="whitespace-nowrap">{{ ticket.type }}</span></div>
                            </td>
                            <td class="px-2 py-2 whitespace-nowrap cursor-pointer dark:bg-gray-800 group-hover:bg-gray-50 dark:group-hover:bg-gray-900">
                                <div class="text-left"><span class="whitespace-nowrap">{{ ticket.status }}</span></div>
                            </td>
                            <td class="px-2 py-2 whitespace-nowrap cursor-pointer dark:bg-gray-800 group-hover:bg-gray-50 dark:group-hover:bg-gray-900">
                                <div class="text-left"><span class="whitespace-nowrap">{{ ticket.reporter_email }}</span></div>
                            </td>
                            <td class="py-2 cursor-pointer px-2 td-fit text-right align-middle dark:bg-gray-800 group-hover:bg-gray-50 dark:group-hover:bg-gray-900">
                                <TicketsTableActions
                                    :ticket="ticket"
                                    @reload="$emit('reload')"
                                />
                            </td>
                        </tr>
                        <!---->
                    </tbody>
                </table>
            </div>
            <div class="border-t border-gray-200 dark:border-gray-700" v-if="tickets.length > 0">
                <div class="border-t border-gray-200 dark:border-gray-700">
                    <div class="rounded-b-lg" per-page="25">
                        <nav class="flex justify-between items-center">
                            <button 
                                class="text-xs font-bold py-3 px-4 focus:outline-none rounded-bl-lg focus:ring focus:ring-inset text-gray-300 dark:text-gray-600" 
                                :class="{' rounded-br-lg text-primary-500 hover:text-primary-400 active:text-primary-600' : pagination.current_page !== 1}"
                                :disabled="pagination.current_page == 1" 
                                rel="prev" 
                                dusk="previous"
                                @click.prevent="$emit('previousPage')"
                                >
                                Previous
                            </button>
                            <span class="text-xs px-4">{{ pagination.from }} - {{ pagination.to }} of {{ pagination.total }}</span>
                            <button 
                                class="text-xs font-bold py-3 px-4 focus:outline-none rounded-bl-lg focus:ring focus:ring-inset text-gray-300 dark:text-gray-600" 
                                :class="{' rounded-br-lg text-primary-500 hover:text-primary-400 active:text-primary-600': pagination.current_page !== pagination.last_page}"
                                :disabled="pagination.current_page == pagination.last_page" 
                                rel="next" 
                                dusk="next"
                                @click.prevent="$emit('nextPage')"
                            >
                                Next
                            </button>
                        </nav>
                    </div>
                </div>
            </div>
            <div class="relative" v-if="tickets.length == 0">
                <div class="flex flex-col justify-center items-center px-6 py-8 space-y-6" dusk="tickets-empty-dialog">
                    <div class="flex flex-col justify-center items-center px-6 space-y-3">
                        <svg class="inline-block text-gray-300 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" width="65" height="51" viewBox="0 0 65 51">
                            <path class="fill-current" d="M56 40h2c.552285 0 1 .447715 1 1s-.447715 1-1 1h-2v2c0 .552285-.447715 1-1 1s-1-.447715-1-1v-2h-2c-.552285 0-1-.447715-1-1s.447715-1 1-1h2v-2c0-.552285.447715-1 1-1s1 .447715 1 1v2zm-5.364125-8H38v8h7.049375c.350333-3.528515 2.534789-6.517471 5.5865-8zm-5.5865 10H6c-3.313708 0-6-2.686292-6-6V6c0-3.313708 2.686292-6 6-6h44c3.313708 0 6 2.686292 6 6v25.049375C61.053323 31.5511 65 35.814652 65 41c0 5.522847-4.477153 10-10 10-5.185348 0-9.4489-3.946677-9.950625-9zM20 30h16v-8H20v8zm0 2v8h16v-8H20zm34-2v-8H38v8h16zM2 30h16v-8H2v8zm0 2v4c0 2.209139 1.790861 4 4 4h12v-8H2zm18-12h16v-8H20v8zm34 0v-8H38v8h16zM2 20h16v-8H2v8zm52-10V6c0-2.209139-1.790861-4-4-4H6C3.790861 2 2 3.790861 2 6v4h52zm1 39c4.418278 0 8-3.581722 8-8s-3.581722-8-8-8-8 3.581722-8 8 3.581722 8 8 8z"></path>
                        </svg>
                        <h3 class="text-base font-normal">No Flag Ticket results.</h3>
                    </div>
                </div>
            </div>
        </section>
        <div 
            v-else
            class="bg-white dark:bg-gray-800 rounded-lg shadow flex flex-col justify-center items-center px-6 py-8">
            <h3 class="text-base font-normal mt-3">Loading...</h3>
        </div>
    </div>
</template>
<script>
import TicketsTableActions from './TicketsTableActions'
export default {
    components: {
        TicketsTableActions
    },
    props: {
        tickets: {
            type: Array,
            default: [],
        },
        loading: {
            type: Boolean,
            default: false,
            required: true
        },
        pagination: {
            type: Object,
            default: {},
        }
    },
    data() {
        return {
            headers: [
                'Tenant ID',
                'Title',
                'Type',
                'Status',
                'Reporter Email',
                '',
            ],
        }
    },
}

</script>