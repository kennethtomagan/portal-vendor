<template>
    <div>
        <Dropdown class="h-9">
            <slot name="sr-only">
                <span class="sr-only">{{ __('Standalone Actions') }}</span>
            </slot>
            <slot name="trigger">
                <DropdownTrigger
                :show-arrow="false"
                class="rounded hover:bg-gray-200 dark:hover:bg-gray-800 focus:outline-none focus:ring"
                >
                <BasicButton component="span">
                    <Icon :solid="true" type="dots-horizontal" />
                </BasicButton>
                </DropdownTrigger>
            </slot>

            <template #menu>
                <DropdownMenu width="auto" class="px-1">
                <ScrollWrap
                    :height="250"
                    class="divide-y divide-gray-100 dark:divide-gray-800 divide-solid"
                >
                    <slot />

                    <div v-if="actions.length > 0" class="py-1">
                    <DropdownMenuItem
                        v-for="action in actions"
                        :key="action.url"
                        :data-action-id="action.url"
                        as="button"
                        class="border-none"
                        @click="() => handleActionClick(action)"
                        :title="action.title"
                        :destructive="false"
                    >
                        {{ action.title }}
                    </DropdownMenuItem>
                    </div>
                </ScrollWrap>
                </DropdownMenu>
            </template>
            </Dropdown>
        <!----><!---->
        <ConfirmModal 
            :show="showConfirmModal"
            :action="selectedAction"
            @close="showConfirmModal = false"
            @submit="changeStatus"
        />
    </div>
</template>

<script>
import ConfirmModal from './ConfirmModal'

export default {
    props: {
        ticket: {
            type: Object,
            default: {},
        },
    },
    components: {
        ConfirmModal
    },
    data() {
        return {
            showConfirmModal: false,
            selectedAction: {},
        }
    },
    computed: {
        actions() {
            let actions =  [
                {
                    title: 'Move to Pending',
                    status: 'pending',
                    url: '/nova-api/tickets/action?action=move-to-pending&pivotAction=false&search=&filters=W10%3D&trashed='
                },
                {
                    title: 'Move to In Progress',
                    status: 'in-progress',
                    url: '/nova-api/tickets/action?action=move-to-in-progress&pivotAction=false&search=&filters=W10%3D&trashed='
                },
                {
                    title: 'Move to Completed',
                    status: 'completed',
                    url: '/nova-api/tickets/action?action=move-to-completed&pivotAction=false&search=&filters=W10%3D&trashed='
                }
            ];

            return actions.filter(action => action.status !== this.ticket.status);
        },
    },
    methods: {
        handleActionClick(action) {
            this.selectedAction = action;
            this.showConfirmModal = true;
        },
        changeStatus() {
            let formData = {
                ticketId: this.ticket.id,
                status: this.selectedAction.status,
                tenantId: this.ticket.tenant_id,
                connection: this.ticket.connection,
            } 
            Nova.request().put('/api/flag-tickets/status', formData)
            .then(() => {
                Nova.success('The action was executed successfully.');
                this.$emit('reload');
            })
            .catch(error => {
                Nova.error('Error: '+ error.response.data.message)
            });

            this.showConfirmModal = false;
        }
    }
}
</script>

