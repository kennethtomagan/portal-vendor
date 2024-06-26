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
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow mt-3 py-2 px-6 divide-y divide-gray-100 dark:divide-gray-700">
                        <div class="flex flex-col md:flex-row -mx-6 px-6 py-2 md:py-0 space-y-2 md:space-y-0" dusk="stock">
                            <div class="md:w-1/4 md:py-3">
                                <h4 class="font-normal"><span>Variant</span></h4>
                            </div>
                            <div class="md:w-3/4 md:py-3 break-all lg:break-words">
                                <p class="flex items-center" v-if="variant.option">{{ variant.option.name }}</p>
                            </div>
                        </div>
                        <div class="flex flex-col md:flex-row -mx-6 px-6 py-2 md:py-0 space-y-2 md:space-y-0" dusk="stock">
                            <div class="md:w-1/4 md:py-3">
                                <h4 class="font-normal"><span>Variant Option</span></h4>
                            </div>
                            <div class="md:w-3/4 md:py-3 break-all lg:break-words">
                                <p class="flex items-center" v-if="variant.option_value">{{ variant.option_value.name }}</p>
                            </div>
                        </div>
                        <div v-if="!isCentral" class="flex flex-col md:flex-row -mx-6 px-6 py-2 md:py-0 space-y-2 md:space-y-0" dusk="backorder">
                            <div class="md:w-1/4 md:py-3">
                                <h4 class="font-normal"><span>Buy Price</span></h4>
                            </div>
                            <div class="md:w-3/4 md:py-3 break-all lg:break-words">
                                <p class="flex items-center">{{ variant.buy_price }}</p>
                            </div>
                        </div>
                        <div class="flex flex-col md:flex-row -mx-6 px-6 py-2 md:py-0 space-y-2 md:space-y-0" dusk="backorder">
                            <div class="md:w-1/4 md:py-3">
                                <h4 class="font-normal"><span>Sell Price</span></h4>
                            </div>
                            <div class="md:w-3/4 md:py-3 break-all lg:break-words">
                                <p class="flex items-center">{{ variant.sell_price }}</p>
                            </div>
                        </div>
                        <div class="flex flex-col md:flex-row -mx-6 px-6 py-2 md:py-0 space-y-2 md:space-y-0" dusk="purchasability">
                            <div class="md:w-1/4 md:py-3">
                                <h4 class="font-normal"><span>SKU</span></h4>
                            </div>
                            <div class="md:w-3/4 md:py-3 break-all lg:break-words">
                                <p class="flex items-center">{{ variant.sku }}</p>
                            </div>
                        </div>
                        <div class="flex flex-col md:flex-row -mx-6 px-6 py-2 md:py-0 space-y-2 md:space-y-0" dusk="purchasability">
                            <div class="md:w-1/4 md:py-3">
                                <h4 class="font-normal"><span>PURCHASABILITY</span></h4>
                            </div>
                            <div class="md:w-3/4 md:py-3 break-all lg:break-words">
                                <p class="flex items-center">{{ variant.purchasable }}</p>
                            </div>
                        </div>
                        <div class="flex flex-col md:flex-row -mx-6 px-6 py-2 md:py-0 space-y-2 md:space-y-0" dusk="purchasability">
                            <div class="md:w-1/4 md:py-3">
                                <h4 class="font-normal"><span>STOCK</span></h4>
                            </div>
                            <div class="md:w-3/4 md:py-3 break-all lg:break-words">
                                <p class="flex items-center">{{ variant.stock }}</p>
                            </div>
                        </div>
                        <div class="flex flex-col md:flex-row -mx-6 px-6 py-2 md:py-0 space-y-2 md:space-y-0" dusk="purchasability">
                            <div class="md:w-1/4 md:py-3">
                                <h4 class="font-normal"><span>Backorder</span></h4>
                            </div>
                            <div class="md:w-3/4 md:py-3 break-all lg:break-words">
                                <p class="flex items-center">{{ variant.backorder }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <ModalFooter>
                <div class="flex items-center ml-auto">
                                
                    <LoadingButton
                        type="submit"
                        ref="runButton"
                        dusk="confirm-action-button"
                        @click="$emit('close')"
                    >
                        Close
                    </LoadingButton>
                </div>
            </ModalFooter>
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
        variant: {
            type: Object,
            default: null
        },
    },
    data() {
        return {
            loading:false
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
    }
}
</script>

