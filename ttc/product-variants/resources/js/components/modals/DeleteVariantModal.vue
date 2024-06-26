<template>
    <Modal
        :show="show"
        @close-via-escape="handlePreventModalAbandonmentOnClose"
        data-testid="confirm-action-modal"
        tabindex="-1"
        role="dialog"
        :size="'3xl'"
    >
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden space-y-6 mt-8">
            <div class="py-2">
                <ModalHeader v-text="'Remove ' + variant.option_value.name"/>
                <div class="px-4">
                    <p class="px-8 py-4">
                        Are you sure you want to remove {{ variant.option_value.name }} variant?
                    </p>
                    
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
                        @click="$emit('submit', variant)"
                    >
                        Delete
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
        variant: {
            type: Object,
            default: {},
        },
    },
    data() {
        return {
            loading: false,
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

