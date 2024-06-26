<template>
    <section>
        <div class="flex mb-3">
            <div class="relative h-9 w-full md:w-1/3 md:flex-shrink-0">
                <h1 class="font-normal text-xl md:text-xl flex items-center"><span>Variants</span></h1>
            </div>
            <div class="inline-flex items-center space-x-2 ml-auto" v-if="notAvailableOptions.length > 0 && isFromCentral">
                <div class="flex-shrink-0">
                    <a
                        @click="showCreateModal = true" 
                        size="md" 
                        class="flex-shrink-0 h-9 px-4 focus:outline-none ring-primary-200 dark:ring-gray-600 focus:ring text-white dark:text-gray-800 inline-flex items-center font-bold shadow rounded focus:outline-none ring-primary-200 dark:ring-gray-600 focus:ring bg-primary-500 hover:bg-primary-400 active:bg-primary-600 text-white dark:text-gray-800 inline-flex items-center font-bold px-4 h-9 text-sm flex-shrink-0 h-9 px-4 focus:outline-none ring-primary-200 dark:ring-gray-600 focus:ring text-white dark:text-gray-800 inline-flex items-center font-bold" dusk="create-button" href="#">
                        <span class="hidden md:inline-block">Add Variant</span>
                    </a>
                </div>
            </div>
        </div>
        <Card class="p-3">
            <div class="relative">
                    
                <section v-if="!loading">
                    <div class="overflow-hidden overflow-x-auto relative">
                        <table class="w-full divide-y divide-gray-100 dark:divide-gray-700" data-testid="resource-table">
                            <thead class="bg-gray-50 dark:bg-gray-800">
                                <tr>
                                <th
                                    class="text-left px-2 whitespace-nowrap uppercase text-gray-500 text-xxs tracking-wide py-2"
                                    v-for="(header, index) in headers" 
                                    :key="index"
                                >
                                    <span>{{ header }}</span>
                                </th>
                                <th class="uppercase text-xxs tracking-wide px-2 py-2"><span class="sr-only"></span></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                <tr 
                                    v-for="variant in variants"
                                    :key="variant.id"
                                    class="group"
                                >
                                    <td class="px-2 py-2 whitespace-nowrap cursor-pointer dark:bg-gray-800 group-hover:bg-gray-50 dark:group-hover:bg-gray-900">
                                        <div class="text-left"><span class="whitespace-nowrap">{{ variant.option?.name }}</span></div>
                                    </td>
                                    <td class="px-2 py-2 whitespace-nowrap cursor-pointer dark:bg-gray-800 group-hover:bg-gray-50 dark:group-hover:bg-gray-900">
                                        <div class="text-left"><span class="whitespace-nowrap">{{ variant.option_value?.name }}</span></div>
                                    </td>
                                    <td 
                                        v-if="!isCentral"
                                        class="px-2 py-2 whitespace-nowrap cursor-pointer dark:bg-gray-800 group-hover:bg-gray-50 dark:group-hover:bg-gray-900">
                                        <div class="text-left"><span class="whitespace-nowrap">{{ variant.buy_price }}</span></div>
                                    </td>
                                    <td class="px-2 py-2 whitespace-nowrap cursor-pointer dark:bg-gray-800 group-hover:bg-gray-50 dark:group-hover:bg-gray-900">
                                        <div class="text-left"><span class="whitespace-nowrap">{{ variant.sell_price }}</span></div>
                                    </td>
                                    <td class="px-2 py-2 whitespace-nowrap cursor-pointer dark:bg-gray-800 group-hover:bg-gray-50 dark:group-hover:bg-gray-900">
                                        <div class="text-left"><span class="whitespace-nowrap">{{ variant.purchasable }}</span></div>
                                    </td>
                                    <td class="py-2 cursor-pointer px-2 td-fit text-right align-middle dark:bg-gray-800 group-hover:bg-gray-50 dark:group-hover:bg-gray-900">
                                        <div class="flex items-center justify-end space-x-0 text-gray-400">
                                        <button 
                                            @click.prevent="viewVariant(variant)"
                                            aria-label="View" 
                                            dusk="2-delete-button"
                                            class="toolbar-button hover:text-primary-500 px-2 disabled:opacity-50 disabled:pointer-events-none v-popper--has-tooltip">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="24" height="24" class="inline-block" role="presentation">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </button>
                                        <button 
                                            @click.prevent="editVariant(variant)"
                                            aria-label="Edit" 
                                            dusk="2-delete-button"
                                            class="toolbar-button hover:text-primary-500 px-2 disabled:opacity-50 disabled:pointer-events-none v-popper--has-tooltip">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="24" height="24" class="inline-block" role="presentation">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </button>
                                        <button 
                                            @click.prevent="deleteVariant(variant)"
                                            v-if="!variant.from_central"
                                            aria-label="Delete" 
                                            dusk="2-delete-button"
                                            class="toolbar-button hover:text-primary-500 px-2 disabled:opacity-50 disabled:pointer-events-none v-popper--has-tooltip">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="24" height="24" class="inline-block" role="presentation">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                    </td>
                                </tr>
                                <!---->
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        </Card>
        <ViewVariantModal 
            :show="showViewModal" 
            :variant="selectedVariant"
            @close="showViewModal = false"
            :isCentral="isCentral"
        />
        <EditVariantModal 
            :show="showEditModal" 
            :variant="selectedVariant"
            @close="showEditModal = false"
            @reload="getVariants"
            :isCentral="isCentral"
        />
        <DeleteVariantModal 
            :show="showDeleteModal"
            :variant="selectedVariant"
            @close="showDeleteModal = false"
            @submit="deleteAction"
        />
        <CreateVariantModal
            :show="showCreateModal"
            @close="showCreateModal = false"
            :options="notAvailableOptions"
            :resourceId="resourceId"
            @reload="getVariants"
        />
    </section>
