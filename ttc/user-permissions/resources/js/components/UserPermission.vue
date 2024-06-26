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
                <ModalHeader v-text="'Update Permissions'"/>
                <div class="flex justify-end pr-6 text-primary-500 pt-4">
                    <p class="text-blue-600 underline hover:text-blue-800 cursor-pointer" @click.prevent="showResetModal = true">Reset Permissions</p>
                </div>
                <div class="px-4">
                    <PermissionList 
                        :resources="resources" 
                        :permissions="permissions" 
                        :activeResource="activeResource"
                        :loading="loading"
                        @changeResource="fetchPermissions"
                        @changedPermissions="changedPermissions"
                    />
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
                        @click="updatePermissions"
                    >
                    Update
                    </LoadingButton>
                </div>
            </ModalFooter>
        </div>
        
        <!-- Action Modals -->
        <DeleteModal 
            @confirm="resetPermissions"
            @close="showResetModal = false"
            :show="showResetModal"
            :mode="'Reset'"
            :working.sync="loading"
        />
    </Modal>
</template>

<script>
import PermissionList from '../../../../Permissions/resources/js/components/PermissionList';
import DeleteModal from "./../../../../../vendor/laravel/nova/resources/js/components/Modals/DeleteResourceModal"

export default {
    props: ['resourceName', 'resourceId', 'panel', 'action'],
    components: {
        PermissionList,
        DeleteModal
    },
    data() {
        return {
            loading: false,
            resourceId: this.action.resourceId,
            resources: this.action.permissionResource,
            activeResource: this.action.permissionResource[0],
            tableName: this.action.table,
            permissions: [],
            showResetModal: false
        }
    },
	mounted() {
		if (this.resources[0]) {
			this.fetchPermissions(this.resources[0]);
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

		fetchPermissions(resource) {
			this.activeResource = resource;
            this.loading = true;
            console.log('this.table', this.table)
            Nova.request().get('/nova-api/entities/' + this.resourceId +'/permissions',  {
                params: {
                    resource_name: resource,
                    role_id: this.action.roleId,
                    table: this.tableName
                }
            })
            .then(response => {
				this.permissions = response.data.data;
				this.loading = false;

            })
            .catch(error => {
                console.error('Error:', error);
                this.loading = false;
            });
		},
        
		updatePermissions()
		{
			const data = {
				resource_name: this.activeResource,
				permission_ids: this.permissions.filter(item => item.enabled).map(item => item.id),
                role_id: this.action.roleId,
                table: this.tableName
			};
            this.loading = true;
            Nova.request().put('/nova-api/entities/'+ this.resourceId +'/permissions', data)
            .then(response => {
                Nova.success(this.activeResource + ' permissions updated successfully');
                this.loading = false;
                this.$emit('close');
            })
            .catch(error => {
                if (error.response.status == 422) {
                    Nova.error('Error:'+ error.response.data.message)
                }
                this.loading = false;
            });
		},

        resetPermissions()
        {
			const data = {
                role_id: this.action.roleId,
                table: this.tableName
			};
            this.loading = true;
            Nova.request().put('/nova-api/entities/'+ this.resourceId +'/permissions/reset', data)
            .then(response => {
                Nova.success('Permissions reset successfully');
                this.loading = false;
                this.showResetModal = false;
                this.$emit('close');
            })
            .catch(error => {
                if (error.response.status == 422) {
                    Nova.error('Error:'+ error.response.data.message)
                }
                this.loading = false;
                this.showResetModal = false;
            });
        }
    }
}
</script>

