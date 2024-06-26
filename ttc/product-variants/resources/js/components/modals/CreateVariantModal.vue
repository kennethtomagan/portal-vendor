<template>
    <Modal
        :show="show"
        @close-via-escape="handlePreventModalAbandonmentOnClose"
        data-testid="confirm-action-modal"
        tabindex="-1"
        role="dialog"
        :size="'4xl'"
    >
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden space-y-6 mt-8">
            <div class="py-2">
                <ModalHeader v-text="'Product Variant'"/>
                <div class="px-4">
                    <div class="md:flex md:flex-row space-y-2 md:space-y-0 py-5" index="0">
                        <div class="w-full md:mt-2 px-6 md:px-8 md:w-1/5">
                            <label for="currency-pricing-select-field" class="inline-block leading-tight space-x-1">
                                <span>Variant Options</span>
                            </label>
                        </div>
                        <div class="w-full space-y-2 px-6 md:px-8 md:w-3/5">
                            <div class="flex relative w-full">
                                <select v-model="data.option_id" class="w-full block form-control form-select form-select-bordered">
                                    <option value="">Choose an option</option>
                                    <option :value="option.id" v-for="(option, index) in options">{{ option.name }}</option>
                                </select>
                                <svg class="flex-shrink-0 pointer-events-none form-select-arrow" xmlns="http://www.w3.org/2000/svg" width="10" height="6" viewBox="0 0 10 6">
                                    <path class="fill-current" d="M8.292893.292893c.390525-.390524 1.023689-.390524 1.414214 0 .390524.390525.390524 1.023689 0 1.414214l-4 4c-.390525.390524-1.023689.390524-1.414214 0l-4-4c-.390524-.390525-.390524-1.023689 0-1.414214.390525-.390524 1.023689-.390524 1.414214 0L5 3.585786 8.292893.292893z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="md:flex md:flex-row space-y-2 md:space-y-0 py-5" index="0">
                        <div class="w-full md:mt-2 px-6 md:px-8 md:w-1/5">
                            <label for="currency-pricing-select-field" class="inline-block leading-tight space-x-1">
                                <span>Currency</span>
                            </label>
                        </div>
                        <div class="w-full space-y-2 px-6 md:px-8 md:w-3/5">
                            <div class="flex relative w-full">
                                <select v-model="data.currency_id" class="w-full block form-control form-select form-select-bordered">
                                    <option value="">Choose an option</option>
                                    <option value="1">USD</option>
                                    <option value="2">AUD</option>
                                    <option value="3">PHP</option>
                                </select>
                                <svg class="flex-shrink-0 pointer-events-none form-select-arrow" xmlns="http://www.w3.org/2000/svg" width="10" height="6" viewBox="0 0 10 6">
                                    <path class="fill-current" d="M8.292893.292893c.390525-.390524 1.023689-.390524 1.414214 0 .390524.390525.390524 1.023689 0 1.414214l-4 4c-.390525.390524-1.023689.390524-1.414214 0l-4-4c-.390524-.390525-.390524-1.023689 0-1.414214.390525-.390524 1.023689-.390524 1.414214 0L5 3.585786 8.292893.292893z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="md:flex md:flex-row space-y-2 md:space-y-0 py-5" index="1">
                        <div class="w-full md:mt-2 px-6 md:px-8 md:w-1/5">
                            <label for="price-pricing-currency-field" class="inline-block leading-tight space-x-1">
                                <span>Sell Price</span>
                                <span class="text-red-500 text-sm">*</span>
                            </label>
                        </div>
                        <div class="w-full space-y-2 px-6 md:px-8 md:w-3/5">
                            <div class="flex flex-wrap items-stretch w-full relative">
                                <div class="flex -mr-px">
                                    <span class="flex items-center leading-normal rounded rounded-r-none border border-r-0 border-gray-300 dark:border-gray-700 px-3 whitespace-nowrap bg-gray-100 dark:bg-gray-800 text-gray-500 text-sm font-bold">$</span>
                                </div>
                                <input v-model="data.price"  class="flex-shrink flex-grow flex-auto leading-normal w-px flex-1 rounded-l-none form-control form-input form-input-bordered" type="number" step="0.01" placeholder="Product Price">
                            </div>
                        </div>
                    </div>
                    <div class="md:flex md:flex-row space-y-2 md:space-y-0 py-5" index="0">
                        <div class="w-full md:mt-2 px-6 md:px-8 md:w-1/5">
                            <label for="sku-other-details-text-field" class="inline-block leading-tight space-x-1"><span>SKU</span>
                                <span class="text-red-500 text-sm">*</span>
                            </label>
                        </div>
                        <div class="w-full space-y-2 px-6 md:px-8 md:w-3/5">
                            <div class="space-y-1">
                                <input 
                                    v-model="data.sku" 
                                    type="text"
                                    placeholder="SKU"
                                    class="w-full form-control form-input form-input-bordered"
                                    id="sku-other-details-text-field" 
                                    dusk="sku" 
                                    maxlength="-1"
                                >
                            </div>
                        </div>
                    </div>
                    <div class="md:flex md:flex-row space-y-2 md:space-y-0 py-5" index="0">
                        <div class="w-full md:mt-2 px-6 md:px-8 md:w-1/5">
                            <label for="stock-inventory-text-field" class="inline-block leading-tight space-x-1">
                                <span>Stock</span>
                            </label>
                        </div>
                        <div class="w-full space-y-2 px-6 md:px-8 md:w-3/5">
                            <div class="space-y-1">
                                <input 
                                    v-model="data.stock" 
                                    type="number" 
                                    placeholder="Stock" 
                                    class="w-full form-control form-input form-input-bordered" >
                            </div>
                        </div>
                    </div>
                    <div class="md:flex md:flex-row space-y-2 md:space-y-0 py-5" index="0">
                        <div class="w-full md:mt-2 px-6 md:px-8 md:w-1/5">
                            <label for="backorder-inventory-text-field" class="inline-block leading-tight space-x-1">
                                <span>Backorder</span>
                            </label>
                        </div>
                        <div class="w-full space-y-2 px-6 md:px-8 md:w-3/5">
                            <div class="space-y-1">
                                <input 
                                    type="number" 
                                    placeholder="backorder" 
                                    class="w-full form-control form-input form-input-bordered" 
                                    v-model="data.backorder" 
                                    >
                            </div>
                        </div>
                    </div>
                    <div class="md:flex md:flex-row space-y-2 md:space-y-0 py-5" index="2">
                        <div class="w-full md:mt-2 px-6 md:px-8 md:w-1/5">
                            <label for="purchasability-inventory-select-field" class="inline-block leading-tight space-x-1">
                                <span>Purchasability</span><!---->
                            </label>
                        </div>
                        <div class="w-full space-y-2 px-6 md:px-8 md:w-3/5">
                            <div class="flex relative w-full">
                                <select v-model="data.purchasability" class="w-full block form-control form-select form-select-bordered">
                                    <option disabled="" value="">Choose an option</option>
                                    <option value="always">Always</option>
                                    <option value="in_stock">In stock</option>
                                    <option value="backorder">Backorder</option>
                                </select>
                                <svg class="flex-shrink-0 pointer-events-none form-select-arrow" xmlns="http://www.w3.org/2000/svg" width="10" height="6" viewBox="0 0 10 6">
                                    <path class="fill-current" d="M8.292893.292893c.390525-.390524 1.023689-.390524 1.414214 0 .390524.390525.390524 1.023689 0 1.414214l-4 4c-.390525.390524-1.023689.390524-1.414214 0l-4-4c-.390524-.390525-.390524-1.023689 0-1.414214.390525-.390524 1.023689-.390524 1.414214 0L5 3.585786 8.292893.292893z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
                <ModalFooter>
                    <div class="flex items-center ml-auto">
                        <CancelButton
                            component="button"
                            type="button"
                            dusk="cancel-action-button"
                            class="ml-auto mr-3"
                            @click="$emit('close')"
                        >
                        Cancel
                        </CancelButton>

                        <LoadingButton
                            :disabled="loading"
                            :loading="loading"
                            @click="createVariantOptions"
                        >
                        Add
                        </LoadingButton>
                    </div>
                </ModalFooter>
        </div>
        </div>
        
    </Modal>
</template>

<script>

export default {
    props: {
        show: {
            type: Boolean,
            default: false,
            required: true
        },
        isCentral: {
            type: Boolean,
            default: false,
            required: true
        },
        options: {
            type: Array,
            default: null
        },
        resourceId: {
            type: Number,
            required: true
        },
    },
    data() {
        return {
            loading:false,
            data: {
                option_id: "",
                currency_id: "",
                price: null,
                sku: null,
                stock: 0,
                backorder: 0,
                purchasability: 'always',
            }
        }
    },
    methods: {
        handlePreventModalAbandonmentOnClose(event) {
            this.handlePreventModalAbandonment( () => { this.$emit('close') },
                () => {
                event.stopPropagation()
                }
            )
        },
        createVariantOptions() {
            this.loading = true;
                Nova.request().post('/api/product/'+ this.resourceId +'/create-variant', this.data)
                .then(() => {
                    this.loading = false;
                    this.$emit('reload');
                    this.$emit('close');
                })
                .catch(error => {
                    console.error('Error:', error);
                    this.loading = false;
                });
        }
    },
}
</script>

