<template>
    <div class="flex border border-gray-200">
        <!-- Sidebar -->
        <div class="w-36">
            <div class="min-h-screen flex w-full">
                <ul class="bg-white rounded-md w-full" v-if="resources.length > 0">
                    <li 
                        class="py-2 px-4 border-b border-r border-gray-200 font-medium cursor-pointer text-blue-600 uppercase w-full" 
                        :class="resource == activeResource ? 'border-r-0' : 'text-primary-500'"
                        v-for="resource in resources"
                        :key="resource"
                        @click="changeResource(resource)"
                    >
                        <span :class="resource == activeResource ? 'font-bold' : ''">{{ resource }}</span>
                    </li>
                </ul>
            </div>
        </div>
        <!-- Content -->
        <div class="w-full">
            <div class="flex justify-center items-center py-12" v-if="loading">
                <span class="text-xl font-semibold">Loading...</span>
            </div>
            <div class="w-full p-8" v-else>
                <div
                    class="flex items-center justify-between border-b border-gray-200 py-2"
                    v-for="permission in list"
                    :key="permission.id"
                >
                    <span class="text-gray-700 font-bold capitalize ">{{ permission.name.replace(activeResource, '') }}</span>
                    <label class="switch float-r">
                        <span class="custom" v-if="permission.custom">custom</span>
                        <input type="checkbox" v-model="permission.enabled">
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props:[
        'resources',
        'permissions',
        'activeResource',
        'loading'
    ],
    computed: {
        list() {
            return this.permissions;
        }
    },
    methods: {
        changeResource($resource) {
            this.$emit('changeResource', $resource)
        }
    },
    watch: {
        list: {
            handler: function(value) {
                this.$emit('changed', value)
            },
            deep: true
        }
    }
}
</script>
