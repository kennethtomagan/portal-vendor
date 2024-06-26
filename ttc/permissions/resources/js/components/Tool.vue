<template>
	<section>
		<div class="mb-3">
			<h1 class="font-normal text-xl md:text-xl flex items-center"><span>Permissions</span></h1>
		</div>
		<card class="p-6 flex flex-col">
			<div>
				<div class="md:flex items-center mb-3">
   					<div class="flex flex-auto truncate items-center">
						<h1 class="text-xl font-bold mb-4 w-1/1 border-b-1 border-gray-400">Change Permissions</h1>
					</div>
					<div class="ml-auto flex items-center" v-if="!isEdit">
						<a 
							@click.prevent="isEdit = true"
							class="rounded hover:bg-gray-200 dark:hover:bg-gray-800 focus:outline-none focus:ring v-popper--has-tooltip" 
							data-testid="edit-resource" 
							dusk="edit-resource-button" 
							tabindex="1">
							<span size="lg" align="center" component="span" class="cursor-pointer rounded text-sm font-bold focus:outline-none focus:ring ring-primary-200 dark:ring-gray-600 inline-flex items-center justify-center h-9 px-3">
								<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="24" height="24" class="inline-block" role="presentation">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
								</svg>
							</span>
						</a>
					</div>
				</div>
			</div>
			<PermissionList 
				:resources="resources" 
				:permissions="permissions" 
				:activeResource="activeResource"
				:loading="loading"
				@changeResource="fetchPermissions"
				@changedPermissions="changedPermissions"
			/>
			
			<div class="flex items-center ml-auto mt-2 mr-2" v-if="isEdit">
				<CancelButton
					component="button"
					type="button"
					dusk="cancel-action-button"
					class="ml-auto mr-3"
					@click="isEdit = false"
				>
					Cancel
				</CancelButton>

				<LoadingButton
					:disabled="loading"
					:loading="loading"
					@click.prevent="updatePermissions"
				>
					Update
				</LoadingButton>
			</div>
		</card>
	</section>
</template>

<script>
import PermissionList from './PermissionList'

export default {
    props: [
		'resourceId',
		'panel'
    ],
	components: {
		PermissionList
	},
	data() {
		return {
			resources: this.panel?.fields[0]?.permissionResource ?? [],
			activeResource: {},
			loading: false,
			permissions: [],
			isEdit: false,
		};
	},
	mounted() {
		if (this.resources[0]) {
			this.fetchPermissions(this.resources[0]);
		}
	},
	methods: {
		fetchPermissions(resource) {
			this.activeResource = resource;
            this.loading = true;
            Nova.request().get('/nova-api/roles/' + this.resourceId +'/permissions',  {
                params: {
                    resource_name: resource,
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
		changedPermissions(value) {
			this.permissions = value;
		},
		updatePermissions()
		{
			const data = {
				resource_name: this.activeResource,
				permission_ids: this.permissions.filter(item => item.enabled).map(item => item.id)
			};
            this.loading = true;
            Nova.request().put('/nova-api/roles/'+ this.resourceId +'/permissions', data)
            .then(response => {
                Nova.success(this.activeResource + ' permissions updated successfully');
                this.loading = false;
                this.isEdit = false;
				
            })
            .catch(error => {
                if (error.response.status == 422) {
                    Nova.error('Error:'+ error.response.data.message)
                }
                this.loading = false;
            });
		}
	}
};
</script>
<style>

</style>