</template>

<script>
import ViewVariantModal from './modals/ViewVariantModal';
import EditVariantModal from './modals/EditVariantModal';
import DeleteVariantModal from './modals/DeleteVariantModal';
import CreateVariantModal from './modals/CreateVariantModal';

export default {
    props: ['resourceName', 'resourceId', 'panel', 'action'],
    components: {
        ViewVariantModal,
        EditVariantModal,
        DeleteVariantModal,
        CreateVariantModal
    },
    data() {
        return {
			isCentral: this.panel?.fields[0]?.isCentral ?? false,
            variants: [],
            notAvailableOptions: [],
            loading: false,
            showViewModal: false,
            showEditModal: false,
            showDeleteModal: false,
            showCreateModal: false,
            selectedVariant: null,
            headers: [
                'Variant',
                'Option',
                'Buy Price',
                'Sell Price',
                'Purchasability',
                'Stock',
                '',
            ],
        }
    },
    mounted() {
        this.getVariants();
        
        if (this.isCentral) {
            this.headers = this.headers.filter(header => header !== 'Buy Price');
        }
    },
    methods: {
        getVariants() {
            this.loading = true;
                Nova.request().get('/api/product/'+ this.resourceId +'/variants')
                .then(response => {
                    this.variants = response.data.data;
                    this.loading = false;
                    this.getNotAvailableOptions();
                })
                .catch(error => {
                    console.error('Error:', error);
                    this.loading = false;
                });
        },
        getNotAvailableOptions() {
            Nova.request().get('/api/product/'+ this.resourceId +'/not-available-variants-options')
            .then(response => {
                console.log(response.data);
                this.notAvailableOptions = response.data;
            })
            .catch(error => {
                console.error('Error:', error);
            });
        },
        viewVariant(variant) {
            this.showViewModal = true;
            this.selectedVariant = variant;
        },
        editVariant(variant) {
            this.showEditModal = true;
            this.selectedVariant = variant;
        },
        deleteVariant(variant) {
            this.showDeleteModal = true;
            this.selectedVariant = variant;
        },
        deleteAction(variant) {
            this.loading = true;
                Nova.request().delete('/api/product/variants/' + variant.id)
                .then(response => {
                    this.loading = false;
                    this.showDeleteModal = false;
                    if (response.data.has_variants == 0) {
                        window.location.reload();
                    } else {
                        this.getVariants();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    this.loading = false;
                });
        }
    },
    computed: {
        isFromCentral(){
            if (this.variants.length > 0 && !this.variants[0].from_central) {
                return true;
            }
            return false;
        }
    }
}
</script>
